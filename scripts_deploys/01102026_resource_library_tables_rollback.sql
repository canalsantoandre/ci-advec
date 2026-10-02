-- ============================================================
-- SCRIPT DE ROLLBACK: MÓDULO RESOURCE LIBRARY
-- Banco de Dados: db_advec
-- Data: 01/10/2026
-- ============================================================

-- 1. Remove permissões do perfil SysAdm
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 16;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 16;

-- 2. Remove ações e módulo
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 16;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 16;

-- 3. Drop das tabelas em ordem correta respeitando foreign keys
DROP TABLE IF EXISTS `schedule_resources`;
DROP TABLE IF EXISTS `collection_resources`;
DROP TABLE IF EXISTS `collections`;
DROP TABLE IF EXISTS `resources`;
DROP TABLE IF EXISTS `resource_types`;
