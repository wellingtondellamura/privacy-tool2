# Spec 005 — Internacionalização (i18n)

> **Idiomas**: `en_US` (inglês) e `pt_BR` (português brasileiro)
> **Escopo**: Interface do usuário (Vue + Inertia). Filament (admin) **não** será traduzido.

---

## 1. Diagnóstico Atual

### 1.1 Frontend (Vue/Inertia)

| Item | Estado |
|------|--------|
| Pacote i18n | Nenhum instalado |
| Funções de tradução (`$t`, `__`) | Inexistentes |
| Strings hardcoded em pt_BR | **~54 componentes Vue** com texto embutido diretamente nos templates |
| Seletor de idioma | Inexistente |
| Locale no `app.js` | Não configurado |

**Componentes com maior volume de texto hardcoded:**

- `Pages/Welcome.vue` — textos institucionais, descrição do método, medalhas
- `Pages/Manual.vue` — manual do usuário completo
- `Pages/MitraMethod.vue` — descrição acadêmica do método
- `Pages/Inspection/Show.vue` — formulário de inspeção, labels, status
- `Pages/EvaluationRound/Show.vue` — gerenciamento de rodadas
- `Pages/EvaluationRound/Review.vue` — revisão e publicação
- `Pages/Results/Individual.vue`, `Team.vue`, `Round.vue` — exibição de resultados, mapas de medalhas
- `Pages/Workspace/Index.vue` — criação de projetos, convites
- `Components/QuestionCard.vue` — card de pergunta do questionário
- `Layouts/AuthenticatedLayout.vue`, `PublicLayout.vue` — navegação, footer
- `Constants/ProjectOptions.js` — labels de cores e ícones

### 1.2 Backend (Laravel)

| Item | Estado |
|------|--------|
| `config/app.locale` | `pt_BR` via env `APP_LOCALE` |
| `lang/pt_BR.json` | ~400 chaves (Breeze/framework) |
| `lang/pt_BR/*.php` | `auth`, `passwords`, `pagination`, `validation` |
| `lang/en/` | Inexistente (usa fallback built-in do Laravel) |
| Enums com labels pt_BR | `Visibility::label()` ("Privado", "Apenas Score", "Relatório Completo") |
| Services com strings pt_BR | `ResponseLabelResolver` ("Suficiente", "Insuficiente", etc.) |
| Services com strings pt_BR | `AggregationService::medalForScore()` ("Ouro", "Prata", "Bronze", "Incipiente") |
| Services com strings pt_BR | `DivergenceService::classify()` ("baixa", "média", "alta") |
| Flash messages em controllers | Hardcoded pt_BR ("Inspeção iniciada...", "Projeto criado...") |
| Error messages inline | Hardcoded pt_BR nos `withErrors()` |

### 1.3 Questionário (Banco de Dados)

O questionário é **versionado e armazenado no banco** via seeder (`QuestionnaireV1Seeder`):

```
QuestionnaireVersion
  └── Section (2: "Existência e Qualidade da Informação", "Formato de Apresentação")
        └── Category (5 por seção: "Pessoas/Atores", "Propósito de uso", etc.)
              └── Question (47 total — textos longos em pt_BR)
```

**Campos textuais no banco:**

| Tabela | Campo | Exemplo |
|--------|-------|---------|
| `sections` | `name` | "Existência e Qualidade da Informação" |
| `categories` | `name` | "Pessoas/Atores" |
| `questions` | `text` | "Informações sobre os atores tais como: Nome, endereço..." |

**Impacto**: Os textos do questionário são carregados do banco e renderizados diretamente no frontend via `Inspection/Show.vue` → `QuestionCard.vue`. Não passam por nenhuma camada de tradução.

### 1.4 Snapshots (ResultSnapshot / RoundSnapshot)

Os snapshots armazenam **nomes de seções, categorias e medalhas em pt_BR** como JSON persistido:

```json
{
  "sections": [
    { "name": "Existência e Qualidade da Informação", "medal": "Ouro", ... }
  ],
  "medal": { "name": "Prata" }
}
```

Como o banco será zerado, todos os snapshots já serão criados com as chaves neutras desde o início.

---

## 2. Análise de Impacto

### 2.1 Impacto por Camada

| Camada | Impacto | Esforço |
|--------|---------|---------|
| **Vue templates** (strings estáticas) | Alto — ~54 arquivos para extrair strings | Alto |
| **Vue JS/constants** (mapas de status, medalhas, cores) | Médio — ~8 mapas/helpers | Baixo |
| **Backend flash messages** | Médio — ~30 mensagens em ~10 controllers | Médio |
| **Backend Enums/Services** | Médio — 3 enums/services com labels | Baixo |
| **Questionário (DB)** | **Alto** — 47 perguntas + 10 categorias + 2 seções | **Alto** |
| **Snapshots** | Baixo — já serão criados com chaves neutras | Nenhum |
| **Badge público (JS embed)** | Baixo — 3 templates no `RoundBadgeController` | Baixo |
| **E-mail (ProjectInvitationMail)** | Baixo — 1 template | Baixo |

### 2.2 Impacto Específico no Questionário

O questionário é o ponto de **maior complexidade** para i18n porque:

1. **Textos vêm do banco de dados**, não de arquivos de tradução
2. **47 perguntas** com textos longos e especializados precisam de tradução profissional
3. **Seções e categorias** têm nomes que aparecem em múltiplas telas (inspeção, resultados, comparação, diretório público)
4. **Labels de resposta** ("Suficiente"/"Insuficiente"/"Inexistente") são resolvidos no backend via `ResponseLabelResolver` e enviados ao frontend como `options` do `Section`
5. **Medalhas** ("Ouro", "Prata", etc.) são calculadas no `AggregationService` e persistidas nos snapshots

---

## 3. Estratégia Proposta

### 3.1 Stack Tecnológica

| Componente | Tecnologia |
|------------|-----------|
| Frontend i18n | **vue-i18n v10** (padrão da comunidade Vue 3) |
| Arquivos de tradução frontend | **JSON** — um por idioma (`en.json`, `pt_BR.json`) em `resources/js/locales/` |
| Backend i18n | **Laravel built-in** (`__()`, `trans()`, arquivos em `lang/`) |
| Questionário i18n | **Coluna JSON translatable** no banco (via `spatie/laravel-translatable`) |
| Detecção de idioma | **Preferência do usuário** (coluna `locale` na tabela `users`) + fallback Accept-Language |
| Persistência de preferência | Coluna `locale` na tabela `users`, compartilhada via Inertia |

### 3.2 Arquitetura Geral

```
┌──────────────────────────────────────────────────┐
│                    FRONTEND                       │
│                                                   │
│  vue-i18n                                         │
│  ├── resources/js/locales/pt_BR.json              │
│  ├── resources/js/locales/en.json                 │
│  └── $t('key') nos templates Vue                  │
│                                                   │
│  Locale vem de:                                   │
│  └── props.auth.user.locale (via Inertia share)   │
│      └── fallback: navigator.language             │
│          └── fallback: 'pt_BR'                    │
├──────────────────────────────────────────────────┤
│                    BACKEND                        │
│                                                   │
│  Middleware SetLocale                              │
│  ├── user->locale (se autenticado)                │
│  ├── Accept-Language header (se guest)            │
│  └── config('app.fallback_locale')                │
│                                                   │
│  Controllers                                      │
│  └── __('messages.inspection_activated')           │
│                                                   │
│  Questionário (spatie/laravel-translatable)        │
│  ├── sections.name      → JSON {"pt_BR":"...", "en":"..."}  │
│  ├── categories.name    → JSON {"pt_BR":"...", "en":"..."}  │
│  └── questions.text     → JSON {"pt_BR":"...", "en":"..."}  │
│                                                   │
│  Services                                         │
│  ├── ResponseLabelResolver → __('labels.high_iq') │
│  ├── AggregationService    → __('medals.gold')    │
│  └── DivergenceService     → __('divergence.low') │
└──────────────────────────────────────────────────┘
```

---

## 4. Plano de Implementação

### Fase 1 — Infraestrutura (sem mudança visual)

#### 1.1 Instalar dependências

```bash
# Frontend
npm install vue-i18n@10

# Backend (questionário translatable)
composer require spatie/laravel-translatable
```

#### 1.2 Criar estrutura de arquivos de tradução do frontend

```
resources/js/
├── locales/
│   ├── en.json
│   └── pt_BR.json
├── plugins/
│   └── i18n.js          ← configuração do vue-i18n
```

#### 1.3 Configurar vue-i18n no `app.js`

```js
// resources/js/plugins/i18n.js
import { createI18n } from 'vue-i18n';
import en from '../locales/en.json';
import ptBR from '../locales/pt_BR.json';

export function createI18nInstance(locale = 'pt_BR') {
    return createI18n({
        legacy: false,           // Composition API
        locale,
        fallbackLocale: 'pt_BR',
        messages: { en, pt_BR: ptBR },
    });
}
```

```js
// resources/js/app.js — modificar setup
import { createI18nInstance } from './plugins/i18n';

createInertiaApp({
    setup({ el, App, props, plugin }) {
        const locale = props.initialPage.props.auth?.user?.locale ?? 'pt_BR';
        const i18n = createI18nInstance(locale);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
});
```

#### 1.4 Adicionar coluna `locale` na tabela `users`

```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->string('locale', 10)->default('pt_BR')->after('is_admin');
});
```

#### 1.5 Compartilhar locale via Inertia

```php
// HandleInertiaRequests.php — adicionar ao share
'locale' => app()->getLocale(),
```

#### 1.6 Middleware `SetLocale`

```php
// app/Http/Middleware/SetLocale.php
class SetLocale
{
    public function handle(Request $request, Closure $next)
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

#### 1.7 Criar arquivos de tradução do backend

```
lang/
├── en/
│   ├── auth.php
│   ├── messages.php       ← flash messages dos controllers
│   ├── labels.php         ← labels de resposta, medalhas, divergência
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
├── en.json                ← strings do framework em inglês (já built-in)
├── pt_BR/
│   ├── auth.php
│   ├── messages.php
│   ├── labels.php
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
└── pt_BR.json
```

### Fase 2 — Questionário Translatable

#### 2.1 Tornar modelos translatable via `spatie/laravel-translatable`

```php
// app/Models/Section.php
use Spatie\Translatable\HasTranslations;

class Section extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    // ...
}
```

```php
// app/Models/Category.php
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    // ...
}
```

```php
// app/Models/Question.php
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasTranslations;

    public $translatable = ['text'];
    // ...
}
```

#### 2.2 Alterar migrations originais para usar JSON

Como o banco será zerado, alterar diretamente a migration original (`2026_02_24_000004_create_questionnaire_tables.php`) para que as colunas já sejam criadas como `json`:

```php
// 2026_02_24_000004_create_questionnaire_tables.php
Schema::create('sections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('questionnaire_version_id')->constrained()->cascadeOnDelete();
    $table->json('name');           // ← era string
    $table->integer('order');
    $table->timestamps();
});

Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')->constrained()->cascadeOnDelete();
    $table->json('name');           // ← era string
    $table->integer('order');
    $table->timestamps();
});

Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->json('text');           // ← era text
    $table->integer('order');
    $table->timestamps();
});
```

Nenhuma migration de dados é necessária.

#### 2.3 Atualizar o Seeder

```php
// QuestionnaireV1Seeder — usar formato translatable
Section::create([
    'questionnaire_version_id' => $version->id,
    'name' => [
        'pt_BR' => 'Existência e Qualidade da Informação',
        'en' => 'Information Existence and Quality',
    ],
    'order' => 1,
    'response_profile' => 'information_quality',
]);

// Idem para categories e questions
```

#### 2.4 Impacto nos Snapshots

**Problema**: Snapshots existentes contêm nomes em pt_BR puro (string, não JSON). Novos snapshots devem persistir na locale do momento do fechamento **ou** armazenar a chave translatable.

**Estratégia**: armazenar **chaves neutras** para medalhas (`gold`, `silver`, `bronze`, `incipient`) e o **`id`** de seções/categorias nos snapshots. A tradução é feita na camada de apresentação via lookup. Isso desacopla completamente a persistência do idioma.

#### 2.5 Refatorar medalhas para chaves neutras

```php
// AggregationService.php
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

```php
// lang/pt_BR/labels.php
return [
    'medals' => [
        'gold' => 'Ouro',
        'silver' => 'Prata',
        'bronze' => 'Bronze',
        'incipient' => 'Incipiente',
    ],
];
```

```php
// lang/en/labels.php
return [
    'medals' => [
        'gold' => 'Gold',
        'silver' => 'Silver',
        'bronze' => 'Bronze',
        'incipient' => 'Incipient',
    ],
];
```

Os `CloseInspectionAction` e `CloseRoundAction` passam a gravar chaves neutras nos snapshots. Todas as renderizações de medalha (frontend e badge público) fazem lookup da tradução ao invés de usar o valor direto.

#### 2.6 Refatorar ResponseLabelResolver

```php
// ResponseLabelResolver.php
public static function resolve(AnswerLevel $level, ResponseProfile $profile): string
{
    $key = "labels.response.{$profile->value}.{$level->value}";
    return __($key);
}
```

```php
// lang/pt_BR/labels.php
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
```

```php
// lang/en/labels.php
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
```

#### 2.7 Refatorar DivergenceService

```php
// DivergenceService.php — manter chaves neutras internamente
public static function classify(float $variance): string
{
    return match (true) {
        $variance <= 10 => 'low',
        $variance <= 30 => 'medium',
        default => 'high',
    };
}
```

### Fase 3 — Extração de Strings do Frontend

#### 3.1 Convenção de chaves

Adotar **namespace por feature** com dot notation:

```json
{
  "auth.login": "Entrar",
  "auth.register": "Cadastrar",
  "auth.forgot_password": "Esqueceu a senha?",
  "workspace.create_project": "Criar Novo Projeto",
  "workspace.pending_invitations": "Convites Pendentes",
  "inspection.status.draft": "Rascunho",
  "inspection.status.active": "Ativa",
  "inspection.status.closed": "Concluída",
  "results.medal.gold": "Ouro",
  "results.medal.silver": "Prata",
  "common.save": "Salvar",
  "common.cancel": "Cancelar",
  "common.delete": "Excluir",
  "common.saving": "Salvando..."
}
```

#### 3.2 Processo de extração

Para cada componente Vue, substituir texto hardcoded:

**Antes:**
```vue
<template>
    <h1>Criar Novo Projeto</h1>
    <button>Salvar</button>
    <button>Cancelar</button>
</template>
```

**Depois:**
```vue
<template>
    <h1>{{ $t('workspace.create_project') }}</h1>
    <button>{{ $t('common.save') }}</button>
    <button>{{ $t('common.cancel') }}</button>
</template>
```

Para JavaScript:

**Antes:**
```js
const translateStatus = (status) => {
    const map = { 'draft': 'Rascunho', 'active': 'Ativa', 'closed': 'Concluída' };
    return map[status] || status;
};
```

**Depois:**
```js
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const translateStatus = (status) => t(`inspection.status.${status}`);
```

#### 3.3 Ordem de migração recomendada (por prioridade)

1. **Componentes compartilhados** — `Layouts/`, `Components/` (base reusável)
2. **Auth** — `Pages/Auth/*` (baixa complexidade, boa prática inicial)
3. **Questionário** — `Inspection/Show.vue`, `QuestionCard.vue` (alta prioridade funcional)
4. **Resultados** — `Results/Individual.vue`, `Team.vue`, `Round.vue`
5. **Rodadas** — `EvaluationRound/Show.vue`, `Review.vue`
6. **Workspace/Projetos** — `Workspace/Index.vue`, `Project/Show.vue`
7. **Páginas públicas** — `Welcome.vue`, `Manual.vue`, `MitraMethod.vue`, `PublicDirectory/`
8. **Comparações** — `Comparison/Show.vue`
9. **Perfil** — `Profile/`

### Fase 4 — Seletor de Idioma e UX

#### 4.1 Componente `LocaleSwitcher.vue`

Componente dropdown simples no header (dentro de `AuthenticatedLayout.vue` e `PublicLayout.vue`):

```vue
<template>
    <select :value="currentLocale" @change="switchLocale">
        <option value="pt_BR">Português</option>
        <option value="en">English</option>
    </select>
</template>
```

#### 4.2 Endpoint para salvar preferência

```
PATCH /profile/locale  { locale: 'en' }
```

Para visitantes não autenticados: armazenar em cookie/localStorage e enviar via Accept-Language.

#### 4.3 Formatação de datas

Substituir `toLocaleDateString('pt-BR')` hardcoded por:

```js
const { locale } = useI18n();
new Date(dateString).toLocaleDateString(locale.value === 'pt_BR' ? 'pt-BR' : 'en-US');
```

Ou usar composable dedicado:

```js
// composables/useFormatDate.js
export function useFormatDate() {
    const { locale } = useI18n();
    const formatDate = (date) =>
        new Date(date).toLocaleDateString(locale.value === 'pt_BR' ? 'pt-BR' : 'en-US');
    return { formatDate };
}
```

### Fase 5 — Flash Messages e Validação do Backend

#### 5.1 Extrair mensagens dos controllers

```php
// Antes
return redirect()->back()->with('success', 'Inspeção iniciada e mudou para status Ativa.');

// Depois
return redirect()->back()->with('success', __('messages.inspection_activated'));
```

#### 5.2 Validação

As regras de validação do Laravel já são traduzidas via `lang/{locale}/validation.php`. Basta manter os arquivos em `en/` e `pt_BR/`.

#### 5.3 E-mail

O `ProjectInvitationMail` deve usar `__()` para o conteúdo do e-mail, com o locale setado baseado no idioma do destinatário (ou do remetente como fallback).

---

## 5. Tratamento de Casos Especiais

### 5.1 Badge Público (`RoundBadgeController`)

O badge é um script JS embeddable que renderiza HTML. O conteúdo deve ser traduzido com base na locale da publicação ou do round. Opções:

- **Opção A**: Renderizar na locale do projeto/publicação (fixo ao publicar)
- **Opção B**: Aceitar parâmetro `?lang=en` no endpoint do badge

**Recomendação**: Opção B — permite que o consumidor do badge escolha o idioma.

### 5.2 Diretório Público (`PublicDirectory`)

As páginas públicas não têm usuário autenticado. Usar:
- Cookie/localStorage para persistir preferência
- Accept-Language header como fallback
- Parâmetro `?lang=` na URL como override

### 5.3 Questionário — Textos longos

Os textos das 47 perguntas são longos e especializados (LGPD/privacidade). As traduções para inglês serão fornecidas pelo responsável do projeto e incluídas diretamente no seeder.

---

## 6. Estrutura Final de Arquivos

```
lang/
├── en/
│   ├── auth.php
│   ├── labels.php          ← medalhas, response labels, divergência, visibility
│   ├── messages.php        ← flash messages dos controllers
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
├── en.json                 ← framework strings (built-in Laravel)
├── pt_BR/
│   ├── auth.php
│   ├── labels.php
│   ├── messages.php
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
└── pt_BR.json

resources/js/
├── locales/
│   ├── en.json             ← todas as strings do frontend em inglês
│   └── pt_BR.json          ← todas as strings do frontend em pt_BR
├── plugins/
│   └── i18n.js             ← configuração vue-i18n
├── composables/
│   └── useFormatDate.js    ← formatação de data locale-aware
```

---

## 7. Riscos e Mitigações

| Risco | Probabilidade | Mitigação |
|-------|--------------|-----------|
| ~~Snapshots legados~~ | N/A | Banco zerado — não se aplica |
| Performance com 2 JSONs de tradução carregados | Baixa | JSONs são pequenos (~50KB cada); lazy-loading não necessário |
| Labels de resposta inconsistentes entre inspeções | Baixa | Labels resolvidos em runtime via locale, não persistidos |

---

## 8. Checklist de Entrega

- [ ] `vue-i18n` instalado e configurado no `app.js`
- [ ] Arquivos `en.json` e `pt_BR.json` criados no frontend
- [ ] `spatie/laravel-translatable` instalado e configurado
- [ ] Migrations originais atualizadas com colunas JSON para campos translatable
- [ ] Migration para adicionar `locale` na tabela `users`
- [ ] Middleware `SetLocale` registrado
- [ ] Locale compartilhado via Inertia (`HandleInertiaRequests`)
- [ ] `ResponseLabelResolver` refatorado para usar `__()`
- [ ] `AggregationService::medalForScore()` retornando chaves neutras
- [ ] `DivergenceService::classify()` retornando chaves neutras
- [ ] `Visibility::label()` refatorado para usar `__()`
- [ ] Flash messages dos controllers extraídas para `lang/*/messages.php`
- [ ] Todos os componentes Vue migrados para `$t()`
- [ ] Componente `LocaleSwitcher` implementado
- [ ] Endpoint `PATCH /profile/locale`
- [ ] Seeder atualizado com traduções `en` + `pt_BR`
- [ ] Formatação de datas locale-aware
- [ ] Badge público com suporte a `?lang=`
- [ ] Testes automatizados cobrindo troca de idioma
- [ ] `ProjectInvitationMail` traduzido
