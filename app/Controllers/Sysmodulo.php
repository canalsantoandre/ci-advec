<?php

namespace App\Controllers;

use App\Models\SessionModel;

class Sysmodulo extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'sysmodulo/');

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

    private function isAdmin()
    {
        $session = session();
        $userData = $session->get('dsh_usuario');
        if (empty($userData['obj_user'])) {
            return false;
        }
        $user = $userData['obj_user'];
        $perfilModel = new \App\Models\PerfilModel();
        $perfil = $perfilModel->find($user->id_perfil);
        if ($perfil && (intval($user->id_usuario) === 1 || strtoupper(trim($perfil->nome_perfil)) === 'SYSADM')) {
            return true;
        }
        return false;
    }

    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $db = db_connect();

        // 1. Categorias
        $categorias = $db->table('tb_sys_categoria_modulo')
            ->orderBy('ordem_exibicao_categoria', 'ASC')
            ->get()
            ->getResult();

        // 2. Módulos
        $modulosAll = $db->table('tb_sys_modulo')
            ->orderBy('ordem_exibicao_modulo', 'ASC')
            ->get()
            ->getResult();

        // 3. Ações por Módulo
        $acoesAll = $db->table('tb_sys_modulo_acao')->get()->getResult();
        $acoesPorModulo = [];
        foreach ($acoesAll as $act) {
            $acoesPorModulo[$act->id_modulo][] = $act->modulo_acao;
        }

        // 4. Estrutura por Categoria
        $modulosPorCategoria = [];
        foreach ($categorias as $cat) {
            $modulosPorCategoria[$cat->id_categoria_modulo] = [
                'categoria' => $cat,
                'menus'     => []
            ];
        }

        $modulosIndex = [];
        foreach ($modulosAll as $m) {
            $m->acoes = isset($acoesPorModulo[$m->id_modulo]) ? $acoesPorModulo[$m->id_modulo] : [];
            $m->subitens = [];
            $modulosIndex[$m->id_modulo] = $m;
        }

        foreach ($modulosAll as $m) {
            $catId = $m->id_categoria_modulo;
            if (!empty($m->id_modulo_pai) && isset($modulosIndex[$m->id_modulo_pai])) {
                $modulosIndex[$m->id_modulo_pai]->subitens[] = $m;
            } else {
                if (isset($modulosPorCategoria[$catId])) {
                    $modulosPorCategoria[$catId]['menus'][] = $m;
                }
            }
        }

        $data['modulosPorCategoria'] = $modulosPorCategoria;
        $data['categorias'] = $categorias;
        $data['content_view'] = view('_acesso/sys_modulo/sys-modulo-list', $data);
        return view('_layout', $data);
    }

    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('sysmodulo')->with('error', 'Acesso não permitido.');
        }

        $db = db_connect();

        $data['categorias'] = $db->table('tb_sys_categoria_modulo')
            ->orderBy('ordem_exibicao_categoria', 'ASC')
            ->get()
            ->getResult();

        $data['modulosPai'] = $db->table('tb_sys_modulo')
            ->where('id_modulo_pai', null)
            ->orderBy('nome_modulo', 'ASC')
            ->get()
            ->getResult();

        $data['modulo'] = null;
        $data['acoesModulo'] = [];
        $data['content_view'] = view('_acesso/sys_modulo/sys-modulo-form', $data);
        return view('_layout', $data);
    }

    public function editar($id_modulo)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $db = db_connect();

        $modulo = $db->table('tb_sys_modulo')
            ->where('id_modulo', $id_modulo)
            ->get()
            ->getFirstRow();

        if (!$modulo) {
            return redirect()->to('sysmodulo')->with('error', 'Módulo não encontrado.');
        }

        $data['categorias'] = $db->table('tb_sys_categoria_modulo')
            ->orderBy('ordem_exibicao_categoria', 'ASC')
            ->get()
            ->getResult();

        $data['modulosPai'] = $db->table('tb_sys_modulo')
            ->where('id_modulo_pai', null)
            ->where('id_modulo !=', $id_modulo)
            ->orderBy('nome_modulo', 'ASC')
            ->get()
            ->getResult();

        $acoes = $db->table('tb_sys_modulo_acao')
            ->where('id_modulo', $id_modulo)
            ->get()
            ->getResult();

        $acoesArr = [];
        foreach ($acoes as $ac) {
            $acoesArr[] = $ac->modulo_acao;
        }

        $data['modulo'] = $modulo;
        $data['acoesModulo'] = $acoesArr;
        $data['content_view'] = view('_acesso/sys_modulo/sys-modulo-form', $data);
        return view('_layout', $data);
    }

    public function salvar()
    {
        $data = $this->session();

        $id_modulo = $this->request->getPost('id_modulo');
        $is_novo   = empty($id_modulo);

        if ($is_novo && empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }
        if (!$is_novo && empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $nome_modulo          = trim($this->request->getPost('nome_modulo') ?? '');
        $uri_modulo           = trim($this->request->getPost('uri_modulo') ?? '');
        $id_categoria_modulo  = $this->request->getPost('id_categoria_modulo');
        $id_modulo_pai        = $this->request->getPost('id_modulo_pai');
        $icon_class_modulo    = trim($this->request->getPost('icon_class_modulo') ?? '');
        $status_modulo        = $this->request->getPost('status_modulo') ?? 1;
        $acoesInput           = $this->request->getPost('modulo_acoes');

        if (empty($nome_modulo) || empty($uri_modulo) || empty($id_categoria_modulo)) {
            return redirect()->back()->withInput()->with('error', 'Nome do módulo, URI e Categoria são obrigatórios.');
        }

        // Garante barra / no final da URI se não houver
        if (substr($uri_modulo, -1) !== '/') {
            $uri_modulo .= '/';
        }

        $db = db_connect();
        $db->transStart();

        if ($is_novo) {
            // Regra MAX(id_modulo) + 1
            $maxIdRow = $db->table('tb_sys_modulo')
                ->selectMax('id_modulo')
                ->get()
                ->getFirstRow();

            $id_modulo = (!empty($maxIdRow->id_modulo)) ? ($maxIdRow->id_modulo + 1) : 1;

            $maxOrdemRow = $db->table('tb_sys_modulo')
                ->where('id_categoria_modulo', $id_categoria_modulo)
                ->selectMax('ordem_exibicao_modulo')
                ->get()
                ->getFirstRow();

            $ordem = (!empty($maxOrdemRow->ordem_exibicao_modulo)) ? ($maxOrdemRow->ordem_exibicao_modulo + 1) : 1;

            $db->table('tb_sys_modulo')->insert([
                'id_modulo'             => $id_modulo,
                'id_categoria_modulo'   => $id_categoria_modulo,
                'id_modulo_pai'         => !empty($id_modulo_pai) ? $id_modulo_pai : null,
                'nome_modulo'           => $nome_modulo,
                'icon_class_modulo'     => $icon_class_modulo,
                'uri_modulo'            => $uri_modulo,
                'ordem_exibicao_modulo' => $ordem,
                'status_modulo'         => (int)$status_modulo
            ]);
        } else {
            $db->table('tb_sys_modulo')
                ->where('id_modulo', $id_modulo)
                ->update([
                    'id_categoria_modulo' => $id_categoria_modulo,
                    'id_modulo_pai'       => !empty($id_modulo_pai) ? $id_modulo_pai : null,
                    'nome_modulo'         => $nome_modulo,
                    'icon_class_modulo'   => $icon_class_modulo,
                    'uri_modulo'          => $uri_modulo,
                    'status_modulo'       => (int)$status_modulo
                ]);
        }

        // Atualizar Ações do Módulo na tb_sys_modulo_acao
        $db->table('tb_sys_modulo_acao')->where('id_modulo', $id_modulo)->delete();

        $acoesArr = ['read'];
        if (!empty($acoesInput)) {
            $rawAcoes = is_array($acoesInput) ? $acoesInput : explode(',', (string)$acoesInput);
            foreach ($rawAcoes as $acItem) {
                $slug = strtolower(trim((string)$acItem));
                $slug = preg_replace('/[^a-z0-9_]/', '', $slug);
                if (!empty($slug)) {
                    $acoesArr[] = $slug;
                }
            }
        }
        $acoesArr = array_values(array_unique($acoesArr));

        foreach ($acoesArr as $acao) {
            $db->table('tb_sys_modulo_acao')->insert([
                'id_modulo'   => $id_modulo,
                'modulo_acao' => $acao
            ]);
        }

        // Garantir associação para o perfil SysAdm (1)
        $id_perfil_admin = 1;
        $hasPerfilModulo = $db->table('tb_sys_perfil_modulo')
            ->where(['id_perfil' => $id_perfil_admin, 'id_modulo' => $id_modulo])
            ->countAllResults() > 0;

        if (!$hasPerfilModulo) {
            $db->table('tb_sys_perfil_modulo')->insert([
                'id_perfil' => $id_perfil_admin,
                'id_modulo' => $id_modulo
            ]);
        }

        foreach ($acoesArr as $acao) {
            $hasPerfilAction = $db->table('tb_sys_perfil_modulo_acao')
                ->where(['id_perfil' => $id_perfil_admin, 'id_modulo' => $id_modulo, 'modulo_acao' => $acao])
                ->countAllResults() > 0;

            if (!$hasPerfilAction) {
                $db->table('tb_sys_perfil_modulo_acao')->insert([
                    'id_perfil'   => $id_perfil_admin,
                    'id_modulo'   => $id_modulo,
                    'modulo_acao' => $acao
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Erro ao salvar o módulo.');
        }

        return redirect()->to('sysmodulo')->with('success', 'Módulo salvo com sucesso!');
    }

    public function apagar($id_modulo)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $db = db_connect();

        $hasChildren = $db->table('tb_sys_modulo')
            ->where('id_modulo_pai', $id_modulo)
            ->countAllResults() > 0;

        if ($hasChildren) {
            return redirect()->to('sysmodulo')->with('error', 'Existem submenus vinculados a este módulo.');
        }

        $db->transStart();
        $db->table('tb_sys_perfil_modulo_acao')->where('id_modulo', $id_modulo)->delete();
        $db->table('tb_sys_perfil_modulo')->where('id_modulo', $id_modulo)->delete();
        $db->table('tb_sys_modulo_acao')->where('id_modulo', $id_modulo)->delete();
        $db->table('tb_sys_modulo')->where('id_modulo', $id_modulo)->delete();
        $db->transComplete();

        return redirect()->to('sysmodulo')->with('success', 'Módulo excluído com sucesso.');
    }

    public function reordenar()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setJSON(['erro' => 1, 'mensagem' => 'Acesso negado.']);
        }

        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['erro' => 1, 'mensagem' => 'IDs não informados.']);
        }

        $db = db_connect();
        $db->transStart();

        $ordem = 1;
        foreach ($ids as $id) {
            $db->table('tb_sys_modulo')
                ->where('id_modulo', (int)$id)
                ->update(['ordem_exibicao_modulo' => $ordem]);
            $ordem++;
        }

        $db->transComplete();
        return $this->response->setJSON(['erro' => 0, 'mensagem' => 'Ordenação atualizada com sucesso!']);
    }

    public function salvarCategoria()
    {
        $data = $this->session();

        $id_categoria_modulo = $this->request->getPost('id_categoria_modulo');
        $nome_categoria_modulo = trim($this->request->getPost('nome_categoria_modulo') ?? '');

        if (empty($nome_categoria_modulo)) {
            return $this->response->setJSON(['erro' => 1, 'mensagem' => 'O nome da categoria é obrigatório.']);
        }

        $db = db_connect();
        $db->transStart();

        if (empty($id_categoria_modulo)) {
            $maxId = $db->table('tb_sys_categoria_modulo')->selectMax('id_categoria_modulo')->get()->getFirstRow();
            $id_cat = (!empty($maxId->id_categoria_modulo)) ? ($maxId->id_categoria_modulo + 1) : 1;

            $maxOrdem = $db->table('tb_sys_categoria_modulo')->selectMax('ordem_exibicao_categoria')->get()->getFirstRow();
            $ordem = (!empty($maxOrdem->ordem_exibicao_categoria)) ? ($maxOrdem->ordem_exibicao_categoria + 1) : 1;

            $db->table('tb_sys_categoria_modulo')->insert([
                'id_categoria_modulo'      => $id_cat,
                'nome_categoria_modulo'    => $nome_categoria_modulo,
                'ordem_exibicao_categoria' => $ordem,
                'exibir_titulo'            => 1,
                'status_categoria'         => 1
            ]);
        } else {
            $db->table('tb_sys_categoria_modulo')
                ->where('id_categoria_modulo', $id_categoria_modulo)
                ->update(['nome_categoria_modulo' => $nome_categoria_modulo]);
        }

        $db->transComplete();
        return $this->response->setJSON(['erro' => 0, 'mensagem' => 'Categoria salva com sucesso!']);
    }

    public function apagarCategoria($id_categoria_modulo)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('sysmodulo')->with('error', 'Acesso negado.');
        }

        $db = db_connect();
        $hasModules = $db->table('tb_sys_modulo')
            ->where('id_categoria_modulo', $id_categoria_modulo)
            ->countAllResults() > 0;

        if ($hasModules) {
            return redirect()->to('sysmodulo')->with('error', 'Existem módulos vinculados a esta categoria.');
        }

        $db->table('tb_sys_categoria_modulo')
            ->where('id_categoria_modulo', $id_categoria_modulo)
            ->delete();

        return redirect()->to('sysmodulo')->with('success', 'Categoria excluída.');
    }
}
