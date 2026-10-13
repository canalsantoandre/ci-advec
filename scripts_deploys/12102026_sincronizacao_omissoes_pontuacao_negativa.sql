-- ==============================================================================
-- Script de Deploy: Sincronização Automática de Omissões e Penalização de -25 pts
-- Data: 12/10/2026
-- Objetivo: Garantir que escalas passadas não confirmadas sejam migradas para 'OMISSAO'
--           aplicando penalização de -25 pontos no ranking e mudando status visual.
-- ==============================================================================

UPDATE `tb_escala_voluntario`
SET 
  `status_confirmacao` = 'OMISSAO',
  `status_presenca`    = 0
WHERE `data_culto` < CURDATE()
  AND (
    `status_confirmacao` = 'PENDENTE'
    OR `status_confirmacao` IS NULL
    OR `status_confirmacao` = ''
    OR `status_confirmacao` = 'NAO_CONFIRMADO'
  );
