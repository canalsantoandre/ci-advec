-- ============================================================
-- SCRIPT DE DEPLOY: GESTÃO DE ESCALAS - BLOQUEIO RETROATIVO, PERMISSÃO UPDATE_PAST E RANKING
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- Descrição: 
--   1. Registra permissão 'update_past' no módulo 13 (escala/)
--   2. Concede 'update_past' ao Perfil SysAdm (id_perfil = 1)
--   3. Cria índices otimizados para busca de meses com escala e histórico de confirmação
-- ============================================================

-- 1. Inserção da Ação 'update_past' para o Módulo de Escalas (id_modulo = 13)
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) 
VALUES (13, 'update_past');

-- 2. Concessão da Ação 'update_past' ao Perfil SysAdm (id_perfil = 1)
INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) 
VALUES (1, 13, 'update_past');

-- 3. Criação de Índices Otimizados para Grade de Gestão e Auditoria de Omissões
ALTER TABLE `tb_escala_voluntario` 
  ADD INDEX `idx_ev_dep_data` (`id_departamento`, `data_culto`),
  ADD INDEX `idx_ev_data_conf` (`data_culto`, `status_confirmacao`),
  ADD INDEX `idx_ev_status_conf` (`status_confirmacao`);
