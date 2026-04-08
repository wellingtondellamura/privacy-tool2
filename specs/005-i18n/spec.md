# Spec 005 — Internacionalização (i18n)

**Versão:** 1.0
**Idiomas:** `pt_BR` (português brasileiro), `en` (inglês)
**Escopo:** Interface do usuário (Vue + Inertia) e backend de mensagens. Filament (admin) **não** será traduzido.
**Premissa:** Banco de dados zerado (sem retrocompatibilidade de dados existentes).

---

# 1️⃣ OBJETIVO

Adicionar suporte a dois idiomas na interface do usuário, permitindo que:

- O usuário autenticado escolha seu idioma preferido (`pt_BR` ou `en`)
- Visitantes não autenticados recebam o idioma baseado no browser (Accept-Language)
- Todos os textos da interface (frontend e backend) sejam traduzidos dinamicamente
- O questionário (armazenado no banco) exiba perguntas, categorias e seções no idioma ativo
- Snapshots armazenem chaves neutras (locale-independent)

---

# 2️⃣ DEPENDÊNCIAS

```bash
# Frontend
npm install vue-i18n@10

# Backend
composer require spatie/laravel-translatable
```

---

# 3️⃣ MODELO DE DADOS

## 3.1 Nova coluna: `users.locale`

```sql
ALTER TABLE users ADD COLUMN locale VARCHAR(10) DEFAULT 'pt_BR' AFTER is_admin;
```

Migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('locale', 10)->default('pt_BR')->after('is_admin');
});
```

Atualizar `User` model `$fillable`:

```php
protected $fillable = ['name', 'email', 'password', 'is_admin', 'locale'];
```

---

## 3.2 Campos translatable no questionário

Alterar a migration original `2026_02_24_000004_create_questionnaire_tables.php`:

| Tabela | Campo | Tipo anterior | Novo tipo |
|--------|-------|---------------|-----------|
| `sections` | `name` | `string` | `json` |
| `categories` | `name` | `string` | `json` |
| `questions` | `text` | `text` | `json` |

```php
Schema::create('sections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('questionnaire_version_id')->constrained()->cascadeOnDelete();
    $table->json('name');
    $table->integer('order');
    $table->timestamps();
});

Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')->constrained()->cascadeOnDelete();
    $table->json('name');
    $table->integer('order');
    $table->timestamps();
});

Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->json('text');
    $table->integer('order');
    $table->timestamps();
});
```

---

# 4️⃣ MODELOS — ALTERAÇÕES

## 4.1 `Section` — adicionar `HasTranslations`

```php
use Spatie\Translatable\HasTranslations;

class Section extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['questionnaire_version_id', 'name', 'order', 'response_profile'];
    // restante inalterado
}
```

## 4.2 `Category` — adicionar `HasTranslations`

```php
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['section_id', 'name', 'order'];
    // restante inalterado
}
```

## 4.3 `Question` — adicionar `HasTranslations`

```php
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['text'];
    protected $fillable = ['category_id', 'text', 'order'];
    // restante inalterado
}
```

## 4.4 `User` — adicionar `locale` ao fillable

```php
protected $fillable = ['name', 'email', 'password', 'is_admin', 'locale'];
```

---

# 5️⃣ MIDDLEWARE — `SetLocale`

Criar `app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            app()->setLocale($user->locale);
        } else {
            $preferred = $request->getPreferredLanguage(['pt_BR', 'en']);
            app()->setLocale($preferred ?? config('app.fallback_locale'));
        }

        return $next($request);
    }
}
```

Registrar no `bootstrap/app.php` (ou `app/Http/Kernel.php`) no grupo `web`, **antes** de `HandleInertiaRequests`.

---

# 6️⃣ INERTIA — COMPARTILHAMENTO DE LOCALE

Alterar `HandleInertiaRequests.php`:

```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
        'locale' => app()->getLocale(),
        'flash' => [
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
            'warning' => $request->session()->get('warning'),
            'info' => $request->session()->get('info'),
        ],
        'app_url' => config('app.url'),
    ];
}
```

---

# 7️⃣ FRONTEND — VUE-I18N

## 7.1 Plugin `i18n.js`

Criar `resources/js/plugins/i18n.js`:

```js
import { createI18n } from 'vue-i18n';
import en from '../locales/en.json';
import ptBR from '../locales/pt_BR.json';

export function createI18nInstance(locale = 'pt_BR') {
    return createI18n({
        legacy: false,
        locale,
        fallbackLocale: 'pt_BR',
        messages: { en, pt_BR: ptBR },
    });
}
```

## 7.2 Integração no `app.js`

```js
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18nInstance } from './plugins/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const locale = props.initialPage.props.locale ?? 'pt_BR';
        const i18n = createI18nInstance(locale);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
```

## 7.3 Composable `useFormatDate`

Criar `resources/js/composables/useFormatDate.js`:

```js
import { useI18n } from 'vue-i18n';

export function useFormatDate() {
    const { locale } = useI18n();

    const formatDate = (date) => {
        if (!date) return '';
        const loc = locale.value === 'pt_BR' ? 'pt-BR' : 'en-US';
        return new Date(date).toLocaleDateString(loc);
    };

    return { formatDate };
}
```

---

# 8️⃣ ESTRUTURA DE ARQUIVOS DE TRADUÇÃO

## 8.1 Frontend (`resources/js/locales/`)

Convenção de chaves: **namespace por feature** com dot notation.

### `resources/js/locales/pt_BR.json`

```json
{
  "common.save": "Salvar",
  "common.cancel": "Cancelar",
  "common.delete": "Excluir",
  "common.saving": "Salvando...",
  "common.confirm": "Confirmar",
  "common.ok": "OK",
  "common.close": "Fechar",
  "common.back": "Voltar",
  "common.view": "Ver",
  "common.open": "Abrir",
  "common.warning": "Aviso",
  "common.success": "Sucesso",
  "common.error": "Erro",
  "common.info": "Informação",
  "common.processing": "Processando...",
  "common.saved": "Salvo.",
  "common.yes": "Sim",
  "common.no": "Não",

  "nav.dashboard": "Dashboard",
  "nav.mitra_method": "Método Mitra",
  "nav.manual": "Manual de Uso",
  "nav.public_directory": "Diretório Público",
  "nav.profile": "Perfil",
  "nav.logout": "Sair",
  "nav.login": "Entrar",
  "nav.register": "Cadastre-se",

  "auth.login_title": "Entrar",
  "auth.register_title": "Cadastrar",
  "auth.forgot_password_title": "Esqueceu a Senha",
  "auth.reset_password_title": "Redefinir Senha",
  "auth.confirm_password_title": "Confirmar Senha",
  "auth.verify_email_title": "Verificação de E-mail",
  "auth.email": "E-mail",
  "auth.email_professional": "E-mail profissional",
  "auth.email_placeholder": "voce@exemplo.com",
  "auth.password": "Senha",
  "auth.password_placeholder": "••••••••",
  "auth.password_current": "Senha Atual",
  "auth.password_new": "Nova Senha",
  "auth.password_confirm": "Confirmar Senha",
  "auth.remember_me": "Lembrar-me",
  "auth.forgot_password_link": "Esqueceu a senha?",
  "auth.forgot_password_description": "Esqueceu sua senha? Sem problemas. Apenas informe seu endereço de email e nós enviaremos um link de redefinição de senha para que você possa escolher uma nova.",
  "auth.send_reset_link": "Enviar Link de Redefinição de Senha",
  "auth.reset_password_button": "Redefinir Senha",
  "auth.confirm_password_description": "Esta é uma área segura do aplicativo. Por favor, confirme sua senha antes de continuar.",
  "auth.verify_email_description": "Obrigado por se registrar! Antes de começar, você poderia verificar seu endereço de email acessando o link que acabamos de enviar? Se você não recebeu o email, ficaremos felizes em enviar outro.",
  "auth.verify_email_sent": "Um novo link de verificação foi enviado para o endereço de email fornecido durante o registro.",
  "auth.resend_verification": "Reenviar Email de Verificação",
  "auth.no_account": "Não tem uma conta?",
  "auth.has_account": "Já possui conta?",
  "auth.back_home": "Voltar ao Início",
  "auth.logout_link": "Sair da conta",
  "auth.name": "Nome completo",
  "auth.name_placeholder": "João Silva",

  "dashboard.title": "Dashboard",
  "dashboard.logged_in": "Você está logado!",

  "workspace.title": "Meu Workspace",
  "workspace.new_project": "Novo Projeto",
  "workspace.create_project_title": "Criar Novo Projeto",
  "workspace.create_project_description": "Um projeto pode ser um software, aplicativo ou site a ser inspecionado.",
  "workspace.project_name": "Nome do Projeto",
  "workspace.project_name_placeholder": "Ex: Sistema de Gestão Interna",
  "workspace.description": "Descrição",
  "workspace.description_placeholder": "Breve descrição sobre o contexto e finalidade do projeto...",
  "workspace.url_label": "URL do Produto (Site)",
  "workspace.create_button": "Criar Projeto",
  "workspace.pending_invitations": "Convites Pendentes",
  "workspace.invitation_project": "Projeto: {name}",
  "workspace.invitation_role": "Papel de acesso:",
  "workspace.role_evaluator": "Avaliador",
  "workspace.role_observer": "Observador",
  "workspace.role_owner": "Proprietário",
  "workspace.decline": "Recusar",
  "workspace.accept_invitation": "Aceitar Convite",
  "workspace.no_projects": "Nenhum projeto",
  "workspace.no_projects_description": "Comece criando um novo projeto para gerenciar inspeções.",
  "workspace.no_description": "Sem descrição.",
  "workspace.inspections_count": "{count} inspeções",
  "workspace.created_by": "Criado por {name}",

  "project.no_description": "Nenhuma descrição fornecida.",
  "project.edit_name": "Nome do Projeto",
  "project.edit_description": "Descrição",
  "project.edit_description_placeholder": "Breve descrição do projeto...",
  "project.edit_url": "Website (URL)",
  "project.edit_url_placeholder": "https://...",
  "project.save_changes": "Salvar Alterações",
  "project.export_json": "Exportar JSON",
  "project.consolidated_result": "Resultado Consolidado",
  "project.compare_rounds": "Comparar Rodadas ({count})",
  "project.new_round": "Nova Rodada",
  "project.rounds_title": "Rodadas de Avaliação",
  "project.no_rounds": "Não há rodadas de avaliação criadas para este projeto ainda.",
  "project.create_first_round": "Criar Primeira Rodada",
  "project.created_at": "Criada em {date}",
  "project.closed_at": "Fechada em {date}",
  "project.score_display": "Score: {score}%",
  "project.inspections_title": "Inspeções",
  "project.no_inspections": "Não há inspeções criadas para este projeto ainda.",
  "project.no_inspections_description": "Crie ou abra uma Rodada de Avaliação para iniciar uma nova inspeção.",
  "project.inspection_label": "Inspeção #{id}",
  "project.responsible_label": "Responsável: {name}",
  "project.my_result": "Meu Resultado",
  "project.members_title": "Membros do Projeto",
  "project.invite": "Convidar",
  "project.invite_email_label": "E-mail",
  "project.invite_email_placeholder": "email@exemplo.com",
  "project.invite_role_label": "Papel",
  "project.send_invitation": "Enviar Convite",
  "project.pending_invitations": "Convites Pendentes",
  "project.invitation_sent_at": "Enviado {date}",
  "project.user_has_account": "Usuário já possui conta",
  "project.resend": "Reenviar",
  "project.expired": "Expirado",
  "project.pending": "Pendente",
  "project.product_url": "URL do Produto",
  "project.resend_modal_title": "Reenviar Convite",
  "project.resend_modal_message": "Deseja reenviar este convite? O prazo será estendido por mais 7 dias.",
  "project.resend_confirm": "Sim, Reenviar",
  "project.new_round_modal_title": "Nova Rodada de Avaliação",
  "project.new_round_modal_message": "Dê um nome para identificar esta nova rodada de inspeções.",
  "project.new_round_create": "Criar Rodada",
  "project.round_name_label": "Nome da Rodada",
  "project.round_name_placeholder": "Ex: Rodada Março 2024",

  "publication.modal_title": "Publicar no Diretório",
  "publication.update_message": "Atualize a visibilidade da publicação ou remova-a do diretório público.",
  "publication.create_message": "Esta rodada consolidada será publicada no diretório público. Escolha o nível de visibilidade.",
  "publication.create_inspection_message": "Esta inspeção será publicada no diretório público. Escolha o nível de visibilidade.",
  "publication.update_button": "Atualizar",
  "publication.publish_button": "Publicar",
  "publication.visibility_label": "Visibilidade Pública",
  "publication.visibility_engagement": "Engajamento (Visibilidade)",
  "publication.private_label": "Privado",
  "publication.private_description": "Visível apenas internamente no projeto.",
  "publication.private_members_description": "Visível apenas para membros do projeto.",
  "publication.score_label": "Apenas Score (Consolidado)",
  "publication.score_short_label": "Apenas Score",
  "publication.score_description": "Exibe a pontuação média e medalha no diretório. Detalhes técnicos ocultos.",
  "publication.score_short_description": "Exibe a pontuação e medalha, mas mantém detalhes ocultos.",
  "publication.full_label": "Relatório Consolidado Completo",
  "publication.full_short_label": "Relatório Completo",
  "publication.full_description": "Torna o diagnóstico e as médias por seção visíveis publicamente.",
  "publication.full_short_description": "Torna o relatório consolidado visível para qualquer pessoa com o link.",
  "publication.remove_link": "Remover do Diretório Público",

  "inspection.sections": "Seções",
  "inspection.back_to_round": "Voltar à Rodada",
  "inspection.actions": "Ações",
  "inspection.draft_message": "A inspeção está em rascunho. Ative para permitir respostas.",
  "inspection.draft_awaiting": "Aguardando o responsável",
  "inspection.activate_button": "Ativar Inspeção",
  "inspection.global_progress": "Progresso Global:",
  "inspection.close_button": "Finalizar Inspeção",
  "inspection.only_responsible_prefix": "Apenas o responsável",
  "inspection.only_responsible_suffix": "pode finalizar esta inspeção.",
  "inspection.closed_title": "Inspeção Concluída",
  "inspection.view_results": "Ver Resultados",
  "inspection.details": "Detalhes",
  "inspection.score_label": "Score Obtido:",
  "inspection.status_label": "Status:",
  "inspection.responsible_label": "Responsável:",
  "inspection.responsible_system": "Sistema",
  "inspection.version_label": "Versão:",
  "inspection.start_label": "Início:",
  "inspection.end_label": "Conclusão:",
  "inspection.instructions": "Responda às questões abaixo de acordo com a realidade atual do produto. As respostas são salvas automaticamente.",
  "inspection.draft_alert": "Inspeção não iniciada. As respostas estão desativadas.",
  "inspection.closed_alert": "Inspeção fechada. As respostas não podem ser editadas.",
  "inspection.close_modal_title": "Finalizar Inspeção",
  "inspection.close_modal_message": "Tem certeza? A inspeção será consolidada e nenhuma nova resposta será permitida. Esta ação é irreversível.",
  "inspection.close_confirm": "Sim, Finalizar",
  "inspection.new_inspection": "Nova Inspeção",

  "inspection.status.draft": "Rascunho",
  "inspection.status.active": "Ativa",
  "inspection.status.closed": "Concluída",
  "inspection.status.draft_detail": "Rascunho (Não Iniciada)",
  "inspection.status.active_detail": "Ativa (Em progresso)",

  "question.saving": "Salvando...",
  "question.specify_label": "Por favor, especifique",
  "question.specify_placeholder": "Descreva a situação...",
  "question.save_error": "Erro ao salvar a resposta.",

  "round.new_inspection": "Nova Inspeção",
  "round.close_round": "Fechar Rodada",
  "round.adjust_publication": "Ajustar Publicação",
  "round.publish_directory": "Publicar no Diretório",
  "round.consolidated_result": "Resultado Consolidado",
  "round.inspections_title": "Inspeções da Rodada",
  "round.no_inspections": "Não há inspeções nesta rodada ainda.",
  "round.create_first": "Criar Primeira Inspeção",
  "round.inspection_label": "Inspeção #{id}",
  "round.responsible_status": "Responsável: {name} • Status: {status}",
  "round.team_result": "Resultado Equipe",
  "round.diagnosis_title": "Diagnóstico Técnico e Análise Geral",
  "round.info_title": "Informações da Rodada",
  "round.project_label": "Projeto",
  "round.created_at": "Data de Criação",
  "round.closed_at": "Data de Fechamento",
  "round.inspections_label": "Inspeções",
  "round.inspections_linked": "{count} vinculadas",
  "round.badge_title": "Selo Embeddable",
  "round.badge_private_warning": "A visibilidade da rodada está definida como **Privada**. O selo embeddable exige que a rodada seja pública.",
  "round.badge_description": "Gere um selo oficial para exibir o resultado consolidado desta rodada em seu site ou sistema externo.",
  "round.generate_badge": "Gerar Selo Oficial",
  "round.badge_preview": "Preview do Selo",
  "round.badge_style_label": "Escolha o Estilo",
  "round.badge_style_default": "Padrão (Card)",
  "round.badge_style_compact": "Compacto (Linha)",
  "round.badge_style_minimal": "Minimalista (Texto)",
  "round.badge_code_label": "Código de Incorporação",
  "round.reactivate": "Ativar Novamente",
  "round.revoke_badge": "Revogar Selo",
  "round.status_closed": "Concluída",
  "round.status_active": "Ativa",
  "round.status_draft": "Rascunho",
  "round.published_badge": "Publicado ({visibility})",
  "round.revoke_modal_title": "Revogar Selo",
  "round.revoke_modal_message": "Tem certeza que deseja revogar este selo? Ele deixará de ser exibido em sites externos imediatamente.",
  "round.revoke_confirm": "Revogar",

  "review.page_title": "Revisar e Fechar - {name}",
  "review.header_title": "Revisar e Fechar Rodada",
  "review.header_description": "Confira os resultados das inspeções e informe o diagnóstico final.",
  "review.summary_title": "Sumário de Inspeções",
  "review.inspection_label": "Inspeção #{id}",
  "review.score_pts": "{score} pts",
  "review.warning_unclosed": "Atenção: Algumas inspeções ainda não foram concluídas e serão ignoradas no cálculo consolidado.",
  "review.diagnosis_title": "Diagnóstico e Análise Técnica",
  "review.diagnosis_label": "Informe o comentário/diagnóstico da rodada:",
  "review.diagnosis_placeholder": "Escreva aqui as conclusões técnicas, pontos de melhoria e análise geral desta rodada de avaliação...",
  "review.diagnosis_help": "Este texto será exibido no topo do relatório consolidado e no snapshot público (se habilitado).",
  "review.preview_title": "Preview Consolidado",
  "review.show_details": "Ver Detalhes",
  "review.hide_details": "Ocultar",
  "review.no_data": "Sem Dados",
  "review.avg_score": "Pontuação Média",
  "review.actions_title": "Ações de Encerramento",
  "review.publish_immediately": "Publicar imediatamente",
  "review.publish_description": "Tornar os resultados visíveis no diretório público assim que fechar.",
  "review.visibility_score": "Apenas Score e Medalha",
  "review.visibility_full": "Relatório Completo (com Diagnóstico)",
  "review.close_button": "Sim, Fechar Rodada Agora",
  "review.cancel_link": "Cancelar e Voltar",

  "results.individual_title": "Resultados Individuais",
  "results.individual_subtitle": "Inspeção #{id} — {round}",
  "results.team_title": "Resultado Consolidado da Equipe",
  "results.team_subtitle": "Inspeção #{id} — {round}",
  "results.round_title": "Resultado Consolidado da Rodada",
  "results.round_subtitle": "{round} — {project}",
  "results.back_to_round": "Voltar à Rodada",
  "results.view_team": "Ver Resultado Consolidado (Equipe)",
  "results.view_individual": "Ver Meu Resultado (Individual)",
  "results.global_score": "Pontuação Global",
  "results.global_avg_score": "Pontuação Média Global",
  "results.section_performance": "Desempenho por Seção",
  "results.section_avg_performance": "Desempenho Médio por Seção",
  "results.analysis_title": "Análise Técnica e Diagnóstico",
  "results.divergence_high": "Divergência Alta",
  "results.divergence_moderate": "Divergência Moderada",
  "results.divergence_consensus": "Consenso",
  "results.score_label": "Score: {score}",
  "results.variance_label": "Var: {variance}",

  "results.medal.gold": "Ouro",
  "results.medal.silver": "Prata",
  "results.medal.bronze": "Bronze",
  "results.medal.incipient": "Incipiente",

  "results.footer.project_name": "Nome do Projeto",
  "results.footer.website": "Website URL",
  "results.footer.inspection_date": "Data da Inspeção",
  "results.footer.round_date": "Data da Rodada",
  "results.footer.sequential_id": "ID Sequencial",
  "results.footer.round_id": "ID da Rodada",
  "results.footer.references": "Referências Oficiais",
  "results.footer.trmodel_link": "Metodologia TRModel (USP)",
  "results.footer.lgpd_link": "Portal Oficial: LGPD (Governo Federal)",
  "results.footer.description": "Uma iniciativa para ampliar a aderência a boas práticas técnicas de dados pessoais e empoderar a transparência.",
  "results.footer.copyright": "Privacy Tool.",
  "results.footer.developed_with": "Desenvolvido com o rigor técnico TR-Model v1.0",

  "comparison.breadcrumb_evolution": "Evolução e Comparação",
  "comparison.title": "Evolução / Comparação",
  "comparison.reference": "Referência: {label}",
  "comparison.comparison": "Comparação: {label}",
  "comparison.global_variation": "Variação Global",
  "comparison.section_analysis": "Análise de Variâncias por Seção",
  "comparison.base_label": "Base:",
  "comparison.current_label": "Atual:",

  "directory.badge": "Transparência Coletiva",
  "directory.title": "Diretório Público",
  "directory.description": "Explore ferramentas que priorizaram a transparência e a conformidade de dados pessoais.",
  "directory.all_medals": "Todas as Medalhas",
  "directory.all_years": "Todos os Anos",
  "directory.sort_best": "Melhor Score",
  "directory.sort_worst": "Pior Score",
  "directory.sort_recent": "Mais Recentes",
  "directory.published_at": "Publicado em {date}",
  "directory.global_score": "Score Global",
  "directory.view_details": "Ver detalhes do relatório",
  "directory.no_results": "Nenhuma ferramenta encontrada",
  "directory.no_results_description": "Tente ajustar seus filtros para encontrar o que procura.",
  "directory.clear_filters": "Limpar todos os filtros",

  "directory.summary_badge": "Relatório de Privacidade",
  "directory.summary_score": "Score Global",
  "directory.summary_classification": "Classificação Final",
  "directory.summary_diagnosis": "Diagnóstico Geral",
  "directory.summary_dimensions": "Resumo por Dimensão",
  "directory.summary_score_label": "Score",
  "directory.summary_cta_title": "Deseja o relatório detalhado?",
  "directory.summary_cta_description": "Esta visualização apresenta apenas os índices gerais e medalhas por seção conforme configurado pelo projeto.",
  "directory.summary_back": "Voltar ao Diretório",

  "directory.full_consolidated_score": "Score Consolidado",
  "directory.full_classification": "Classificação",
  "directory.full_achieved_through": "Alcançado através de {count} auditorias",
  "directory.full_no_medal": "Sem medalha atribuída",
  "directory.full_conclusion": "Conclusão do Relatório",
  "directory.full_dimension_detail": "Detalhamento por Dimensão",
  "directory.full_back": "Voltar para o Diretório",
  "directory.full_inspection_date": "Data da Inspeção: {date}",

  "profile.title": "Perfil",
  "profile.info_title": "Informações do Perfil",
  "profile.info_description": "Atualize as informações de perfil e o endereço de e-mail da sua conta.",
  "profile.name": "Nome",
  "profile.email": "E-mail",
  "profile.email_unverified": "Seu endereço de e-mail não foi verificado.",
  "profile.resend_verification": "Clique aqui para reenviar o e-mail de verificação.",
  "profile.verification_sent": "Um novo link de verificação foi enviado para o seu endereço de e-mail.",
  "profile.update_password_title": "Atualizar Senha",
  "profile.update_password_description": "Certifique-se de que sua conta esteja usando uma senha longa e aleatória para se manter segura.",
  "profile.delete_title": "Excluir Conta",
  "profile.delete_warning": "Depois que sua conta for excluída, todos os seus recursos e dados serão permanentemente excluídos. Antes de excluir sua conta, faça o download de quaisquer dados ou informações que deseja reter.",
  "profile.delete_button": "Excluir Conta",
  "profile.delete_confirm_title": "Tem certeza que deseja excluir sua conta?",
  "profile.delete_confirm_description": "Depois que sua conta for excluída, todos os seus recursos e dados serão permanentemente excluídos. Digite sua senha para confirmar que deseja excluir permanentemente sua conta.",
  "profile.export_title": "Exportar Todos os Dados",
  "profile.export_description": "Baixe todas as informações de todos os projetos em que você participa em um único arquivo ZIP contendo arquivos JSON individuais.",
  "profile.export_button": "Baixar Projetos (ZIP)",

  "colors.primary": "Primária",
  "colors.primary_dark": "Primária Escura",
  "colors.secondary_green": "Secundária (Verde)",
  "colors.secondary_yellow": "Secundária (Amarelo)",
  "colors.secondary_red": "Secundária (Vermelho)",
  "colors.tertiary_purple": "Terciária (Roxo)",
  "colors.tertiary_pink": "Terciária (Rosa)",
  "colors.neutral": "Neutral",

  "footer.description": "Uma iniciativa para ampliar a aderência a boas práticas técnicas de dados pessoais e empoderar a transparência.",
  "footer.references": "Referências Oficiais",
  "footer.trmodel_link": "Metodologia TRModel (USP)",
  "footer.lgpd_link": "Portal Oficial: LGPD (Governo Federal)",
  "footer.copyright": "Privacy Tool. Todos os direitos reservados."
}
```

> **Nota**: O arquivo `en.json` segue a mesma estrutura com todas as chaves traduzidas para inglês. As páginas `Welcome.vue`, `Manual.vue` e `MitraMethod.vue` contêm blocos extensos de texto institucional/acadêmico que devem ter chaves próprias com prefixo `welcome.*`, `manual.*` e `mitra.*` respectivamente. Essas chaves não foram listadas individualmente aqui por serem textos longos e descritivos — devem ser extraídas no momento da implementação de cada componente.

---

## 8.2 Backend (`lang/`)

### `lang/pt_BR/messages.php` — Flash messages dos controllers

```php
<?php

return [
    // ProjectController
    'project_created' => 'Projeto criado com sucesso.',
    'project_updated' => 'Projeto atualizado com sucesso.',
    'project_deleted' => 'Projeto removido com sucesso.',
    'cannot_change_owner_role' => 'Não é possível alterar o papel do dono do projeto.',
    'member_role_updated' => 'Papel do membro atualizado com sucesso.',

    // InspectionController
    'round_required' => 'É necessário selecionar uma rodada de avaliação para iniciar uma inspeção.',
    'cannot_add_to_closed_round' => 'Não é possível adicionar inspeções a uma rodada que já está fechada.',
    'inspection_activated' => 'Inspeção iniciada e mudou para status Ativa.',
    'only_responsible_can_change' => 'Apenas o responsável pela inspeção pode mudar seu status.',
    'inspection_closed' => 'Inspeção finalizada e instantâneos gerados.',

    // InvitationController
    'invitation_sent' => 'Convite enviado com sucesso para :email.',
    'invitation_accepted' => 'Convite aceito com sucesso.',
    'invitation_declined' => 'Convite recusado/cancelado com sucesso.',
    'invitation_resent' => 'Convite reenviado com sucesso para :email.',

    // ResultController
    'no_results_for_user' => 'Nenhum resultado encontrado para este usuário.',
    'inspection_must_be_closed' => 'A inspeção deve estar concluída para ver resultados da equipe.',
    'consolidated_not_found' => 'Resultados consolidados não encontrados.',
    'round_consolidated_not_found' => 'Resultado consolidado da rodada não encontrado.',
    'inspections_same_project' => 'As inspeções devem pertencer ao mesmo projeto.',
    'both_inspections_closed' => 'Ambas as inspeções devem estar concluídas.',
    'rounds_same_project' => 'As rodadas devem pertencer ao mesmo projeto.',
    'both_rounds_closed' => 'Ambas as rodadas devem estar concluídas.',

    // EvaluationRoundController
    'round_created' => 'Rodada de avaliação criada com sucesso.',
    'round_already_closed' => 'Esta rodada já foi fechada.',
    'round_closed' => 'Rodada fechada com sucesso.',

    // EvaluationRoundPublicationController
    'round_published' => 'Rodada publicada com sucesso no diretório.',
    'publication_not_found' => 'Publicação não encontrada.',
    'publication_updated' => 'Visibilidade da publicação atualizada.',
    'publication_removed' => 'Publicação removida do diretório.',

    // RoundBadgeController
    'badge_generated' => 'Selo gerado com sucesso.',
    'badge_revoked' => 'Selo revogado.',
    'badge_style_updated' => 'Estilo do selo atualizado.',

    // DataExportController
    'no_projects_to_export' => 'Você não possui projetos para exportar.',
    'zip_creation_failed' => 'Não foi possível criar o arquivo ZIP.',
];
```

### `lang/en/messages.php`

```php
<?php

return [
    'project_created' => 'Project created successfully.',
    'project_updated' => 'Project updated successfully.',
    'project_deleted' => 'Project removed successfully.',
    'cannot_change_owner_role' => 'Cannot change the owner\'s role.',
    'member_role_updated' => 'Member role updated successfully.',

    'round_required' => 'You must select an evaluation round to start an inspection.',
    'cannot_add_to_closed_round' => 'Cannot add inspections to a closed round.',
    'inspection_activated' => 'Inspection started and moved to Active status.',
    'only_responsible_can_change' => 'Only the responsible person can change the inspection status.',
    'inspection_closed' => 'Inspection finalized and snapshots generated.',

    'invitation_sent' => 'Invitation sent successfully to :email.',
    'invitation_accepted' => 'Invitation accepted successfully.',
    'invitation_declined' => 'Invitation declined/cancelled successfully.',
    'invitation_resent' => 'Invitation resent successfully to :email.',

    'no_results_for_user' => 'No results found for this user.',
    'inspection_must_be_closed' => 'The inspection must be closed to view team results.',
    'consolidated_not_found' => 'Consolidated results not found.',
    'round_consolidated_not_found' => 'Round consolidated result not found.',
    'inspections_same_project' => 'Inspections must belong to the same project.',
    'both_inspections_closed' => 'Both inspections must be closed.',
    'rounds_same_project' => 'Rounds must belong to the same project.',
    'both_rounds_closed' => 'Both rounds must be closed.',

    'round_created' => 'Evaluation round created successfully.',
    'round_already_closed' => 'This round has already been closed.',
    'round_closed' => 'Round closed successfully.',

    'round_published' => 'Round published to directory successfully.',
    'publication_not_found' => 'Publication not found.',
    'publication_updated' => 'Publication visibility updated.',
    'publication_removed' => 'Publication removed from directory.',

    'badge_generated' => 'Badge generated successfully.',
    'badge_revoked' => 'Badge revoked.',
    'badge_style_updated' => 'Badge style updated.',

    'no_projects_to_export' => 'You have no projects to export.',
    'zip_creation_failed' => 'Failed to create the ZIP file.',
];
```

### `lang/pt_BR/labels.php` — Labels de domínio

```php
<?php

return [
    'medals' => [
        'gold' => 'Ouro',
        'silver' => 'Prata',
        'bronze' => 'Bronze',
        'incipient' => 'Incipiente',
    ],

    'response' => [
        'information_quality' => [
            'high' => 'Suficiente',
            'medium' => 'Insuficiente',
            'low' => 'Inexistente',
            'other' => 'Outro',
        ],
        'presentation_format' => [
            'high' => 'Apropriado',
            'medium' => 'Inapropriado',
            'low' => 'Necessita melhorias',
            'other' => 'Outro',
        ],
    ],

    'divergence' => [
        'low' => 'baixa',
        'medium' => 'média',
        'high' => 'alta',
    ],

    'visibility' => [
        'private' => 'Privado',
        'score_public' => 'Apenas Score',
        'full_public' => 'Relatório Completo',
    ],

    'unknown' => 'Desconhecido',
];
```

### `lang/en/labels.php`

```php
<?php

return [
    'medals' => [
        'gold' => 'Gold',
        'silver' => 'Silver',
        'bronze' => 'Bronze',
        'incipient' => 'Incipient',
    ],

    'response' => [
        'information_quality' => [
            'high' => 'Sufficient',
            'medium' => 'Insufficient',
            'low' => 'Non-existent',
            'other' => 'Other',
        ],
        'presentation_format' => [
            'high' => 'Appropriate',
            'medium' => 'Inappropriate',
            'low' => 'Needs improvement',
            'other' => 'Other',
        ],
    ],

    'divergence' => [
        'low' => 'low',
        'medium' => 'medium',
        'high' => 'high',
    ],

    'visibility' => [
        'private' => 'Private',
        'score_public' => 'Score Only',
        'full_public' => 'Full Report',
    ],

    'unknown' => 'Unknown',
];
```

---

# 9️⃣ SERVICES — REFATORAÇÕES

## 9.1 `AggregationService::medalForScore()` — chaves neutras

**Antes:**
```php
public static function medalForScore(int $score): string
{
    return match (true) {
        $score >= 91 => 'Ouro',
        $score >= 61 => 'Prata',
        $score >= 41 => 'Bronze',
        default => 'Incipiente',
    };
}
```

**Depois:**
```php
public static function medalForScore(int $score): string
{
    return match (true) {
        $score >= 91 => 'gold',
        $score >= 61 => 'silver',
        $score >= 41 => 'bronze',
        default => 'incipient',
    };
}
```

> Os snapshots passam a gravar `gold`, `silver`, `bronze`, `incipient`. A tradução ocorre no frontend via `$t('results.medal.gold')` ou no backend via `__('labels.medals.gold')`.

---

## 9.2 `ResponseLabelResolver` — usar `__()`

**Antes:**
```php
public static function resolve(AnswerLevel $level, ResponseProfile $profile): string
{
    $map = [
        ResponseProfile::INFORMATION_QUALITY->value => [
            AnswerLevel::HIGH->value => 'Suficiente',
            // ...
        ],
    ];
    return $map[$profile->value][$level->value] ?? 'Desconhecido';
}
```

**Depois:**
```php
public static function resolve(AnswerLevel $level, ResponseProfile $profile): string
{
    return __("labels.response.{$profile->value}.{$level->value}");
}

public static function optionsForProfile(ResponseProfile $profile): array
{
    return collect(AnswerLevel::cases())
        ->map(fn($level) => [
            'value' => $level->value,
            'label' => self::resolve($level, $profile),
        ])->toArray();
}
```

---

## 9.3 `DivergenceService::classify()` — chaves neutras

**Antes:**
```php
public static function classify(float $variance): string
{
    return match (true) {
        $variance <= 10 => 'baixa',
        $variance <= 30 => 'média',
        default => 'alta',
    };
}
```

**Depois:**
```php
public static function classify(float $variance): string
{
    return match (true) {
        $variance <= 10 => 'low',
        $variance <= 30 => 'medium',
        default => 'high',
    };
}
```

> Tradução no frontend via `$t('results.divergence_high')` etc.

---

## 9.4 `Visibility::label()` — usar `__()`

**Antes:**
```php
public function label(): string
{
    return match ($this) {
        self::PRIVATE => 'Privado',
        self::SCORE_PUBLIC => 'Apenas Score',
        self::FULL_PUBLIC => 'Relatório Completo',
    };
}
```

**Depois:**
```php
public function label(): string
{
    return __("labels.visibility.{$this->value}");
}
```

---

# 🔟 CONTROLLERS — REFATORAÇÕES

Trocar todas as strings hardcoded por chamadas `__('messages.key')`.

**Exemplo de refatoração**:

```php
// Antes
return redirect()->route('projects.show', $project->id)
    ->with('success', 'Projeto criado com sucesso.');

// Depois
return redirect()->route('projects.show', $project->id)
    ->with('success', __('messages.project_created'));
```

```php
// Antes (com interpolação)
return redirect()->back()
    ->with('success', "Convite enviado com sucesso para {$validated['email']}.");

// Depois
return redirect()->back()
    ->with('success', __('messages.invitation_sent', ['email' => $validated['email']]));
```

```php
// Antes (withErrors)
return redirect()->back()
    ->withErrors(['status' => 'Apenas o responsável pela inspeção pode mudar seu status.']);

// Depois
return redirect()->back()
    ->withErrors(['status' => __('messages.only_responsible_can_change')]);
```

### Controllers a refatorar:

| Controller | Strings |
|------------|---------|
| `ProjectController` | 5 |
| `InspectionController` | 5 |
| `InvitationController` | 4 |
| `ResultController` | 8 |
| `EvaluationRoundController` | 3 |
| `EvaluationRoundPublicationController` | 4 |
| `RoundBadgeController` | 3 |
| `DataExportController` | 2 |
| **Total** | **34** |

---

# 1️⃣1️⃣ SEEDER — ATUALIZAÇÃO

O `QuestionnaireV1Seeder` deve usar arrays de tradução:

```php
Section::create([
    'questionnaire_version_id' => $version->id,
    'name' => [
        'pt_BR' => 'Existência e Qualidade da Informação',
        'en' => 'Information Existence and Quality',
    ],
    'order' => 1,
    'response_profile' => 'information_quality',
]);

Category::create([
    'section_id' => $section->id,
    'name' => [
        'pt_BR' => 'Pessoas/Atores',
        'en' => 'People/Actors',
    ],
    'order' => 1,
]);

Question::create([
    'category_id' => $category->id,
    'text' => [
        'pt_BR' => 'Informações sobre os atores tais como: Nome, endereço, telefone, e-mail e responsável pela empresa?',
        'en' => 'Information about the actors such as: Name, address, phone, email and company responsible?',
    ],
    'order' => 1,
]);
```

> As traduções inglesas das 47 perguntas serão fornecidas pelo responsável do projeto.

---

# 1️⃣2️⃣ SNAPSHOTS — CHAVES NEUTRAS

Os `CloseInspectionAction` e `CloseRoundAction` já gravam snapshots com dados de medalha e nomes de seção/categoria. Alterações:

1. **Medalhas**: gravar a chave neutra (`gold` em vez de `Ouro`)
2. **Nomes de seção/categoria**: serão gravados na locale ativa do momento (comportamento padrão do `spatie/laravel-translatable` — retorna o valor da locale atual). Adicionalmente, gravar o **`id`** da seção/categoria para permitir lookup traduzido no frontend se necessário.
3. **Divergência**: gravar a chave neutra (`low`, `medium`, `high` em vez de `baixa`, `média`, `alta`)

---

# 1️⃣3️⃣ EMAIL — `ProjectInvitationMail`

Refatorar o template Blade `resources/views/emails/invitation.blade.php` para usar `__()`:

```blade
# {{ __('email.invitation_heading') }}

{{ __('email.invitation_body', ['project' => $project->name]) }}

{{ __('email.invitation_role', ['role' => __('labels.roles.' . $invitation->role)]) }}

@if($userExists)
{{ __('email.invitation_existing_user') }}

@component('mail::button', ['url' => $loginUrl])
{{ __('email.invitation_login_button') }}
@endcomponent
@else
{{ __('email.invitation_new_user') }}

@component('mail::button', ['url' => $registerUrl])
{{ __('email.invitation_register_button') }}
@endcomponent
@endif

{{ __('email.invitation_ignore') }}

{{ __('email.thanks') }}<br>
{{ __('email.team', ['app' => config('app.name')]) }}
```

Setar a locale antes de enviar o email:

```php
// No controller ou job de envio
$locale = $user?->locale ?? 'pt_BR';
App::setLocale($locale);
Mail::to($invitation->email)->send(new ProjectInvitationMail($invitation));
```

---

# 1️⃣4️⃣ COMPONENTE `LocaleSwitcher`

## 14.1 Componente Vue

Criar `resources/js/Components/LocaleSwitcher.vue`:

```vue
<script setup>
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';

const { locale } = useI18n();
const page = usePage();

const switchLocale = (event) => {
    const newLocale = event.target.value;
    locale.value = newLocale;

    if (page.props.auth.user) {
        router.patch(route('profile.locale'), { locale: newLocale }, {
            preserveState: true,
            preserveScroll: true,
        });
    } else {
        localStorage.setItem('locale', newLocale);
        router.reload();
    }
};
</script>

<template>
    <select
        :value="locale"
        @change="switchLocale"
        class="text-sm border-gray-300 rounded-md"
    >
        <option value="pt_BR">Português</option>
        <option value="en">English</option>
    </select>
</template>
```

## 14.2 Usar nos Layouts

Inserir `<LocaleSwitcher />` em:

- `AuthenticatedLayout.vue` — no header, ao lado do menu de usuário
- `PublicLayout.vue` — no header, ao lado dos links de navegação

## 14.3 Rota e Controller

```php
// routes/web.php
Route::patch('/profile/locale', [ProfileController::class, 'updateLocale'])
    ->middleware('auth')
    ->name('profile.locale');
```

```php
// ProfileController.php
public function updateLocale(Request $request)
{
    $validated = $request->validate([
        'locale' => 'required|in:pt_BR,en',
    ]);

    $request->user()->update(['locale' => $validated['locale']]);

    return back();
}
```

---

# 1️⃣5️⃣ BADGE PÚBLICO — SUPORTE A `?lang=`

O `RoundBadgeController::script()` deve aceitar um parâmetro `lang`:

```php
public function script(RoundBadge $badge)
{
    $lang = request()->query('lang', 'pt_BR');
    if (!in_array($lang, ['pt_BR', 'en'])) {
        $lang = 'pt_BR';
    }

    app()->setLocale($lang);

    // Renderizar badge com medalha traduzida
    $medalLabel = __('labels.medals.' . $data['medal']);
    // ... usar $medalLabel no template JS
}
```

---

# 1️⃣6️⃣ FRONTEND — PADRÃO DE EXTRAÇÃO DE STRINGS

## 16.1 Templates

```vue
<!-- Antes -->
<h1>Criar Novo Projeto</h1>
<button>Salvar</button>

<!-- Depois -->
<h1>{{ $t('workspace.create_project_title') }}</h1>
<button>{{ $t('common.save') }}</button>
```

## 16.2 JavaScript (Composition API)

```js
// Antes
const translateStatus = (status) => {
    const map = { 'draft': 'Rascunho', 'active': 'Ativa', 'closed': 'Concluída' };
    return map[status] || status;
};

// Depois
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const translateStatus = (status) => t(`inspection.status.${status}`);
```

## 16.3 Props com fallback

```js
// Antes
defineProps({ title: { default: 'Aviso' } });

// Depois — usar computed
const props = defineProps({ title: { default: null } });
const { t } = useI18n();
const displayTitle = computed(() => props.title ?? t('common.warning'));
```

## 16.4 Datas

```js
// Antes
new Date(d).toLocaleDateString('pt-BR');

// Depois
import { useFormatDate } from '@/composables/useFormatDate';
const { formatDate } = useFormatDate();
formatDate(d);
```

## 16.5 Medalhas vindas do backend

```js
// Backend agora retorna 'gold', 'silver', etc.
// Frontend traduz:
const medalLabel = t(`results.medal.${snapshot.medal.name}`);

// CSS classes usam a chave neutra diretamente:
const medalClasses = {
    'gold': 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'silver': 'bg-gray-100 text-gray-800 border-gray-300',
    'bronze': 'bg-orange-100 text-orange-800 border-orange-300',
    'incipient': 'bg-red-100 text-red-800 border-red-300',
};

const medalImages = {
    'gold': '/images/badges-gold.png',
    'silver': '/images/badges-silver.png',
    'bronze': '/images/badges-bronze.png',
};
```

---

# 1️⃣7️⃣ ORDEM DE IMPLEMENTAÇÃO

| Ordem | Tarefa | Escopo |
|-------|--------|--------|
| 1 | Instalar `vue-i18n` e `spatie/laravel-translatable` | Dependências |
| 2 | Criar `i18n.js`, `locales/pt_BR.json`, `locales/en.json` (vazio) | Frontend infra |
| 3 | Integrar vue-i18n no `app.js` | Frontend infra |
| 4 | Criar migration `users.locale`, atualizar User model | Backend DB |
| 5 | Criar middleware `SetLocale`, registrar | Backend infra |
| 6 | Alterar `HandleInertiaRequests` para compartilhar `locale` | Backend infra |
| 7 | Alterar migration do questionário para colunas JSON | Backend DB |
| 8 | Adicionar `HasTranslations` nos models Section, Category, Question | Backend models |
| 9 | Atualizar `QuestionnaireV1Seeder` com traduções bilíngues | Backend seeder |
| 10 | Refatorar `AggregationService::medalForScore()` para chaves neutras | Backend service |
| 11 | Refatorar `DivergenceService::classify()` para chaves neutras | Backend service |
| 12 | Refatorar `ResponseLabelResolver` para usar `__()` | Backend service |
| 13 | Refatorar `Visibility::label()` para usar `__()` | Backend enum |
| 14 | Criar `lang/pt_BR/messages.php` e `lang/en/messages.php` | Backend translations |
| 15 | Criar `lang/pt_BR/labels.php` e `lang/en/labels.php` | Backend translations |
| 16 | Refatorar flash messages em todos os controllers | Backend controllers |
| 17 | Refatorar `CloseInspectionAction` e `CloseRoundAction` (snapshots) | Backend actions |
| 18 | Extrair strings: `Layouts/` e `Components/` | Frontend components |
| 19 | Extrair strings: `Pages/Auth/*` | Frontend pages |
| 20 | Extrair strings: `Pages/Inspection/Show.vue`, `QuestionCard.vue` | Frontend pages |
| 21 | Extrair strings: `Pages/Results/*` | Frontend pages |
| 22 | Extrair strings: `Pages/EvaluationRound/*` | Frontend pages |
| 23 | Extrair strings: `Pages/Workspace/*`, `Pages/Project/*` | Frontend pages |
| 24 | Extrair strings: `Pages/Welcome.vue`, `Manual.vue`, `MitraMethod.vue` | Frontend pages |
| 25 | Extrair strings: `Pages/PublicDirectory/*`, `Comparison/*` | Frontend pages |
| 26 | Extrair strings: `Pages/Profile/*` | Frontend pages |
| 27 | Extrair strings: `Constants/ProjectOptions.js` | Frontend constants |
| 28 | Criar composable `useFormatDate`, substituir datas hardcoded | Frontend composable |
| 29 | Criar componente `LocaleSwitcher.vue` | Frontend component |
| 30 | Implementar rota `PATCH /profile/locale` | Backend route |
| 31 | Adicionar `LocaleSwitcher` nos layouts | Frontend layouts |
| 32 | Refatorar `RoundBadgeController` para suporte `?lang=` | Backend controller |
| 33 | Refatorar `ProjectInvitationMail` e template Blade | Backend email |
| 34 | Preencher `locales/en.json` com todas as traduções | Frontend translations |
| 35 | Testes automatizados | Testes |

---

# 1️⃣8️⃣ REGRAS DE NEGÓCIO

## RN-I18N-01 — Locale do usuário

O locale é determinado pela seguinte precedência:

1. `users.locale` (se autenticado)
2. `Accept-Language` header do browser (se guest)
3. `config('app.fallback_locale')` = `pt_BR`

## RN-I18N-02 — Locales suportadas

Apenas `pt_BR` e `en` são aceitas. Qualquer outro valor é rejeitado na validação.

## RN-I18N-03 — Questionário translatable

Os campos `sections.name`, `categories.name` e `questions.text` são armazenados como JSON translatable. O model retorna automaticamente o valor na locale ativa via `spatie/laravel-translatable`.

## RN-I18N-04 — Snapshots locale-independent

Medalhas nos snapshots usam chaves neutras (`gold`, `silver`, `bronze`, `incipient`). A tradução ocorre exclusivamente na camada de apresentação.

## RN-I18N-05 — Divergência locale-independent

Classificações de divergência nos snapshots usam chaves neutras (`low`, `medium`, `high`). A tradução ocorre no frontend.

## RN-I18N-06 — Labels de resposta

Os labels de resposta (Suficiente/Insuficiente etc.) são resolvidos em runtime pelo `ResponseLabelResolver` usando a locale ativa. Não são persistidos.

## RN-I18N-07 — Badge público

O badge aceita `?lang=pt_BR|en` para renderizar no idioma solicitado. Default: `pt_BR`.

## RN-I18N-08 — Email

Emails são enviados na locale do destinatário (se usuário existente) ou na locale do remetente como fallback.

## RN-I18N-09 — Filament

O painel admin Filament permanece em português. O `spatie/laravel-translatable` retorna `pt_BR` por padrão quando a locale não está setada.

---

# 1️⃣9️⃣ CHECKLIST DE ENTREGA

- [ ] `vue-i18n` instalado e configurado no `app.js`
- [ ] `resources/js/plugins/i18n.js` criado
- [ ] `resources/js/locales/pt_BR.json` criado com todas as chaves
- [ ] `resources/js/locales/en.json` criado com todas as traduções
- [ ] `spatie/laravel-translatable` instalado
- [ ] Migration original do questionário alterada para colunas JSON
- [ ] Migration `users.locale` criada
- [ ] `User` model atualizado (`fillable`, `locale`)
- [ ] `Section`, `Category`, `Question` models com `HasTranslations`
- [ ] Middleware `SetLocale` criado e registrado
- [ ] `HandleInertiaRequests` compartilhando `locale`
- [ ] `lang/pt_BR/messages.php` com todas as flash messages
- [ ] `lang/en/messages.php` com todas as traduções
- [ ] `lang/pt_BR/labels.php` com medalhas, response, divergência, visibility
- [ ] `lang/en/labels.php` com todas as traduções
- [ ] `AggregationService::medalForScore()` retornando chaves neutras
- [ ] `DivergenceService::classify()` retornando chaves neutras
- [ ] `ResponseLabelResolver` usando `__()`
- [ ] `Visibility::label()` usando `__()`
- [ ] Todos os controllers refatorados para `__('messages.*')`
- [ ] `CloseInspectionAction` e `CloseRoundAction` gravando chaves neutras
- [ ] `QuestionnaireV1Seeder` com traduções `en` + `pt_BR`
- [ ] Todos os componentes Vue usando `$t()`
- [ ] `Constants/ProjectOptions.js` usando tradução
- [ ] `resources/js/composables/useFormatDate.js` criado
- [ ] Datas formatadas com locale ativa em todos os componentes
- [ ] `Components/LocaleSwitcher.vue` criado
- [ ] Rota `PATCH /profile/locale` implementada
- [ ] `LocaleSwitcher` inserido em `AuthenticatedLayout` e `PublicLayout`
- [ ] `RoundBadgeController` com suporte a `?lang=`
- [ ] `ProjectInvitationMail` e template Blade traduzidos
- [ ] `lang/pt_BR/email.php` e `lang/en/email.php` criados
- [ ] Testes automatizados cobrindo troca de idioma
