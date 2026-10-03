-- ============================================================
-- SCRIPT DE ROLLBACK: SELF-ONBOARDING VIA CONVITE DE VOLUNTÁRIOS
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

-- 1. Remoção da Tabela de Convites
DROP TABLE IF EXISTS `tb_departamento_convite`;

-- 2. Remoção das Colunas de Aprovação da tb_voluntario
ALTER TABLE `tb_voluntario` 
  DROP COLUMN  `id_convite_origem`,
  DROP COLUMN  `status_aprovacao`;

-- 3. Remoção da Ação send_invite
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 12 AND `modulo_acao` = 'send_invite';
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 12 AND `modulo_acao` = 'send_invite';
