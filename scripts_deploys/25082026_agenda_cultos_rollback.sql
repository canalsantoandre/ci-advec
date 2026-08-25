-- ============================================================
-- SCRIPT DE ROLLBACK: AGENDA DE CULTOS (id_modulo = 4)
-- Banco de Dados: db_advec (db_advec.mysql.dbaas.com.br)
-- Data: 25/08/2026
-- ============================================================

DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 4;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 4;
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 4;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 4;

DROP TABLE IF EXISTS `tb_culto_convidado`;
DROP TABLE IF EXISTS `tb_agenda_culto`;
