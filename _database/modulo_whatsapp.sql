-- ============================================================
-- SCRIPT DE DEPLOY: MÓDULO WHATSAPP (EVOLUTION API)
-- Banco de Dados: db_advec
-- Categoria: GESTÃO DE ACESSO (id_categoria_modulo = 3)
-- Acesso: Exclusivo SysAdm (id_perfil = 1)
-- ============================================================

CREATE TABLE IF NOT EXISTS `whatsapp_configs` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `empresa_id` INT NOT NULL DEFAULT 1,
  `usuario_id` INT NOT NULL DEFAULT 1,
  `name` VARCHAR(255) NULL COMMENT 'Nome de identificação amigável da conexão',
  `external_id` VARCHAR(100) NULL,
  `instance_name` VARCHAR(100) NOT NULL COMMENT 'Nome técnico da instância na Evolution API (usado no n8n)',
  `api_url` VARCHAR(255) NOT NULL DEFAULT 'http://evolution.eabc.com.br',
  `api_key` VARCHAR(255) NOT NULL DEFAULT 'ic6F5CEABCDIGI7636LNnxF5KqKjc9TZaJ',
  `phone` VARCHAR(30) NULL COMMENT 'Número conectado',
  `profile_name` VARCHAR(100) NULL COMMENT 'Nome do perfil no WhatsApp',
  `profile_picture` TEXT NULL COMMENT 'URL da foto de perfil',
  `status` VARCHAR(50) NOT NULL DEFAULT 'not_configured' COMMENT 'not_configured, configuring, waiting_qr, connected, disconnected',
  `connected` TINYINT(1) NOT NULL DEFAULT 0,
  `last_connection` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  KEY `idx_empresa_usuario` (`empresa_id`, `usuario_id`),
  KEY `idx_instance_name` (`instance_name`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Instâncias e conexões do WhatsApp via Evolution API';

INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (15, 3, NULL, 'WhatsApp', 'bi bi-whatsapp', 'whatsapp/', 8, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = 'WhatsApp',
  `icon_class_modulo` = 'bi bi-whatsapp',
  `uri_modulo` = 'whatsapp/',
  `id_categoria_modulo` = 3,
  `status_modulo` = 1;

INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(15, 'create'),
(15, 'read'),
(15, 'update'),
(15, 'delete');

INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES (1, 15);

INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 15, 'create'),
(1, 15, 'read'),
(1, 15, 'update'),
(1, 15, 'delete');
