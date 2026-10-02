-- ============================================================
-- SCRIPT DE DEPLOY: MÓDULO Biblioteca (COLEÇÕES E MATERIAIS)
-- Banco de Dados: db_advec
-- Data: 01/10/2026
-- Acesso: EXCLUSIVO SysAdm (id_perfil = 1)
-- ============================================================

-- 1. Criação da Tabela de Tipos de Recursos (resource_types)
CREATE TABLE IF NOT EXISTS `resource_types` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL COMMENT 'Nome amigável do tipo',
  `code` VARCHAR(30) NOT NULL UNIQUE COMMENT 'video, audio, pdf, link, text',
  `icon` VARCHAR(100) NOT NULL DEFAULT 'bi bi-file-earmark' COMMENT 'Classe do ícone Bootstrap',
  `color` VARCHAR(30) NOT NULL DEFAULT 'primary' COMMENT 'Cor do badge tema',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tipos de mídia da Biblioteca';

-- Carga inicial dos tipos de recursos
INSERT INTO `resource_types` (`id`, `name`, `code`, `icon`, `color`) VALUES
(1, 'Vídeo (YouTube / Vídeo)', 'video', 'bi bi-play-circle-fill', 'danger'),
(2, 'Áudio / Música (Spotify / MP3)', 'audio', 'bi bi-music-note-beamed', 'success'),
(3, 'Documento / PDF / Partitura', 'pdf', 'bi bi-file-earmark-pdf-fill', 'warning'),
(4, 'Link Externo (Drive / Web)', 'link', 'bi bi-link-45deg', 'info'),
(5, 'Texto / Cifra / Letra', 'text', 'bi bi-file-text-fill', 'secondary')
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `icon` = VALUES(`icon`),
  `color` = VALUES(`color`);

-- 2. Criação da Tabela de Recursos Avulsos (resources)
CREATE TABLE IF NOT EXISTS `resources` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL COMMENT 'Título do material de apoio',
  `description` TEXT NULL COMMENT 'Orientações, tom musical, observações',
  `url` TEXT NULL COMMENT 'URL externa estritamente validada (http:// ou https://)',
  `content_text` MEDIUMTEXT NULL COMMENT 'Letra, cifra ou texto rico quando tipo for text',
  `resource_type_id` INT NOT NULL COMMENT 'FK para resource_types.id',
  `department_id` INT NULL COMMENT 'FK para tb_departamento.id_departamento. Se NULL, disponível globalmente',
  `thumbnail_url` TEXT NULL COMMENT 'Thumbnail gerada automaticamente ou informada',
  `external_id` VARCHAR(100) NULL COMMENT 'ID extraído de YouTube, Spotify, etc.',
  `provider` VARCHAR(50) NULL DEFAULT 'generic' COMMENT 'youtube, spotify, drive, pdf, generic, text',
  `embed_url` TEXT NULL COMMENT 'URL pronta para renderização em iframe embed seguro',
  `metadata` JSON NULL COMMENT 'Metadados adicionais em formato JSON',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `created_by` INT NULL COMMENT 'ID do usuário criador',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  KEY `idx_resource_type` (`resource_type_id`),
  KEY `idx_department` (`department_id`),
  KEY `idx_provider` (`provider`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_resources_type` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_types` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Itens individuais da biblioteca de recursos';

-- 3. Criação da Tabela de Coleções / Playlists (collections)
CREATE TABLE IF NOT EXISTS `collections` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL COMMENT 'Título da Coleção / Repertório / Playlist',
  `description` TEXT NULL COMMENT 'Descrição do conjunto de materiais',
  `department_id` INT NULL COMMENT 'FK para tb_departamento.id_departamento (NULL = Global)',
  `cover_url` TEXT NULL COMMENT 'Capa da coleção',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `created_by` INT NULL COMMENT 'ID do usuário criador',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  KEY `idx_coll_department` (`department_id`),
  KEY `idx_coll_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Coleções e Playlists de Recursos';

-- 4. Criação da Tabela N:N de Itens da Coleção (collection_resources)
CREATE TABLE IF NOT EXISTS `collection_resources` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `collection_id` INT NOT NULL COMMENT 'FK para collections.id',
  `resource_id` INT NOT NULL COMMENT 'FK para resources.id',
  `order` INT NOT NULL DEFAULT 0 COMMENT 'Ordem de reprodução/exibição na coleção',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_coll_res` (`collection_id`, `resource_id`),
  KEY `idx_coll_res_order` (`collection_id`, `order`),
  CONSTRAINT `fk_coll_res_coll` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_coll_res_res` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Relacionamento N:N entre coleções e recursos com ordenação';

-- 5. Criação da Tabela N:N de Recursos Anexados à Escala (schedule_resources)
CREATE TABLE IF NOT EXISTS `schedule_resources` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `data_culto` DATE NOT NULL COMMENT 'Data do culto escalado',
  `id_culto_padrao` INT NOT NULL DEFAULT 0 COMMENT 'ID do culto padrão',
  `id_departamento` INT NOT NULL COMMENT 'ID do departamento escalado',
  `id_area` INT NOT NULL DEFAULT 0 COMMENT '0 para Geral (Todas as Sub-áreas) ou ID de tb_departamento_area.id_area',
  `resource_id` INT NOT NULL COMMENT 'FK para resources.id',
  `display_order` INT NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição dos materiais na escala',
  `created_by` INT NULL COMMENT 'ID do usuário que anexou',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_sched_res` (`data_culto`, `id_culto_padrao`, `id_departamento`, `id_area`, `resource_id`),
  KEY `idx_sched_res_lookup` (`data_culto`, `id_culto_padrao`, `id_departamento`, `id_area`, `display_order`),
  CONSTRAINT `fk_sched_res_res` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Recursos desmembrados anexados às escalas dos cultos com suporte a sub-área';

-- 5.1 Caso a tabela `schedule_resources` já exista em ambiente de desenvolvimento/homologação sem a coluna `id_area`:
-- Executar este bloco caso necessário:
-- ALTER TABLE `schedule_resources` 
--   ADD COLUMN `id_area` INT NOT NULL DEFAULT 0 COMMENT '0 para Geral ou ID de tb_departamento_area.id_area' AFTER `id_departamento`,
--   DROP KEY `uniq_sched_res`,
--   ADD UNIQUE KEY `uniq_sched_res` (`data_culto`, `id_culto_padrao`, `id_departamento`, `id_area`, `resource_id`);

-- 6. Inserção do Módulo Biblioteca na tb_sys_modulo (id_modulo = 16)
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (16, 2, NULL, 'Biblioteca', 'bi bi-collection-play-fill', 'resource/', 9, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = 'Biblioteca',
  `icon_class_modulo` = 'bi bi-collection-play-fill',
  `uri_modulo` = 'resource/',
  `id_categoria_modulo` = 2,
  `status_modulo` = 1;

-- 7. Inserção das Ações do Módulo Biblioteca
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(16, 'create'),
(16, 'read'),
(16, 'update'),
(16, 'delete');

-- 8. Permissões de Acesso EXCLUSIVAS para o Perfil SysAdm (id_perfil = 1)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES (1, 16);

INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 16, 'create'),
(1, 16, 'read'),
(1, 16, 'update'),
(1, 16, 'delete');
