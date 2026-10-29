-- ==============================================================================
-- Script de Deploy: Módulo de Clientes API, Rate Limiting & Swagger
-- Data: 29/10/2026
-- Descrição: Criação da tabela de credenciais de API (tb_api_client) para integração
--            com n8n, automações e Swagger UI.
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `tb_api_client` (
  `id_api_client` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome_aplicacao` varchar(150) NOT NULL,
  `client_id` varchar(100) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `jwt_secret` varchar(255) NOT NULL,
  `tempo_expiracao` int(11) NOT NULL DEFAULT '3600',
  `limite_requisicoes_minuto` int(11) NOT NULL DEFAULT '60',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_acesso` datetime DEFAULT NULL,
  `total_requisicoes` int(11) NOT NULL DEFAULT '0',
  `descricao` text,
  `date_insert` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id_api_client`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção do módulo no menu do sistema (se aplicável)
INSERT INTO `tb_sys_modulo` (`id_modulo`,`id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
SELECT 17, 1, NULL, 'Aplicações API', 'bi bi-shield-lock-fill', 'api-client', 99, 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_sys_modulo` WHERE `uri_modulo` = 'api-client');
