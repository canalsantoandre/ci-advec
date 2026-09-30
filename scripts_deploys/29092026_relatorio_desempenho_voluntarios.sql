-- =====================================================================================
-- SCRIPT DE DEPLOY: RELATÓRIO DE DESEMPENHO E CONTROLE DE CANCELAMENTOS DE VOLUNTÁRIOS
-- DATA: 29/09/2026
-- DESCRIÇÃO: Índices e otimizações para consultas de assiduidade, confirmações, recusas
--            e justificativas de cancelamento no relatório de desempenho.
-- =====================================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Garante colunas de status de confirmação e justificativa em tb_escala_voluntario
SET @dbname = DATABASE();
SET @tablename = 'tb_escala_voluntario';

-- Coluna: status_confirmacao
SET @col_conf = 'status_confirmacao';
SET @prestatement_conf = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col_conf) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `status_confirmacao` ENUM(\'PENDENTE\', \'CONFIRMADO\', \'RECUSADO\') NOT NULL DEFAULT \'PENDENTE\' AFTER `status_presenca`;'
));
PREPARE stmt_conf FROM @prestatement_conf; EXECUTE stmt_conf; DEALLOCATE PREPARE stmt_conf;

-- Coluna: justificativa_recusa
SET @col_just = 'justificativa_recusa';
SET @prestatement_just = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col_just) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `justificativa_recusa` TEXT NULL AFTER `status_confirmacao`;'
));
PREPARE stmt_just FROM @prestatement_just; EXECUTE stmt_just; DEALLOCATE PREPARE stmt_just;

-- Coluna: data_resposta
SET @col_dtresp = 'data_resposta';
SET @prestatement_dtresp = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @col_dtresp) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `data_resposta` DATETIME NULL AFTER `justificativa_recusa`;'
));
PREPARE stmt_dtresp FROM @prestatement_dtresp; EXECUTE stmt_dtresp; DEALLOCATE PREPARE stmt_dtresp;

-- 2. Índices para otimização do relatório de desempenho
SET @idx_conf = 'idx_status_confirmacao';
SET @prestatement_idx1 = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @idx_conf) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD INDEX `idx_status_confirmacao` (`status_confirmacao`);'
));
PREPARE stmt_idx1 FROM @prestatement_idx1; EXECUTE stmt_idx1; DEALLOCATE PREPARE stmt_idx1;

SET @idx_dtculto = 'idx_data_culto';
SET @prestatement_idx2 = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @idx_dtculto) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD INDEX `idx_data_culto` (`data_culto`);'
));
PREPARE stmt_idx2 FROM @prestatement_idx2; EXECUTE stmt_idx2; DEALLOCATE PREPARE stmt_idx2;

SET FOREIGN_KEY_CHECKS = 1;
