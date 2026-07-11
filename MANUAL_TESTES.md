# Manual de Execução dos Testes Automatizados E2E (Playwright)

Este manual descreve a estrutura, o fluxo de execução e a forma de rodar a suíte de testes de ponta a ponta (E2E) do sistema **Mitra**.

A suíte valida o fluxo completo de uma rodada de avaliação desde o cadastro/login dos membros até a consolidação final da rodada e geração do selo de privacidade.

---

## 📋 Pré-requisitos e Preparação do Ambiente

Antes de iniciar os testes, garanta que os seguintes passos foram concluídos no seu ambiente de desenvolvimento:

### 1. Dependências do Frontend e Backend
Certifique-se de que as dependências do Node.js e do Composer estejam instaladas:
```bash
npm install
composer install
```

### 2. Banco de Dados e Servidor Local
Os testes rodam integrados com a aplicação local. Inicie o servidor do Laravel na porta `8000`:
```bash
php artisan serve --port=8000
```

### 3. Compilação dos Assets (Crítico)
Os testes utilizam os arquivos Vue compilados em produção. Sempre que alterar componentes Vue do frontend, recompile os arquivos antes de rodar os testes:
```bash
npm run build
```

---

## 🚀 Script Auxiliar (Recomendado)

Criamos um script bash `run_tests.sh` que facilita o processo de compilação dos assets e execução dos testes, salvando as evidências automaticamente em uma pasta customizada informada via parâmetro.

```bash
# Permissão de execução (se necessário)
chmod +x run_tests.sh

# Rodar os testes salvando as capturas em uma pasta específica
./run_tests.sh ./pasta-minhas-evidencias
```

---

## ⚡ Comandos Nativos para Executar os Testes

Com o servidor rodando em `http://127.0.0.1:8000`, utilize a CLI do Playwright na raiz do projeto:

### Executar toda a suíte de testes (3 cenários sequenciais)
```bash
npx playwright test
```

### Executar um cenário de consenso específico
Você pode filtrar os testes usando a flag `-g` seguido pelo nome do consenso correspondente:

* **Para rodar o modelo Decisão do Dono:**
  ```bash
  npx playwright test -g "owner_decides"
  ```

* **Para rodar o modelo Voto da Maioria:**
  ```bash
  npx playwright test -g "majority_vote"
  ```

* **Para rodar o modelo Convergência de Avaliadores:**
  ```bash
  npx playwright test -g "evaluator_convergence"
  ```

### Executar em Modo Visual (UI do Playwright)
A interface interativa do Playwright permite depurar o teste passo a passo, ver telas renderizadas em tempo real e inspecionar a rede:
```bash
npx playwright test --ui
```

---

## 🔄 Cenários e Fluxos Cobertos

Cada um dos três testes executa a simulação completa de uma rodada de avaliação com **5 membros ativos** (1 Dono e 4 Avaliadores). Os fluxos de banco de dados são semeados dinamicamente antes de cada teste com a classe `E2ETestSeeder`.

### 1. Caso `owner_decides` (Decisão do Dono)
* **Objetivo**: Validar a resolução manual e publicação imediata no diretório.
* **Passos**:
  1. Todos os 5 membros preenchem o questionário (a primeira resposta diverge entre eles).
  2. O Dono loga, acessa a revisão, escolhe manualmente a nota **Alto** para resolver a divergência e confirma a mensagem de sucesso.
  3. Preenche a conclusão final, ativa o checkbox "Publicar imediatamente" e fecha a rodada.
  4. O teste navega até o diretório público (`/tools`) e valida a exibição do projeto.

### 2. Caso `majority_vote` (Voto da Maioria)
* **Objetivo**: Validar a consolidação automática com base nos votos da maioria e geração de selo.
* **Passos**:
  1. Os avaliadores respondem de forma a induzir um empate na questão 1 (2 votos Médio, 2 votos Baixo, 1 Alto).
  2. O Dono revisa a rodada e confirma que a nota consolidada foi resolvida de forma conservadora para **Baixo**.
  3. A rodada é fechada e o selo oficial em PDF é gerado e baixado.

### 3. Caso `evaluator_convergence` (Convergência dos Avaliadores)
* **Objetivo**: Validar a facilitação de discussões via chat de equipe.
* **Passos**:
  1. O Dono identifica uma divergência, escreve um comentário de sensemaking no painel de discussão do chat.
  2. O modal de sucesso do comentário é fechado pelo teste.
  3. O Dono digita o diagnóstico e finaliza a rodada com a nota média da rodada calculada automaticamente.

---

## 📂 Análise de Evidências

Todas as capturas de tela geradas nos momentos chaves de cada rodada são salvas no diretório local de evidências:
`C:\Users\wellington\.gemini\antigravity-ide\brain\94e4aa89-bbc9-40f9-8174-c0697d8320f2\evidences\`

### Lista de arquivos de imagem gerados:
* `1_filled_questionnaire_[consenso]_user_[index].png` — Telas de preenchimento de cada um dos avaliadores.
* `2_review_dashboard_divergence_owner_decides.png` — Painel de controle do Dono com os cards de divergência pendentes.
* `3_resolved_divergence_owner_decides.png` — Ação do Dono selecionando a resposta final.
* `4_closed_round_dashboard_owner_decides.png` — Visão geral da rodada após o fechamento.
* `5_public_directory_owner_decides.png` — Confirmação do projeto publicado no diretório de transparência.
* `6_review_majority_tie_majority_vote.png` — Painel com empate resolvido automaticamente pela maioria.
* `7_closed_round_majority_majority_vote.png` — Rodada fechada sob o modelo de voto majoritário.
* `7_badge_generated_majority_vote.png` — O selo PDF renderizado após o fechamento.
* `8_review_discussion_chat_evaluator_convergence.png` — Histórico do chat de debate com a observação do dono.
* `9_closed_round_convergence_evaluator_convergence.png` — Rodada fechada sob a convergência da equipe.

---

## 🛠️ Resolução de Problemas Comuns (Troubleshooting)

#### 1. Erro: "locator.click: Clicking the checkbox did not change its state" ou cliques ignorados
* **Motivo**: O modal global de alerta de sucesso (`AlertModal`) está visível na tela e bloqueando a interação com elementos no fundo.
* **Correção**: Certifique-se de que o teste aguarda e fecha o modal clicando em `"OK"` após ações como resolver respostas ou postar comentários.

#### 2. Erro de redirecionamento ou URL após login
* **Motivo**: O teste espera a rota `/projects`, mas a aplicação redireciona para `/dashboard`.
* **Correção**: A URL de redirecionamento foi flexibilizada para aceitar ambas na regex `/\/(projects|dashboard)/`.

#### 3. Banco de dados travado ("database is locked")
* **Motivo**: O SQLite impede múltiplas escritas simultâneas se houver processos concorrentes.
* **Correção**: Os testes foram configurados no `playwright.config.js` para rodar com `workers: 1` sequencialmente. Certifique-se de fechar sessões de terminal travadas ou processos paralelos de testes.
