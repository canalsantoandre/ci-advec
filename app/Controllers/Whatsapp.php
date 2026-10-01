<?php

namespace App\Controllers;

use App\Models\WhatsappModel;
use App\Models\SessionModel;
use App\Models\PerfilModel;
use Config\Services;
use CodeIgniter\API\ResponseTrait;
use Exception;

class Whatsapp extends BaseController
{
    use ResponseTrait;

    protected $session;
    protected $model;

    public function __construct()
    {
        $this->session = Services::session();
        $this->model = new WhatsappModel();
    }

    private function session()
    {
        $data = [];
        $sessionModel = new SessionModel();
        $data = $sessionModel->retornaSessao($data, 'whatsapp/');

        if ($this->isAdmin()) {
            if (!isset($data['sys_action'])) {
                $data['sys_action'] = new \stdClass();
            }
            $data['sys_action']->create = true;
            $data['sys_action']->read   = true;
            $data['sys_action']->update = true;
            $data['sys_action']->delete = true;
        }

        return $data;
    }

    /**
     * Verifica se o usuário autenticado possui o perfil SysAdm (id_perfil = 1 ou nome SysAdm)
     */
    private function isAdmin(): bool
    {
        $session = session();
        $userData = $session->get('dsh_usuario');
        if (empty($userData['obj_user'])) {
            return false;
        }
        $user = $userData['obj_user'];
        $perfilModel = new PerfilModel();
        $perfil = $perfilModel->find($user->id_perfil);
        if ($perfil && (intval($user->id_usuario) === 1 || strtoupper(trim($perfil->nome_perfil)) === 'SYSADM')) {
            return true;
        }
        return false;
    }

    private function validateAuth(): array
    {
        if (!$this->isAdmin()) {
            throw new Exception("Acesso restrito ao perfil SysAdm.", 403);
        }

        $session = session();
        $userData = $session->get('dsh_usuario');
        $user = $userData['obj_user'] ?? null;

        if (!$user) {
            throw new Exception("Sua sessão expirou ou você não está autorizado.", 401);
        }

        return [
            'usuario_id' => (int) ($user->id_usuario ?? 1),
            'empresa_id' => 1
        ];
    }

    private function jsonRespond(array $data, int $status = 200)
    {
        $data['csrf_token'] = csrf_hash();
        return $this->respond($data, $status);
    }

    private function getPlanLimit(int $empresaId = 1): int
    {
        return 2;
    }

    private function getConnection(int $id, array $auth)
    {
        $config = $this->model->find($id);
        if (!$config || $config['empresa_id'] != $auth['empresa_id']) {
            throw new Exception("Conexão não encontrada ou acesso negado.", 404);
        }
        return $config;
    }

    private function generateInstanceName(int $tenantId, int $connectionNumber): string
    {
        return sprintf('advec_wa_%02d', $connectionNumber);
    }

    /**
     * View principal do módulo WhatsApp no ADVEC
     */
    public function index()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('accessdeny');
        }

        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $empresaId = 1;
        $data['title'] = 'Gestão de WhatsApp - ADVEC';
        $data['connections'] = $this->model->getAllByTenant($empresaId);
        $data['connectionsCount'] = $this->model->countByTenant($empresaId);
        $data['planLimit'] = $this->getPlanLimit($empresaId);

        $data['content_view'] = view('_acesso/whatsapp/whatsapp-list', $data);
        return view('_layout', $data);
    }

    public function create()
    {
        try {
            $auth = $this->validateAuth();
            $empresaId = $auth['empresa_id'];

            $name = $this->request->getPost('name');
            $name = trim($name ?? '');

            if (empty($name)) {
                return $this->jsonRespond([
                    'success' => false,
                    'message' => 'O nome da conexão é obrigatório.'
                ], 400);
            }

            $connectionsCount = $this->model->countByTenant($empresaId);
            $planLimit = $this->getPlanLimit($empresaId);

            if ($connectionsCount >= $planLimit) {
                return $this->jsonRespond([
                    'success' => false,
                    'message' => 'Você atingiu o limite de conexões WhatsApp do seu plano.'
                ], 403);
            }

            $apiUrl = env('evolution.apiUrl') ?: 'http://evolution.eabc.com.br';
            $apiKey = env('evolution.apiKey') ?: 'ic6F5CEABCDIGI7636LNnxF5KqKjc9TZaJ';

            $instanceName = $this->generateInstanceName($empresaId, (time() % 100000));

            $data = [
                'empresa_id'    => $empresaId,
                'usuario_id'    => $auth['usuario_id'],
                'name'          => $name,
                'api_url'       => $apiUrl,
                'api_key'       => $apiKey,
                'instance_name' => $instanceName,
                'status'        => 'waiting_qr',
                'connected'     => false
            ];

            $savedId = $this->model->saveConfig($data);

            if (!$savedId) {
                return $this->jsonRespond([
                    'success' => false,
                    'message' => 'Erro ao salvar configurações no banco de dados.'
                ], 500);
            }

            $endpoint = rtrim($apiUrl, '/') . '/instance/create';
            $payload = [
                'instanceName' => $instanceName,
                'token'        => bin2hex(random_bytes(16)),
                'qrcode'       => true,
                'integration'  => 'WHATSAPP-BAILEYS'
            ];

            $client = Services::curlrequest();

            try {
                $response = $client->post($endpoint, [
                    'headers' => [
                        'apikey'       => $apiKey,
                        'Content-Type' => 'application/json'
                    ],
                    'json'        => $payload,
                    'http_errors' => false,
                    'timeout'     => 15
                ]);

                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody(), true);

                if ($statusCode >= 200 && $statusCode < 300) {
                    $this->model->updateStatus($savedId, 'waiting_qr');

                    return $this->jsonRespond([
                        'success' => true,
                        'message' => 'Instância criada com sucesso! Conecte seu aparelho.',
                        'data'    => [
                            'id'            => $savedId,
                            'instance_name' => $instanceName,
                            'status'        => 'waiting_qr',
                            'evolution'     => $body
                        ]
                    ]);
                } else {
                    $errMsg = $body['response']['message'][0] ?? ($body['message'] ?? 'Erro retornado pela Evolution API.');
                    return $this->jsonRespond([
                        'success' => false,
                        'message' => 'Erro ao criar instância na API: ' . $errMsg
                    ], 400);
                }
            } catch (Exception $e) {
                return $this->jsonRespond([
                    'success' => false,
                    'message' => 'Falha ao conectar com o servidor da Evolution API: ' . $e->getMessage()
                ], 502);
            }
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function status($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/instance/connectionState/' . $config['instance_name'];
            $client = Services::curlrequest();

            $response = $client->get($endpoint, [
                'headers' => [
                    'apikey' => $config['api_key']
                ],
                'http_errors' => false,
                'timeout'     => 10
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            $state = $body['instance']['state'] ?? 'close';
            $connected = ($state === 'open');

            $status = 'disconnected';
            if ($connected) {
                $status = 'connected';
            } elseif ($state === 'connecting') {
                $status = 'waiting_qr';
            } elseif ($state === 'close') {
                $status = 'disconnected';
            }

            $this->model->updateConnection($config['id'], $connected);
            $this->model->updateStatus($config['id'], $status);

            $qrBase64 = null;
            if (!$connected) {
                $qrEndpoint = rtrim($config['api_url'], '/') . '/instance/connect/' . $config['instance_name'];
                $qrResponse = $client->get($qrEndpoint, [
                    'headers' => [
                        'apikey' => $config['api_key']
                    ],
                    'http_errors' => false,
                    'timeout'     => 10
                ]);

                if ($qrResponse->getStatusCode() === 200) {
                    $qrBody = json_decode($qrResponse->getBody(), true);
                    $qrBase64 = $qrBody['base64'] ?? null;
                    if ($qrBase64) {
                        $status = 'waiting_qr';
                        $this->model->updateStatus($config['id'], $status);
                    }
                }
            }

            $updatedConfig = $this->model->find($config['id']);

            return $this->jsonRespond([
                'success' => true,
                'data'    => [
                    'connected' => $connected,
                    'state'     => $state,
                    'status'    => $status,
                    'qr_base64' => $qrBase64,
                    'config'    => $updatedConfig
                ]
            ]);
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function qrcode($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/instance/connect/' . $config['instance_name'];
            $client = Services::curlrequest();

            $response = $client->get($endpoint, [
                'headers' => [
                    'apikey' => $config['api_key']
                ],
                'http_errors' => false,
                'timeout'     => 15
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && (isset($body['base64']) || isset($body['code']))) {
                $this->model->updateStatus($config['id'], 'waiting_qr');

                return $this->jsonRespond([
                    'success' => true,
                    'data'    => [
                        'base64' => $body['base64'] ?? null,
                        'code'   => $body['code'] ?? null
                    ]
                ]);
            } else {
                return $this->jsonRespond([
                    'success' => false,
                    'message' => 'Não foi possível gerar o QR Code. Verifique se a instância já está conectada.',
                    'details' => $body
                ], 400);
            }
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function restart($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/instance/restart/' . $config['instance_name'];
            $client = Services::curlrequest();

            $response = $client->post($endpoint, [
                'headers' => [
                    'apikey' => $config['api_key']
                ],
                'http_errors' => false,
                'timeout'     => 15
            ]);

            $body = json_decode($response->getBody(), true);

            return $this->jsonRespond([
                'success' => true,
                'message' => 'Instância reiniciada com sucesso.',
                'data'    => $body
            ]);
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function disconnect($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/instance/logout/' . $config['instance_name'];
            $client = Services::curlrequest();

            $response = $client->delete($endpoint, [
                'headers' => [
                    'apikey' => $config['api_key']
                ],
                'http_errors' => false,
                'timeout'     => 15
            ]);

            $this->model->updateConnection($config['id'], false);
            $this->model->updateStatus($config['id'], 'disconnected');

            return $this->jsonRespond([
                'success' => true,
                'message' => 'WhatsApp desconectado com sucesso.'
            ]);
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function profile($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/chat/fetchProfile/' . $config['instance_name'];
            $client = Services::curlrequest();

            $response = $client->post($endpoint, [
                'headers' => [
                    'apikey'       => $config['api_key'],
                    'Content-Type' => 'application/json'
                ],
                'json'        => ['number' => $config['phone'] ?? ''],
                'http_errors' => false,
                'timeout'     => 15
            ]);

            $body = json_decode($response->getBody(), true);

            $profileData = [];
            if (!empty($body['name'])) {
                $profileData['profile_name'] = $body['name'];
            }
            if (!empty($body['picture'])) {
                $profileData['profile_picture'] = $body['picture'];
            }

            if (!empty($profileData)) {
                $this->model->updateProfile($config['id'], $profileData);
            }

            return $this->jsonRespond([
                'success' => true,
                'message' => 'Perfil sincronizado com sucesso.',
                'data'    => $profileData
            ]);
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function delete($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $endpoint = rtrim($config['api_url'], '/') . '/instance/delete/' . $config['instance_name'];
            $client = Services::curlrequest();

            try {
                $client->delete($endpoint, [
                    'headers' => [
                        'apikey' => $config['api_key']
                    ],
                    'http_errors' => false,
                    'timeout'     => 10
                ]);
            } catch (Exception $e) {
                // Log and proceed to delete local record
            }

            $this->model->delete($config['id']);

            return $this->jsonRespond([
                'success' => true,
                'message' => 'Instância e conexão removidas com sucesso.'
            ]);
        } catch (Exception $e) {
            return $this->jsonRespond([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function export($id = null)
    {
        try {
            $auth = $this->validateAuth();
            $config = $this->getConnection((int)$id, $auth);

            $exportData = [
                'name'          => $config['name'],
                'instance_name' => $config['instance_name'],
                'api_url'       => $config['api_url'],
                'api_key'       => $config['api_key'],
                'phone'         => $config['phone'],
                'status'        => $config['status'],
                'connected'     => (bool)$config['connected'],
                'exported_at'   => date('c')
            ];

            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Content-Disposition', 'attachment; filename="whatsapp_' . $config['instance_name'] . '.json"')
                ->setBody(json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
