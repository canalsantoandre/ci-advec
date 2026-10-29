-- ==============================================================================
-- Script de Rollback: Módulo de Clientes API, Rate Limiting & Swagger
-- Data: 29/10/2026
-- Descrição: Reversão da tabela tb_api_client e remoção do módulo do menu.
-- ==============================================================================

DELETE FROM `tb_sys_modulo` WHERE id_modulo= 17;
DROP TABLE IF EXISTS `tb_api_client`;
