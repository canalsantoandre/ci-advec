-- ==============================================================================
-- ROLLBACK: Remoção dos campos de Análise de Justificativas de Cancelamento
-- Data: 12/10/2026
-- ==============================================================================

SET @dbname = DATABASE();
SET @tablename = 'tb_escala_voluntario';

-- 1. Remove índice idx_escala_status_analise se existir
SET @indexname = 'idx_escala_status_analise';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @indexname) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP INDEX `idx_escala_status_analise`;',
  'SELECT 1'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 2. Remove justificativa_analisado_em
SET @colname = 'justificativa_analisado_em';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP COLUMN `justificativa_analisado_em`;',
  'SELECT 1'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 3. Remove justificativa_analisado_por
SET @colname = 'justificativa_analisado_por';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP COLUMN `justificativa_analisado_por`;',
  'SELECT 1'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 4. Remove justificativa_parecer_gestor
SET @colname = 'justificativa_parecer_gestor';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP COLUMN `justificativa_parecer_gestor`;',
  'SELECT 1'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;

-- 5. Remove status_analise_justificativa
SET @colname = 'status_analise_justificativa';
SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @colname) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP COLUMN `status_analise_justificativa`;',
  'SELECT 1'
));
PREPARE stmt_exec FROM @stmt;
EXECUTE stmt_exec;
DEALLOCATE PREPARE stmt_exec;
