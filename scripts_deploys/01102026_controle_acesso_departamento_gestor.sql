-- ============================================================
-- SCRIPT DE DEPLOY: CONTROLE DE ACESSO POR DEPARTAMENTO (GESTOR)
-- Banco de Dados: db_advec
-- Data: 01/10/2026
-- Descrição: Criação da tabela de relacionamento N:N entre Departamentos e Usuários Gestores do Sistema
-- ============================================================

-- 1. Criação da Tabela de Gestores por Departamento
CREATE TABLE IF NOT EXISTS `tb_departamento_gestor` (
  `id_departamento_gestor` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_departamento` INT NOT NULL,
  `id_sys_usuario` INT NOT NULL,
  `date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_departamento_usuario` (`id_departamento`, `id_sys_usuario`),
  KEY `idx_dep_gestor_dep` (`id_departamento`),
  KEY `idx_dep_gestor_usu` (`id_sys_usuario`),
  CONSTRAINT `fk_dg_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `tb_departamento` (`id_departamento`) ON DELETE CASCADE,
  CONSTRAINT `fk_dg_sys_usuario` FOREIGN KEY (`id_sys_usuario`) REFERENCES `tb_sys_usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Vínculo de usuários do sistema responsáveis/gestores por departamentos';
