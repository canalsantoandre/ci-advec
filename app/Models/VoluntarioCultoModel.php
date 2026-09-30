<?php

namespace App\Models;

use CodeIgniter\Model;

class VoluntarioCultoModel extends Model
{
    protected $table            = 'tb_voluntario_culto';
    protected $primaryKey       = 'id_voluntario_culto';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_voluntario',
        'id_culto_padrao',
        'date_insert'
    ];

    /**
     * Retorna todos os cultos padrão vinculados à disponibilidade de um voluntário
     */
    public function getCultosDoVoluntario($id_voluntario)
    {
        $db = db_connect();
        $builder = $db->table('tb_voluntario_culto as vc');
        $builder->select('
            vc.*,
            cp.nome_culto,
            cp.dia_semana,
            cp.horario_inicio,
            cp.horario_termino,
            cp.descricao,
            cp.cor_evento,
            cp.status_culto
        ');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = vc.id_culto_padrao', 'inner');
        $builder->where('vc.id_voluntario', (int)$id_voluntario);
        $builder->orderBy('cp.dia_semana', 'ASC');
        $builder->orderBy('cp.horario_inicio', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna array simples com os IDs dos cultos padrão selecionados pelo voluntário [1, 2, 5]
     */
    public function getIdsCultosDoVoluntario($id_voluntario)
    {
        $db = db_connect();
        $rows = $db->table($this->table)
            ->select('id_culto_padrao')
            ->where('id_voluntario', (int)$id_voluntario)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'id_culto_padrao'));
    }

    /**
     * Sincroniza vínculos de disponibilidade de cultos do voluntário
     * $cultoIds: array de IDs de cultos padrão selecionados
     */
    public function sincronizarCultos($id_voluntario, array $cultoIds)
    {
        $id_voluntario = (int)$id_voluntario;
        if ($id_voluntario <= 0) {
            return 0;
        }

        $db = db_connect();

        // Remove vínculos existentes
        $db->table($this->table)->where('id_voluntario', $id_voluntario)->delete();

        // Filtra e limpa IDs recebidos
        $cultoIds = array_filter(array_map('intval', $cultoIds), function($id) {
            return $id > 0;
        });

        if (empty($cultoIds)) {
            // Nenhuma restrição gravada -> Disponibilidade Total (Coringa)
            return 0;
        }

        $inseridos = 0;
        foreach (array_unique($cultoIds) as $idCulto) {
            $db->table($this->table)->insert([
                'id_voluntario'   => $id_voluntario,
                'id_culto_padrao' => (int)$idCulto
            ]);
            $inseridos++;
        }

        return $inseridos;
    }

    /**
     * Retorna lista de voluntários aptos e disponíveis para um determinado culto padrão
     * 
     * Regra da Disponibilidade Total (Coringa):
     * - Voluntários que escolheram especificamente o $id_culto_padrao
     * - OU Voluntários que NÃO possuem nenhum registro na tabela tb_voluntario_culto (disponibilidade irrestrita)
     */
    public function getVoluntariosDisponiveisPorCulto($id_culto_padrao, $id_departamento = null, $id_area = null, $onlyActive = true)
    {
        $id_culto_padrao = (int)$id_culto_padrao;
        $db = db_connect();

        $builder = $db->table('tb_voluntario as v');
        $builder->distinct();
        $builder->select('
            v.id_voluntario,
            v.nome,
            v.nickname,
            v.nivel_conhecimento,
            v.max_escalas_mes,
            v.email,
            v.telefone_whatsapp,
            v.foto_url,
            v.status,
            v.hash_voluntario,
            CASE 
                WHEN vc_esp.id_culto_padrao IS NOT NULL THEN "ESPECIFICA"
                ELSE "TOTAL"
            END as tipo_disponibilidade
        ');

        // LEFT JOIN para verificar se escolheu este culto especificamente
        $builder->join(
            'tb_voluntario_culto as vc_esp',
            "vc_esp.id_voluntario = v.id_voluntario AND vc_esp.id_culto_padrao = {$id_culto_padrao}",
            'left'
        );

        // LEFT JOIN para verificar se possui qualquer restrição de culto cadastrada
        $builder->join(
            'tb_voluntario_culto as vc_all',
            'vc_all.id_voluntario = v.id_voluntario',
            'left'
        );

        // Filtro por departamento ou área se fornecidos
        if (!empty($id_departamento) || !empty($id_area)) {
            $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'inner');
            if (!empty($id_departamento)) {
                $builder->where('vda.id_departamento', (int)$id_departamento);
            }
            if (!empty($id_area)) {
                $builder->where('vda.id_area', (int)$id_area);
            }
        }

        if ($onlyActive) {
            $builder->where('v.status', 1);
        }

        // Aplicação da REGRA DO CORINGA:
        // Apto se escolheu o culto especificamente (vc_esp IS NOT NULL)
        // OU se não tem nenhuma restrição na tabela (vc_all.id_voluntario IS NULL)
        $builder->groupStart()
            ->where('vc_esp.id_voluntario IS NOT NULL', null, false)
            ->orWhere('vc_all.id_voluntario IS NULL', null, false)
            ->groupEnd();

        $builder->orderBy('v.nome', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Verifica o status de disponibilidade de um voluntário para um determinado culto
     * Retorna: ['disponivel' => true/false, 'tipo' => 'TOTAL' | 'ESPECIFICA' | 'INDISPONIVEL']
     */
    public function getStatusDisponibilidadeVoluntario($id_voluntario, $id_culto_padrao)
    {
        $id_voluntario   = (int)$id_voluntario;
        $id_culto_padrao = (int)$id_culto_padrao;

        $db = db_connect();

        // Conta quantos cultos o voluntário cadastrou como preferência
        $totalCadastrados = $db->table($this->table)
            ->where('id_voluntario', $id_voluntario)
            ->countAllResults();

        if ($totalCadastrados === 0) {
            // Regra do Coringa: não cadastrou restrição => disponível para todos
            return [
                'disponivel' => true,
                'tipo'       => 'TOTAL',
                'label'      => 'Disponibilidade Total (Coringa)'
            ];
        }

        // Verifica se cadastrou especificamente este culto
        $temEspecifico = $db->table($this->table)
            ->where('id_voluntario', $id_voluntario)
            ->where('id_culto_padrao', $id_culto_padrao)
            ->countAllResults();

        if ($temEspecifico > 0) {
            return [
                'disponivel' => true,
                'tipo'       => 'ESPECIFICA',
                'label'      => 'Disponível neste Culto'
            ];
        }

        return [
            'disponivel' => false,
            'tipo'       => 'INDISPONIVEL',
            'label'      => 'Não disponível neste culto'
        ];
    }

    /**
     * Retorna booleano simples se o voluntário está disponível no culto
     */
    public function isVoluntarioDisponivelNoCulto($id_voluntario, $id_culto_padrao)
    {
        $status = $this->getStatusDisponibilidadeVoluntario($id_voluntario, $id_culto_padrao);
        return (bool)$status['disponivel'];
    }
}
