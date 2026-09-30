-- ============================================================
-- SCRIPT DE DEPLOY: MÓDULO DE GESTÃO DE VOLUNTÁRIOS E ESCALAS POR DEPARTAMENTO
-- Banco de Dados: db_advec
-- Data: 29/09/2026
-- ============================================================

-- 1. Criação da Tabela de Departamentos
CREATE TABLE IF NOT EXISTS `tb_departamento` (
  `id_departamento` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_filial` INT DEFAULT 1,
  `nome` VARCHAR(150) NOT NULL,
  `descricao` TEXT NULL,
  `logo_url` VARCHAR(255) NULL,
  `responsavel_nome` VARCHAR(150) NOT NULL,
  `responsavel_telefone` VARCHAR(30) NOT NULL,
  `cor_identificacao` VARCHAR(20) DEFAULT '#2563eb',
  `status` INT DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_nome` (`nome`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Criação da Tabela de Áreas / Sub-áreas do Departamento
CREATE TABLE IF NOT EXISTS `tb_departamento_area` (
  `id_area` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_departamento` INT NOT NULL,
  `nome_area` VARCHAR(150) NOT NULL,
  `descricao` TEXT NULL,
  `status` INT DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_id_departamento` (`id_departamento`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_dep_area_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `tb_departamento` (`id_departamento`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Criação da Tabela de Voluntários
CREATE TABLE IF NOT EXISTS `tb_voluntario` (
  `id_voluntario` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_filial` INT DEFAULT 1,
  `hash_voluntario` VARCHAR(60) NULL,
  `nome` VARCHAR(150) NOT NULL,
  `nickname` VARCHAR(100) NULL COMMENT 'Apelido / Nickname do voluntário',
  `nivel_conhecimento` VARCHAR(30) DEFAULT 'JUNIOR' COMMENT 'APRENDIZ, JUNIOR, PLENO, SENIOR',
  `email` VARCHAR(150) NOT NULL,
  `telefone_whatsapp` VARCHAR(30) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `foto_url` VARCHAR(255) NULL,
  `status` INT DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
  `max_escalas_mes` INT DEFAULT 0 COMMENT '0=Sem limite / ilimitado, >0 limite mensal de escalas',
  `redes_sociais` TEXT NULL COMMENT 'JSON com links dinâmicos de redes sociais',
  `observacao` TEXT NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_nome` (`nome`),
  KEY `idx_nickname` (`nickname`),
  KEY `idx_nivel_conhecimento` (`nivel_conhecimento`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_max_escalas_mes` (`max_escalas_mes`),
  KEY `idx_hash_voluntario` (`hash_voluntario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Criação da Tabela N:N Voluntário - Departamentos e Áreas
CREATE TABLE IF NOT EXISTS `tb_voluntario_departamento_area` (
  `id_voluntario_area` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_voluntario` INT NOT NULL,
  `id_departamento` INT NOT NULL,
  `id_area` INT NOT NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_voluntario_dep_area` (`id_voluntario`, `id_departamento`, `id_area`),
  KEY `idx_id_voluntario` (`id_voluntario`),
  KEY `idx_id_departamento` (`id_departamento`),
  KEY `idx_id_area` (`id_area`),
  CONSTRAINT `fk_vda_voluntario` FOREIGN KEY (`id_voluntario`) REFERENCES `tb_voluntario` (`id_voluntario`) ON DELETE CASCADE,
  CONSTRAINT `fk_vda_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `tb_departamento` (`id_departamento`) ON DELETE CASCADE,
  CONSTRAINT `fk_vda_area` FOREIGN KEY (`id_area`) REFERENCES `tb_departamento_area` (`id_area`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Criação da Tabela de Escalas de Voluntários por Culto / Departamento / Área
CREATE TABLE IF NOT EXISTS `tb_escala_voluntario` (
  `id_escala_voluntario` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `data_culto` DATE NOT NULL,
  `id_culto_padrao` INT NULL,
  `id_culto` INT NULL DEFAULT NULL,
  `id_departamento` INT NOT NULL,
  `id_area` INT NOT NULL,
  `id_voluntario` INT NOT NULL,
  `status_presenca` INT DEFAULT 1 COMMENT '1=Presente/Confirmado, 0=Ausente, 2=Pendente',
  `observacao` VARCHAR(255) NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_escala_data_cultopadrao_area_vol` (`data_culto`, `id_culto_padrao`, `id_departamento`, `id_area`, `id_voluntario`),
  KEY `idx_data_culto` (`data_culto`),
  KEY `idx_id_culto_padrao` (`id_culto_padrao`),
  KEY `idx_id_culto` (`id_culto`),
  KEY `idx_id_departamento` (`id_departamento`),
  KEY `idx_id_area` (`id_area`),
  KEY `idx_id_voluntario` (`id_voluntario`),
  KEY `idx_status_presenca` (`status_presenca`),
  CONSTRAINT `fk_esc_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `tb_departamento` (`id_departamento`) ON DELETE CASCADE,
  CONSTRAINT `fk_esc_area` FOREIGN KEY (`id_area`) REFERENCES `tb_departamento_area` (`id_area`) ON DELETE CASCADE,
  CONSTRAINT `fk_esc_voluntario` FOREIGN KEY (`id_voluntario`) REFERENCES `tb_voluntario` (`id_voluntario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Inserção Inicial de Departamentos
INSERT INTO `tb_departamento` (`id_departamento`, `id_filial`, `nome`, `descricao`, `logo_url`, `responsavel_nome`, `responsavel_telefone`, `cor_identificacao`, `status`)
VALUES 
(1, 1, 'Comunicação', 'Departamento responsável pela fotografia, cobertura nas redes sociais, stories, reels e telão nos cultos.', NULL, 'Líder de Comunicação', '(11) 98765-4321', '#2563eb', 1),
(2, 1, 'Transmissão', 'Departamento de transmissão ao vivo (streaming), operação de câmeras de palco/nave, corte de vídeo e mixagem de áudio.', NULL, 'Líder de Transmissão', '(11) 98765-4322', '#7c3aed', 0),
(3, 1, 'Iluminação', 'Departamento responsável pelo controle e operação da mesa de iluminação, cenas e ambiência visual dos cultos.', NULL, 'Líder de Iluminação', '(11) 98765-4323', '#d97706', 0)
ON DUPLICATE KEY UPDATE
  `nome` = VALUES(`nome`),
  `descricao` = VALUES(`descricao`),
  `responsavel_nome` = VALUES(`responsavel_nome`),
  `responsavel_telefone` = VALUES(`responsavel_telefone`),
  `cor_identificacao` = VALUES(`cor_identificacao`),
  `status` = VALUES(`status`);

-- 7. Inserção Inicial de Áreas / Sub-áreas dos Departamentos
-- Comunicação (id_departamento = 1)
INSERT INTO `tb_departamento_area` (`id_area`, `id_departamento`, `nome_area`, `descricao`, `status`) VALUES
(1, 1, 'Fotografia', 'Captura de fotos profissionais do altar, congregação e momentos especiais', 1),
(2, 1, 'Telão', 'Operação do telão e projeção de letras/avisos', 1),
(3, 1, 'Edição', 'Ediçao de fotografias para redes sociais e instagram', 1),
(4, 1, 'Reels/Story', 'Captação e edição rápida de vídeos verticais para Reels', 1),
(5, 1, 'Social Media', 'Gestão, postagens e interações nas plataformas digitais', 1)
ON DUPLICATE KEY UPDATE `nome_area` = VALUES(`nome_area`), `status` = 1;

-- Transmissão (id_departamento = 2)
INSERT INTO `tb_departamento_area` (`id_area`, `id_departamento`, `nome_area`, `descricao`, `status`) VALUES
(6, 2, 'Câmera 1', 'Operador de câmera principal (palco/altar)', 1),
(7, 2, 'Câmera 2', 'Operador de câmera secundária / nave / congregação', 1),
(8, 2, 'Corte', 'Direção de corte de vídeo e switcher da transmissão', 1),
(9, 2, 'Mix Áudio', 'Operação e mixagem de áudio para a transmissão ao vivo', 1)
ON DUPLICATE KEY UPDATE `nome_area` = VALUES(`nome_area`), `status` = 1;

-- Iluminação (id_departamento = 3)
INSERT INTO `tb_departamento_area` (`id_area`, `id_departamento`, `nome_area`, `descricao`, `status`) VALUES
(10, 3, 'Cenas', 'Operação da mesa DMX e troca dinâmica de cenas de iluminação', 1)
ON DUPLICATE KEY UPDATE `nome_area` = VALUES(`nome_area`), `status` = 1;

-- 8. Inserção dos Novos Módulos na tb_sys_modulo
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES 
(11, 2, NULL, 'Departamentos', 'bi bi-diagram-3-fill', 'departamento/', 4, 1),
(12, 2, NULL, 'Voluntários', 'bi bi-person-hearts', 'voluntario/', 5, 1),
(13, 1, NULL, 'Escala de Voluntários', 'bi bi-calendar-check-fill', 'escala/', 6, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = VALUES(`nome_modulo`),
  `icon_class_modulo` = VALUES(`icon_class_modulo`),
  `uri_modulo` = VALUES(`uri_modulo`),
  `ordem_exibicao_modulo` = VALUES(`ordem_exibicao_modulo`),
  `status_modulo` = 1;

-- 9. Inserção das Ações dos Módulos 11, 12 e 13
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(11, 'create'), (11, 'read'), (11, 'update'), (11, 'delete'),
(12, 'create'), (12, 'read'), (12, 'update'), (12, 'delete'),
(13, 'create'), (13, 'read'), (13, 'update'), (13, 'delete');

-- 10. Vincular Módulos 11, 12 e 13 aos Perfis (1=SysAdm, 2=Adm Geral)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES
(1, 11), (1, 12), (1, 13),
(2, 11), (2, 12), (2, 13);

-- 11. Concessão de Ações aos Perfis
INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 11, 'create'), (1, 11, 'read'), (1, 11, 'update'), (1, 11, 'delete'),
(1, 12, 'create'), (1, 12, 'read'), (1, 12, 'update'), (1, 12, 'delete'),
(1, 13, 'create'), (1, 13, 'read'), (1, 13, 'update'), (1, 13, 'delete'),
(2, 11, 'create'), (2, 11, 'read'), (2, 11, 'update'), (2, 11, 'delete'),
(2, 12, 'create'), (2, 12, 'read'), (2, 12, 'update'), (2, 12, 'delete'),
(2, 13, 'create'), (2, 13, 'read'), (2, 13, 'update'), (2, 13, 'delete');
