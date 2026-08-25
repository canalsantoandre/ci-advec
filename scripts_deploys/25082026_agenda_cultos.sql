-- ============================================================
-- SCRIPT DE DEPLOY: AGENDA DE CULTOS E PRESENÇA DE CONVIDADOS
-- Banco de Dados: db_advec
-- Data: 25/08/2026
-- ============================================================

-- 1. Criação da Tabela de Cultos
CREATE TABLE IF NOT EXISTS `tb_agenda_culto` (
  `id_culto` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_filial` INT DEFAULT 1,
  `titulo_culto` VARCHAR(150) NOT NULL,
  `data_culto` DATE NOT NULL,
  `horario_inicio` TIME NOT NULL,
  `horario_termino` TIME NOT NULL,
  `descricao` TEXT NULL,
  `cor_evento` VARCHAR(20) DEFAULT '#2563eb',
  `status_culto` INT DEFAULT 1,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_data_culto` (`data_culto`),
  KEY `idx_status_culto` (`status_culto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Criação da Tabela de Vinculação de Convidados por Culto
CREATE TABLE IF NOT EXISTS `tb_culto_convidado` (
  `id_culto_convidado` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_culto` INT NOT NULL,
  `id_convidado` INT NOT NULL,
  `status_presenca` INT DEFAULT 1,
  `observacao` VARCHAR(255) NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_culto_convidado` (`id_culto`, `id_convidado`),
  KEY `idx_id_culto` (`id_culto`),
  KEY `idx_id_convidado` (`id_convidado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Inserção Direta do Módulo Agenda (id_modulo = 4)
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (4, 1, NULL, 'Agenda de Cultos', 'bi bi-calendar-event', 'agenda/', 2, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = 'Agenda de Cultos',
  `icon_class_modulo` = 'bi bi-calendar-event',
  `uri_modulo` = 'agenda/',
  `status_modulo` = 1;

-- 4. Inserção Direta das Ações do Módulo Agenda (id_modulo = 4)
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(4, 'create'),
(4, 'read'),
(4, 'update'),
(4, 'delete');

-- 5. Vincular Módulo 4 aos Perfis (1 = SysAdm, 2 = Adm Geral, 3 = Secretaria)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES
(1, 4),
(2, 4),
(3, 4);

-- 6. Dar Permissões de Ação para o Módulo 4 aos Perfis 1, 2 e 3
INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 4, 'create'), (1, 4, 'read'), (1, 4, 'update'), (1, 4, 'delete'),
(2, 4, 'create'), (2, 4, 'read'), (2, 4, 'update'), (2, 4, 'delete'),
(3, 4, 'create'), (3, 4, 'read'), (3, 4, 'update'), (3, 4, 'delete');
