<?php

namespace App\Controllers;

use App\Models\ResourceModel;
use App\Models\ResourceTypeModel;
use App\Models\CollectionModel;
use App\Models\ScheduleResourceModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoGestorModel;
use App\Models\SessionModel;
use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;
use Exception;

class Resource extends BaseController
{
    use ResponseTrait;

    protected $resourceModel;
    protected $resourceTypeModel;
    protected $collectionModel;
    protected $scheduleResourceModel;
    protected $departamentoModel;
    protected $departamentoGestorModel;

    public function __construct()
    {
        $this->resourceModel           = new ResourceModel();
        $this->resourceTypeModel       = new ResourceTypeModel();
        $this->collectionModel         = new CollectionModel();
        $this->scheduleResourceModel   = new ScheduleResourceModel();
        $this->departamentoModel       = new DepartamentoModel();
        $this->departamentoGestorModel = new DepartamentoGestorModel();
    }

    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'resource/');
        return $data;
    }

    /**
     * View principal: Listagem de Recursos e Coleções da Biblioteca
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $departamentosPermitidos    = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $depFiltro = $this->request->getGet('department_id');
        $filtros = [
            'department_id'    => $depFiltro,
            'resource_type_id' => $this->request->getGet('resource_type_id'),
            'busca'            => trim((string)$this->request->getGet('busca')),
            'status'           => $this->request->getGet('status') !== null ? $this->request->getGet('status') : 1
        ];

        if ($depFiltro !== null && $depFiltro !== '') {
            if ($depFiltro === 'global' || $depFiltro === '0') {
                $filtros['department_id'] = 'global';
            } else {
                $depId = (int)$depFiltro;
                if (!in_array($depId, $departamentosPermitidosIds)) {
                    $filtros['department_id'] = !empty($departamentosPermitidosIds) ? $departamentosPermitidosIds[0] : -1;
                } else {
                    $filtros['department_id'] = $depId;
                }
            }
        } else {
            $filtros['department_id'] = null;
            $filtros['departamentos_permitidos'] = $departamentosPermitidosIds;
        }

        $data['title']                      = 'Biblioteca - Gestão de Materiais & Coleções';
        $data['filtros']                    = $filtros;
        $data['tipos']                      = $this->resourceTypeModel->getTiposAtivos();
        $data['departamentos']              = $departamentosPermitidos;
        $data['departamentosPermitidosIds'] = $departamentosPermitidosIds;
        $data['recursos']                   = $this->resourceModel->listarRecursos($filtros);
        $data['colecoes']                   = $this->collectionModel->listarColecoes($filtros);

        // Estatísticas Rápidas
        $data['totalRecursos']   = count($data['recursos']);
        $data['totalColecoes']   = count($data['colecoes']);

        $data['content_view']  = view('resource/resource-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Criação de Recurso
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $departamentosPermitidos = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);

        $data['title']         = 'Novo Material / Recurso - Biblioteca';
        $data['recurso']       = null;
        $data['tipos']         = $this->resourceTypeModel->getTiposAtivos();
        $data['departamentos'] = $departamentosPermitidos;

        $data['content_view']  = view('resource/resource-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Recurso
     */
    public function editar($id = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $recurso = $this->resourceModel->buscarPorId((int)$id);
        if (!$recurso) {
            session()->setFlashdata('erro', 'Recurso não encontrado.');
            return redirect()->to(base_url('resource'));
        }

        $departamentosPermitidos = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);

        $data['title']         = 'Editar Material / Recurso - ' . esc($recurso->title);
        $data['recurso']       = $recurso;
        $data['tipos']         = $this->resourceTypeModel->getTiposAtivos();
        $data['departamentos'] = $departamentosPermitidos;

        $data['content_view']  = view('resource/resource-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva Recurso (POST) com Auto-Parsing e Sanitização Estrita
     */
    public function salvar()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create) && empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Você não possui permissão para salvar materiais.']);
        }

        try {
            $id               = (int)$this->request->getPost('id');
            $title            = trim((string)$this->request->getPost('title'));
            $description      = trim((string)$this->request->getPost('description'));
            $url              = trim((string)$this->request->getPost('url'));
            $content_text     = trim((string)$this->request->getPost('content_text'));
            $resource_type_id = (int)$this->request->getPost('resource_type_id');
            $department_id    = $this->request->getPost('department_id') !== '' ? (int)$this->request->getPost('department_id') : null;
            $status           = (int)($this->request->getPost('status') ?? 1);

            // Validações Básicas
            if (empty($title)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'O título do recurso é obrigatório.']);
            }

            if ($resource_type_id <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Selecione o tipo de mídia.']);
            }

            // Sanitização de Texto / Prevenção XSS
            $titleClean       = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
            $descriptionClean = htmlspecialchars(strip_tags($description), ENT_QUOTES, 'UTF-8');

            // Validação Estrita de Protocolo de URL (apenas http:// ou https://)
            if (!empty($url)) {
                if (!ResourceModel::isValidHttpUrl($url)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'URL inválida. Por motivos de segurança, são permitidos estritamente os protocolos http:// ou https://.'
                    ]);
                }
            }

            // Auto-Parsing de Metadados (YouTube, Spotify, Drive, PDF)
            $parsed = ResourceModel::parseUrl($url, $resource_type_id);

            $session = session();
            $user = $session->get('dsh_usuario')['obj_user'] ?? null;
            $userId = $user ? (int)$user->id_usuario : 1;

            $dadosSalvar = [
                'title'            => $titleClean,
                'description'      => $descriptionClean,
                'url'              => !empty($url) ? $url : null,
                'content_text'     => !empty($content_text) ? $content_text : null,
                'resource_type_id' => $resource_type_id,
                'department_id'    => ($department_id && $department_id > 0) ? $department_id : null,
                'thumbnail_url'    => $parsed['thumbnail_url'],
                'external_id'      => $parsed['external_id'],
                'provider'         => $parsed['provider'],
                'embed_url'        => $parsed['embed_url'],
                'metadata'         => !empty($parsed['metadata']) ? json_encode($parsed['metadata']) : null,
                'status'           => $status,
                'created_by'       => $userId
            ];

            if ($id > 0) {
                $this->resourceModel->update($id, $dadosSalvar);
                $savedId = $id;
                $msg = 'Material atualizado com sucesso!';
            } else {
                $savedId = $this->resourceModel->insert($dadosSalvar);
                $msg = 'Material cadastrado com sucesso na Biblioteca!';
            }

            return $this->response->setJSON([
                'status'      => 'success',
                'message'     => $msg,
                'resource_id' => $savedId
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Erro ao processar material: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exclui Recurso (Soft Delete)
     */
    public function excluir($id = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Você não possui permissão para excluir materiais.']);
        }

        $id = (int)$id;
        if ($id <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID inválido.']);
        }

        $this->resourceModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Recurso removido com sucesso!']);
    }

    /**
     * Formulário de Criação de Coleção / Playlist
     */
    public function novaColecao()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $departamentosPermitidos    = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $data['title']         = 'Nova Coleção / Repertório - Biblioteca';
        $data['colecao']       = null;
        $data['departamentos'] = $departamentosPermitidos;
        $data['recursosDisponiveis'] = $this->resourceModel->listarRecursos([
            'status' => 1,
            'departamentos_permitidos' => $departamentosPermitidosIds
        ]);

        $data['content_view']  = view('resource/collection-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Coleção
     */
    public function editarColecao($id = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $colecao = $this->collectionModel->getColecaoComRecursos((int)$id);
        if (!$colecao) {
            session()->setFlashdata('erro', 'Coleção não encontrada.');
            return redirect()->to(base_url('resource'));
        }

        $departamentosPermitidos    = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $data['title']         = 'Editar Coleção - ' . esc($colecao->title);
        $data['colecao']       = $colecao;
        $data['departamentos'] = $departamentosPermitidos;
        $data['recursosDisponiveis'] = $this->resourceModel->listarRecursos([
            'status' => 1,
            'departamentos_permitidos' => $departamentosPermitidosIds
        ]);

        $data['content_view']  = view('resource/collection-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva Coleção com Drag-and-Drop de Recursos
     */
    public function salvarColecao()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create) && empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Você não possui permissão para salvar coleções.']);
        }

        try {
            $id            = (int)$this->request->getPost('id');
            $title         = trim((string)$this->request->getPost('title'));
            $description   = trim((string)$this->request->getPost('description'));
            $department_id = $this->request->getPost('department_id') !== '' ? (int)$this->request->getPost('department_id') : null;
            $cover_url     = trim((string)$this->request->getPost('cover_url'));
            $status        = (int)($this->request->getPost('status') ?? 1);

            $resourceIdsRaw = $this->request->getPost('resource_ids');
            $resourceIds = [];
            if (is_array($resourceIdsRaw)) {
                $resourceIds = array_map('intval', $resourceIdsRaw);
            } elseif (is_string($resourceIdsRaw) && trim($resourceIdsRaw) !== '') {
                $resourceIds = array_map('intval', explode(',', $resourceIdsRaw));
            }

            if (empty($title)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'O título da coleção é obrigatório.']);
            }

            $titleClean       = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
            $descriptionClean = htmlspecialchars(strip_tags($description), ENT_QUOTES, 'UTF-8');

            $session = session();
            $user = $session->get('dsh_usuario')['obj_user'] ?? null;
            $userId = $user ? (int)$user->id_usuario : 1;

            $dadosColecao = [
                'title'         => $titleClean,
                'description'   => $descriptionClean,
                'department_id' => ($department_id && $department_id > 0) ? $department_id : null,
                'cover_url'     => !empty($cover_url) ? $cover_url : null,
                'status'        => $status,
                'created_by'    => $userId
            ];

            if ($id > 0) {
                $this->collectionModel->update($id, $dadosColecao);
                $collectionId = $id;
            } else {
                $collectionId = $this->collectionModel->insert($dadosColecao);
            }

            // Sincroniza a lista ordenada de recursos da coleção
            $this->collectionModel->sincronizarRecursos($collectionId, $resourceIds);

            return $this->response->setJSON([
                'status'        => 'success',
                'message'       => 'Coleção e materiais salvos com sucesso!',
                'collection_id' => $collectionId
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Erro ao salvar coleção: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exclui Coleção
     */
    public function excluirColecao($id = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Você não possui permissão para excluir coleções.']);
        }

        $id = (int)$id;
        if ($id <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID inválido.']);
        }

        $this->collectionModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Coleção removida com sucesso!']);
    }

    /**
     * AJAX: Auto-Parse de URL em tempo real para preview instantâneo no formulário
     */
    public function autoParseUrl()
    {
        $url = trim((string)$this->request->getGet('url'));
        $typeId = (int)$this->request->getGet('type_id') ?: 1;

        if (empty($url) || !ResourceModel::isValidHttpUrl($url)) {
            return $this->response->setJSON([
                'valid'   => false,
                'message' => 'Informe uma URL válida com protocolo http:// ou https://'
            ]);
        }

        $parsed = ResourceModel::parseUrl($url, $typeId);
        return $this->response->setJSON([
            'valid' => true,
            'data'  => $parsed
        ]);
    }

    /**
     * AJAX: Retorna lista de recursos disponíveis para o modal de anexos na escala
     */
    public function apiList()
    {
        $session = session();
        $user = $session->get('dsh_usuario')['obj_user'] ?? null;
        $departamentosPermitidos = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($user);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $departmentId = $this->request->getGet('department_id');
        $typeId       = $this->request->getGet('type_id');
        $busca        = trim((string)$this->request->getGet('busca'));

        $filtros = [
            'resource_type_id' => $typeId,
            'busca'            => $busca,
            'status'           => 1
        ];

        if ($departmentId !== null && $departmentId !== '') {
            $filtros['department_or_global'] = (int)$departmentId;
        } else {
            $filtros['departamentos_permitidos'] = $departamentosPermitidosIds;
        }

        $recursos = $this->resourceModel->listarRecursos($filtros);
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $recursos
        ]);
    }

    /**
     * AJAX: Retorna lista de coleções disponíveis para o modal de anexos na escala
     */
    public function apiCollections()
    {
        $session = session();
        $user = $session->get('dsh_usuario')['obj_user'] ?? null;
        $departamentosPermitidos = $this->departamentoGestorModel->getDepartamentosPermitidosPorUsuario($user);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $departmentId = $this->request->getGet('department_id');
        $filtros = [
            'status' => 1
        ];

        if ($departmentId !== null && $departmentId !== '') {
            $filtros['department_or_global'] = (int)$departmentId;
        } else {
            $filtros['departamentos_permitidos'] = $departamentosPermitidosIds;
        }

        $colecoes = $this->collectionModel->listarColecoes($filtros);
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $colecoes
        ]);
    }

    /**
     * AJAX: Retorna os recursos já anexados a um culto/escala
     */
    public function getRecursosEscala()
    {
        $dataCulto       = (string)$this->request->getGet('data_culto');
        $idCultoPadrao   = (int)$this->request->getGet('id_culto_padrao');
        $idDepartamento  = (int)$this->request->getGet('id_departamento');
        $idAreaRaw       = $this->request->getGet('id_area');
        $idArea          = ($idAreaRaw !== null && $idAreaRaw !== '') ? (int)$idAreaRaw : null;

        $recursos = $this->scheduleResourceModel->getRecursosDoCulto($dataCulto, $idCultoPadrao, $idDepartamento, $idArea);
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $recursos
        ]);
    }

    /**
     * AJAX: Anexa recursos avulsos ou uma coleção completa na escala do culto
     */
    public function anexarNaEscala()
    {
        try {
            $dataCulto      = trim((string)$this->request->getPost('data_culto'));
            $idCultoPadrao  = (int)$this->request->getPost('id_culto_padrao');
            $idDepartamento = (int)$this->request->getPost('id_departamento');
            $idArea         = (int)($this->request->getPost('id_area') ?? 0);
            $collectionId   = (int)$this->request->getPost('collection_id');
            $rawResourceIds = $this->request->getPost('resource_ids');

            if (empty($dataCulto) || $idDepartamento <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data e departamento são obrigatórios.']);
            }

            $session = session();
            $user = $session->get('dsh_usuario')['obj_user'] ?? null;
            $userId = $user ? (int)$user->id_usuario : 1;

            $totalAnexados = 0;

            // Se for anexar coleção completa (desmembra itens em schedule_resources)
            if ($collectionId > 0) {
                $totalAnexados = $this->scheduleResourceModel->anexarColecaoNaEscala($dataCulto, $idCultoPadrao, $idDepartamento, $idArea, $collectionId, $userId);
            } elseif (!empty($rawResourceIds)) {
                $resourceIds = is_array($rawResourceIds) ? array_map('intval', $rawResourceIds) : array_map('intval', explode(',', (string)$rawResourceIds));
                $totalAnexados = $this->scheduleResourceModel->anexarRecursos($dataCulto, $idCultoPadrao, $idDepartamento, $idArea, $resourceIds, $userId);
            }

            $recursosAtualizados = $this->scheduleResourceModel->getRecursosDoCulto($dataCulto, $idCultoPadrao, $idDepartamento);

            return $this->response->setJSON([
                'status'         => 'success',
                'message'        => "{$totalAnexados} material(is) anexado(s) à escala!",
                'total_anexados' => $totalAnexados,
                'data'           => $recursosAtualizados
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao anexar material: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Remove um recurso específico da escala do culto
     */
    public function removerDaEscala()
    {
        try {
            $dataCulto      = trim((string)$this->request->getPost('data_culto'));
            $idCultoPadrao  = (int)$this->request->getPost('id_culto_padrao');
            $idDepartamento = (int)$this->request->getPost('id_departamento');
            $idAreaRaw       = $this->request->getPost('id_area');
            $idArea          = ($idAreaRaw !== null && $idAreaRaw !== '') ? (int)$idAreaRaw : null;
            $resourceId     = (int)$this->request->getPost('resource_id');

            $this->scheduleResourceModel->removerRecursoDaEscala($dataCulto, $idCultoPadrao, $idDepartamento, $resourceId, $idArea);
            $recursosAtualizados = $this->scheduleResourceModel->getRecursosDoCulto($dataCulto, $idCultoPadrao, $idDepartamento);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Material desanexado da escala!',
                'data'    => $recursosAtualizados
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao desanexar material: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Reordena os recursos anexados à escala via Drag-and-Drop
     */
    public function reordenarEscala()
    {
        try {
            $dataCulto      = trim((string)$this->request->getPost('data_culto'));
            $idCultoPadrao  = (int)$this->request->getPost('id_culto_padrao');
            $idDepartamento = (int)$this->request->getPost('id_departamento');
            $rawResourceIds = $this->request->getPost('resource_ids');

            $resourceIds = is_array($rawResourceIds) ? array_map('intval', $rawResourceIds) : array_map('intval', explode(',', (string)$rawResourceIds));

            $this->scheduleResourceModel->reordenarRecursos($dataCulto, $idCultoPadrao, $idDepartamento, $resourceIds);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Ordem dos materiais atualizada com sucesso!'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao reordenar: ' . $e->getMessage()]);
        }
    }
}
