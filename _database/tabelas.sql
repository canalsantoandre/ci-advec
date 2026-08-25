drop table if exists tb_funcao_eclesiastica;
create table tb_funcao_eclesiastica(
	id_funcao_eclesiastica		int not null auto_increment,
	nm_funcao_eclesiastica		varchar(100),
	nm_sigla					varchar(3),
    cor							varchar(10),
    icon						varchar(30),
    ordem						int,
	primary key (id_funcao_eclesiastica)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into tb_funcao_eclesiastica ( nm_funcao_eclesiastica, nm_sigla, cor, ordem) values 
('Pastor','PR','#e5e4e2',1),
('Diácono','DC','silver',2),
('Obreiro','OB','#b87333',3),
('Evangelista','EV','$e5e4e2',4),
('Cantor','CA','gold',5),
('Palestrante','','#e5e4e2',6),
('Missionário','MS','#e5e4e2',7),
('Pastora','PRA','#e5e4e2',8),
('Missionária','PRA','#e5e4e2',9),
('Diaconisa','DC','silver',10),
('Tecladista','TC','silver',11),
('Baterista','BT','silver',12),
('Baixista','BX','silver',13),
('Saxofonista','SX','silver',14),
('Violonista','VL','silver',15),
('Guitarrista','GT','silver',16),
('Vocais','VL','silver',17),
('Não Definida','NT','silver',18);

drop table if exists  `tb_convidado`;
create table `tb_convidado` (
	`id_convidado` 		int not null auto_increment PRIMARY KEY,
    `nome_convidado`				varchar(100),
    `nome_convidado_visualizacao` varchar(100),
    `sexo`				char(1),
    `data_nascimento`	date,
    `email`				varchar(150),
    `telefone`			varchar(11),
	`cep`               varchar(8),
    `logradouro`        varchar(150),
    `numero`            varchar(10),
    `complemento`        varchar(20),
    `bairro`            varchar(150),
    `cidade`            varchar(150),
    `uf`			    varchar(150),
    `id_filial`			int,
    `nome_igreja`		varchar(150),
    `id_funcao_eclesiastica` int,
    `link_fotos`		varchar(200),
    `observacao`		text,
    `musico`			int default 0,
    
    `id_google`			varchar(100),
	`url_foto_google`	text,
	`email_google`	varchar(100),
    
    `id_facebook`		varchar(100),
	`url_foto_facebook`	text,
	`email_facebook`	varchar(100),
    
    `id_instagram`		varchar(100),
	`url_foto_instagram`	text,
	`email_instagram`	varchar(100),
	`nick_instagram`	varchar(50),
    
    `data_envio`		datetime,
    `remote_ip`			varchar(20),
    `status_convidado`	int default 1,
    `email_confirmado`	int default 0,
    `token_pwd`			varchar(150),
	`validade_token_pwd` datetime,
    `alterar_senha` 	int default 1,
    `senha_usuario`		varchar(100),
	`data_ultimo_login`	datetime,
    `token`				varchar(100),
    `hash_convidado`	varchar(100)		
    );

/*
alter table tb_convidado add `observacao`		text after link_fotos;
*/
create table `tb_convidado_link_foto` (
	`id_convidado_link_foto` 			int not null auto_increment PRIMARY KEY,
    `id_convidado`	int,
    `descricao_link_foto`	varchar(30),
    `link_foto`				varchar(200)
);
/*
alter table tb_convidado 
add musico int default 0 after observacao;
*/

create table `tb_filial` (
	`id_filial` 			int not null auto_increment PRIMARY KEY,
    `nome_filial`			varchar(100),
    `nome_fantasia`			varchar(100),
    `cep`				varchar(9),
	`logradouro`		varchar(100),
	`numero_endereco`	varchar(10),
	`complemento`	 	varchar(20),
	`bairro`	 		varchar(50),
	`cidade`	 		varchar(50),
	`uf`	 			char(2)
);

create table `tb_culto` (
	`id_culto` 			int not null auto_increment PRIMARY KEY,
    `id_filial`			int,
    `nome_culto`		varchar(100),
    `imagem`			varchar(200),
    `horario_inicio`	time,
    `horario_termino`	time,
    `dia_semana`		varchar(1), /*1-DOMINGO 2-SEGUNDA 3-TERÇA 4-QUARTA 5-QUINTA 6-SEXTA 7-SÁBADO*/
    `tema_mensal`		varchar(40)
);