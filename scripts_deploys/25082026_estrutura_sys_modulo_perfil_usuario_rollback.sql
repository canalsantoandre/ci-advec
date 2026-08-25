-- ============================================================
-- SCRIPT DE ROLLBACK: MÓDULOS DO SISTEMA (id_modulo = 9)
-- Banco de Dados: db_advec (db_advec.mysql.dbaas.com.br)
-- Data: 25/08/2026
-- ============================================================

DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 9;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 9;
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 9;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 9;
