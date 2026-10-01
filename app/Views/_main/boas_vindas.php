<?php
/**
 * View: _main/boas_vindas.php
 * Tela de Boas-Vindas Clean & Moderna para o Sistema de Gestão ADVEC
 */

// Saudação dinâmica com base no horário
$hora = (int) date('H');
if ($hora >= 5 && $hora < 12) {
    $saudacao = 'Bom dia';
    $iconeSaudacao = 'bi-sun-fill text-warning';
} elseif ($hora >= 12 && $hora < 18) {
    $saudacao = 'Boa tarde';
    $iconeSaudacao = 'bi-brightness-high-fill text-warning';
} else {
    $saudacao = 'Boa noite';
    $iconeSaudacao = 'bi-moon-stars-fill text-info';
}

// Formatação da data atual em português
$diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$diaSemana = $diasSemana[(int)date('w')];
$diaMes = date('d');
$mesNome = $meses[(int)date('n')];
$ano = date('Y');
$dataExtenso = "{$diaSemana}, {$diaMes} de {$mesNome} de {$ano}";

$nomeUsuario = isset($usuario->nome) ? esc($usuario->nome) : 'Usuário';
$primeiroNome = explode(' ', trim($nomeUsuario))[0];
$perfilUsuario = isset($usuario->nome_perfil) ? esc($usuario->nome_perfil) : 'Colaborador';
$emailUsuario = isset($usuario->email) ? esc($usuario->email) : '';
?>

<!-- Header / Breadcrumb -->
<div class="app-content-header py-3">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h4 class="mb-0 fw-bold text-dark-emphasis">
          <i class="bi <?= $iconeSaudacao ?> me-2"></i><?= $saudacao ?>, <?= $primeiroNome ?>!
        </h4>
        <p class="text-secondary small mb-0">
          <i class="bi bi-calendar3 me-1"></i><?= $dataExtenso ?>
        </p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end mb-0 bg-transparent p-0 small">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none">Início</a></li>
          <li class="breadcrumb-item active" aria-current="page">Boas-Vindas</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Conteúdo Principal -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Hero Card de Boas-Vindas -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden position-relative hero-welcome-card">
      <div class="card-body p-4 p-lg-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center gy-4">
          <div class="col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-75 dark-pill shadow-xs mb-3 border border-light-subtle">
              <span class="badge bg-primary-subtle text-primary rounded-pill fw-semibold px-2 py-1">
                <i class="bi bi-shield-check me-1"></i><?= $perfilUsuario ?>
              </span>
              <span class="small text-secondary fw-medium">ADVEC Sistema de Gestão</span>
            </div>
            
            <h2 class="display-6 fw-bold mb-3 text-dark-emphasis tracking-tight">
              Que bom ter você por aqui, <span class="text-primary"><?= $primeiroNome ?></span>!
            </h2>
            
            <p class="lead fs-6 text-secondary mb-4 col-xl-10" style="line-height: 1.6;">
              Seu painel está pronto para uso. Navegue pelas opções no menu lateral ou utilize os atalhos rápidos abaixo para acessar suas rotinas, escalas e módulos com rapidez e segurança.
            </p>

            <div class="d-flex flex-wrap gap-2 pt-1">
              <span class="badge bg-body-tertiary text-secondary border px-3 py-2 rounded-pill font-monospace small">
                <i class="bi bi-person-circle me-1 text-primary"></i> <?= $nomeUsuario ?>
              </span>
              <?php if (!empty($emailUsuario)): ?>
                <span class="badge bg-body-tertiary text-secondary border px-3 py-2 rounded-pill font-monospace small">
                  <i class="bi bi-envelope me-1 text-primary"></i> <?= $emailUsuario ?>
                </span>
              <?php endif; ?>
              <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill small">
                <i class="bi bi-check-circle-fill me-1"></i> Sistema Operacional
              </span>
            </div>
          </div>

          <div class="col-lg-4 text-center d-none d-lg-block">
            <div class="welcome-illustration position-relative mx-auto">
              <div class="welcome-icon-circle shadow-lg rounded-circle d-flex align-items-center justify-content-center mx-auto bg-primary text-white">
                <i class="bi bi-grid-1x2-fill" style="font-size: 3.5rem;"></i>
              </div>
              <div class="floating-badge badge-1 shadow-sm rounded-3 p-2 bg-body border position-absolute">
                <i class="bi bi-calendar-check-fill text-success fs-5"></i>
              </div>
              <div class="floating-badge badge-2 shadow-sm rounded-3 p-2 bg-body border position-absolute">
                <i class="bi bi-people-fill text-primary fs-5"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Background glow decoration -->
      <div class="hero-bg-glow"></div>
    </div>

    <!-- Seção de Acessos Rápidos Disponíveis -->
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <h5 class="fw-bold mb-1 text-dark-emphasis">
              <i class="bi bi-compass me-2 text-primary"></i>Módulos e Acessos Disponíveis
            </h5>
            <p class="text-secondary small mb-0">Atalhos diretos configurados para o seu nível de perfil</p>
          </div>
        </div>
      </div>

      <?php
      // Se houver perfis de acesso definidos na sessão
      if (isset($usuario->perfil_acesso) && is_array($usuario->perfil_acesso) && count($usuario->perfil_acesso) > 0):
        $modulosExibidos = 0;
        foreach ($usuario->perfil_acesso as $mod):
          $temSubitens = (isset($mod->subitem) && is_array($mod->subitem) && count($mod->subitem) > 0);
          $linkModulo = $temSubitens ? base_url($mod->subitem[0]->uri_modulo) : base_url($mod->uri_modulo);
          $iconeModulo = !empty($mod->icon_class_modulo) ? $mod->icon_class_modulo : 'bi bi-folder2-open';
          $categoria = !empty($mod->nome_categoria_modulo) ? $mod->nome_categoria_modulo : 'Geral';
          $modulosExibidos++;
      ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
          <a href="<?= $linkModulo ?>" class="text-decoration-none">
            <div class="card h-100 border rounded-4 shadow-sm quick-access-card transition-all">
              <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="module-icon-box rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
                      <i class="<?= esc($iconeModulo) ?> fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small fw-normal">
                      <?= esc($categoria) ?>
                    </span>
                  </div>
                  <h6 class="fw-bold text-dark-emphasis mb-1"><?= esc($mod->nome_modulo) ?></h6>
                  <p class="text-secondary small mb-3">
                    <?= $temSubitens ? count($mod->subitem) . ' opções disponíveis' : 'Acessar funcionalidade' ?>
                  </p>
                </div>
                <div class="d-flex align-items-center text-primary fw-semibold small pt-2 border-top border-light-subtle">
                  <span>Abrir módulo</span>
                  <i class="bi bi-arrow-right ms-auto transition-arrow"></i>
                </div>
              </div>
            </div>
          </a>
        </div>
      <?php 
        endforeach;
      else:
        // Cards de Fallback Padrão e Elegantes caso a listagem dinâmica ainda não esteja carregada
      ?>
        <!-- Card Voluntários -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <a href="<?= base_url('voluntario') ?>" class="text-decoration-none">
            <div class="card h-100 border rounded-4 shadow-sm quick-access-card transition-all">
              <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="module-icon-box rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
                      <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small">Equipe</span>
                  </div>
                  <h6 class="fw-bold text-dark-emphasis mb-1">Voluntários</h6>
                  <p class="text-secondary small mb-3">Cadastro, sub-áreas e gestão da equipe</p>
                </div>
                <div class="d-flex align-items-center text-primary fw-semibold small pt-2 border-top border-light-subtle">
                  <span>Acessar</span>
                  <i class="bi bi-arrow-right ms-auto transition-arrow"></i>
                </div>
              </div>
            </div>
          </a>
        </div>

        <!-- Card Escalas -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <a href="<?= base_url('escala/grade') ?>" class="text-decoration-none">
            <div class="card h-100 border rounded-4 shadow-sm quick-access-card transition-all">
              <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="module-icon-box rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success">
                      <i class="bi bi-calendar3 fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small">Escala</span>
                  </div>
                  <h6 class="fw-bold text-dark-emphasis mb-1">Grade de Escalas</h6>
                  <p class="text-secondary small mb-3">Planejamento e alocação de cultos</p>
                </div>
                <div class="d-flex align-items-center text-success fw-semibold small pt-2 border-top border-light-subtle">
                  <span>Acessar</span>
                  <i class="bi bi-arrow-right ms-auto transition-arrow"></i>
                </div>
              </div>
            </div>
          </a>
        </div>

        <!-- Card Departamentos -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <a href="<?= base_url('departamento') ?>" class="text-decoration-none">
            <div class="card h-100 border rounded-4 shadow-sm quick-access-card transition-all">
              <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="module-icon-box rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis">
                      <i class="bi bi-diagram-3-fill fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small">Estrutura</span>
                  </div>
                  <h6 class="fw-bold text-dark-emphasis mb-1">Departamentos</h6>
                  <p class="text-secondary small mb-3">Áreas, liderança e configurações</p>
                </div>
                <div class="d-flex align-items-center text-warning-emphasis fw-semibold small pt-2 border-top border-light-subtle">
                  <span>Acessar</span>
                  <i class="bi bi-arrow-right ms-auto transition-arrow"></i>
                </div>
              </div>
            </div>
          </a>
        </div>

        <!-- Card Minha Conta / Troca de Senha -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <a href="<?= base_url('usuario/alterar_senha') ?>" class="text-decoration-none">
            <div class="card h-100 border rounded-4 shadow-sm quick-access-card transition-all">
              <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="module-icon-box rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info-emphasis">
                      <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small">Segurança</span>
                  </div>
                  <h6 class="fw-bold text-dark-emphasis mb-1">Segurança & Senha</h6>
                  <p class="text-secondary small mb-3">Atualize suas credenciais de acesso</p>
                </div>
                <div class="d-flex align-items-center text-info-emphasis fw-semibold small pt-2 border-top border-light-subtle">
                  <span>Configurar</span>
                  <i class="bi bi-arrow-right ms-auto transition-arrow"></i>
                </div>
              </div>
            </div>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Dicas e Informações de Ajuda -->
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border rounded-4 shadow-sm">
          <div class="card-body p-4">
            <h6 class="fw-bold text-dark-emphasis mb-3 d-flex align-items-center">
              <i class="bi bi-info-circle-fill text-primary me-2"></i>Orientações Gerais do Sistema
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="p-3 rounded-3 bg-body-tertiary border border-light-subtle h-100">
                  <div class="d-flex align-items-start gap-2 mb-1">
                    <i class="bi bi-check2-square text-success fs-5"></i>
                    <div>
                      <span class="fw-semibold text-dark-emphasis small">Controle Departamental</span>
                      <p class="text-secondary small mb-0 mt-1">
                        Suas permissões e visualizações são automaticamente filtradas de acordo com os departamentos associados ao seu usuário.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 rounded-3 bg-body-tertiary border border-light-subtle h-100">
                  <div class="d-flex align-items-start gap-2 mb-1">
                    <i class="bi bi-shield-check text-primary fs-5"></i>
                    <div>
                      <span class="fw-semibold text-dark-emphasis small">Segurança da Sessão</span>
                      <p class="text-secondary small mb-0 mt-1">
                        Mantenha sempre sua senha pessoal atualizada e utilize o botão de logout ao encerrar o uso em computadores compartilhados.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border rounded-4 shadow-sm h-100 bg-gradient">
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="p-2 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                  <i class="bi bi-headset fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark-emphasis">Precisa de Suporte?</h6>
                  <span class="small text-secondary">Equipe de TI & Mídia</span>
                </div>
              </div>
              <p class="text-secondary small mb-3">
                Em caso de dúvidas operacionais ou necessidade de alteração de permissões, entre em contato com o administrador do sistema.
              </p>
            </div>
            <div class="pt-3 border-top border-light-subtle text-muted small d-flex align-items-center justify-content-between">
              <span>Versão do Sistema</span>
              <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">v2.0</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
/* Estilos Customizados da Tela de Boas-Vindas */
.hero-welcome-card {
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(15, 23, 42, 0.03) 100%);
  border: 1px solid rgba(0, 0, 0, 0.06) !important;
}

[data-bs-theme="dark"] .hero-welcome-card {
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(15, 23, 42, 0.4) 100%);
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .hero-welcome-card .dark-pill {
  background-color: rgba(30, 41, 59, 0.8) !important;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

.hero-bg-glow {
  position: absolute;
  top: -50%;
  right: -10%;
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 1;
}

.welcome-illustration {
  width: 170px;
  height: 170px;
}

.welcome-icon-circle {
  width: 130px;
  height: 130px;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
  box-shadow: 0 12px 28px rgba(37, 99, 235, 0.3) !important;
}

.floating-badge {
  z-index: 3;
}

.badge-1 {
  top: 5px;
  left: -10px;
  animation: floatSlow 4s ease-in-out infinite;
}

.badge-2 {
  bottom: 10px;
  right: -10px;
  animation: floatSlow 4s ease-in-out infinite 2s;
}

@keyframes floatSlow {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}

.quick-access-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  background-color: var(--bs-card-bg);
}

.quick-access-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.07) !important;
  border-color: rgba(37, 99, 235, 0.4) !important;
}

.quick-access-card:hover .transition-arrow {
  transform: translateX(4px);
}

.transition-arrow {
  transition: transform 0.2s ease;
}

.module-icon-box {
  width: 48px;
  height: 48px;
}
</style>
