-- ============================================================
-- SCRIPT DE DEPLOY: CADASTRO DO MÓDULO 'sysmodulo/' E PERMISSÕES
-- Banco de Dados: db_advec
-- Data: 25/08/2026
-- ============================================================


-- 1. Inserção Direta do Módulo Módulos do Sistema (id_modulo = 9)
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (9, 3, NULL, 'Módulos do Sistema', 'bi bi-menu-button-wide-fill', 'sysmodulo/', 3, 1)
ON DUPLICATE KEY UPDATE
  `nome_modulo` = 'Módulos do Sistema',
  `icon_class_modulo` = 'bi bi-menu-button-wide-fill',
  `uri_modulo` = 'sysmodulo/',
  `status_modulo` = 1;

-- 2. Inserção Direta das Ações do Módulo Módulos do Sistema (id_modulo = 9)
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(9, 'create'),
(9, 'read'),
(9, 'update'),
(9, 'delete');

-- 3. Vincular Módulo 9 aos Perfis Principais (1 = SysAdm, 2 = Adm Geral)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES
(1, 9),
(2, 9);

-- 4. Dar Permissões de Ação para o Módulo 9 aos Perfis 1 e 2
INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 9, 'create'), (1, 9, 'read'), (1, 9, 'update'), (1, 9, 'delete'),
(2, 9, 'create'), (2, 9, 'read'), (2, 9, 'update'), (2, 9, 'delete');
