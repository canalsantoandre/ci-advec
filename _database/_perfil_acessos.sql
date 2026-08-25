drop table if exists `tb_sys_categoria_modulo`;

create table `tb_sys_categoria_modulo` (
	`id_categoria_modulo` 		int,
	`nome_categoria_modulo`		varchar(100),
    `icon_class_categoria` 		varchar(100),
	`ordem_exibicao_categoria`	int,
	`exibir_titulo`				int, /*0=não 1 =sim  - Exibir o titulo da categoria na tela*/
	`status_categoria`			int default 1
 );

insert into tb_sys_categoria_modulo (id_categoria_modulo, nome_categoria_modulo, icon_class_categoria, ordem_exibicao_categoria, exibir_titulo,status_categoria) values 
(1,'GERENCIAL','',1,1,1),
(2,'CADASTRO','',2,1,1),
(3,'GESTÃO DE ACESSO','',3,1,1);


drop table if exists `tb_sys_modulo`;
create table `tb_sys_modulo` (
	`id_modulo` 			int,
	`id_categoria_modulo`	int,	
    `id_modulo_pai` 		int default null,
	`nome_modulo`			varchar(100),
    `icon_class_modulo` 	varchar(100),
    `uri_modulo`			varchar(100),
	`ordem_exibicao_modulo`	int,
    `status_modulo`			int default 1
 );

insert into tb_sys_modulo (id_modulo, id_modulo_pai, ordem_exibicao_modulo, id_categoria_modulo,   nome_modulo, icon_class_modulo, uri_modulo, status_modulo) values 
(1, null, 1, 3, 'Usuários','fa fa-users','usuario/',1),
(2, null, 2, 3, 'Perfil de acesso','fa fa-cogs','perfil/',1),
(3, null, 1, 1, 'Dashboard','fa fa-tachometer-alt','dashboard/',1),
(4, null, 2, 1, 'Agenda','fa fa-calendar-alt','agenda/',1),
(5,    4, 1, 1, 'Consulta','fas fa-search','consulta/',1),
(6, null, 3, 1, 'Convidados','far fa-user','convidado/',1),
(7, null, 3, 1, 'Empreendedor','fa-solid fa-user-tie','empreendedor/',1),
(8, null, 3, 1, 'Contato','fa-solid fa-user-tie','contato/',1);



drop table if exists `tb_sys_modulo_acao`;
create table `tb_sys_modulo_acao` (
	`id_modulo` 	int,
	`modulo_acao`	varchar(150)
);

insert into tb_sys_modulo_acao (id_modulo, modulo_acao) values 
(1,'create'),(1,'read'),(1,'update'),(1, 'delete'),(1, 'reset_password'),
(2,'create'),(2,'read'),(2,'update'),(2, 'delete'),
(3,'read'),
(4,'create'),(4,'read'),(4,'update'),(4, 'delete'),
(5,'read'),
(6,'create'),(6,'read'),(6,'update'),(6, 'delete'),(6,'linkfotos_visualizar'),(6,'linkfotos_inserir'),(6,'linkfotos_excluir'),
(7,'read'),
(8,'read');

drop table if exists `tb_sys_perfil`;
create table `tb_sys_perfil` (
	`id_perfil` 			int,
	`nome_perfil`			varchar(100),
    `content_view_default`			varchar(120),
    `status_perfil`			int default 1
 );
 
insert into tb_sys_perfil (id_perfil, nome_perfil, content_view_default, status_perfil) values 
 (1, 'SysAdm', '_main/principal', 1),
 (2, 'Adm Geral', '_mail/principal',1),
 (3, 'Secretaria','_main/principal', 1),
 (4, 'Comunicação','_main/principal', 1),
 (5, 'Louvor','_main/principal', 1),
 (6, 'Músicos','_main/principal', 1),
 (7, 'Empreendedor','_main/principal', 1);

drop table if exists `tb_sys_perfil_modulo`;
create table `tb_sys_perfil_modulo` (
	`id_perfil` 		int,
	`id_modulo`			int
 );
 
insert into tb_sys_perfil_modulo (id_perfil, id_modulo) values 
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),
(2,3),(2,4),(2,5),
(3,3),(3,4),(3,5);
 
drop table if exists `tb_sys_perfil_modulo_acao`;
create table `tb_sys_perfil_modulo_acao` (
	`id_perfil` 		int,
	`id_modulo`			int,
	`modulo_acao`		varchar(150)
 );
 
 -- carga acessos SYS ADM
 insert into tb_sys_perfil_modulo_acao
 select  p.id_perfil, m.id_modulo, m.modulo_acao
 from tb_sys_perfil_modulo as p
 inner join tb_sys_modulo_acao as m on m.id_modulo = p.id_modulo
 where p.id_perfil = 1;
 
 select * from tb_sys_perfil_modulo;
 