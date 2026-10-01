<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->post('dashboard', 'Home::index', ['filter' => 'auth']);
$routes->get('dashboard', 'Home::index', ['filter' => 'auth']);

/* AGENDA DE CULTOS */
$routes->get('agenda', 'Agenda::index', ['filter' => 'auth']);
$routes->group('agenda', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Agenda::index', ['filter' => 'auth']);
    $routes->get('events', 'Agenda::events', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'salvarCulto', 'Agenda::salvarCulto', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'excluirCulto/(:num)', 'Agenda::excluirCulto/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'excluirCulto', 'Agenda::excluirCulto', ['filter' => 'auth']);
    $routes->get('getConvidadosCulto/(:num)', 'Agenda::getConvidadosCulto/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'vincularConvidados', 'Agenda::vincularConvidados', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'removerConvidado', 'Agenda::removerConvidado', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'alternarPresenca', 'Agenda::alternarPresenca', ['filter' => 'auth']);
    $routes->get('dashConvidado/(:num)', 'Agenda::dashConvidado/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'grade', 'Agenda::grade', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'grade/(:num)/(:num)', 'Agenda::grade/$1/$2', ['filter' => 'auth']);
    $routes->post('gerarGradeMes', 'Agenda::gerarGradeMes', ['filter' => 'auth']);
    $routes->post('salvarRapidoAgenda', 'Agenda::salvarRapidoAgenda', ['filter' => 'auth']);
    $routes->get('imprimir/(:num)/(:num)', 'Agenda::imprimir/$1/$2', ['filter' => 'auth']);
    $routes->get('imprimir', 'Agenda::imprimir', ['filter' => 'auth']);
});

/* TIPOS DE CULTO PADRÃO */
$routes->get('cultopadrao', 'Cultopadrao::index', ['filter' => 'auth']);
$routes->group('cultopadrao', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Cultopadrao::index', ['filter' => 'auth']);
    $routes->get('novo', 'Cultopadrao::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:num)', 'Cultopadrao::editar/$1', ['filter' => 'auth']);
    $routes->post('salvar', 'Cultopadrao::salvar', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Cultopadrao::apagar/$1', ['filter' => 'auth']);
});

/* DEPARTAMENTOS */
$routes->get('departamento', 'Departamento::index', ['filter' => 'auth']);
$routes->group('departamento', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Departamento::index', ['filter' => 'auth']);
    $routes->get('novo', 'Departamento::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:num)', 'Departamento::editar/$1', ['filter' => 'auth']);
    $routes->post('salvar', 'Departamento::salvar', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Departamento::apagar/$1', ['filter' => 'auth']);
    $routes->post('salvarArea', 'Departamento::salvarArea', ['filter' => 'auth']);
    $routes->post('excluirArea', 'Departamento::excluirArea', ['filter' => 'auth']);
    $routes->get('getAreasByDepartamento/(:num)', 'Departamento::getAreasByDepartamento/$1', ['filter' => 'auth']);
});

/* VOLUNTÁRIOS */
$routes->get('voluntario', 'Voluntario::index', ['filter' => 'auth']);
$routes->group('voluntario', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Voluntario::index', ['filter' => 'auth']);
    $routes->get('novo', 'Voluntario::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:any)', 'Voluntario::editar/$1', ['filter' => 'auth']);
    $routes->post('salvar', 'Voluntario::salvar', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Voluntario::apagar/$1', ['filter' => 'auth']);
    $routes->get('dashVoluntario/(:num)', 'Voluntario::dashVoluntario/$1', ['filter' => 'auth']);
    $routes->get('desempenho', 'Voluntario::desempenho', ['filter' => 'auth']);
    $routes->get('getJustificativas/(:num)', 'Voluntario::getJustificativas/$1', ['filter' => 'auth']);
    $routes->get('getEstatisticasPeriodo', 'Voluntario::getEstatisticasPeriodo', ['filter' => 'auth']);
    $routes->post('resetSenha', 'Voluntario::resetSenha', ['filter' => 'auth']);
    $routes->get('verificarTelefone', 'Voluntario::verificarTelefone', ['filter' => 'auth']);
    $routes->post('vincularRapido', 'Voluntario::vincularRapido', ['filter' => 'auth']);
    $routes->get('getSubareasPorDepartamento', 'Voluntario::getSubareasPorDepartamento', ['filter' => 'auth']);
});

/* API VOLUNTÁRIOS */
$routes->group('api/voluntarios', ['filter' => 'auth'], static function ($routes) {
    $routes->get('verificar-telefone', 'Voluntario::verificarTelefone');
    $routes->post('vincular-rapido', 'Voluntario::vincularRapido');
    $routes->get('getSubareasPorDepartamento', 'Voluntario::getSubareasPorDepartamento');
});

/* PORTAL DO VOLUNTÁRIO (ACESSO EXCLUSIVO) */
$routes->match(['GET', 'POST'], 'portal/login', 'PortalVoluntario::login');
$routes->get('portal/logout', 'PortalVoluntario::logout');
$routes->get('portal/verificar-otp', 'PortalVoluntario::verificarOtp');
$routes->post('portal/confirmar-troca-senha-otp', 'PortalVoluntario::confirmarTrocaSenhaOtp');
$routes->post('portal/reenviar-otp', 'PortalVoluntario::reenviarOtp');

$routes->get('portal', 'PortalVoluntario::agenda', ['filter' => 'auth_voluntario']);
$routes->group('portal', ['filter' => 'auth_voluntario'], static function ($routes) {
    $routes->get('agenda', 'PortalVoluntario::agenda');
    $routes->get('agenda/(:num)/(:num)', 'PortalVoluntario::agenda/$1/$2');
    $routes->post('confirmarEscala', 'PortalVoluntario::confirmarEscala');
    $routes->post('recusarEscala', 'PortalVoluntario::recusarEscala');
    $routes->get('metricas', 'PortalVoluntario::metricas');
    $routes->get('perfil', 'PortalVoluntario::perfil');
    $routes->post('salvarPerfil', 'PortalVoluntario::salvarPerfil');
    $routes->post('uploadFoto', 'PortalVoluntario::uploadFoto');
    $routes->post('alterarSenha', 'PortalVoluntario::alterarSenha');
});

/* WEBHOOKS */
$routes->get('webhook', 'Webhook::index', ['filter' => 'auth']);
$routes->group('webhook', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Webhook::index', ['filter' => 'auth']);
    $routes->get('novo', 'Webhook::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:num)', 'Webhook::editar/$1', ['filter' => 'auth']);
    $routes->post('salvar', 'Webhook::salvar', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Webhook::apagar/$1', ['filter' => 'auth']);
    $routes->post('testar', 'Webhook::testar', ['filter' => 'auth']);
});

/* ESCALA DE VOLUNTÁRIOS */
$routes->get('escala', 'Escala::index', ['filter' => 'auth']);
$routes->group('escala', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Escala::index', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'grade', 'Escala::grade', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'grade/(:num)', 'Escala::grade/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'grade/(:num)/(:num)/(:num)', 'Escala::grade/$1/$2/$3', ['filter' => 'auth']);
    $routes->post('salvarEscala', 'Escala::salvarEscala', ['filter' => 'auth']);
    $routes->post('removerEscala', 'Escala::removerEscala', ['filter' => 'auth']);
    $routes->post('alternarPresenca', 'Escala::alternarPresenca', ['filter' => 'auth']);
    $routes->get('getVoluntariosPorArea', 'Escala::getVoluntariosPorArea', ['filter' => 'auth']);
    $routes->get('imprimir/(:num)/(:num)/(:num)', 'Escala::imprimir/$1/$2/$3', ['filter' => 'auth']);
    $routes->get('imprimir', 'Escala::imprimir', ['filter' => 'auth']);
});

/* CONVIDADO */
$routes->get('convidado', 'Convidado::index', ['filter' => 'auth']);
$routes->group('convidado', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Convidado::index', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'novo', 'Convidado::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'inserir', 'Convidado::inserirConvidado', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'apagar/(:any)', 'Convidado::apagarConvidado/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:any)', 'Convidado::editarConvidado/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizar', 'Convidado::atualizarConvidado', ['filter' => 'auth']);
});

/* SORTEIO */
$routes->match(['GET', 'POST'], 'sorteio', 'Sorteio::index');

/* EMPREENDEDOR */
$routes->match(['GET', 'POST'], 'empreendedor', 'Empreendedor::lista', ['filter' => 'auth']);
$routes->get('registration', 'Empreendedor::novo');
$routes->match(['GET', 'POST'], 'do-registration', 'Empreendedor::inserir');
$routes->match(['GET', 'POST'], 'confirm-registration/(:any)', 'Empreendedor::confirmar/$1');

/* CONTATO */
$routes->match(['GET', 'POST'], 'contato', 'Contato::lista', ['filter' => 'auth']);
$routes->get('contact', 'Contato::novo');
$routes->match(['GET', 'POST'], 'do-contato', 'Contato::inserir');
$routes->match(['GET', 'POST'], 'confirm-contact/(:any)', 'Contato::confirmar/$1');

/* CONTROLES DE SISTEMA (AUTH, USUARIO, PERFIL, MÓDULOS) */
$routes->match(['GET', 'POST'], 'dshlogin', 'Sessao::login');
$routes->match(['GET', 'POST'], 'dshlogout', 'Sessao::logout');
$routes->match(['GET', 'POST'], 'auth_instagram', 'LInstagram::auth');
$routes->match(['GET', 'POST'], 'accessdeny', 'Accessdeny::index', ['filter' => 'auth']);

$routes->get('usuario', 'Usuario::index', ['filter' => 'auth']);
$routes->group('usuario', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Usuario::index', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'novo', 'Usuario::novo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'inserir', 'Usuario::inserirUsuario', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'apagar/(:any)', 'Usuario::apagarUsuario/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:any)', 'Usuario::editarUsuario/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizar', 'Usuario::atualizarUsuario', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'senha/(:any)', 'Usuario::editarSenha/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizarSenha', 'Usuario::atualizarSenha', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'resetSenha', 'Usuario::resetSenha', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'getModalResetSenha', 'Usuario::getModalResetSenha', ['filter' => 'auth']);
});

$routes->get('perfil', 'Perfil::index', ['filter' => 'auth']);
$routes->group('perfil', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Perfil::index', ['filter' => 'auth']);
    $routes->get('novo', 'Perfil::novoPerfil', ['filter' => 'auth']);
    $routes->post('inserir', 'Perfil::inserirPerfil', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'editar/(:num)', 'Perfil::editarPerfil/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizar', 'Perfil::atualizarPerfil', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Perfil::apagarPerfil/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizarModulo', 'Perfil::atualizarModulo', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'atualizarModuloAcao', 'Perfil::atualizarModuloAcao', ['filter' => 'auth']);
});

$routes->get('sysmodulo', 'Sysmodulo::index', ['filter' => 'auth']);
$routes->group('sysmodulo', static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Sysmodulo::index', ['filter' => 'auth']);
    $routes->get('novo', 'Sysmodulo::novo', ['filter' => 'auth']);
    $routes->get('editar/(:num)', 'Sysmodulo::editar/$1', ['filter' => 'auth']);
    $routes->post('salvar', 'Sysmodulo::salvar', ['filter' => 'auth']);
    $routes->get('apagar/(:num)', 'Sysmodulo::apagar/$1', ['filter' => 'auth']);
    $routes->post('reordenar', 'Sysmodulo::reordenar', ['filter' => 'auth']);
    $routes->post('salvarCategoria', 'Sysmodulo::salvarCategoria', ['filter' => 'auth']);
});

/* WHATSAPP (EVOLUTION API - GESTÃO DE ACESSO SYSADM) */
$routes->get('whatsapp', 'Whatsapp::index', ['filter' => 'auth']);
$routes->group('whatsapp', ['filter' => 'auth'], static function ($routes) {
    $routes->match(['GET', 'POST'], 'index', 'Whatsapp::index');
    $routes->post('create', 'Whatsapp::create');
    $routes->get('status/(:num)', 'Whatsapp::status/$1');
    $routes->get('qrcode/(:num)', 'Whatsapp::qrcode/$1');
    $routes->post('restart/(:num)', 'Whatsapp::restart/$1');
    $routes->post('logout/(:num)', 'Whatsapp::disconnect/$1');
    $routes->post('profile/(:num)', 'Whatsapp::profile/$1');
    $routes->post('send/(:num)', 'Whatsapp::send/$1');
    $routes->delete('delete/(:num)', 'Whatsapp::delete/$1');
    $routes->get('export/(:num)', 'Whatsapp::export/$1');
});

/* GERAL UTILS */
$routes->group('geral', static function ($routes) {
    $routes->match(['GET', 'POST'], 'getModalDelete', 'Geral::getModalDelete', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'getModalDelete/(:any)', 'Geral::getModalDelete/$1', ['filter' => 'auth']);
    $routes->match(['GET', 'POST'], 'getModalConfirm', 'Geral::getModalConfirm', ['filter' => 'auth']);
});
