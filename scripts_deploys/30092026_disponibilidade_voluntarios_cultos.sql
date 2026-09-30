-- ============================================================
-- SCRIPT DE DEPLOY: DISPONIBILIDADE DE CULTOS PARA VOLUNTÁRIOS (N:N)
-- Banco de Dados: db_advec
-- Data: 30/09/2026
-- ============================================================

-- 1. Criação da Tabela de Relacionamento N:N Voluntário - Cultos Padrão
CREATE TABLE IF NOT EXISTS `tb_voluntario_culto` (
  `id_voluntario_culto` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_voluntario` INT NOT NULL,
  `id_culto_padrao` INT NOT NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_voluntario_culto` (`id_voluntario`, `id_culto_padrao`),
  KEY `idx_id_voluntario` (`id_voluntario`),
  KEY `idx_id_culto_padrao` (`id_culto_padrao`),
  CONSTRAINT `fk_vc_voluntario` FOREIGN KEY (`id_voluntario`) REFERENCES `tb_voluntario` (`id_voluntario`) ON DELETE CASCADE,
  CONSTRAINT `fk_vc_culto_padrao` FOREIGN KEY (`id_culto_padrao`) REFERENCES `tb_culto_padrao` (`id_culto_padrao`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Disponibilidade de cultos/dias em que o voluntário pode servir';
