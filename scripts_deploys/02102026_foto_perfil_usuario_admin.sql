-- ============================================================
-- SCRIPT DE DEPLOY: FOTO DE PERFIL DO USUÁRIO ADMIN (SISTEMA)
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

SET @dbname = DATABASE();
SET @tablename_usu = 'tb_sys_usuario';

-- Coluna foto_url na tb_sys_usuario
SET @col_foto_url = 'foto_url';
SET @stmt_foto_url = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_usu AND COLUMN_NAME = @col_foto_url) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_sys_usuario` ADD COLUMN `foto_url` VARCHAR(255) NULL AFTER `nome`;'
));
PREPARE stmt_foto FROM @stmt_foto_url; EXECUTE stmt_foto; DEALLOCATE PREPARE stmt_foto;
