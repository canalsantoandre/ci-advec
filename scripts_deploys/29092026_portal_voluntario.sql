-- ============================================================
-- SCRIPT DE DEPLOY: PORTAL DO VOLUNTÁRIO (AUTENTICAÇÃO, PERFIL E ESCALAS)
-- Banco de Dados: db_advec
-- Data: 29/09/2026
-- ============================================================

-- 1. Adicionar colunas de autenticação e controle de acesso na tabela tb_voluntario
SET @dbname = DATABASE();
SET @tablename_vol = 'tb_voluntario';

-- Coluna: senha (hash bcrypt)
SET @col_senha = 'senha';
SET @prestatement_senha = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_senha) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `senha` VARCHAR(255) NULL AFTER `email`;'
));
PREPARE stmt_senha FROM @prestatement_senha; EXECUTE stmt_senha; DEALLOCATE PREPARE stmt_senha;

-- Coluna: primeiro_acesso
SET @col_primeiro = 'primeiro_acesso';
SET @prestatement_primeiro = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_primeiro) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `primeiro_acesso` INT DEFAULT 1 COMMENT \'1=Primeiro acesso (senha padrao), 0=Senha customizada\' AFTER `senha`;'
));
PREPARE stmt_primeiro FROM @prestatement_primeiro; EXECUTE stmt_primeiro; DEALLOCATE PREPARE stmt_primeiro;

-- Coluna: data_ultimo_login
SET @col_login = 'data_ultimo_login';
SET @prestatement_login = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_login) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `data_ultimo_login` DATETIME NULL AFTER `primeiro_acesso`;'
));
PREPARE stmt_login FROM @prestatement_login; EXECUTE stmt_login; DEALLOCATE PREPARE stmt_login;

-- Coluna: data_ultima_senha
SET @col_dtsenha = 'data_ultima_senha';
SET @prestatement_dtsenha = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_dtsenha) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `data_ultima_senha` DATETIME NULL AFTER `data_ultimo_login`;'
));
PREPARE stmt_dtsenha FROM @prestatement_dtsenha; EXECUTE stmt_dtsenha; DEALLOCATE PREPARE stmt_dtsenha;


-- 2. Adicionar colunas de confirmação e justificativa na tabela tb_escala_voluntario
SET @tablename_esc = 'tb_escala_voluntario';

-- Coluna: status_confirmacao
SET @col_conf = 'status_confirmacao';
SET @prestatement_conf = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_esc AND COLUMN_NAME = @col_conf) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `status_confirmacao` VARCHAR(20) DEFAULT \'PENDENTE\' COMMENT \'PENDENTE, CONFIRMADO, RECUSADO\' AFTER `status_presenca`, ADD KEY `idx_status_confirmacao` (`status_confirmacao`);'
));
PREPARE stmt_conf FROM @prestatement_conf; EXECUTE stmt_conf; DEALLOCATE PREPARE stmt_conf;

-- Coluna: justificativa_recusa
SET @col_just = 'justificativa_recusa';
SET @prestatement_just = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_esc AND COLUMN_NAME = @col_just) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `justificativa_recusa` TEXT NULL AFTER `status_confirmacao`;'
));
PREPARE stmt_just FROM @prestatement_just; EXECUTE stmt_just; DEALLOCATE PREPARE stmt_just;

-- Coluna: data_resposta
SET @col_dtresp = 'data_resposta';
SET @prestatement_dtresp = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_esc AND COLUMN_NAME = @col_dtresp) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `data_resposta` DATETIME NULL AFTER `justificativa_recusa`;'
));
PREPARE stmt_dtresp FROM @prestatement_dtresp; EXECUTE stmt_dtresp; DEALLOCATE PREPARE stmt_dtresp;

-- 3. Inicialização padrão de status de confirmação para escalas existentes
UPDATE `tb_escala_voluntario`
SET `status_confirmacao` = 'CONFIRMADO'
WHERE `status_presenca` = 1 AND (`status_confirmacao` IS NULL OR `status_confirmacao` = 'PENDENTE');
