-- ============================================================
-- SCRIPT DE ROLLBACK: MÓDULO WEBHOOKS E SEGURANÇA OTP (WHATSAPP)
-- Banco de Dados: db_advec
-- Data: 30/09/2026
-- ============================================================

-- 1. Remove tabela de Webhooks
DROP TABLE IF EXISTS `tb_webhook`;

-- 2. Remove Módulo e Permissões
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 14;
DELETE FROM `tb_sys_perfil_modulo` WHERE `id_modulo` = 14;
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 14;
DELETE FROM `tb_sys_modulo` WHERE `id_modulo` = 14;

-- 3. Remove Colunas de OTP em tb_voluntario
ALTER TABLE `tb_voluntario` 
  DROP COLUMN IF EXISTS `otp_expires_at`,
  DROP COLUMN IF EXISTS `otp_code`,
  DROP COLUMN IF EXISTS `force_pwd_change`;

-- 4. Remove Colunas de OTP em tb_sys_usuario
ALTER TABLE `tb_sys_usuario` 
  DROP COLUMN IF EXISTS `otp_expires_at`,
  DROP COLUMN IF EXISTS `otp_code`,
  DROP COLUMN IF EXISTS `force_pwd_change`;
