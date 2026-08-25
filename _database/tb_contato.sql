	drop table if exists tb_contato;
	create table tb_contato (
		id_contato 		int not null auto_increment PRIMARY KEY,
		nome			    varchar(100),
		email			    varchar(100),
		telefone			varchar(15),
		instagram			varchar(100),
		site				varchar(200),
		mensagem			text,
		cidade	 			varchar(50),
		uf	 				char(2),
		date_insert			datetime, 
		hash_id 			text,
		confirmado			char(1),/* S ou N*/
		data_confirmacao	datetime
	);
