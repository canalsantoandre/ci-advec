-- ============================================================
-- SCRIPT DE DEPLOY: MÓDULO CADASTRO DE CULTOS PADRÃO / TIPOS DE CULTO
-- Banco de Dados: db_advec
-- Data: 25/08/2026
-- ============================================================

-- 1. Criação da Tabela de Cultos Padrão (Tipos de Culto)
CREATE TABLE IF NOT EXISTS `tb_culto_padrao` (
  `id_culto_padrao` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_filial` INT DEFAULT 1,
  `nome_culto` VARCHAR(150) NOT NULL,
  `dia_semana` INT NOT NULL COMMENT '0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado',
  `horario_inicio` TIME NOT NULL,
  `horario_termino` TIME NOT NULL,
  `descricao` TEXT NULL,
  `cor_evento` VARCHAR(20) DEFAULT '#2563eb',
  `tipo_recorrencia` VARCHAR(50) DEFAULT 'todas' COMMENT 'todas, apenas_posicao, exceto_posicao, santa_ceia_domingo, exceto_santa_ceia_domingo',
  `posicao_semana` INT NULL DEFAULT NULL COMMENT '1=1ª, 2=2ª, 3=3ª, 4=4ª, 5=5ª ocorrência no mês',
  `status_culto` INT DEFAULT 1,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_dia_semana` (`dia_semana`),
  KEY `idx_status_culto` (`status_culto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Adiciona colunas de recorrência caso a tabela já exista na base
SET @dbname = DATABASE();
SET @tablename = 'tb_culto_padrao';

SET @columnname1 = 'tipo_recorrencia';
SET @prestatement1 = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname1) > 0,
  'SELECT 1',
  'ALTER TABLE tb_culto_padrao ADD COLUMN tipo_recorrencia VARCHAR(50) DEFAULT "todas" AFTER cor_evento;'
));
PREPARE stmt1 FROM @prestatement1; EXECUTE stmt1; DEALLOCATE PREPARE stmt1;

SET @columnname2 = 'posicao_semana';
SET @prestatement2 = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname2) > 0,
  'SELECT 1',
  'ALTER TABLE tb_culto_padrao ADD COLUMN posicao_semana INT NULL DEFAULT NULL AFTER tipo_recorrencia;'
));
PREPARE stmt2 FROM @prestatement2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- 2. Adição do campo id_culto_padrao na Tabela tb_agenda_culto (se não existir)
SET @tablename_agenda = 'tb_agenda_culto';
SET @columnname_agenda = 'id_culto_padrao';
SET @prestatement_agenda = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_agenda AND COLUMN_NAME = @columnname_agenda) > 0,
  'SELECT 1',
  'ALTER TABLE tb_agenda_culto ADD COLUMN id_culto_padrao INT NULL DEFAULT NULL AFTER id_filial, ADD KEY idx_id_culto_padrao (id_culto_padrao);'
));
PREPARE stmt_agenda FROM @prestatement_agenda; EXECUTE stmt_agenda; DEALLOCATE PREPARE stmt_agenda;

-- 3. Inserção do Módulo Tipos de Culto (id_modulo = 10)
INSERT INTO `tb_sys_modulo` (`id_modulo`, `id_categoria_modulo`, `id_modulo_pai`, `nome_modulo`, `icon_class_modulo`, `uri_modulo`, `ordem_exibicao_modulo`, `status_modulo`)
VALUES (10, 1, NULL, 'Tipos de Culto', 'bi bi-journal-bookmark-fill', 'cultopadrao/', 3, 1)
ON DUPLICATE KEY UPDATE 
  `nome_modulo` = 'Tipos de Culto',
  `icon_class_modulo` = 'bi bi-journal-bookmark-fill',
  `uri_modulo` = 'cultopadrao/',
  `status_modulo` = 1;

-- 4. Inserção das Ações do Módulo 10
INSERT IGNORE INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES
(10, 'create'),
(10, 'read'),
(10, 'update'),
(10, 'delete');

-- 5. Permissão Exclusiva para o Perfil SYSADM (id_perfil = 1)
INSERT IGNORE INTO `tb_sys_perfil_modulo` (`id_perfil`, `id_modulo`) VALUES (1, 10);

INSERT IGNORE INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES
(1, 10, 'create'),
(1, 10, 'read'),
(1, 10, 'update'),
(1, 10, 'delete');

-- 6. Inserção dos Cultos Padrão Solicitados com Regras Específicas de Recorrência
INSERT INTO `tb_culto_padrao` (`id_culto_padrao`, `id_filial`, `nome_culto`, `dia_semana`, `horario_inicio`, `horario_termino`, `descricao`, `cor_evento`, `tipo_recorrencia`, `posicao_semana`, `status_culto`) VALUES
(1, 1, 'Escola da Palavra', 0, '09:30:00', '10:30:00', 'Escola da Palavra (todos os domingos exceto no domingo de Santa Ceia)', '#2563eb', 'exceto_santa_ceia_domingo', NULL, 1),
(2, 1, 'Culto de Celebração', 0, '10:30:00', '11:30:00', 'Culto principal de celebração manhã (todos os domingos exceto Santa Ceia)', '#2563eb', 'exceto_santa_ceia_domingo', NULL, 1),
(3, 1, 'Culto de Celebração', 0, '18:30:00', '20:30:00', 'Culto principal de celebração noite (todos os domingos exceto Santa Ceia)', '#2563eb', 'exceto_santa_ceia_domingo', NULL, 1),
(4, 1, 'Culto de Santa Ceia (Domingo)', 0, '18:30:00', '20:30:00', 'Santa Ceia Domingo (1º domingo do mês ou 2º domingo se o dia 1 for domingo)', '#dc2626', 'santa_ceia_domingo', NULL, 1),
(5, 1, 'Culto da Vitória', 2, '20:00:00', '21:30:00', 'Culto da vitória na terça-feira', '#16a34a', 'todas', NULL, 1),
(6, 1, 'Tarde Profética', 3, '20:00:00', '21:30:00', 'Culto de tarde profética na quarta-feira', '#d97706', 'todas', NULL, 1),
(7, 1, 'Culto da Palavra', 4, '20:00:00', '21:30:00', 'Culto de ensino e palavra (todas as quintas exceto a 3ª quinta do mês)', '#7c3aed', 'exceto_posicao', 3, 1),
(8, 1, 'Culto de Santa Ceia (Quinta)', 4, '20:00:00', '21:30:00', 'Culto especial de Santa Ceia na 3ª quinta-feira do mês', '#dc2626', 'apenas_posicao', 3, 1),
(9, 1, 'Culto do Sobrenatural', 5, '20:00:00', '21:30:00', 'Culto do sobrenatural na sexta-feira', '#2563eb', 'todas', NULL, 1)
ON DUPLICATE KEY UPDATE 
  `nome_culto` = VALUES(`nome_culto`),
  `dia_semana` = VALUES(`dia_semana`),
  `horario_inicio` = VALUES(`horario_inicio`),
  `horario_termino` = VALUES(`horario_termino`),
  `descricao` = VALUES(`descricao`),
  `cor_evento` = VALUES(`cor_evento`),
  `tipo_recorrencia` = VALUES(`tipo_recorrencia`),
  `posicao_semana` = VALUES(`posicao_semana`),
  `status_culto` = 1;
