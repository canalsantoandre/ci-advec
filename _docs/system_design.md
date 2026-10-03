# 🏛️ System Design & Guia de Estilo e Arquitetura
**ADVEC Gestão & Portal do Voluntário — Framework US**

Este documento estabelece as diretrizes canônicas de **Design System (UI/UX)**, **Acessibilidade**, **Componentização**, **Regras de Negócio** e **Boas Práticas de Engenharia Backend/Frontend**. Todas as novas telas, módulos e manutenções no sistema devem consultar e obedecer rigorosamente a este guia.

---

## 📑 Sumário

1. [Princípios Fundamentais](#-1-princípios-fundamentais)
2. [Tokens de Cor & Acessibilidade (WCAG 2.1)](#-2-tokens-de-cor--acessibilidade)
3. [Diretrizes de Tema Claro e Escuro (Dark Mode)](#-3-diretrizes-de-tema-claro-e-escuro-dark-mode)
4. [Tipografia & Hierarquia Visual](#-4-tipografia--hierarquia-visual)
5. [Notificações, Modais e Diálogos de Sistema](#-5-notificações-modais-e-diálogos-de-sistema)
6. [Componentes e Padrões de Formulários](#-6-componentes-e-padrões-de-formulários)
7. [Padrões de Arquitetura & Backend](#-7-padrões-de-arquitetura--backend)
8. [Banco de Dados e Governança de Deploy](#-8-banco-de-dados-e-governança-de-deploy)

---

## 🎯 1. Princípios Fundamentais

1. **Mobile-First Real**:
   - Interfaces voltadas para voluntários e líderes devem operar de maneira impecável em smartphones, com áreas de toque mínimas de `44x44px`, suporte a PWA, safe-areas de notch (`viewport-fit=cover`) e rolagem suave.
2. **Zero Native Dialogs Policy (Proibição Estrita de Diálogos Nativos)**:
   - ❌ **É TERMINANTEMENTE PROIBIDO** o uso de `alert()`, `confirm()` ou `prompt()` nativos do navegador.
   - ✅ Todas as notificações devem usar o componente oficial de **Toast** (`USToast.show()` / `usShowToast()`).
   - ✅ Todas as confirmações de ações críticas (exclusão, encerramento, reset) devem usar o **Modal de Confirmação** (`window.usConfirm()`).
3. **Alto Contraste e Legibilidade Instantânea**:
   - Nenhum texto ou elemento interativo pode apresentar baixo contraste com seu fundo de suporte. Elementos visuais como ícones de WhatsApp em fundos azuis devem ser obrigatoriamente brancos (`#FFFFFF`).
4. **Performance Sub-Milissegundo**:
   - Consultas de validação de duplicidade, buscas de telefone e listagens de dados devem ser indexadas e projetadas com consultas O(1) ou `LIMIT 1`.

---

## 🎨 2. Tokens de Cor & Acessibilidade

### Paleta Principal (Design Tokens)

| Token | Código Hex | Uso Principal |
| :--- | :--- | :--- |
| `--advec-navy-dark` | `#0f172a` | Fundo principal Dark Mode, Headers escuros, Navbar |
| `--advec-navy-slate` | `#1e293b` | Superfícies de Cards no Dark Mode, Menus dropdown |
| `--advec-blue-primary` | `#2563eb` | Cor de destaque primária, Ações principais, Gradientes |
| `--advec-blue-hover` | `#1d4ed8` | Estados de hover em botões primários |
| `--advec-bg-light` | `#f1f5f9` | Fundo principal da página (Light Mode) |
| `--advec-card-light` | `#ffffff` | Fundo de cartões e inputs |
| `--advec-text-main` | `#0f172a` | Texto padrão no tema claro |

### Estados e Cores Semânticas

| Estado | Cor | Ícone Bootstrap | Uso |
| :--- | :--- | :--- | :--- |
| **Sucesso** | `#198754` | `bi-check-circle-fill` | Confirmações, aprovações, mensagens enviadas |
| **Erro / Perigo** | `#dc3545` | `bi-exclamation-triangle-fill` | Falhas, bloqueios de segurança, exclusões |
| **Atenção** | `#ffc107` / `#d97706` | `bi-exclamation-circle-fill` | Alertas de validação, expiração, avisos |
| **Informação** | `#0dcaf0` / `#2563eb` | `bi-info-circle-fill` | Dicas, regras de disponibilidade, tutoriais |

### Regra Específica de Contraste: Ícones do WhatsApp
Quando o ícone do WhatsApp (`.bi-whatsapp`) estiver contido em elementos de fundo azul (botões `.btn-primary`, `.btn-info`, tabs `.nav-pills .nav-link.active` ou badges), a cor do ícone deve ser **obrigatoriamente branca (`#FFFFFF`)**:
```css
.btn-primary .bi-whatsapp,
.btn-info .bi-whatsapp,
.nav-pills .nav-link.active .bi-whatsapp,
.badge.bg-primary .bi-whatsapp {
  color: #ffffff !important;
}
```

---

## 🌓 3. Diretrizes de Tema Claro e Escuro (Dark Mode)

### A. Regra Obrigatória para Campos de Formulário (Inputs)
Para garantir contraste e máxima legibilidade de digitação no Dark Mode, todos os inputs mantêm fundo claro e cor de fonte **preta**:
```css
[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select,
[data-bs-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]),
[data-bs-theme="dark"] select,
[data-bs-theme="dark"] textarea {
  background-color: #ffffff !important;
  border-color: #cbd5e1 !important;
  color: #000000 !important;
  font-weight: 500;
}

[data-bs-theme="dark"] .form-control:focus,
[data-bs-theme="dark"] .form-select:focus,
[data-bs-theme="dark"] input:focus,
[data-bs-theme="dark"] select:focus,
[data-bs-theme="dark"] textarea:focus {
  background-color: #ffffff !important;
  color: #000000 !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
}

[data-bs-theme="dark"] .form-floating > label {
  color: #475569 !important;
}

[data-bs-theme="dark"] .form-control::placeholder,
[data-bs-theme="dark"] input::placeholder,
[data-bs-theme="dark"] textarea::placeholder {
  color: #64748b !important;
  opacity: 0.85 !important;
}
```

### B. Prevenção de Flicker (Anti-flicker Script)
Em páginas independentes (como Landing Pages de convite ou Login), inclua o script no `<head>` antes do carregamento do DOM:
```html
<script>
  (function() {
    const t = localStorage.getItem('theme') || 'light';
    let resolved = t;
    if (!t || t === 'auto') {
      resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-bs-theme', resolved);
  })();
</script>
```

---

## 🔤 4. Tipografia & Hierarquia Visual

- **Fonte Oficial**: `Plus Jakarta Sans`, sans-serif (Google Fonts).
- **Pesos Utilizados**:
  - `400 (Regular)`: Textos descritivos e parágrafos.
  - `500 (Medium)`: Labels de formulário e valores de inputs.
  - `600 (SemiBold)`: Subtítulos, badges e itens de menu.
  - `700 / 800 (Bold / ExtraBold)`: Títulos de seção, métricas de dashboard e botões principais.

---

## 🔔 5. Notificações, Modais e Diálogos de Sistema

### A. Sistema de Toasts (`USToast`)
O sistema possui a engine de Toast globalmente disponível através de `USToast.show()` ou `usShowToast()`:
```javascript
// Assinatura: usShowToast(tipo, titulo, mensagem)
usShowToast('success', 'Sucesso!', 'Operação realizada com sucesso.');
usShowToast('error', 'Atenção', 'Este número de WhatsApp já possui cadastro.');
usShowToast('warning', 'Alerta', 'Por favor, preencha todos os campos obrigatórios.');
usShowToast('info', 'Informação', 'Um link de acesso foi gerado.');
```

### B. Modal de Confirmação Global (`usConfirm`)
Para substituição de `confirm()` nativo, utilize o helper padrão:
```javascript
usConfirm({
  title: 'Encerrar Link de Convite',
  message: 'Deseja realmente encerrar este link? Ninguém mais poderá utilizá-lo.',
  confirmText: 'Sim, Encerrar',
  cancelText: 'Cancelar',
  type: 'warning', // 'danger', 'warning', 'primary', 'success', 'info'
  icon: 'bi-x-circle-fill text-warning'
}, function() {
  // Callback executado somente quando o usuário clicar em Confirmar
  executarAcaoNoBackend();
});
```

---

## 📝 6. Componentes e Padrões de Formulários

1. **Floating Labels**:
   - Utilize a classe `.form-floating` com `<input class="form-control" placeholder="...">` e `<label>` correspondente.
2. **Seleção Múltipla com Cards/Chips Interativos (Mobile-First)**:
   - Para seleção de categorias, sub-áreas de atuação ou disponibilidade múltipla, evite selects nativos múltiplos. Utilize cards ou chips selecionáveis com checkbox estilizado (`name="campo[]"`), efeito hover, feedback visual com borda/fundo institucional ao marcar e badge contador dinâmico (`X selecionada(s)`).
3. **Máscaras de Entrada (jQuery Mask)**:
   - Telefones/WhatsApp: `$('#telefone').mask('(00) 00000-0000');`
   - CEP: `$('#cep').mask('00000-000');`
   - CPF: `$('#cpf').mask('000.000.000-00');`
4. **Upload de Imagens com Preview Dinâmico**:
   - Inclua sempre o badge de câmera sobreposto com efeito de hover e disparador via input oculto `type="file"`.
   - Se nenhuma imagem estiver selecionada, renderize dinamicamente via `ui-avatars.com`:
   `https://ui-avatars.com/api/?name=Nome+Do+Usuario&background=2563eb&color=fff&size=120`

---

## 🔐 7. Padrões de Arquitetura & Backend

1. **Validações de Duplicidade O(1)**:
   - Em rotinas onde um dado exclusivo (ex: celular/WhatsApp, e-mail) é informado, faça a checagem com método otimizado antes de processar qualquer persistência:
   ```php
   $voluntarioModel = new VoluntarioModel();
   if ($voluntarioModel->existeTelefone($telefone)) {
       return $this->response->setStatusCode(400)->setJSON([
           'status'  => 'error',
           'message' => 'Este número de WhatsApp já possui cadastro no sistema.'
       ]);
   }
   ```
2. **Links e Tokens Públicos**:
   - Nunca exponha IDs numéricos sequenciais em links públicos ou compartilháveis externos. Utilize tokens criptograficamente seguros (32 a 64 caracteres hexadecimais gerados via `bin2hex(random_bytes(16))`).
3. **Controle de Acesso RBAC**:
   - Valide sempre se o usuário logado possui a permissão/ação requerida para o módulo (ex: `send_invite`, `update_past`) através de `PerfilModel::usuarioTemAcao()`.

---

## 🗄️ 8. Banco de Dados e Governança de Deploy

1. **Scripts na Pasta `scripts_deploys/`**:
   - Toda alteração estrutural (tabelas, colunas, índices) deve gerar **obrigatoriamente** dois arquivos na pasta `scripts_deploys/`:
     - `DDMMAAAA_nome_migracao.sql` (UP)
     - `DDMMAAAA_nome_migracao_rollback.sql` (DOWN)
2. **Idempotência Obrigatória**:
   - Scripts de migração e rollback devem ser idempotentes, utilizando `INFORMATION_SCHEMA` para evitar falhas em reexecuções:
   ```sql
   SET @dbname = DATABASE();
   SET @tablename = 'tb_voluntario';
   SET @indexname = 'idx_voluntario_telefone';

   SET @stmt = (SELECT IF(
     (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @indexname) > 0,
     'SELECT 1',
     'ALTER TABLE `tb_voluntario` ADD INDEX `idx_voluntario_telefone` (`telefone_whatsapp`);'
   ));

   PREPARE stmt_idx FROM @stmt;
   EXECUTE stmt_idx;
   DEALLOCATE PREPARE stmt_idx;
   ```
