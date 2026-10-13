-- Rollback: Remove coluna data_especifica de tb_culto_padrao
ALTER TABLE `tb_culto_padrao`
  DROP COLUMN `data_especifica`;
