-- ============================================================
-- SCRIPT DE ROLLBACK: GESTÃO DE ESCALAS - BLOQUEIO RETROATIVO, PERMISSÃO UPDATE_PAST E RANKING
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- Descrição: Reverte permissão 'update_past' e índices criados
-- ============================================================

-- 1. Remoção da Permissão 'update_past' dos Perfis
DELETE FROM `tb_sys_perfil_modulo_acao` 
WHERE `id_modulo` = 13 AND `modulo_acao` = 'update_past';

-- 2. Remoção da Ação 'update_past' do Módulo 13
DELETE FROM `tb_sys_modulo_acao` 
WHERE `id_modulo` = 13 AND `modulo_acao` = 'update_past';

-- 3. Remoção dos Índices Otimizados
ALTER TABLE `tb_escala_voluntario` 
  DROP INDEX IF EXISTS `idx_ev_dep_data`,
  DROP INDEX IF EXISTS `idx_ev_data_conf`,
  DROP INDEX IF EXISTS `idx_ev_status_conf`;
