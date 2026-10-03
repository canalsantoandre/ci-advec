-- ============================================================
-- SCRIPT DE ROLLBACK: FOTO DE PERFIL DO USUÁRIO ADMIN (SISTEMA)
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

ALTER TABLE `tb_sys_usuario` 
  DROP COLUMN `foto_url`;
