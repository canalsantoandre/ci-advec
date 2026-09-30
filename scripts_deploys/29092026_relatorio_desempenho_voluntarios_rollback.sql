-- =====================================================================================
-- SCRIPT DE ROLLBACK: RELATÓRIO DE DESEMPENHO E CONTROLE DE CANCELAMENTOS DE VOLUNTÁRIOS
-- DATA: 29/09/2026
-- =====================================================================================

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `tb_escala_voluntario` 
  DROP INDEX `idx_status_confirmacao`,
  DROP INDEX `idx_data_culto`;

SET FOREIGN_KEY_CHECKS = 1;
