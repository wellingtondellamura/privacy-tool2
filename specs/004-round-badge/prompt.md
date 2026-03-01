## Módulo 004 — RoundBadge (Selo Embeddable)

Você é um agente especialista em:

* Laravel
* Refatoração incremental segura
* Execução orientada a SDD + BDD
* Vue 3 + Inertia
* Preservação de determinismo e imutabilidade

---

# 📁 CONTEXTO NORMATIVO

Arquivos oficiais:

```
specs/004-round-badge/spec.md
specs/004-round-badge/features/
```

Plano incremental definido anteriormente.

Hierarquia de autoridade:

1. spec.md
2. features/
3. Plano incremental com checkpoints
4. spec.md global
5. ux.md

Você NÃO pode reinterpretar spec.md.
Você NÃO pode alterar regras matemáticas.
Você NÃO pode recalcular snapshot.
Você NÃO pode expor dados sensíveis.

---

# 🎯 OBJETIVO

Implementar o módulo RoundBadge completo, incluindo:

Backend:

* Model
* Migration
* Service
* Policies
* Endpoints autenticados
* Endpoints públicos
* Script embeddable
* Rate limiting
* Cache

Frontend (área autenticada):

* Interface de geração de selo
* Revogação
* Seleção de estilo
* Preview
* Código embeddable para copiar

Sem quebrar:

* EvaluationRound
* RoundSnapshot
* Publicação
* Diretório público
* Comparação histórica

---

# 🚨 REGRAS ABSOLUTAS

Você NÃO pode:

* Recalcular score
* Alterar snapshot
* Alterar RoundSnapshot
* Permitir badge para round não publicada
* Permitir badge para visibility private
* Expor user_id
* Expor respostas individuais
* Alterar fórmulas matemáticas
* Implementar lógica de negócio no frontend

Frontend apenas consome dados.

---

# 🔁 MODO DE EXECUÇÃO OBRIGATÓRIO

Implementar por fases.

Após cada fase:

1. Rodar testes existentes
2. Rodar Gherkin do módulo
3. Validar integridade
4. Confirmar ausência de regressão
5. Confirmar snapshot não foi alterado
6. Só então avançar

Nunca implementar múltiplas fases simultaneamente.

---

# 🔷 FASE 0 — BASELINE

* Rodar todos testes
* Registrar hash de RoundSnapshot.payload_json
* Confirmar estabilidade

CHECKPOINT 0 obrigatório.

---

# 🔷 FASE 1 — MODELAGEM BACKEND

Microtarefas:

* Criar migration round_badges
* Criar model RoundBadge
* Criar relação 1:1 com EvaluationRound
* Criar índice unique public_token
* Criar enum style
* Adicionar unique constraint evaluation_round_id

CHECKPOINT 1:

* Migration reversível
* Nenhum teste existente falhou
* Nenhum snapshot alterado

---

# 🔷 FASE 2 — SERVICE + POLICY

Microtarefas:

* Criar RoundBadgeService
* Implementar:

  * createBadge()
  * revokeBadge()
  * updateStyle()
* Validar elegibilidade:

  * status = published
  * visibility != private
* Criar RoundBadgePolicy (owner-only)

CHECKPOINT 2:

* Rodar features de criação e revogação
* Confirmar que não há recalculo
* Confirmar que snapshot permanece idêntico

---

# 🔷 FASE 3 — ENDPOINTS PRIVADOS

Microtarefas:

* POST /rounds/{id}/badge
* DELETE /rounds/{id}/badge
* PUT /rounds/{id}/badge/style
* Validar policy
* Retornar DTO seguro

CHECKPOINT 3:

* Gherkin de criação passa
* Gherkin de revogação passa
* Non-owner recebe 403

---

# 🔷 FASE 4 — ENDPOINTS PÚBLICOS

Microtarefas:

* GET /badge/{token}
* GET /badge/{token}.js
* Implementar JSON seguro
* Implementar script embeddable
* Implementar 404 para inválidos
* Aplicar rate limit
* Aplicar cache 5 minutos

CHECKPOINT 4:

* Gherkin público passa
* Badge nunca expõe user_id
* Badge usa exclusivamente RoundSnapshot
* Badge retorna 404 para inválidos

---

# 🔷 FASE 5 — FRONTEND (ÁREA AUTENTICADA)

Local: Página da EvaluationRound publicada.

Adicionar seção:

```
Selo Embeddable
```

### Comportamento:

Se round.status != published:

* Não mostrar seção

Se round.visibility = private:

* Mostrar aviso explicando que selo não pode ser criado

Se elegível:

* Botão "Gerar selo"
* Exibir preview visual
* Exibir código:

```html
<script src="https://SEUDOMINIO/badge/{token}.js"></script>
```

* Botão "Copiar código"
* Dropdown de estilo
* Botão "Revogar selo"

### Regras:

* Nenhum cálculo no frontend
* Apenas consumir endpoints
* Atualizar UI conforme resposta backend
* Confirmar revogação com modal
* Não expor token em logs
* Não renderizar controles para non-owner

CHECKPOINT 5:

* Owner consegue gerar selo
* Non-owner não vê controles
* Revogação remove preview
* Nenhum cálculo duplicado
* Nenhum dado sensível visível

---

# 🔷 FASE 6 — HARDENING FINAL

Microtarefas:

* Confirmar que badge não sobrevive se visibility mudar para private
* Confirmar que badge não sobrevive se round deixar de ser published
* Confirmar que token antigo não funciona após revogação
* Confirmar que cache invalida corretamente
* Confirmar ausência de N+1

CHECKPOINT FINAL:

* Todos testes verdes
* Todos Gherkin verdes
* Hash de snapshot idêntico ao baseline
* Nenhuma regressão no diretório público
* Nenhuma regressão em comparação histórica
* Nenhuma alteração em cálculo

---

# 🎨 UX OBRIGATÓRIA (APLICAR ux.md)

* Preview visual claro
* Microinterações suaves
* Feedback imediato após gerar selo
* Feedback imediato após revogar
* Botão copiar com confirmação visual
* Explicação clara do que o selo exibe
* Estados disabled adequados
* Não depender apenas de cor

---

# 🔐 SEGURANÇA FRONTEND

* Nunca confiar no frontend para elegibilidade
* Backend sempre valida
* Nunca renderizar user_id
* Nunca incluir payload completo no JS
* Nunca expor diagnóstico completo no selo

---

# 📌 INSTRUÇÃO FINAL

1. Comece pela Fase 0.
2. Execute incrementalmente.
3. Não pule checkpoints.
4. Preserve determinismo.
5. Preserve imutabilidade.
6. Preserve integridade histórica.

Nunca implemente todas fases simultaneamente.

---

# 🧩 RESULTADO ESPERADO

Após implementação:

```
Project
 └── EvaluationRound
       ├── RoundSnapshot
       ├── Publication
       └── RoundBadge
```

Com:

* Selo seguro
* Script embeddable
* Revogável
* Determinístico
* Integrado ao frontend
* Totalmente testado

---