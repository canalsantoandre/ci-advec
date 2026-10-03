-- ============================================================
-- SCRIPT DE DEPLOY: SELF-ONBOARDING VIA CONVITE DE VOLUNTÁRIOS
-- Banco de Dados: db_advec
-- Data: 02/10/2026
-- ============================================================

-- 1. Criação da Tabela de Convites de Departamento
CREATE TABLE IF NOT EXISTS `tb_departamento_convite` (
  `id_convite` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `token` VARCHAR(64) NOT NULL UNIQUE COMMENT 'Token único do convite',
  `tipo` ENUM('DIRETO', 'LOTE') NOT NULL DEFAULT 'DIRETO' COMMENT 'DIRETO individual ou LOTE compartilhado',
  `id_departamento` INT NOT NULL,
  `id_usuario_criador` INT NOT NULL,
  `telefone` VARCHAR(30) NULL COMMENT 'Telefone alvo para convite DIRETO',
  `capacidade_maxima` INT NOT NULL DEFAULT 1 COMMENT 'Quantidade máxima de cadastros permitidos',
  `usos_realizados` INT NOT NULL DEFAULT 0 COMMENT 'Quantidade de cadastros finalizados com sucesso',
  `status` ENUM('ATIVO', 'ENCERRADO', 'EXPIRADO') NOT NULL DEFAULT 'ATIVO',
  `expires_at` DATETIME NULL COMMENT 'Data limite de expiração do convite',
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_token` (`token`),
  KEY `idx_id_departamento` (`id_departamento`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_dep_convite_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `tb_departamento` (`id_departamento`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Adição de Colunas de Controle de Aprovação na tb_voluntario (Idempotente)
SET @dbname = DATABASE();
SET @tablename_vol = 'tb_voluntario';

-- Coluna status_aprovacao
SET @col_status_aprov = 'status_aprovacao';
SET @stmt_status_aprov = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_status_aprov) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `status_aprovacao` ENUM(\'APROVADO\', \'PENDENTE\', \'REJEITADO\') NOT NULL DEFAULT \'APROVADO\' AFTER `status`, ADD KEY `idx_status_aprovacao` (`status_aprovacao`);'
));
PREPARE stmt_aprov FROM @stmt_status_aprov; EXECUTE stmt_aprov; DEALLOCATE PREPARE stmt_aprov;

-- Coluna id_convite_origem
SET @col_convite_orig = 'id_convite_origem';
SET @stmt_convite_orig = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename_vol AND COLUMN_NAME = @col_convite_orig) > 0,
  'SELECT 1',
  'ALTER TABLE `tb_voluntario` ADD COLUMN `id_convite_origem` INT NULL AFTER `status_aprovacao`, ADD KEY `idx_id_convite_origem` (`id_convite_origem`);'
));
PREPARE stmt_orig FROM @stmt_convite_orig; EXECUTE stmt_orig; DEALLOCATE PREPARE stmt_orig;

-- 3. Limpeza de Duplicidades e Registro Seguro da Ação send_invite (id_modulo = 12)
DELETE FROM `tb_sys_modulo_acao` WHERE `id_modulo` = 12 AND `modulo_acao` = 'send_invite';
INSERT INTO `tb_sys_modulo_acao` (`id_modulo`, `modulo_acao`) VALUES (12, 'send_invite');

-- 4. Limpeza de Duplicidades e Concessão da Ação send_invite aos Perfis Administradores (1=SysAdm, 2=Adm Geral)
DELETE FROM `tb_sys_perfil_modulo_acao` WHERE `id_modulo` = 12 AND `modulo_acao` = 'send_invite' AND `id_perfil` IN (1, 2);
INSERT INTO `tb_sys_perfil_modulo_acao` (`id_perfil`, `id_modulo`, `modulo_acao`) VALUES 
(1, 12, 'send_invite'),
(2, 12, 'send_invite');
