-- ============================================================
-- SCRIPT DE ROLLBACK: CADASTRO DE CULTOS PADRÃO (MÓDULO 10)
-- Banco de Dados: db_advec
-- Data: 25/08/2026
-- ============================================================

-- 1. Remoção das permissões e registros do Módulo 10
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 10;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 10;
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 10;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 10;

-- 2. Remoção da coluna id_culto_padrao na tabela tb_agenda_culto (se existir)
SET @dbname = DATABASE();
SET @tablename = 'tb_agenda_culto';
SET @columnname = 'id_culto_padrao';
SET @prestatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'ALTER TABLE tb_agenda_culto DROP COLUMN id_culto_padrao;',
  'SELECT 1'
));
PREPARE stmt FROM @prestatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Exclusão da Tabela de Cultos Padrão
DROP TABLE IF EXISTS `tb_culto_padrao`;
