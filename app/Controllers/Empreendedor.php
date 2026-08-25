<?php

namespace App\Controllers;

class Empreendedor extends BaseController
{

    private function session()
    {
        /* ------------------------------------------------ */
        /* RECUPERA ACESSOS E DADOS DE ACAO DAS TELAS */
        /* ------------------------------------------------ */
        $data = [];
        $session = new \App\Models\SessionModel();
        $data = $session->retornaSessao($data, 'empreendedor/');

        return $data;
    }
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $convidadoModel = new \App\Models\ConvidadoModel();
        $data['convidados'] = $convidadoModel->listaConvidados();

        $data['content_view'] = view('empreendedor/list', $data);
        return view('_layout', $data);
    }

    public function novo()
    {

        //$data = $this->session();
        $data = [];
        //$data['content_view'] = view('empreendedor/form-cadastro-01', $data);
        $data['content_view'] = view('empreendedor/form-cadastro-02', $data);
        //$data['content_view'] = view('empreendedor/form-cadastro-encerrado', $data);
        return $data['content_view'];
    }

    public function inserir()
    {

        //$data = $this->session();
        $data['fields'] = $this->request->getVar();

        /*INSERE SOLICITANTE */
        helper(['form']);
        $rules = [
            't_nome' => 'required',
            't_email' => 'required|valid_email|alreadyEmailExists[t_email]',
            't_telefone' => 'required|mobileValidation[t_telefone]|alreadyFoneExists[t_telefone]'
            //'cb_acompanhante' => 'greater_than[0]|validateQuantidade[cb_acompanhante,t_qtde_acompanhante]'
        ];
        $errors = [
            't_nome' => [
                'required' => 'Nome'
            ],
            't_email' => [
                'required' => 'Email',
                'valid_email' => 'Formato de email inválido',
                'alreadyEmailExists' => 'Esse email já foi registrado por outro participante'
            ],
            't_telefone' => [
                'mobileValidation' => 'Formato de número de telefone inválido use 11 999999999',
                'alreadyFoneExists' => 'Esse telefone já foi registrado por outro participante'
            ]//,
            // 'cb_acompanhante' => [
            //      'greater_than' => 'Defina a forma de pagamento',
            //      'validateQuantidade' => 'Informe a quantidade de acompanhantes'
            //  ],
        ];


        if (!$this->validate($rules, $errors)) {
            $data['validation'] = $this->validator;
        } else {

            $model = new \App\Models\EmpreendedorModel();

            $telefone = preg_replace('/[^0-9]/', '', $this->request->getGetPost('t_telefone'));

            if ( strlen($telefone) == 9){ $telefone = "11".$telefone;}
            if ( strlen($telefone) == 11){ $telefone = "55".$telefone;}

            $model->set('nome', $this->request->getGetPost('t_nome'));
            $model->set('email', $this->request->getGetPost('t_email'));
            $model->set('telefone', $telefone);
            $model->set('ramo_atividade', $this->request->getGetPost('t_ramo_atividade'));
            $model->set('instagram', $this->request->getGetPost('t_instagram'));
            $model->set('mensagem', $this->request->getGetPost('t_mensagem'));
            $model->set('qtde_convidado', $this->request->getGetPost('t_qtde_acompanhante'));
            $model->set('date_insert', date('Y-m-d H:i:s'));

            $hash_id = SHA1($this->request->getGetPost('t_nome') . date('Y-m-d H:i:s'));
            $model->set('hash_id', $hash_id);
            $model->insert();
            $id_empreendedor = $model->insertID();
            $this->enviarX1($hash_id);
            return redirect()->to('confirm-registration/' . $hash_id);
        }

        $data['content_view'] = view('empreendedor/form-cadastro-02', $data);
        return $data['content_view'];
    }

    public function confirmar($hash_id)
    {
        $arg = [];
        $arg['hash_id'] = $hash_id;

        $model = new \App\Models\EmpreendedorModel();
        $data["inscricao"] = $model->findByColumn($arg, true);

        $data['content_view'] = view('empreendedor/form-cadastro-02-confirm', $data);
        return $data['content_view'];
    }

    public function lista()
    {

        $data = $this->session();

        $model = new \App\Models\EmpreendedorModel();
        $empreendedor = $model->lista();
        $data['empreendedor'] = $empreendedor;

        $totalConvidados = 0;
        $totalGeral = 0;
        foreach ($empreendedor as $row) {
            $totalConvidados += $row->qtde_convidado;
            $totalGeral += 1 + $row->qtde_convidado;
        }
        $data['totalConvidados'] = $totalConvidados;
        $data['totalGeral'] = $totalGeral;
        $data['content_view'] = view('empreendedor/empreendedor-list', $data);
        return view('_layout', $data);
    }

    public function enviarX1($hash_id)
    {

        $arg = [];
        $arg['hash_id'] = $hash_id;

        $model = new \App\Models\EmpreendedorModel();
        $inscricao = $model->findByColumn($arg, true);

        $phoneNumber = $inscricao->telefone;
        // Limpa o número de telefone, mantendo apenas dígitos
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);


        $message = '*'.ucfirst($inscricao->nome).'*, sua inscrição no Encontro de Empreendedores foi realizada com sucesso!🤝

Acontecerá dia *15/03/2025* às 09h00 

*Local:* 
ADVEC Santo André.
Av. Industrial, 1607 - Jardim
Santo André - SP
09080-510

Mais informações fique ligado no instagram https://instagram.com/advecsantoandre

Fique na Paz e até lá!!';


        // URL da API
        $url = 'https://api.eabc.com.br/api/messages/send';

        // Mensagem a ser enviada
        //$message = $user['message'];

        // Cabeçalhos da requisição
        $headers = [
            'Content-Type: application/json',
            'Authorization: 5662D236881AFCA1147F0F78D63B308C1E1ED956' 
        ];

        // Dados da requisição
        $data = json_encode([
            'number' => $phoneNumber,
            'body' => $message
        ]);

        // Iniciar o cURL
        $ch = curl_init();

        // Configurações do cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica se houve erro
        if (curl_errno($ch)) {
            return 'Erro ao enviar mensagem: ' . curl_error($ch);
        }

        // Verifica o código de status da resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Fecha a conexão cURL
        curl_close($ch);

        // Verifica o código de status HTTP
        if ($httpCode == 200) {
            echo json_encode(["status" => "success", "message" => "Message sent to whatsApp - " . $phoneNumber]);
        } else {
            echo json_encode(["status" => "success", "message" => "Message not sent to whatsApp - " . $phoneNumber]);
        }

    }
}
