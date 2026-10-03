-- ============================================================
-- SCRIPT DE DEPLOY: ÍNDICE DE PERFORMANCE EM TELEFONE VOLUNTÁRIO
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

SET @dbname = DATABASE();
SET @tablename = 'tb_voluntario';
SET @indexname = 'idx_voluntario_telefone';

SET @stmt = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @indexname) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD INDEX `idx_voluntario_telefone` (`telefone_whatsapp`);'
));

PREPARE stmt_idx FROM @stmt;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;
