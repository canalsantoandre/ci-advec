-- ==============================================================================
-- DEPLOY: Adição de campos para Análise de Justificativas de Cancelamento de Escala
-- Data: 12/10/2026
-- Objetivo: Permitir que gestores aprovem (abonem) ou reprovem justificativas de recusa,
--           evitando o desconto indevido de pontos no ranking de voluntários.
-- ==============================================================================

SET @dbname = DATABASE();
SET @tablename = 'tb_escala_voluntario';

-- 1. status_analise_justificativa
SET @colname = 'status_analise_justificativa';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `status_analise_justificativa` VARCHAR(25) NOT NULL DEFAULT \'PENDENTE\' AFTER `justificativa_recusa`;'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 2. justificativa_parecer_gestor
SET @colname = 'justificativa_parecer_gestor';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `justificativa_parecer_gestor` TEXT NULL DEFAULT NULL AFTER `status_analise_justificativa`;'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 3. justificativa_analisado_por
SET @colname = 'justificativa_analisado_por';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `justificativa_analisado_por` INT NULL DEFAULT NULL AFTER `justificativa_parecer_gestor`;'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 4. justificativa_analisado_em
SET @colname = 'justificativa_analisado_em';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD COLUMN `justificativa_analisado_em` DATETIME NULL DEFAULT NULL AFTER `justificativa_analisado_por`;'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 5. Índice para consultas rápidas de análise
SET @indexname = 'idx_escala_status_analise';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @indexname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD INDEX `idx_escala_status_analise` (`status_confirmacao`, `status_analise_justificativa`);'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;
