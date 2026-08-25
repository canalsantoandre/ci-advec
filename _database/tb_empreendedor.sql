drop table if exists tb_empreendedor;
create table tb_empreendedor (
	id_empreendedor 		int not null auto_increment PRIMARY KEY,
    nome			    varchar(100),
    email			    varchar(100),
    telefone			varchar(15),
	ramo_atividade		varchar(100),
	instagram			varchar(100),
	site				varchar(200),
	mensagem			text,
	qtde_convidado		int,
	cidade	 			varchar(50),
	uf	 				char(2),
	date_insert			datetime, 
	hash_id 			text,
	confirmado			char(1),/* S ou N*/
	data_confirmacao	datetime
);
