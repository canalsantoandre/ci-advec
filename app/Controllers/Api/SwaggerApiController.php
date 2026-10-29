<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SwaggerApiController extends BaseController
{
    /**
     * Retorna a especificação OpenAPI 3.0 em JSON
     */
    public function openapi(): ResponseInterface
    {
        $appConfig = config('App');
        $baseUrl = !empty($appConfig->baseURL) ? rtrim($appConfig->baseURL, '/') : rtrim(base_url(), '/');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title'       => 'ADVEC - API de Integrações e Escalas',
                'description' => 'API REST oficial da ADVEC para automações externas (n8n, bots de WhatsApp, etc.) com autenticação OAuth2 / JWT e controle de Rate Limit.',
                'version'     => '1.0.0',
                'contact'     => [
                    'name'  => 'Equipe de Tecnologia ADVEC',
                    'email' => 'suporte@advec.com.br'
                ]
            ],
            'servers' => [
                [
                    'url'         => $baseUrl . '/api/v1',
                    'description' => 'Servidor Base: ' . $baseUrl
                ],
                [
                    'url'         => '/api/v1',
                    'description' => 'Caminho Relativo (Mesmo Domínio Atual)'
                ]
            ],
            'tags' => [
                [
                    'name'        => 'Autenticação',
                    'description' => 'Emissão e renovação de tokens JWT de acesso'
                ],
                [
                    'name'        => 'Escalas e Lembretes',
                    'description' => 'Consulta de voluntários pendentes e confirmação de envio de mensagens'
                ]
            ],
            'paths' => [
                '/auth/token' => [
                    'post' => [
                        'tags'        => ['Autenticação'],
                        'summary'     => 'Obter Token de Acesso JWT',
                        'description' => 'Autentica a aplicação externa usando Client ID e Client Secret gerados no painel ADVEC. Retorna um Bearer JWT assinado com HMAC-SHA256.',
                        'operationId' => 'obterToken',
                        'requestBody' => [
                            'required'    => true,
                            'description' => 'Credenciais da aplicação de API',
                            'content'     => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['client_id', 'client_secret'],
                                        'properties' => [
                                            'client_id' => [
                                                'type'        => 'string',
                                                'description' => 'Identificador único da aplicação (ex: advec_n8n_escalas)',
                                                'example'     => 'advec_n8n_escalas'
                                            ],
                                            'client_secret' => [
                                                'type'        => 'string',
                                                'description' => 'Segredo de autenticação da aplicação',
                                                'example'     => 'sec_77220678f07452fb20b5d7f771f647bac2368f227232ded6'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Token JWT gerado com sucesso',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'access_token' => [
                                                    'type'        => 'string',
                                                    'description' => 'Token JWT assinado para usar no header Authorization: Bearer <token>',
                                                    'example'     => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
                                                ],
                                                'token_type' => [
                                                    'type'    => 'string',
                                                    'example' => 'Bearer'
                                                ],
                                                'expires_in' => [
                                                    'type'        => 'integer',
                                                    'description' => 'Tempo de vida do token em segundos',
                                                    'example'     => 3600
                                                ],
                                                'client_id' => [
                                                    'type'    => 'string',
                                                    'example' => 'advec_n8n_escalas'
                                                ],
                                                'app_name' => [
                                                    'type'    => 'string',
                                                    'example' => 'n8n - Automação Cobrança de Escalas'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '401' => [
                                'description' => 'Credenciais inválidas ou aplicação inativa',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status'  => ['type' => 'string', 'example' => 'error'],
                                                'code'    => ['type' => 'string', 'example' => 'INVALID_CREDENTIALS'],
                                                'message' => ['type' => 'string', 'example' => 'Client ID ou Client Secret incorretos ou aplicação inativa.']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '429' => [
                                'description' => 'Limite de requisições excedido (Rate Limit)',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status'      => ['type' => 'string', 'example' => 'error'],
                                                'code'        => ['type' => 'string', 'example' => 'RATE_LIMIT_EXCEEDED'],
                                                'message'     => ['type' => 'string', 'example' => 'Muitas tentativas de autenticação. Limite de segurança excedido. Tente novamente em 1 minuto.'],
                                                'retry_after' => ['type' => 'integer', 'example' => 60]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '/escalas/lembretes-pendentes' => [
                    'get' => [
                        'tags'        => ['Escalas e Lembretes'],
                        'summary'     => 'Consultar Voluntários e Escalas Pendentes',
                        'description' => 'Retorna a lista de voluntários ativos que possuem escalas futuras pendentes de confirmação. Agrupa os cultos do mesmo dia e já entrega a mensagem personalizada com emojis e link para envio direto no WhatsApp.',
                        'operationId' => 'consultarLembretesPendentes',
                        'security'    => [
                            ['BearerAuth' => []]
                        ],
                        'parameters' => [
                            [
                                'name'        => 'data_inicio',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Data inicial para o filtro (formato YYYY-MM-DD). Padrão: hoje.',
                                'schema'      => ['type' => 'string', 'format' => 'date', 'example' => '2026-10-29']
                            ],
                            [
                                'name'        => 'data_fim',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Data final para o filtro (formato YYYY-MM-DD). Padrão: hoje + 30 dias.',
                                'schema'      => ['type' => 'string', 'format' => 'date', 'example' => '2026-11-30']
                            ],
                            [
                                'name'        => 'id_departamento',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'ID do departamento para filtrar (ex: 1 para Comunicação, 2 para Transmissão). Se omitido, busca de todos os departamentos.',
                                'schema'      => ['type' => 'integer', 'example' => 1]
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Lista de voluntários pendentes agrupados por pessoa',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status' => ['type' => 'string', 'example' => 'success'],
                                                'total_voluntarios_pendentes' => ['type' => 'integer', 'example' => 2],
                                                'data_geracao' => ['type' => 'string', 'example' => '2026-10-29 14:30:00'],
                                                'voluntarios'  => [
                                                    'type'  => 'array',
                                                    'items' => [
                                                        'type'       => 'object',
                                                        'properties' => [
                                                            'id_voluntario'     => ['type' => 'integer', 'example' => 28],
                                                            'nome_completo'     => ['type' => 'string', 'example' => 'Mariana Procopio'],
                                                            'primeiro_nome'     => ['type' => 'string', 'example' => 'Mariana'],
                                                            'telefone_whatsapp' => ['type' => 'string', 'description' => 'Apenas dígitos, ideal para nós de WhatsApp', 'example' => '11950207384'],
                                                            'departamento'      => ['type' => 'string', 'example' => 'Comunicação'],
                                                            'ids_escala'        => [
                                                                'type'        => 'array',
                                                                'description' => 'IDs de escala deste voluntário para posterior baixa',
                                                                'items'       => ['type' => 'integer'],
                                                                'example'     => [141, 143]
                                                            ],
                                                            'cultos' => [
                                                                'type'  => 'array',
                                                                'items' => [
                                                                    'type'       => 'object',
                                                                    'properties' => [
                                                                        'id_escala'       => ['type' => 'integer', 'example' => 141],
                                                                        'data'            => ['type' => 'string', 'example' => '2026-11-01'],
                                                                        'data_formatada'  => ['type' => 'string', 'example' => '01/11/2026'],
                                                                        'horario'         => ['type' => 'string', 'example' => '09:00'],
                                                                        'culto'           => ['type' => 'string', 'example' => 'Escola da Palavra'],
                                                                        'area'            => ['type' => 'string', 'example' => 'Edição']
                                                                    ]
                                                                ]
                                                            ],
                                                            'total_escalas'     => ['type' => 'integer', 'example' => 2],
                                                            'link_portal'       => ['type' => 'string', 'example' => $baseUrl . '/portal/agenda'],
                                                            'mensagem_pronta'   => ['type' => 'string', 'description' => 'Texto pronto com formatação de WhatsApp, emojis e link individual', 'example' => "ADVEC: Olá, *Mariana*! 🙌 Graça e paz!\n\nPassando para lembrar..."]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '401' => [
                                'description' => 'Token ausente, inválido ou expirado',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status'  => ['type' => 'string', 'example' => 'error'],
                                                'code'    => ['type' => 'string', 'example' => 'TOKEN_EXPIRED'],
                                                'message' => ['type' => 'string', 'example' => 'Token expirado. Por favor, renove via /api/v1/auth/token.']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '/escalas/marcar-lembrete' => [
                    'post' => [
                        'tags'        => ['Escalas e Lembretes'],
                        'summary'     => 'Marcar Lembretes como Enviados (Baixa)',
                        'description' => 'Atualiza o campo `data_ultimo_lembrete` para NOW() nas escalas informadas. Evita que a mesma pessoa receba lembretes repetidos no mesmo dia pelo n8n.',
                        'operationId' => 'marcarLembreteEnviado',
                        'security'    => [
                            ['BearerAuth' => []]
                        ],
                        'requestBody' => [
                            'required'    => true,
                            'description' => 'Array com os IDs das escalas que acabaram de ser notificadas via WhatsApp',
                            'content'     => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['ids_escala'],
                                        'properties' => [
                                            'ids_escala' => [
                                                'type'        => 'array',
                                                'description' => 'Lista de IDs de escala (vindo diretamente de $json.ids_escala do endpoint anterior)',
                                                'items'       => ['type' => 'integer'],
                                                'example'     => [141, 143]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Escalas atualizadas com sucesso',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status'              => ['type' => 'string', 'example' => 'success'],
                                                'escalas_atualizadas' => ['type' => 'boolean', 'example' => true],
                                                'message'             => ['type' => 'string', 'example' => 'Lembrete marcado como enviado com sucesso para 2 escala(s).']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '400' => [
                                'description' => 'Parâmetro ids_escala inválido ou ausente',
                                'content'     => [
                                    'application/json' => [
                                        'schema' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'status'  => ['type' => 'string', 'example' => 'error'],
                                                'message' => ['type' => 'string', 'example' => 'O parâmetro ids_escala deve ser um array com os IDs das escalas.']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type'         => 'http',
                        'scheme'       => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description'  => 'Informe o token JWT obtido no endpoint /api/v1/auth/token'
                    ],
                    'OAuth2ClientCredentials' => [
                        'type'  => 'oauth2',
                        'flows' => [
                            'clientCredentials' => [
                                'tokenUrl' => $baseUrl . '/api/v1/auth/token',
                                'scopes'   => new \stdClass()
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setJSON($spec);
    }

    /**
     * Renderiza o Swagger UI Oficial em página completa
     */
    public function ui()
    {
        $data = [
            'openapiUrl' => base_url('api/v1/openapi.json'),
            'baseUrl'    => base_url('api/v1')
        ];

        return view('api_client/swagger_ui', $data);
    }
}
