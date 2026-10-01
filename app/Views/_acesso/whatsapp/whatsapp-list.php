<link rel="stylesheet" href="<?= base_url('assets/css/whatsapp.css') ?>">

<!-- Loader Overlay -->
<div id="loader-overlay" class="loading-overlay">
    <div class="loading-box">
        <div class="spinner-border text-primary mb-3" style="width: 3.2rem; height: 3.2rem;" role="status"></div>
        <h5 class="fw-bold mb-1 card-title-text">Processando requisição...</h5>
        <p class="text-theme-secondary mb-0 small">Por favor, aguarde alguns instantes.</p>
    </div>
</div>

<!-- Content Header (Page header) -->
<div class="app-content-header mb-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold page-title d-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp text-success fs-3"></i> Gestão de WhatsApp
                </h3>
                <p class="text-theme-secondary small mb-0">Gerencie conexões de WhatsApp via Evolution API (Exclusivo SysAdm)</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">Gestão de Acesso</li>
                    <li class="breadcrumb-item active" aria-current="page">WhatsApp</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="app-content">
    <div class="container-fluid">

        <!-- Plan Usage Card & Header -->
        <div class="row g-3 mb-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="p-3 app-widget-card h-100 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                        <h5 class="fw-bold mb-0 card-title-text">Instâncias Conectadas</h5>
                    </div>
                    <p class="text-theme-secondary small mb-0">
                        Cada instância gerada possui um identificador exclusivo para automações no <strong>n8n</strong>, notificações e envio de códigos OTP.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-3 app-widget-card h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-theme-secondary small text-uppercase tracking-wider">Uso do Plano</span>
                        <span class="fw-bold card-title-text"><?= esc($connectionsCount) ?> / <?= esc($planLimit) ?> conexões</span>
                    </div>
                    <?php
                    $percent = $planLimit > 0 ? min(100, ($connectionsCount / $planLimit) * 100) : 0;
                    $available = max(0, $planLimit - $connectionsCount);
                    ?>
                    <div class="progress-bar-custom mb-3">
                        <div class="progress-fill <?= $percent >= 100 ? 'bg-danger' : '' ?>" style="width: <?= esc($percent) ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-theme-muted d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span class="text-theme-secondary fw-medium"><?= esc($available) ?> disponíveis</span>
                        </small>
                        <?php if ($connectionsCount < $planLimit): ?>
                            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createConnectionModal">
                                <i class="bi bi-plus-lg"></i> Conectar WhatsApp
                            </button>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                <i class="bi bi-exclamation-octagon me-1"></i> Limite atingido
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connections Grid -->
        <div class="row g-3" id="connections-grid">
            <?php if (empty($connections)): ?>
                <div class="col-12">
                    <div class="app-widget-card text-center py-5 px-4 my-2">
                        <div class="avatar-wrapper-platform mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-inbox text-theme-secondary fs-3"></i>
                        </div>
                        <h5 class="card-title-text fw-bold mb-1">Nenhuma conexão encontrada</h5>
                        <p class="text-theme-secondary small mb-3 mx-auto" style="max-width: 420px;">Você ainda não cadastrou nenhuma conexão de WhatsApp. Clique no botão abaixo para iniciar.</p>
                        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createConnectionModal">
                            <i class="bi bi-plus-lg me-1"></i> Adicionar Conexão
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($connections as $conn): ?>
                    <?php
                    $statusClass = 'secondary';
                    $statusText = 'Desconhecido';
                    $statusIcon = 'question-circle';

                    switch ($conn['status']) {
                        case 'connected':
                            $statusClass = 'connected';
                            $statusText = 'Conectado';
                            $statusIcon = 'check-circle-fill';
                            break;
                        case 'waiting_qr':
                            $statusClass = 'waiting_qr';
                            $statusText = 'Aguardando Conexão';
                            $statusIcon = 'qr-code-scan';
                            break;
                        case 'disconnected':
                            $statusClass = 'disconnected';
                            $statusText = 'Desconectado';
                            $statusIcon = 'x-circle-fill';
                            break;
                        case 'configuring':
                        case 'configured':
                            $statusClass = 'configured';
                            $statusText = 'Aguardando ação...';
                            $statusIcon = 'hourglass-split';
                            break;
                    }
                    ?>
                    <div class="col-md-6 col-lg-4 connection-card-wrapper" id="conn-card-<?= esc($conn['id']) ?>">
                        <div class="card-whatsapp-platform h-100 p-3 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Top Card Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                    <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1;">
                                        <div class="avatar-wrapper-platform">
                                            <?php if (!empty($conn['profile_picture'])): ?>
                                                <img src="<?= esc($conn['profile_picture']) ?>" alt="Avatar">
                                            <?php else: ?>
                                                <i class="bi bi-whatsapp"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div style="min-width: 0; flex: 1;">
                                            <h5 class="fw-bold card-title-text mb-0 text-truncate" title="<?= esc($conn['name'] ?? 'WhatsApp') ?>">
                                                <?= esc($conn['name'] ?? 'WhatsApp') ?>
                                            </h5>

                                            <!-- Nome da Instância para n8n e integrações -->
                                            <div class="my-1">
                                                <div class="instance-tag-wrapper" title="Nome da Instância para uso no n8n e integrações">
                                                    <span class="instance-tag-label">Instância:</span>
                                                    <span class="instance-tag-value text-truncate" style="max-width: 140px; display: inline-block; vertical-align: middle;" id="inst-name-<?= esc($conn['id']) ?>"><?= esc($conn['instance_name']) ?></span>
                                                    <button type="button" class="btn-copy-instance" onclick="copyInstanceName('<?= esc($conn['instance_name']) ?>', this)" title="Copiar nome da instância">
                                                        <i class="bi bi-copy"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-theme-secondary small fw-medium text-truncate" id="phone-<?= esc($conn['id']) ?>">
                                                <?= !empty($conn['phone']) ? '+' . esc($conn['phone']) : '<span class="text-theme-muted">Sem número vinculado</span>' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown flex-shrink-0">
                                        <button class="btn btn-icon-action" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opções">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="checkStatus(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-arrow-clockwise me-2 text-info"></i>Atualizar Status
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="showQRModal(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-qr-code me-2 text-warning"></i>Conectar / Exibir QR
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="syncProfile(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-person-badge me-2 text-primary"></i>Sincronizar Perfil
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="restartInstance(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-bootstrap-reboot me-2 text-secondary"></i>Reiniciar Instância
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="disconnectInstance(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-plug me-2"></i>Desconectar
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteInstance(<?= esc($conn['id']) ?>)">
                                                    <i class="bi bi-trash me-2"></i>Remover Conexão
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="mb-3">
                                    <span class="badge-custom badge-<?= esc($statusClass) ?> w-100" id="badge-<?= esc($conn['id']) ?>" data-status="<?= esc($conn['status']) ?>">
                                        <i class="bi bi-<?= esc($statusIcon) ?>" id="badge-icon-<?= esc($conn['id']) ?>"></i>
                                        <span id="badge-text-<?= esc($conn['id']) ?>"><?= esc($statusText) ?></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom Footer info -->
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10">
                                <span class="text-theme-muted d-flex align-items-center gap-1" style="font-size: 0.78rem;" id="last-conn-<?= esc($conn['id']) ?>">
                                    <i class="bi bi-clock"></i>
                                    <?= !empty($conn['last_connection']) ? date('d/m/Y H:i', strtotime($conn['last_connection'])) : 'Nunca conectado' ?>
                                </span>
                                <button class="btn btn-sm btn-card-footer py-1 px-2" onclick="exportJSON(<?= esc($conn['id']) ?>)" title="Exportar Configuração">
                                    <i class="bi bi-download me-1"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Connection Modal -->
<div class="modal fade" id="createConnectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content adaptive-modal shadow border-0">
            <div class="modal-header adaptive-modal-header">
                <h5 class="modal-title fw-bold card-title-text d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary"></i> Conectar Novo WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-theme-secondary small mb-4">
                    Defina um nome amigável para identificar esse número ou setor (ex: Secretaria, Pastoral, Geral). A instância técnica na Evolution API será gerada de forma automática.
                </p>

                <div class="mb-3">
                    <label for="connection_name" class="form-label fw-semibold card-title-text">Nome da conexão <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="connection_name" placeholder="ex: WhatsApp ADVEC Principal" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer adaptive-modal-footer">
                <button type="button" class="btn btn-card-footer px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="createInstance()">
                    <i class="bi bi-check-lg me-1"></i> Gerar Conexão
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content adaptive-modal shadow border-0">
            <div class="modal-header adaptive-modal-header">
                <h5 class="modal-title fw-bold card-title-text d-flex align-items-center gap-2">
                    <i class="bi bi-qr-code text-warning"></i> Conectar Aparelho
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeQRModal()"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="qr-wrapper" class="py-2 mb-3">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-theme-secondary mb-0">Carregando QR Code...</p>
                </div>

                <div class="instruction-box text-start">
                    <p class="fw-semibold card-title-text mb-2 fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-phone text-success"></i> Siga os passos no celular:
                    </p>
                    <ol class="small mb-0 ps-3 text-theme-secondary">
                        <li>Abra o WhatsApp no smartphone</li>
                        <li>Acesse <strong>Configurações</strong> ou <strong>Mais opções</strong> (<i class="bi bi-three-dots-vertical"></i>)</li>
                        <li>Selecione <strong>Aparelhos conectados</strong></li>
                        <li>Toque em <strong>Conectar um aparelho</strong> e aponte a câmera para este QR Code</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer adaptive-modal-footer">
                <button type="button" class="btn btn-card-footer w-100 py-2" data-bs-dismiss="modal" onclick="closeQRModal()">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
<script>
    window.baseUrl = '<?= rtrim(base_url(), '/') ?>';
</script>
<script src="<?= base_url('assets/js/whatsapp.js') ?>?v=<?= date('Hi') ?>"></script>