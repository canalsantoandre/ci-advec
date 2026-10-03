-- ============================================================
-- SCRIPT DE LIMPEZA: CONVITES DE TESTES E AUTOCADASTROS
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

-- 1. Desvincula o id_convite_origem dos voluntários para preservar a integridade referencial
UPDATE `tb_voluntario` 
SET `id_convite_origem` = NULL 
WHERE `id_convite_origem` IS NOT NULL;

-- 2. (Opcional) Remove pré-cadastros de voluntários de teste que ficaram PENDENTES
-- Descomente a linha abaixo se quiser limpar também os voluntários pendentes de aprovação:
-- DELETE FROM `tb_voluntario` WHERE `status_aprovacao` = 'PENDENTE';

-- 3. Limpa todos os convites da tabela
DELETE FROM `tb_departamento_convite`;

-- 4. Reseta o auto incremento para começar do 1 novamente
ALTER TABLE `tb_departamento_convite` AUTO_INCREMENT = 1;
