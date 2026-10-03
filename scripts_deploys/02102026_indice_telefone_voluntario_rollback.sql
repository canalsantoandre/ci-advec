-- ============================================================
-- SCRIPT DE ROLLBACK: ÍNDICE DE PERFORMANCE EM TELEFONE VOLUNTÁRIO
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

SET @dbname = DATABASE();
SET @tablename = 'tb_voluntario';
SET @indexname = 'idx_voluntario_telefone';

SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @indexname) > 0,
  'ALTER TABLE `tb_voluntario` DROP INDEX `idx_voluntario_telefone`;',
  'SELECT 1'
));

PREPARE stmt_idx_drop FROM @stmt;
EXECUTE stmt_idx_drop;
DEALLOCATE PREPARE stmt_idx_drop;
