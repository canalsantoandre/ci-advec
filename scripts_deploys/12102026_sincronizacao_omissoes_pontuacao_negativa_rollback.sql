-- ==============================================================================
-- Script de Rollback: Sincronização Automática de Omissões
-- Data: 12/10/2026
-- ==============================================================================

UPDATE `tb_escala_voluntario`
SET 
  `status_confirmacao` = 'PENDENTE',
  `status_presenca`    = 2
WHERE `status_confirmacao` = 'OMISSAO';
