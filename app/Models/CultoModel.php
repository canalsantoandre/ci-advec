<?php

namespace App\Models;

use CodeIgniter\Model;

class CultoModel extends Model
{
    protected $table            = 'tb_agenda_culto';
    protected $primaryKey       = 'id_culto';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_filial',
        'titulo_culto',
        'data_culto',
        'horario_inicio',
        'horario_termino',
        'descricao',
        'cor_evento',
        'status_culto'
    ];

    protected $useTimestamps    = false;

    /**
     * Retorna lista de cultos no formato de eventos para o calendário
     */
    public function getCultosEvents($start = null, $end = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('status_culto', 1);

        if ($start) {
            $startDate = substr($start, 0, 10);
            $builder->where('data_culto >=', $startDate);
        }
        if ($end) {
            $endDate = substr($end, 0, 10);
            $builder->where('data_culto <=', $endDate);
        }

        $query = $builder->get();
        $cultos = $query->getResult('object');

        $events = [];
        foreach ($cultos as $c) {
            $startDateTime = $c->data_culto . 'T' . $c->horario_inicio;
            $endDateTime   = $c->data_culto . 'T' . $c->horario_termino;

            // Contar quantos convidados estão vinculados a este culto
            $totalConvidados = $this->db->table('tb_culto_convidado')
                ->where('id_culto', $c->id_culto)
                ->countAllResults();

            $events[] = [
                'id'              => $c->id_culto,
                'title'           => $c->titulo_culto . ($totalConvidados > 0 ? " ({$totalConvidados} conv.)" : ""),
                'start'           => $startDateTime,
                'end'             => $endDateTime,
                'backgroundColor' => $c->cor_evento ? $c->cor_evento : '#2563eb',
                'borderColor'     => $c->cor_evento ? $c->cor_evento : '#2563eb',
                'extendedProps'   => [
                    'titulo_culto'     => $c->titulo_culto,
                    'data_culto'       => date('d/m/Y', strtotime($c->data_culto)),
                    'data_culto_raw'   => $c->data_culto,
                    'horario_inicio'   => substr($c->horario_inicio, 0, 5),
                    'horario_termino'  => substr($c->horario_termino, 0, 5),
                    'descricao'        => $c->descricao,
                    'cor_evento'       => $c->cor_evento,
                    'totalConvidados'  => $totalConvidados
                ]
            ];
        }

        return $events;
    }

    /**
     * Retorna detalhes de um culto específico com total de participantes
     */
    public function getCultoDetalhes($id_culto)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_culto', $id_culto);
        return $builder->get()->getFirstRow();
    }
}
