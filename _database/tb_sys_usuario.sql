drop table if exists `tb_sys_usuario`;
create table `tb_sys_usuario` (
	`id_usuario` 		int(11) not null auto_increment PRIMARY KEY,
    `hash_user`			varchar(100),
	`usuario`			varchar(100),
    `nome`				varchar(100),
    `senha`				varchar(100),
    `status_usuario`	int default 0,
    `id_perfil` 		int,
    `alterar_senha` 	int default 1,
    `senha_usuario`		varchar(100),
	`data_ultimo_login`	datetime,
    `data_ultima_senha` datetime
    );
    
insert into tb_sys_usuario (usuario, nome, senha, status_usuario,id_perfil, alterar_senha,senha_usuario, data_ultimo_login, data_ultima_senha, hash_user) values 
/*senha 'master' */
('elpidio','Elpidio N Lima','$2y$10$z/0dAmnQgoKZaK6Ae6mXpuq../Y4woRMML3QD4aVs33BgDrsZXjA2',1,1,0,'',now(), now(), sha1('US1'));


update tb_sys_usuario set senha= '$2y$10$z/0dAmnQgoKZaK6Ae6mXpuq../Y4woRMML3QD4aVs33BgDrsZXjA2' where id_usuario = 1;