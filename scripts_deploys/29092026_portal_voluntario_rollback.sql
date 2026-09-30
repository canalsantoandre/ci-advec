-- ============================================================
-- SCRIPT DE ROLLBACK: PORTAL DO VOLUNTÁRIO
-- Banco de Dados: db_advec
-- Data: 29/09/2026
-- ============================================================

-- 1. Reverter colunas de tb_escala_voluntario
ALTER TABLE `tb_escala_voluntario` 
  DROP COLUMN IF EXISTS `data_resposta`,
  DROP COLUMN IF EXISTS `justificativa_recusa`,
  DROP COLUMN IF EXISTS `status_confirmacao`;

-- 2. Reverter colunas de tb_voluntario
ALTER TABLE `tb_voluntario` 
  DROP COLUMN IF EXISTS `data_ultima_senha`,
  DROP COLUMN IF EXISTS `data_ultimo_login`,
  DROP COLUMN IF EXISTS `primeiro_acesso`,
  DROP COLUMN IF EXISTS `senha`;
