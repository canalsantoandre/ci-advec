-- ============================================================
-- SCRIPT DE CARGA COMPLETA: VOLUNTÁRIOS E ASSOCIAÇÃO DE SUB-ÁREAS
-- Banco de Dados: db_advec
-- Data: 29/09/2026
--
-- Departamentos:
--   1: Comunicação
--   2: Transmissão
-- ============================================================

-- 1. Garantir que os Departamentos 1 (Comunicação) e 2 (Transmissão) existam
INSERT INTO `tb_departamento` (`id_departamento`, `id_filial`, `nome`, `descricao`, `responsavel_nome`, `responsavel_telefone`, `cor_identificacao`, `status`)
VALUES 
(1, 1, 'Comunicação', 'Departamento de Comunicação, Mídia e Cobertura', 'Líder Comunicação', '(11) 98765-4321', '#2563eb', 1),
(2, 1, 'Transmissão', 'Departamento de Transmissão ao Vivo e Streaming', 'Líder Transmissão', '(11) 98765-4322', '#7c3aed', 1)
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`), `status` = 1;

-- 2. Garantir as Sub-Áreas do Departamento 1 (Comunicação)
INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 1, 'Fotografia', 'Captura e tratamento de fotos dos cultos e eventos', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 1 AND `nome_area` = 'Fotografia');

INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 1, 'Telão', 'Operação de projeção, letras e avisos', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 1 AND `nome_area` = 'Telão');

INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 1, 'Edição / Design', 'Criação de artes, edição de imagens e identidade visual', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 1 AND `nome_area` IN ('Edição / Design', 'Edição'));

INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 1, 'Story/Reels', 'Captação e edição dinâmica de Reels e Stories', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 1 AND `nome_area` IN ('Story/Reels', 'Reels/Story'));

INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 1, 'Social Media', 'Planejamento, postagens e engajamento nas redes sociais', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 1 AND `nome_area` = 'Social Media');

-- Padronizar nomes caso existissem variações anteriores no Dept 1
UPDATE `tb_departamento_area` SET `nome_area` = 'Edição / Design' WHERE `id_departamento` = 1 AND `nome_area` = 'Edição';
UPDATE `tb_departamento_area` SET `nome_area` = 'Story/Reels' WHERE `id_departamento` = 1 AND `nome_area` = 'Reels/Story';

-- 3. Garantir a Sub-Área do Departamento 2 (Transmissão)
INSERT INTO `tb_departamento_area` (`id_departamento`, `nome_area`, `descricao`, `status`)
SELECT 2, 'Transmissão', 'Operação de streaming, switcher e câmeras', 1
WHERE NOT EXISTS (SELECT 1 FROM `tb_departamento_area` WHERE `id_departamento` = 2 AND `nome_area` = 'Transmissão');

-- 4. Inserção / Atualização dos 49 Voluntários
INSERT INTO `tb_voluntario` 
(`id_filial`, `hash_voluntario`, `nome`, `nickname`, `nivel_conhecimento`, `email`, `telefone_whatsapp`, `data_nascimento`, `foto_url`, `status`, `max_escalas_mes`, `redes_sociais`, `observacao`)
VALUES
(1, SHA1('VOL_1_Ana_Carla'), 'Ana Carla', NULL, 'JUNIOR', 'ana.carla@voluntario.advec.com', '(11) 97380-7675', '2000-05-13', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_2_Ana_lice_Souza'), 'Ana lice Souza De Andrade', NULL, 'APRENDIZ', 'ana.lice@voluntario.advec.com', '(11) 91337-0356', '2000-03-08', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_3_Arthur_Bruno'), 'Arthur Bruno', NULL, 'SENIOR', 'arthur.bruno@voluntario.advec.com', '(16) 98165-6856', '2000-07-12', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_4_Bruna'), 'Bruna', NULL, 'APRENDIZ', 'bruna@voluntario.advec.com', '(11) 93430-3002', '2000-12-02', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_5_Bruna_Marchiori'), 'Bruna Marchiori', NULL, 'SENIOR', 'bruna.marchiori@voluntario.advec.com', '(11) 96575-4990', '2000-06-20', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_6_Byanca'), 'Byanca', NULL, 'SENIOR', 'byanca@voluntario.advec.com', '(11) 96991-0179', '2000-12-18', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_7_Carol_Barreto'), 'Carol Barreto', 'Carol', 'PLENO', 'carol.barreto@voluntario.advec.com', '(11) 98261-8023', '2000-12-20', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_8_Cellyne_Victhoria'), 'Cellyne Victhória Rodrigues André', 'ce', 'PLENO', 'cellyne.andre@voluntario.advec.com', '(11) 95001-3973', '2000-12-30', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_9_Danilo_Froes'), 'Danilo Froes', NULL, 'PLENO', 'danilo.froes@voluntario.advec.com', '(11) 96931-4733', '2000-06-12', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_10_Eder_Augusto'), 'Eder Augusto', NULL, 'PLENO', 'eder.augusto@voluntario.advec.com', '(11) 98498-9724', '2000-03-09', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_11_Elielton_Carvalho'), 'Elielton Carvalho', NULL, 'SENIOR', 'elielton.carvalho@voluntario.advec.com', '(11) 91014-7358', '2000-04-14', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_12_Elpidio_Junior'), 'Elpidio Junior', NULL, 'SENIOR', 'elpidio.junior@voluntario.advec.com', '(11) 99825-5020', '2000-02-25', NULL, 1, 0, NULL, NULL),
(1, SHA1('VOL_13_Emanuelly_Miranda'), 'Emanuelly Miranda', 'Manu', 'JUNIOR', 'emanuelly.miranda@voluntario.advec.com', '(11) 97797-7547', '2000-09-01', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_14_Emilia_Fernanda'), 'Emília Fernanda', 'Mila', 'PLENO', 'emilia.fernanda@voluntario.advec.com', '(11) 96304-7228', '2000-10-02', NULL, 1, 3, NULL, NULL),
(1, SHA1('VOL_15_Emilly_Siqueira'), 'Emilly Siqueira Cruz', NULL, 'JUNIOR', 'emilly.cruz@voluntario.advec.com', '(11) 96223-0395', '2000-03-17', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_16_Ester_Souza'), 'Ester Souza Lima', 'Esterzinha', 'SENIOR', 'ester.lima@voluntario.advec.com', '(11) 91606-4880', '2000-10-29', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_17_Gabriel_Candido'), 'Gabriel Candido Gonçalves', 'Candido', 'PLENO', 'gabriel.candido@voluntario.advec.com', '(11) 97414-7074', '2000-11-26', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_18_Gabrielle_lidia'), 'Gabrielle lidia gonçalves', 'Gabs', 'PLENO', 'gabrielle.goncalves@voluntario.advec.com', '(11) 99906-7679', '2000-11-26', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_19_Gessica_Vianna'), 'Géssica Vianna Calabis', NULL, 'PLENO', 'gessica.calabis@voluntario.advec.com', '(11) 99331-0505', '2000-08-25', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_20_Heloisa_Fernanda'), 'Heloísa Fernanda', 'Helo', 'PLENO', 'heloisa.fernanda@voluntario.advec.com', '(11) 94809-3735', '2000-01-10', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_21_Jhuliany_Miranda'), 'Jhuliany Miranda', 'Tia Jhu', 'APRENDIZ', 'jhuliany.miranda@voluntario.advec.com', '(11) 99856-2126', '2000-08-02', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_22_Julia_Almeida'), 'Julia Almeida', 'Juju', 'SENIOR', 'julia.almeida@voluntario.advec.com', '(11) 98580-5353', '2000-07-03', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_23_Larissa'), 'Larissa', NULL, 'APRENDIZ', 'larissa@voluntario.advec.com', '(11) 97287-1565', '2000-01-02', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_24_Lucas_Araujo'), 'Lucas Araujo', NULL, 'APRENDIZ', 'lucas.araujo@voluntario.advec.com', '(11) 95900-7787', '2000-12-28', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_25_Luis_Matheus'), 'Luis Matheus da Cruz Pinto', NULL, 'SENIOR', 'luis.matheus@voluntario.advec.com', '(11) 98200-5177', '2000-03-05', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_26_Maria_Eduarda'), 'Maria Eduarda Marconi Basílio', 'marconi', 'SENIOR', 'maria.marconi@voluntario.advec.com', '(11) 93017-0788', '2000-01-04', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_27_Mariana_Procopio'), 'Mariana Procopio', 'Mari', 'SENIOR', 'mariana.procopio@voluntario.advec.com', '(11) 95020-7384', '2000-06-17', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_28_Matheus_Dantas'), 'Matheus Dantas Santana', NULL, 'APRENDIZ', 'matheus.dantas@voluntario.advec.com', '(11) 98582-3909', '2000-05-06', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_29_Matheus_Olial'), 'Matheus Olial', 'Olial', 'PLENO', 'matheus.olial@voluntario.advec.com', '(11) 97784-3015', '2000-04-08', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_30_Miguel_Ribeiro'), 'Miguel Ribeiro Santos', NULL, 'APRENDIZ', 'miguel.ribeiro@voluntario.advec.com', '(11) 97635-8682', '2000-01-08', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_31_Monaliza_Francisco'), 'Monaliza Francisco', 'Mona', 'SENIOR', 'monaliza.francisco@voluntario.advec.com', '(11) 95236-1950', '2000-01-06', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_32_Nicollas_Dantas'), 'Nicollas Dantas Santana', NULL, 'APRENDIZ', 'nicollas.dantas@voluntario.advec.com', '(11) 99344-0930', '2000-01-01', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_33_Pericles'), 'Péricles', 'Periclão', 'PLENO', 'pericles@voluntario.advec.com', '(11) 97731-2995', '2000-12-10', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_34_Raislaine'), 'Raislaine', NULL, 'PLENO', 'raislaine@voluntario.advec.com', '(79) 99838-2324', '2000-01-25', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_35_Ranieri_Meireles'), 'Ranieri Meireles', NULL, 'APRENDIZ', 'ranieri.meireles@voluntario.advec.com', '(11) 96499-8474', '2000-01-01', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_36_Robert_Kennedy'), 'Robert Kennedy', NULL, 'APRENDIZ', 'robert.kennedy@voluntario.advec.com', '(99) 99197-9936', '2000-12-27', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_37_Rose_Goncalves'), 'Rose Gonçalves', NULL, 'PLENO', 'rose.goncalves@voluntario.advec.com', '(11) 98099-2424', '2000-10-23', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_38_Samuel_Matos'), 'Samuel Matos', NULL, 'PLENO', 'samuel.matos@voluntario.advec.com', '(11) 96487-1752', '2000-04-03', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_39_TATIANY_RAMIRES'), 'TATIANY RAMIRES VELASQUEZ', 'Taty', 'SENIOR', 'tatiany.velasquez@voluntario.advec.com', '(11) 97781-1382', '2000-06-01', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_40_Thierry_Rodrigues'), 'Thierry Rodrigues', NULL, 'PLENO', 'thierry.rodrigues@voluntario.advec.com', '(11) 98486-8010', '2000-11-30', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_41_Tiago_Andrade'), 'Tiago Andrade', NULL, 'SENIOR', 'tiago.andrade@voluntario.advec.com', '(11) 99120-0923', '2000-01-01', NULL, 1, 0, NULL, NULL),
(1, SHA1('VOL_42_Veronica_Primo'), 'Veronica Primo', NULL, 'SENIOR', 'veronica.primo@voluntario.advec.com', '(11) 96427-6045', '2000-11-26', NULL, 1, 4, NULL, NULL),
(1, SHA1('VOL_43_Victor_Gomes'), 'Victor Gomes Silva', NULL, 'SENIOR', 'victor.gomes@voluntario.advec.com', '(11) 99201-9362', '2000-01-01', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_44_Vitoria_Naya'), 'Vitória Naya', NULL, 'APRENDIZ', 'vitoria.naya@voluntario.advec.com', '(11) 97550-5925', '2000-02-16', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_45_Waleria_Wilar'), 'Waleria Wilar', NULL, 'JUNIOR', 'waleria.wilar@voluntario.advec.com', '(99) 99209-3641', '2000-12-06', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_46_Isabelli_Primo'), 'Isabelli Primo Maciel', 'Isa', 'APRENDIZ', 'isabelli.maciel@voluntario.advec.com', '(11) 99134-2817', '2000-06-11', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_47_Beatriz_Tavares'), 'Beatriz Tavares da Silva', 'Be', 'SENIOR', 'beatriz.tavares@voluntario.advec.com', '(11) 97836-1617', '2000-01-08', NULL, 1, 2, NULL, NULL),
(1, SHA1('VOL_48_Alanys_Fernandes'), 'Alanys Fernandes', 'Alanys', 'APRENDIZ', 'alanys.fernandes@voluntario.advec.com', '(11) 99604-6412', '2000-05-15', NULL, 1, 1, NULL, NULL),
(1, SHA1('VOL_49_Jonatas_David'), 'Jonatas David Lima Brito', NULL, 'APRENDIZ', 'jonatas.brito@voluntario.advec.com', '(11) 97953-1452', '2000-10-09', NULL, 1, 4, NULL, NULL)
ON DUPLICATE KEY UPDATE
  `nickname` = VALUES(`nickname`),
  `nivel_conhecimento` = VALUES(`nivel_conhecimento`),
  `telefone_whatsapp` = VALUES(`telefone_whatsapp`),
  `data_nascimento` = VALUES(`data_nascimento`),
  `max_escalas_mes` = VALUES(`max_escalas_mes`),
  `status` = 1;

-- 5. Associação dos Voluntários às Sub-Áreas do Departamento 1 (Comunicação)
INSERT IGNORE INTO `tb_voluntario_departamento_area` (`id_voluntario`, `id_departamento`, `id_area`)
SELECT v.id_voluntario, a.id_departamento, a.id_area
FROM `tb_voluntario` v
CROSS JOIN `tb_departamento_area` a
WHERE a.id_departamento = 1
  AND (
    -- 1. Ana Carla: TELAO, FOTOGRAFIA
    (v.hash_voluntario = SHA1('VOL_1_Ana_Carla') AND a.nome_area IN ('Telão', 'Fotografia'))
    
    -- 2. Ana lice Souza De Andrade: STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_2_Ana_lice_Souza') AND a.nome_area IN ('Story/Reels', 'Fotografia'))
    
    -- 3. Arthur Bruno: EDIÇÃO / DESIGN
    OR (v.hash_voluntario = SHA1('VOL_3_Arthur_Bruno') AND a.nome_area IN ('Edição / Design'))
    
    -- 4. Bruna: EDIÇÃO / DESIGN, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_4_Bruna') AND a.nome_area IN ('Edição / Design', 'Fotografia'))
    
    -- 5. Bruna Marchiori: STORY/REELS, EDIÇÃO / DESIGN, FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_5_Bruna_Marchiori') AND a.nome_area IN ('Story/Reels', 'Edição / Design', 'Fotografia', 'Social Media'))
    
    -- 6. Byanca: STORY/REELS, EDIÇÃO / DESIGN, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_6_Byanca') AND a.nome_area IN ('Story/Reels', 'Edição / Design', 'Fotografia'))
    
    -- 7. Carol Barreto: FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_7_Carol_Barreto') AND a.nome_area IN ('Fotografia', 'Social Media'))
    
    -- 8. Cellyne Victhória Rodrigues André: TELAO, STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_8_Cellyne_Victhoria') AND a.nome_area IN ('Telão', 'Story/Reels', 'Fotografia'))
    
    -- 9. Danilo Froes: TELAO
    OR (v.hash_voluntario = SHA1('VOL_9_Danilo_Froes') AND a.nome_area IN ('Telão'))
    
    -- 10. Eder Augusto: TELAO
    OR (v.hash_voluntario = SHA1('VOL_10_Eder_Augusto') AND a.nome_area IN ('Telão'))
    
    -- 11. Elielton Carvalho: TELAO
    OR (v.hash_voluntario = SHA1('VOL_11_Elielton_Carvalho') AND a.nome_area IN ('Telão'))
    
    -- 13. Emanuelly Miranda: STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_13_Emanuelly_Miranda') AND a.nome_area IN ('Story/Reels'))
    
    -- 14. Emília Fernanda: TELAO
    OR (v.hash_voluntario = SHA1('VOL_14_Emilia_Fernanda') AND a.nome_area IN ('Telão'))
    
    -- 15. Emilly Siqueira Cruz: STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_15_Emilly_Siqueira') AND a.nome_area IN ('Story/Reels', 'Fotografia'))
    
    -- 16. Ester Souza Lima: EDIÇÃO / DESIGN, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_16_Ester_Souza') AND a.nome_area IN ('Edição / Design', 'Fotografia'))
    
    -- 17. Gabriel Candido Gonçalves: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_17_Gabriel_Candido') AND a.nome_area IN ('Fotografia'))
    
    -- 18. Gabrielle lidia gonçalves: STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_18_Gabrielle_lidia') AND a.nome_area IN ('Story/Reels', 'Fotografia'))
    
    -- 19. Géssica Vianna Calabis: STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_19_Gessica_Vianna') AND a.nome_area IN ('Story/Reels', 'Fotografia'))
    
    -- 20. Heloísa Fernanda: STORY/REELS, EDIÇÃO / DESIGN, FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_20_Heloisa_Fernanda') AND a.nome_area IN ('Story/Reels', 'Edição / Design', 'Fotografia', 'Social Media'))
    
    -- 21. Jhuliany Miranda: TELAO
    OR (v.hash_voluntario = SHA1('VOL_21_Jhuliany_Miranda') AND a.nome_area IN ('Telão'))
    
    -- 22. Julia Almeida: TELAO, STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_22_Julia_Almeida') AND a.nome_area IN ('Telão', 'Story/Reels', 'Fotografia'))
    
    -- 23. Larissa: STORY/REELS, FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_23_Larissa') AND a.nome_area IN ('Story/Reels', 'Fotografia', 'Social Media'))
    
    -- 24. Lucas Araujo: SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_24_Lucas_Araujo') AND a.nome_area IN ('Social Media'))
    
    -- 25. Luis Matheus da Cruz Pinto: TELAO, STORY/REELS, EDIÇÃO / DESIGN
    OR (v.hash_voluntario = SHA1('VOL_25_Luis_Matheus') AND a.nome_area IN ('Telão', 'Story/Reels', 'Edição / Design'))
    
    -- 26. Maria Eduarda Marconi Basílio: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_26_Maria_Eduarda') AND a.nome_area IN ('Fotografia'))
    
    -- 27. Mariana Procopio: TELAO, EDIÇÃO / DESIGN, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_27_Mariana_Procopio') AND a.nome_area IN ('Telão', 'Edição / Design', 'Fotografia'))
    
    -- 28. Matheus Dantas Santana: TELAO
    OR (v.hash_voluntario = SHA1('VOL_28_Matheus_Dantas') AND a.nome_area IN ('Telão'))
    
    -- 29. Matheus Olial: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_29_Matheus_Olial') AND a.nome_area IN ('Fotografia'))
    
    -- 30. Miguel Ribeiro Santos: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_30_Miguel_Ribeiro') AND a.nome_area IN ('Fotografia'))
    
    -- 31. Monaliza Francisco: TELAO, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_31_Monaliza_Francisco') AND a.nome_area IN ('Telão', 'Fotografia'))
    
    -- 32. Nicollas Dantas Santana: EDIÇÃO / DESIGN
    OR (v.hash_voluntario = SHA1('VOL_32_Nicollas_Dantas') AND a.nome_area IN ('Edição / Design'))
    
    -- 33. Péricles: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_33_Pericles') AND a.nome_area IN ('Fotografia'))
    
    -- 34. Raislaine: STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_34_Raislaine') AND a.nome_area IN ('Story/Reels'))
    
    -- 35. Ranieri Meireles: STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_35_Ranieri_Meireles') AND a.nome_area IN ('Story/Reels'))
    
    -- 36. Robert Kennedy: TELAO, STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_36_Robert_Kennedy') AND a.nome_area IN ('Telão', 'Story/Reels'))
    
    -- 37. Rose Gonçalves: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_37_Rose_Goncalves') AND a.nome_area IN ('Fotografia'))
    
    -- 38. Samuel Matos: TELAO, STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_38_Samuel_Matos') AND a.nome_area IN ('Telão', 'Story/Reels', 'Fotografia'))
    
    -- 39. TATIANY RAMIRES VELASQUEZ: EDIÇÃO / DESIGN
    OR (v.hash_voluntario = SHA1('VOL_39_TATIANY_RAMIRES') AND a.nome_area IN ('Edição / Design'))
    
    -- 40. Thierry Rodrigues: TELAO, STORY/REELS, EDIÇÃO / DESIGN, FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_40_Thierry_Rodrigues') AND a.nome_area IN ('Telão', 'Story/Reels', 'Edição / Design', 'Fotografia', 'Social Media'))
    
    -- 41. Tiago Andrade: TELAO
    OR (v.hash_voluntario = SHA1('VOL_41_Tiago_Andrade') AND a.nome_area IN ('Telão'))
    
    -- 42. Veronica Primo: STORY/REELS, FOTOGRAFIA, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_42_Veronica_Primo') AND a.nome_area IN ('Story/Reels', 'Fotografia', 'Social Media'))
    
    -- 43. Victor Gomes Silva: STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_43_Victor_Gomes') AND a.nome_area IN ('Story/Reels'))
    
    -- 44. Vitória Naya: STORY/REELS, SOCIAL MEDIA
    OR (v.hash_voluntario = SHA1('VOL_44_Vitoria_Naya') AND a.nome_area IN ('Story/Reels', 'Social Media'))
    
    -- 45. Waleria Wilar: TELAO
    OR (v.hash_voluntario = SHA1('VOL_45_Waleria_Wilar') AND a.nome_area IN ('Telão'))
    
    -- 46. Isabelli Primo Maciel: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_46_Isabelli_Primo') AND a.nome_area IN ('Fotografia'))
    
    -- 47. Beatriz Tavares da Silva: STORY/REELS, FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_47_Beatriz_Tavares') AND a.nome_area IN ('Story/Reels', 'Fotografia'))
    
    -- 48. Alanys Fernandes: FOTOGRAFIA
    OR (v.hash_voluntario = SHA1('VOL_48_Alanys_Fernandes') AND a.nome_area IN ('Fotografia'))
    
    -- 49. Jonatas David Lima Brito: STORY/REELS
    OR (v.hash_voluntario = SHA1('VOL_49_Jonatas_David') AND a.nome_area IN ('Story/Reels'))
  );

-- 6. Associação dos Voluntários de TRANSMISSÃO ao Departamento 2 (Transmissão)
INSERT IGNORE INTO `tb_voluntario_departamento_area` (`id_voluntario`, `id_departamento`, `id_area`)
SELECT v.id_voluntario, a.id_departamento, a.id_area
FROM `tb_voluntario` v
CROSS JOIN `tb_departamento_area` a
WHERE a.id_departamento = 2
  AND a.nome_area = 'Transmissão'
  AND v.hash_voluntario IN (
    SHA1('VOL_8_Cellyne_Victhoria'),
    SHA1('VOL_9_Danilo_Froes'),
    SHA1('VOL_10_Eder_Augusto'),
    SHA1('VOL_12_Elpidio_Junior'),
    SHA1('VOL_27_Mariana_Procopio'),
    SHA1('VOL_38_Samuel_Matos'),
    SHA1('VOL_40_Thierry_Rodrigues'),
    SHA1('VOL_41_Tiago_Andrade')
  );
