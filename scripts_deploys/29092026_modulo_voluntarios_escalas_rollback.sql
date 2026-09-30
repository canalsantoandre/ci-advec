-- ============================================================
-- SCRIPT DE ROLLBACK: MÓDULO DE GESTÃO DE VOLUNTÁRIOS E ESCALAS POR DEPARTAMENTO
-- Banco de Dados: db_advec
-- Data: 29/09/2026
-- ============================================================

-- 1. Remoção de Permissões dos Módulos 11, 12 e 13
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` IN (11, 12, 13);
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` IN (11, 12, 13);
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` IN (11, 12, 13);
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` IN (11, 12, 13);

-- 2. Remoção das Tabelas Criadas (em ordem de dependência)
DROP TABLE IF EXISTS `tb_escala_voluntario`;
DROP TABLE IF EXISTS `tb_voluntario_departamento_area`;
DROP TABLE IF EXISTS `tb_voluntario`;
DROP TABLE IF EXISTS `tb_departamento_area`;
DROP TABLE IF EXISTS `tb_departamento`;
