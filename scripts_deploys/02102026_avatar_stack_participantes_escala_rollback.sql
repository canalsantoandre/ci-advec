-- ============================================================
-- SCRIPT DE ROLLBACK: OTIMIZAÇÃO DE BUSCA DE EQUIPE ESCALADA (AVATAR STACK / FACEPILE)
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

SET @dbname = DATABASE();

-- 1. Remoção do índice idx_escala_culto_dep_vol
SET @tablename_esc = 'tb_escala_voluntario';
SET @indexname_culto_dep = 'idx_escala_culto_dep_vol';
SET @prestatement_idx = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_esc AND INDEX_NAME = @indexname_culto_dep) > 0,
  'ALTER TABLE `tb_escala_voluntario` DROP INDEX `idx_escala_culto_dep_vol`;',
  'SELECT 1'
));
PREPARE stmt_idx FROM @prestatement_idx; EXECUTE stmt_idx; DEALLOCATE PREPARE stmt_idx;

-- 2. Remoção do índice idx_voluntario_perfil_escala
SET @tablename_vol = 'tb_voluntario';
SET @indexname_vol_info = 'idx_voluntario_perfil_escala';
SET @prestatement_vol_idx = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND INDEX_NAME = @indexname_vol_info) > 0,
  'ALTER TABLE `tb_voluntario` DROP INDEX `idx_voluntario_perfil_escala`;',
  'SELECT 1'
));
PREPARE stmt_vol_idx FROM @prestatement_vol_idx; EXECUTE stmt_vol_idx; DEALLOCATE PREPARE stmt_vol_idx;
