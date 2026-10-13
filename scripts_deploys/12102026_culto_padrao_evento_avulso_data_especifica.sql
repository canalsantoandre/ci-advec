-- Adiciona suporte a evento avulso em data específica em tb_culto_padrao
ALTER TABLE `tb_culto_padrao`
  ADD COLUMN `data_especifica` DATE NULL DEFAULT NULL AFTER `tipo_recorrencia`;
