-- ============================================================
-- SCRIPT DE ROLLBACK: MÓDULO WHATSAPP (EVOLUTION API)
-- Banco de Dados: db_advec
-- Data: 30/09/2026
-- ============================================================

DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 15;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 15;
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 15;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 15;
DROP TABLE IF EXISTS `whatsapp_configs`;
