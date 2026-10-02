-- ============================================================
-- SCRIPT DE DEPLOY: OTIMIZAÇÃO DE BUSCA DE EQUIPE ESCALADA (AVATAR STACK / FACEPILE)
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

-- 1. Criação de índice composto para busca ultra-rápida de co-voluntários por culto e departamento
SET @dbname = DATABASE();
SET @tablename_esc = 'tb_escala_voluntario';
SET @indexname_culto_dep = 'idx_escala_culto_dep_vol';

SET @prestatement_idx = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_esc AND INDEX_NAME = @indexname_culto_dep) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_escala_voluntario` ADD INDEX `idx_escala_culto_dep_vol` (`data_culto`, `id_culto_padrao`, `id_departamento`, `id_voluntario`);'
));
PREPARE stmt_idx FROM @prestatement_idx; EXECUTE stmt_idx; DEALLOCATE PREPARE stmt_idx;

-- 2. Garantia de índice em tb_voluntario para busca de perfil (nome, nickname, foto_url, telefone_whatsapp)
SET @tablename_vol = 'tb_voluntario';
SET @indexname_vol_info = 'idx_voluntario_perfil_escala';

SET @prestatement_vol_idx = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND INDEX_NAME = @indexname_vol_info) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD INDEX `idx_voluntario_perfil_escala` (`id_voluntario`, `status`);'
));
PREPARE stmt_vol_idx FROM @prestatement_vol_idx; EXECUTE stmt_vol_idx; DEALLOCATE PREPARE stmt_vol_idx;
