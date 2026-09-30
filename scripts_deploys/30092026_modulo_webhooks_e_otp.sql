-- ============================================================
-- SCRIPT DE DEPLOY: MÓDULO WEBHOOKS E SEGURANÇA OTP (WHATSAPP)
-- Banco de Dados: db_advec
-- Data: 30/09/2026
-- ============================================================

-- 1. Criação da Tabela de Webhooks (Gestão de Integração WhatsApp)
CREATE TABLE IF NOT EXISTS `tb_webhook` (
  `id_webhook` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_filial` INT DEFAULT 1,
  `nome` VARCHAR(150) NOT NULL COMMENT 'Nome identificador do webhook (ex: Evolution API, Z-API)',
  `url` VARCHAR(255) NOT NULL COMMENT 'URL do endpoint HTTP POST do webhook externo',
  `instancia_padrao` VARCHAR(100) NOT NULL COMMENT 'Nome da instância padrão na API de WhatsApp',
  `status` INT DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configurações de Webhooks para envio de mensagens WhatsApp e OTP';

-- 2. Inserção do Módulo Webhooks na tb_sys_modulo (id_modulo = 14)
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (14, 3, NULL, 'Webhooks', 'bi bi-broadcast-pin', 'webhook/', 7, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = 'Webhooks',
  `icon_class_modulo` = 'bi bi-broadcast-pin',
  `uri_modulo` = 'webhook/',
  `status_modulo` = 1;

-- 3. Inserção das Ações do Módulo Webhooks
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(14, 'create'),
(14, 'read'),
(14, 'update'),
(14, 'delete');

-- 4. Permissões para o Perfil Administrador (id_perfil = 1)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES (1, 14);

INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 14, 'create'),
(1, 14, 'read'),
(1, 14, 'update'),
(1, 14, 'delete');

-- 5. Adição de Campos de Segurança e OTP na Tabela tb_voluntario
SET @dbname = DATABASE();
SET @tablename_vol = 'tb_voluntario';

-- Coluna force_pwd_change
SET @col_force_pwd = 'force_pwd_change';
SET @stmt_force_pwd = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_force_pwd) > 0,
  'SELECT 1',
  'ALTER TABLE tb_voluntario ADD COLUMN force_pwd_change TINYINT(1) DEFAULT 1 COMMENT "1=Obriga troca de senha com OTP, 0=Senha OK" AFTER primeiro_acesso;'
));
PREPARE stmt1 FROM @stmt_force_pwd; EXECUTE stmt1; DEALLOCATE PREPARE stmt1;

-- Coluna otp_code
SET @col_otp_code = 'otp_code';
SET @stmt_otp_code = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_otp_code) > 0,
  'SELECT 1',
  'ALTER TABLE tb_voluntario ADD COLUMN otp_code VARCHAR(10) NULL COMMENT "Código OTP de 6 dígitos" AFTER force_pwd_change;'
));
PREPARE stmt2 FROM @stmt_otp_code; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- Coluna otp_expires_at
SET @col_otp_exp = 'otp_expires_at';
SET @stmt_otp_exp = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_otp_exp) > 0,
  'SELECT 1',
  'ALTER TABLE tb_voluntario ADD COLUMN otp_expires_at DATETIME NULL COMMENT "Validade do código OTP" AFTER otp_code;'
));
PREPARE stmt3 FROM @stmt_otp_exp; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

-- 6. Adição de Campos de Segurança e OTP na Tabela tb_sys_usuario (compatibilidade)
SET @tablename_usu = 'tb_sys_usuario';

SET @col_usu_force = 'force_pwd_change';
SET @stmt_usu_force = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_usu AND COLUMN_NAME = @col_usu_force) > 0,
  'SELECT 1',
  'ALTER TABLE tb_sys_usuario ADD COLUMN force_pwd_change TINYINT(1) DEFAULT 0 AFTER alterar_senha;'
));
PREPARE stmt4 FROM @stmt_usu_force; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

SET @col_usu_otp = 'otp_code';
SET @stmt_usu_otp = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_usu AND COLUMN_NAME = @col_usu_otp) > 0,
  'SELECT 1',
  'ALTER TABLE tb_sys_usuario ADD COLUMN otp_code VARCHAR(10) NULL AFTER force_pwd_change;'
));
PREPARE stmt5 FROM @stmt_usu_otp; EXECUTE stmt5; DEALLOCATE PREPARE stmt5;

SET @col_usu_exp = 'otp_expires_at';
SET @stmt_usu_exp = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_usu AND COLUMN_NAME = @col_usu_exp) > 0,
  'SELECT 1',
  'ALTER TABLE tb_sys_usuario ADD COLUMN otp_expires_at DATETIME NULL AFTER otp_code;'
));
PREPARE stmt6 FROM @stmt_usu_exp; EXECUTE stmt6; DEALLOCATE PREPARE stmt6;

-- 7. Forçar troca obrigatória de senha para todos os voluntários já existentes na base
UPDATE `tb_voluntario` SET `force_pwd_change` = 1;
