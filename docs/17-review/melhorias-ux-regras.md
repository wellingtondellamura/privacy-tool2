# Revisão do branch `melhorias-ux-regras`

Documento para um agente revisar as mudanças. Cada item diz **o que** mudou, **por que**, **onde** e **como verificar**. As mudanças ainda não foram commitadas: a base é o commit `ccf6abf` (branch `main`) e o diff está na working tree (56 arquivos modificados + 5 novos).

## 1. Contexto e escopo autorizado

O dono do projeto pediu: deixar a interface mobile friendly, melhorar textos e UX **sem mudar funcionalidade**, verificar as regras de negócio e corrigir erros. Ele aprovou explicitamente estes pacotes:

| Pacote | Conteúdo |
|---|---|
| P1 | Responsividade (mobile) |
| P2 | Correções de exibição, i18n, código morto |
| P3 | Textos explicativos |
| P4 | UX (confirmações, navegação) |
| P5 | Segurança (S1–S4) |
| P6 | Regras de cálculo, com decisões: **R1 = seguir o MANUAL** e **R2 = desvio padrão** |
| P7 | Dependências: só patch/minor + remover pacote não usado |
| P8 | Qualidade técnica (testes, N+1, comparação por ID) |

Os itens de P5 e P6 **mudam comportamento de propósito**. Todo o resto deveria ser só apresentação; se encontrar mudança de comportamento fora de P5/P6, é um problema.

## 2. Como verificar

```bash
git diff ccf6abf --stat
php artisan test            # esperado: 181 passed (antes: 162)
npm run build               # esperado: build sem erros
npm install --legacy-peer-deps   # o conflito de peer deps já existia (ver §6)
```

A verificação visual foi feita com Playwright em viewport 390×844, num SQLite temporário fora do repositório (seed `E2ETestSeeder`, login `e2e_owner@test.com` / `password123`). Telas conferidas: `/projects`, `/projects/1`, `/rounds/1`, `/rounds/1/review` (rascunho e revisão), `/inspections/1`, `/rounds/1/results`, `/inspections/2/team-results`, `/inspections/2/results`, `/profile` e `/tools`. Resultado: todas as capturas com 390px de largura e nenhum erro de console.

Para repetir, use variáveis de ambiente apontando para um SQLite descartável. **Não** use o `.env`, que aponta para MySQL.

```bash
export DB_CONNECTION=sqlite DB_DATABASE=/caminho/tmp.sqlite SESSION_DRIVER=file CACHE_STORE=file QUEUE_CONNECTION=sync MAIL_MAILER=log
```

## 3. Regras de cálculo (P6) — mudam scores

### R1. "Não se aplica" e questões em branco fora do denominador

- **Por quê.** O [MANUAL.md](../../MANUAL.md) §9.4 diz que "Não se aplica" (`AnswerLevel::OTHER`, score `null`) é nulo e que a categoria divide por "total de questões respondidas". O código anterior dividia pelo **total de questões da categoria** e somava `null` como 0. Exemplo: 3×Suficiente + 1×N/A dava 75 em vez de 100.
- **Onde.**
  - [AggregationService.php](../../app/Services/AggregationService.php)
    - `categoryScore(array $scores): ?int` (linha 28): a assinatura mudou, o parâmetro `$totalQuestions` foi removido. Filtra `null`; sem nada aplicável, retorna `null`.
    - `sectionScore` ignora categorias `null` (retorna `null` se não sobrar nenhuma).
    - Novos métodos:
      - `globalScore`: média das seções não nulas, ou 0 se nenhuma.
      - `averageScore`: média por questão entre avaliadores, ignorando `null`.
    - `medalForScore(?int)`: `null` → `'no_data'`.
  - [CloseInspectionAction.php](../../app/Actions/CloseInspectionAction.php), reescrito:
    - as respostas são carregadas uma vez (`groupBy user_id` → `keyBy question_id`), removendo uma query por questão × usuário;
    - os snapshots e a transição ficam em `DB::transaction`;
    - no payload consolidado, o score da **seção** agora é a média das **categorias consolidadas**. Antes era a média dos scores de seção individuais, o que deixava seção e categorias inconsistentes. Hoje cada inspeção tem um único usuário, então na prática o resultado é igual;
    - uma questão sem nenhuma resposta não entra no payload consolidado como score 0, porque fica fora de `$avgQuestionScores`.
  - [CloseRoundAction.php](../../app/Actions/CloseRoundAction.php):
    - os scores são indexados por `question_id` (linha 92), em vez de por posição `[$sIndex][$cIndex][$qIndex]`;
    - média com `averageScore`;
    - resposta consolidada "other" ou média `null` → score `null`, `level = 'other'`;
    - o payload ganhou `std_dev` por questão;
    - o score global usa `globalScore`.
- **Verificar.**
  - `tests/Unit/AggregationServiceTest.php` (novos casos com `null`).
  - `tests/Feature/ClosingTest.php` → "not applicable answers do not lower the score": fecha uma inspeção real com 1 N/A por categoria e espera `global_score = 100`.
- **Riscos a checar.**
  - Snapshots antigos (`payload_json`) continuam com 0 nas questões em branco. O código novo lê esses valores normalmente: rodadas antigas não são recalculadas, por desenho (snapshots são imutáveis).
  - Agora pode haver `score: null` nos payloads. Todo o frontend que exibe score passa por `formatScore` (§5.2). Procure exibições de score que não passaram por ele: `grep -rn "\.score }}" resources/js`.
  - Na Comparação, os totais (`reduce` em `Comparison/Show.vue`) somam `null` como 0, e o delta `null` aparece como "—".

### R2. Divergência classificada por desvio padrão

- **Por quê.** Os limites 10/30 eram aplicados à **variância** em escala 0–100. Notas 100×50 dão variância 625, ou seja, qualquer discordância virava "alta", e "média" só aparecia com cerca de 100 avaliadores. Os filtros baixa/média da tela de revisão nunca eram usados. Aplicar os mesmos limites ao desvio padrão (mesma unidade das notas) dá: 100×90 → σ5 baixa, 100×50 → σ25 média, 100×0 → σ50 alta.
- **Onde.** [DivergenceService.php](../../app/Services/DivergenceService.php):
  - `variance` ignora `null`;
  - novo método `standardDeviation`;
  - `classify(float $standardDeviation)` (linha 48): a **semântica do argumento mudou**, mas o nome e a assinatura ficaram;
  - `forQuestion` (linha 71) retorna `variance`, `std_dev` e `classification`.
- **Verificar.**
  - `tests/Unit/DivergenceServiceTest.php` (casos reescritos).
  - `tests/Feature/I18nTest.php`, linhas ~131-133: continua passando com 5/20/40.
- **Risco.** Procure outros chamadores de `classify` que passem variância: `grep -rn "classify(" app`.

## 4. Segurança e regras de acesso (P5) — mudam comportamento

| Id | O que mudou | Por quê | Onde | Teste |
|---|---|---|---|---|
| S1 | Ao aceitar um convite cujo e-mail já tem conta: sem login → redireciona para `login` com `status`; logado como outra conta → erro `token`. Removido o `Auth::login($user)` para conta existente. | O token funcionava como senha: quem tinha o link entrava na conta. Isso só é explorável com o token (64 caracteres), mas o comportamento estava errado. | [InvitationController.php:72](../../app/Http/Controllers/InvitationController.php#L72) | `InvitationTest`: o teste antigo "accept invitation with existing user" agora usa `actingAs`; foram adicionados 2 testes negativos |
| S2 | O script do selo (`/badge/{token}.js`) monta o DOM com `textContent`/`setAttribute` em vez de `innerHTML`. `href` só se começar com `http(s)://`. `style` é sanitizado. Cor própria para `incipient`. `lang=es` aceito. | O nome do projeto (controlado pelo dono) era injetado como HTML em sites de terceiros (XSS). | [RoundBadgeController.php](../../app/Http/Controllers/RoundBadgeController.php), heredoc `$js` (~linha 136) | `RoundBadgeTest` (só JSON). **O script JS não tem teste automatizado**: revise manualmente. |
| S3 | `exportProject` exige `update` (dono) em vez de `view`. `exportAll` exporta completo só os projetos onde o usuário é dono; nos demais, só o próprio membro e as próprias inspeções (`transformProjectData($project, ?User $onlyFor)`). | Qualquer membro baixava e-mails, respostas e observações de todos, o que contornava a regra de independência dos avaliadores e o `show_evaluations_to_all`. | [DataExportController.php:24](../../app/Http/Controllers/DataExportController.php#L24) | `DataExportTest` (2 testes novos) |
| S4 | `results.team` bloqueia avaliador em inspeção de outro (erro `access`). Na tela da rodada, o botão "Resultado Equipe" (abria a última inspeção fechada de qualquer pessoa) virou "Meu resultado" (inspeção do próprio usuário). | Cada inspeção tem um único avaliador, então "resultado da equipe" era o resultado de uma pessoa, visível a outros avaliadores. | [ResultController.php:49](../../app/Http/Controllers/ResultController.php#L49), `EvaluationRound/Show.vue` (`myClosedInspection`) | `tests/Feature/ResultAccessTest.php` (novo) |
| — | `InspectionController::store` bloqueia observador e rodada fora de `draft`/`active`. | Alinha o backend ao que a tela já fazia (`canCreateInspection`). `active` foi mantido porque o default da coluna `evaluation_rounds.status` é `'active'` (dados legados). | [InspectionController.php:36-46](../../app/Http/Controllers/InspectionController.php#L36) | `InspectionStateMachineTest` (2 testes novos) |

## 5. Frontend

### 5.1 Componentes e utilitários novos

| Arquivo | Função |
|---|---|
| `resources/js/composables/useResultFormat.js` | `formatScore(score, suffix='%')` (`null` → "N/A"), `medalLabel(medal)` (aceita string ou `{name}`; usa `results.medal.*`), `statusLabel`. |
| `resources/js/Components/StatusBadge.vue` | Status único de rodada/inspeção (`draft`/`active`/`review`/`closed`) via chaves `status.*`. Substitui ternários duplicados que rotulavam a rodada em **revisão como "Rascunho"** em `Project/Show.vue` (bug). |
| `resources/js/Components/RoundStepper.vue` | Explica as fases da rodada; trata `active` legado como `draft`. |
| `resources/js/Components/ScoreExplanation.vue` | Quadro recolhível "Como este score é calculado?" nas três telas de resultado. |

### 5.2 Correções de bugs de frontend (comportamento visível)

- **ConflictThread grava na questão errada.** O `useForm` era inicializado com `props.questionId` e não acompanhava a troca de questão na revisão, então comentários iam para a primeira questão aberta. Correção: `form.question_id = props.questionId` no submit ([ConflictThread.vue:45](../../resources/js/Components/ConflictThread.vue#L45)) + `:key="activeQuestion.question_id"` no `Review.vue`. Também ganhou a prop `readonly`.
- **Botão sem cor.** `Button.vue` não tinha a variante `brand`, usada como `confirm-variant` em modais, e o botão de confirmar ficava sem cor. Adicionado `'brand'` (linha 41) e fallback `|| ''`.
- **Erros gerais nunca exibidos.** `withErrors(['status' => ...])` e similares não eram mostrados em nenhuma página. O `AuthenticatedLayout.vue` agora observa `page.props.errors` e abre o modal de erro para as chaves de `GENERAL_ERROR_KEYS` (linha 44). Erros de campo de formulário continuam com seus formulários. **Revisar** se alguma página usa uma dessas chaves como campo de formulário (ex.: `role`); isso duplicaria a mensagem.
- **Sucesso e info viram toast.** Flash `success`/`info` aparece como toast com timeout de 4s, em vez de modal. `error`/`warning` continuam em modal.
- **Revisão em rascunho.** `Review.vue` é acessível em `draft` (botão "Fechar Rodada"), mas o servidor só aceita comentários e decisões em `review`. Agora: banner explicativo, `OwnerDecidePanel` com `can-manage="canManage && isReviewing"` e `ConflictThread :readonly="!isReviewing"`.
- **Fechar rodada sem confirmação.** Fechar a rodada (ação irreversível) agora passa por `ConfirmModal`. "Publicar agora" ganhou um seletor de visibilidade (antes o padrão `score_public` era fixo e invisível).
- **Iniciar revisão.** Agora tem um modal de confirmação: envia e-mail a todos e bloqueia novas inspeções.
- **Código de depuração e modal morto.** Removidos `console.log` (`Review.vue`, `OwnerDecidePanel.vue`) e o modal morto de publicação de inspeção em `Project/Show.vue`, que chamava as rotas inexistentes `inspections.publish`/`inspections.publications.*` e nunca era aberto.
- **Medalhas.** Exibidas traduzidas (antes apareciam "gold"/"silver" nas páginas públicas e na prévia do selo).
- **Selo de publicado.** Usa o rótulo traduzido da visibilidade e some quando a publicação é `private`.
- **Cópia do código do selo.** `alert()` trocado por estado "Copiado!".

### 5.3 Responsividade (P1)

- **`InspectionLayout.vue` (reescrito).** Antes, o layout era `h-screen overflow-hidden` com três colunas empilhadas no celular, e o painel com "Finalizar inspeção" ficava espremido. Agora:
  - no mobile, a sidebar é uma gaveta: `fixed` + `translate`, com slot `#sidebar="{ close }"`;
  - a página rola normalmente no mobile e mantém `md:h-screen` no desktop;
  - o slot `panel` é renderizado **duas vezes**: abaixo do conteúdo (`md:hidden`) e na coluna direita (`hidden md:flex`). Isso duplica componentes, mas nenhum deles tem `id`; vale verificar;
  - novo slot opcional `mobile-bar`, uma barra fixa embaixo;
  - o container rolável tem `id="inspection-scroll"`, usado por `Inspection/Show.vue` para voltar ao topo.
- **`Inspection/Show.vue`.**
  - Navegação anterior/próxima categoria (`flatCategories`).
  - "Ir para as não respondidas".
  - Contagem de não respondidas no modal de fechamento.
  - Ajuda da escala de respostas.
  - Barra mobile com progresso.
- **`QuestionCard.vue`.**
  - Indicador "Salvo ✓".
  - Opções em grade 2×2 com altura mínima de 48px.
  - `role="radiogroup"`/`radio` e `aria-checked`.
  - Título sem `pr-24` no mobile.
- **Projeto, Rodada, Revisão, Resultados e Comparação.**
  - Cabeçalhos `flex-col` → `lg:flex-row`/`sm:flex-row`, botões com `flex-wrap`, `min-w-0`/`truncate` em nomes e e-mails, `py-6 sm:py-12`.
  - Ações de resultado visíveis sem hover em telas de toque (`sm:opacity-0 sm:group-hover:opacity-100`).
  - Revisão: no mobile, a lista e o detalhe fazem scroll de um para o outro, com botões Anterior/Próxima.
- **`Breadcrumbs.vue`.** `flex-wrap` + truncamento. O cabeçalho das telas de resultado causava 509px de largura em viewport de 390px.
- **Tamanho de fonte.** `text-[10px]`/`text-[9px]` → `text-xs` nos arquivos editados.

### 5.4 Textos e i18n

- **Chaves novas no frontend.** 72 chaves aplicadas nos três `resources/js/locales/*.json` (pt_BR, en, es), que continuam com o mesmo número de chaves (906). Os valores que mudaram de texto:
  - `labels.*.other` → "Não se aplica";
  - `question.specify_*` → justificativa opcional de N/A;
  - `settings.consensus_convergence_desc` e `review.convergence_instructions`: o texto antigo prometia que "avaliadores ajustam suas próprias notas", o que é impossível porque a inspeção fechada é imutável; o comportamento real é a média;
  - `manual.sec_resultados_text4` (fórmula);
  - `round.published_badge`.
- **Chaves que já faltavam e foram criadas.** `common.workspace`, `directory.provenance_consensus_model`.
- **Backend.** ~15 strings fixas (PT/EN misturados) nos controllers foram trocadas por `__('messages.*')`, com 20 chaves novas em `lang/{pt_BR,en,es}/messages.php`. `lang/*/labels.php`: `other` → "Não se aplica"; `medals.no_data` adicionado.
- **Conferir chaves faltantes.** Script ad hoc que procura `t('...')` nos `.vue`/`.js` sem chave em `pt_BR.json`. Só sobra o prefixo dinâmico `labels.divergence.`. Chaves montadas com template literal (`status.${...}`, `round_steps.${step}_title`, `results.medal.${...}`, `labels.visibility.${...}`) foram adicionadas manualmente; confira.

## 6. Dependências (P7) e P8

- **npm.**
  - `npm uninstall @tailwindcss/vite`: não era usado; o projeto roda Tailwind 3 via PostCSS.
  - `npm update` dentro das faixas: vue 3.5.43, vite 7.3.7, axios 1.20, @inertiajs/vue3 2.3.29, playwright 1.64, postcss 8.5.29.
  - Foi preciso `--legacy-peer-deps`: o conflito `@vitejs/plugin-vue@5` × `vite@7` já existia e o `prod.sh` já usava essa flag.
- **composer.** `composer update` dentro das constraints: laravel/framework 12.53 → 12.69, pest 4.7.8, livewire 3.8, inertia-laravel 2.0.28. `filament/filament` está fixado em `"4.0"` e não mudou. Algumas dependências **transitivas de dev** subiram de major permitidas pelos pais (hamcrest 3, phpdocumentor/reflection-docblock 6).
- **Diretório público (N+1).** Nova relação `EvaluationRound::latestSnapshot()` (`latestOfMany`, linha 55) com eager load, que substitui uma query por item. `->questionnaireVersion->version` → `?->version_number`: a coluna nunca se chamou `version`, então o valor era sempre vazio.
- **Comparação.** `ComparisonService::comparePayloads` casa seção/categoria/questão por ID e cai para a posição quando o ID não existe na base (versões de questionário diferentes têm IDs diferentes). O delta é `null` quando um dos lados é `null`.
- **E-mail de revisão.** Já implementava `ShouldQueue`, então não foi alterado. A análise inicial dizia o contrário; isso foi corrigido.
- **Docs.** `docs/02-business-rules/br_001_scoring_formula.md` e `br_002_divergence_resolution.md` atualizados para as regras novas.

## 7. Pendências conhecidas (não resolvidas)

- **`npm audit`: 7 alertas** (braces, micromatch, fast-glob, postcss-selector-parser) na cadeia de build do Tailwind 3. Só são corrigidos com Tailwind 4 (major), fora do escopo.
- **Questões em inglês na revisão.** No banco de teste, o texto das questões na tela de revisão saiu em inglês. Esse texto vem de `question_text` gravado no snapshot no fechamento da inspeção (`$question->text` no locale do processo). A causa não foi investigada.
- **Componente morto.** `DivergencePanel.vue` não é importado em lugar nenhum e não foi tocado.
- **Rótulos genéricos na revisão.** As notas dos avaliadores usam `labels.answer_level.*` (Alto/Médio/Baixo), não os rótulos do perfil da seção (Suficiente/…). Comportamento anterior, não alterado.

## 8. Sugestões de foco para a revisão

1. `CloseRoundAction::calculateRoundPayload`: indexação por `question_id` e tratamento de `null` com os modelos de consenso. O `MajorityVoteStrategy` pode retornar `'other'` → score `null`.
2. Heredoc do script do selo em `RoundBadgeController`: não deve existir `$` interpolado além de `{$apiUrl}` e nenhuma rota até `innerHTML`.
3. `AuthenticatedLayout`: interação entre o `watch` de `errors` e formulários com campos chamados `status`/`role`/`message`.
4. `InspectionLayout`: slot `panel` duplicado e acessibilidade da gaveta (foco não é aprisionado; Esc não fecha).
5. Coerência das traduções em `en`/`es` (foram escritas manualmente).

## 9. Segunda etapa: página inicial e área não logada

### 9.1 Correções e responsividade

| Item | O que mudou | Por quê | Onde |
|---|---|---|---|
| Menu da página inicial | Os links de seção viram um menu recolhível (☰) abaixo de `md`. "Método Mitra" e o seletor de idioma saem da barra em telas pequenas. Os botões usam `whitespace-nowrap`. | Em 390px, "Método Mitra" ficava por cima do logotipo e "Cadastre-se" quebrava em duas linhas. | `Pages/Welcome.vue` |
| Menu do layout público | Mesmo padrão de menu recolhível. | Abaixo de `md`, "Manual" e "Diretório" ficavam escondidos sem alternativa. | `Layouts/PublicLayout.vue` |
| Ordenação do diretório | A leitura dos filtros iniciais aceita só strings. | **Bug.** Sem query string, o servidor envia `filters` como `[]` e `[].sort` é `Array.prototype.sort`. A ordenação recebia uma função: o seletor ficava em branco e o parâmetro sumia da URL. | `Pages/PublicDirectory/Index.vue` (`initialFilter`) |
| Filtros do diretório | Empilhados no celular, com `aria-label`. | O texto "Todas as medalhas" era cortado. | idem |
| Versões no rodapé | Removidas as props `laravelVersion`/`phpVersion` da rota `/` e a linha "Desenvolvido com Laravel vX & PHP vY". | Exibir as versões em página pública facilita a busca por vulnerabilidades conhecidas. | `routes/web.php`, `Pages/Welcome.vue`, `Layouts/PublicLayout.vue` (props não usadas) |
| Ilustrações e espaçamentos | Ilustrações limitadas a 16rem e hero com menos espaço vertical no celular. | Reduzir a rolagem no celular. | `Pages/Welcome.vue` |

### 9.2 Textos (estilo do autor)

284 chaves `pt_BR` das áreas `welcome`, `mitra`, `manual`, `auth`, `terms`, `privacy`, `directory` e `footer`, mais `nav.manual` e `nav.public_directory`, foram reescritas no estilo do autor (skill `estilo-della-mura`):

- registro impessoal com passiva sintética;
- títulos e botões sem maiúsculas em todas as palavras;
- sem travessões;
- sem primeira pessoa ("Acreditamos", "enviaremos", "Nossa inspeção").

A regra era **não mudar o sentido**. As validações aplicadas na reescrita:

- placeholders `{...}` e tags HTML idênticos aos originais;
- nenhum sinal de escrita de IA da lista da skill.

Exceções em que o sentido mudou, por o texto contradizer o sistema (traduzidas também para `en`/`es`):

| Chave | Mudança |
|---|---|
| `manual.sec_convites_text2` | Conta existente agora exige login (consequência de S1). |
| `manual.sec_rodadas_text2` | Inclui a fase de revisão no ciclo de vida. |
| `manual.sec_rodadas_text4` | Desvio padrão em vez de variância; na Convergência ninguém altera a própria nota, a questão recebe a média. |
| `manual.sec_questionario_text2` | Inclui a resposta "Não se aplica". |
| `manual.sec_exportacao_text1` e `text2` | Exportação completa só para o dono (consequência de S3). |

Também foram corrigidos os rótulos de botão citados no manual, que não existiam na interface ("Nova Rodada", "Fechar Rodada", "Exportar JSON"), e o local de criação da inspeção (tela da rodada).

### 9.3 Textos incorretos corrigidos por decisão do dono (pt_BR, en, es)

- **`welcome.feature2_text`:** "requisitos fiscais" → "requisitos legais". A LGPD não trata de requisitos fiscais. O espanhol dizia "normativos" e passou a dizer "legales".
- **`welcome.feat_export_title`/`feat_export_text`:** a menção a temas e a "modo claro/escuro" foi removida, porque o sistema só tem o tema claro (nenhuma classe `dark:`). O título passou a ser "Exportação universal".
- **`manual.sec_diretorio_text2`** e **`MANUAL.md`** (linha ~355): o filtro por "versão do questionário" foi removido. A interface do diretório não tem esse filtro, embora o backend ainda aceite `?version=`.
