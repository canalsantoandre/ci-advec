# Documentação do Portal do Voluntário, Segurança OTP & Gestão de Webhooks
**ADVEC Gestão - Sistema de Escalas, Engajamento e Segurança**

---

## 📌 1. Visão Geral do Sistema

Este documento descreve a arquitetura, fluxos operacionais, regras de negócio e estrutura de dados implementadas para o **Portal do Voluntário**, o **Módulo de Gestão de Webhooks**, a **Esteira de Segurança de Senha via OTP (WhatsApp)** e o **Módulo de Disponibilidade de Cultos (N:N)**.

O sistema opera em duas camadas integradas:
1. **Painel Administrativo (Liderança / Gestão)**:
   - Gestão de voluntários e líderes;
   - Cadastro e vinculação de sub-áreas de atuação;
   - Grade de cultos e alocação com validação de limites e conflitos;
   - Gestão de Webhooks para mensageria WhatsApp (Evolution API, Z-API, etc.);
   - Relatórios de desempenho, assiduidade e auditoria de cancelamentos.
2. **Portal Exclusivo do Voluntário (Mobile-First)**:
   - Autenticação e esteira de segurança com validação via OTP no WhatsApp;
   - Agenda mensal de escalas com confirmação/recusa rápida;
   - Configuração de disponibilidade para servir em cultos específicos ou geral (coringa);
   - Gamificação e ranking de fidelidade;
   - Edição de perfil e atualização segura de dados cadastrais e foto.

---

## 📐 2. Fluxos do Sistema (Diagramas Mermaid)

### A. Arquitetura Geral e Perfis de Acesso

```mermaid
graph TD
    subgraph "Painel Administrativo (Gestão / Liderança)"
        A[Líder / Admin] -->|Login Padrão| B(Painel Geral ADVEC)
        B --> C[Gestão de Voluntários /voluntario]
        B --> D[Grade de Escalas /escala/grade]
        B --> E[Relatório de Desempenho /voluntario/desempenho]
        B --> W[Gestão de Webhooks /webhook]
        C -->|Resetar Senha / Disponibilidade| C1[(tb_voluntario & tb_voluntario_culto)]
        D -->|Alocar Voluntário| D1[(tb_escala_voluntario)]
        W -->|Configurar Endpoints & Instâncias| W1[(tb_webhook)]
    end

    subgraph "Serviço de Mensageria & Webhook"
        S[WebhookService] -->|HTTP POST Payload Array| EXT[API Externa WhatsApp]
        EXT -->|Envia OTP / Notificação| ZAP[WhatsApp do Voluntário]
    end

    subgraph "Portal do Voluntário (Mobile-First)"
        V[Membro Voluntário] -->|Telefone + Senha| P(Portal /portal/login)
        P -->|force_pwd_change=1| OTP(Desafio OTP WhatsApp /portal/verificar-otp)
        OTP -->|Valida OTP & Nova Senha| P
        P --> F[Minha Agenda /portal/agenda]
        P --> G[Métricas & Ranking /portal/metricas]
        P --> H[Meu Perfil & Disponibilidade /portal/perfil]
        H -->|Disponibilidade de Cultos| C1
    end
```

---

### B. Fluxo de Autenticação com Esteira de Segurança OTP (WhatsApp)

```mermaid
sequenceDiagram
    autonumber
    actor Vol as Voluntário
    participant App as Portal (/portal/login)
    participant Svc as WebhookService
    participant Zap as API WhatsApp (Webhook)
    participant DB as Banco de Dados (tb_voluntario)

    Vol->>App: Informa Telefone (WhatsApp) e Senha
    App->>DB: Busca voluntário ativo pelo número
    alt Voluntário Não Encontrado ou Inativo
        App-->>Vol: Retorna erro "Cadastro inativo ou não encontrado"
    else Voluntário Encontrado
        App->>App: Valida credenciais (password_verify ou senha padrão inicial)
        alt Senha Inválida
            App-->>Vol: Retorna erro "Senha incorreta"
        else Senha Válida
            alt force_pwd_change = 1 ou primeiro_acesso = 1
                App->>DB: Gera OTP de 6 dígitos (validade 10 min)
                App->>Svc: Solicita envio do OTP por WhatsApp
                Svc->>DB: Busca Webhook ativo em tb_webhook
                Svc->>Zap: HTTP POST Payload Array JSON [{instance, to, message}]
                App->>App: Salva desafio temporário otp_auth_challenge na sessão
                App-->>Vol: Redireciona para /portal/verificar-otp
                Note over Vol,App: Acesso ao dashboard bloqueado até validar OTP
            else Senha Definitiva Válida (force_pwd_change = 0)
                App->>App: Cria Sessão Autenticada (dsh_voluntario)
                App-->>Vol: Redireciona para /portal/agenda
            end
        end
    end
```

---

### C. Fluxo de Confirmação de OTP e Redefinição de Senha

```mermaid
sequenceDiagram
    autonumber
    actor Vol as Voluntário
    participant App as Tela OTP (/portal/confirmar-troca-senha-otp)
    participant DB as Banco de Dados (tb_voluntario)

    Vol->>App: Digita Código OTP + Nova Senha + Confirmação
    App->>DB: Valida se OTP confere e otp_expires_at >= NOW()
    alt OTP Inválido ou Expirado
        App-->>Vol: Exibe erro "Código OTP incorreto ou expirado"
    else OTP Válido
        App->>DB: Grava novo hash bcrypt, force_pwd_change=0, primeiro_acesso=0, limpa OTP
        App->>App: Destrói desafio otp_auth_challenge e cria sessão dsh_voluntario
        App-->>Vol: Redireciona para /portal/agenda com toast de boas-vindas
        Note over Vol,DB: A partir deste momento, a senha antiga (telefone) é estritamente rejeitada
    end
```

---

### D. Fluxo de Disponibilidade de Cultos para Servir (N:N)

```mermaid
flowchart TD
    A[Voluntário acessa Perfil /portal/perfil ou Admin /voluntario/editar] --> B{Opções de Cultos Marcadas?}
    
    B -->|Nenhum culto marcado| C[Regra do Coringa / Disponibilidade Total]
    C --> C1[Voluntário pode ser escalado em QUALQUER culto da semana]
    
    B -->|1 ou mais cultos marcados| D[Disponibilidade Específica]
    D --> D1[Voluntário só aparece disponível para os cultos selecionados]
    
    C1 --> E[Sincronização em tb_voluntario_culto]
    D1 --> E
    E --> F[Grade de Escalas /escala/grade respeita a disponibilidade]
```

---

## 🗂️ 3. Módulos e Funcionalidades Entregues

### 1. Gestão de Webhooks (Painel Administrativo)
- **Cadastro e Listagem (`/webhook`)**: Gestão de URLs de webhook e instâncias padrão de WhatsApp.
- **Teste em Tempo Real (`/webhook/testar`)**: Modal AJAX para envio de mensagem de teste para qualquer número de homologação.
- **`WebhookService`**:
  - Higienização e padronização automática com DDI 55 (ex: `5511999999999`).
  - Formatação obrigatória de payload HTTP POST em **Lista/Array JSON**:
    ```json
    [
      {
        "instance": "instancia_padrao_do_banco",
        "to": "5511999999999",
        "message": "Seu código de verificação é: 123456"
      }
    ]
    ```

### 2. Autenticação & Esteira de Segurança OTP
- **Interceptação de Primeiro Acesso e Reset**: Usuários com `force_pwd_change = 1` são obrigados a validar a posse do WhatsApp via OTP antes de acessar o sistema.
- **Reenvio de OTP com Timer**: Interface com contador regressivo de 60 segundos para evitar abusos de requisições de mensagens.
- **Bloqueio Estrito de Senha Padrão**: Após a definição da nova senha, o sistema rejeita o uso do número de telefone como senha.

### 3. Disponibilidade de Cultos (N:N)
- **Tabela Relacional `tb_voluntario_culto`**: Vincula voluntários a múltiplos `tb_culto_padrao`.
- **Regra do Coringa**: Deixar todos os cultos desmarcados representa disponibilidade total em qualquer dia/horário.

### 4. Portal do Voluntário & Perfil
- **Agenda Interativa (`/portal/agenda`)**: Ações rápidas de confirmação ou recusa com modal obrigatório de justificativa.
- **Métricas & Ranking (`/portal/metricas`)**: Gamificação baseada em assiduidade, presenças e confirmações.
- **Meu Perfil (`/portal/perfil`)**: Upload seguro de fotos em `FCPATH` com preview visual, edição de dados e redes sociais.

---

## 💻 4. Tabela de Rotas e Endpoints

| Método | Rota | Descrição | Filtro / Proteção |
| :--- | :--- | :--- | :--- |
| `GET/POST` | `/portal/login` | Tela e processamento de login do voluntário | Público |
| `GET` | `/portal/logout` | Encerra a sessão do voluntário | Público |
| `GET` | `/portal/verificar-otp` | Tela de desafio de código OTP e nova senha | Desafio OTP |
| `POST` | `/portal/confirmar-troca-senha-otp` | Valida OTP e conclui alteração da senha | Desafio OTP |
| `POST` | `/portal/reenviar-otp` | Dispara novo código OTP por WhatsApp | Desafio OTP |
| `GET` | `/portal/agenda` | Agenda mensal do voluntário | `auth_voluntario` |
| `POST` | `/portal/confirmarEscala` | Confirmação de presença em um culto | `auth_voluntario` |
| `POST` | `/portal/recusarEscala` | Recusa de presença com justificativa | `auth_voluntario` |
| `GET` | `/portal/metricas` | Tela de Métricas, Assiduidade e Ranking | `auth_voluntario` |
| `GET` | `/portal/perfil` | Formulário de perfil e disponibilidade | `auth_voluntario` |
| `POST` | `/portal/salvarPerfil` | Salva dados, foto e disponibilidade de cultos | `auth_voluntario` |
| `POST` | `/portal/alterarSenha` | Altera a senha no painel de perfil | `auth_voluntario` |
| `GET` | `/webhook` | Listagem de Webhooks cadastrados | `auth` (Admin) |
| `GET/POST` | `/webhook/novo` / `editar/(:num)` | Cadastro e edição de webhook | `auth` (Admin) |
| `POST` | `/webhook/salvar` | Salva configurações do webhook | `auth` (Admin) |
| `POST` | `/webhook/testar` | Teste de disparo de webhook via AJAX | `auth` (Admin) |
| `GET` | `/voluntario/desempenho` | Relatório de Desempenho e Cancelamentos | `auth` (Admin) |
| `POST` | `/voluntario/resetSenha` | Redefine senha do voluntário e ativa OTP | `auth` (Admin) |
| `GET` | `/voluntario/verificarTelefone` | Verifica existência global de voluntário por telefone | `auth` (Líder/Admin) |
| `POST` | `/voluntario/vincularRapido` | Vincula voluntário existente à sub-área do departamento | `auth` (Líder/Admin) |
| `GET` | `/voluntario/getSubareasPorDepartamento/(:num)` | Retorna sub-áreas ativas do departamento | `auth` (Líder/Admin) |
| `GET` | `/dashboard` | Painel inicial com suporte à view padrão de boas-vindas | `auth` |

---

### 5. Controle de Acesso Departamental para Gestores & Líderes
- **Tabela Relacional `tb_departamento_gestor`**: Associa os usuários do sistema (`tb_sys_usuario`) aos departamentos que eles gerenciam.
- **Filtro Estrito por Usuário Logado**:
  - Usuários líderes/gestores visualizam apenas voluntários e escalas dos departamentos atribuídos ao seu usuário.
  - No formulário de voluntários, sub-áreas de departamentos não gerenciados são exibidas desabilitadas (com cadeado e tooltip explicativo), preservando integridade das demais áreas do voluntário.
- **Gestão de Líderes no Cadastro de Departamento**:
  - No formulário de departamento (`/departamento/novo` e `/departamento/editar`), é possível selecionar diretamente quais usuários do sistema gerenciam a área.
- **Verificação Global de Telefone e Vinculação Rápida**:
  - Ao digitar o telefone no cadastro de novo voluntário, o sistema detecta se ele já serve em outro departamento e abre um modal rápido para adicioná-lo à equipe sem duplicar o cadastro principal.

### 6. View Padrão de Boas-Vindas (`_main/boas_vindas`)
- Tela inicial moderna e clean para recepção de todos os perfis de usuários.
- Saudação dinâmica (*Bom dia / Boa tarde / Boa noite*), cards de atalhos rápidos com base nas permissões do perfil, orientações gerais e suporte ao tema Dark/Light.

---

## 🗄️ 5. Scripts de Banco de Dados (`scripts_deploys/`)

| Script de Deploy | Script de Rollback | Descrição |
| :--- | :--- | :--- |
| [`29092026_portal_voluntario.sql`](file:///Users/elpidio.junior/Documents/_projetos/advec/ci-advec/scripts_deploys/29092026_portal_voluntario.sql) | `29092026_portal_voluntario_rollback.sql` | Estrutura base do portal e confirmação de escalas |
| [`29092026_relatorio_desempenho_voluntarios.sql`](file:///Users/elpidio.junior/Documents/_projetos/advec/ci-advec/scripts_deploys/29092026_relatorio_desempenho_voluntarios.sql) | `29092026_relatorio_desempenho_voluntarios_rollback.sql` | Índices de performance para relatórios |
| [`30092026_disponibilidade_voluntarios_cultos.sql`](file:///Users/elpidio.junior/Documents/_projetos/advec/ci-advec/scripts_deploys/30092026_disponibilidade_voluntarios_cultos.sql) | `30092026_disponibilidade_voluntarios_cultos_rollback.sql` | Tabela relacional `tb_voluntario_culto` (N:N) |
| [`30092026_modulo_webhooks_e_otp.sql`](file:///Users/elpidio.junior/Documents/_projetos/advec/ci-advec/scripts_deploys/30092026_modulo_webhooks_e_otp.sql) | `30092026_modulo_webhooks_e_otp_rollback.sql` | Tabela `tb_webhook`, colunas de OTP e ativação de troca |
| [`01102026_controle_acesso_departamento_gestor.sql`](file:///Users/elpidio.junior/Documents/_projetos/advec/ci-advec/scripts_deploys/01102026_controle_acesso_departamento_gestor.sql) | `01102026_controle_acesso_departamento_gestor_rollback.sql` | Tabela `tb_departamento_gestor` para controle de acesso departamental |

