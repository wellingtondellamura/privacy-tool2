# 📘 PARTE 1 — PLANO INCREMENTAL COM CHECKPOINTS

## Refatoração para EvaluationRound

Premissa:

* Sistema atual está estável.
* Snapshots existem.
* Publicação por Inspection existe.
* Diretório público existe.

Objetivo:

* Introduzir EvaluationRound
* Migrar dados
* Preservar tudo
* Não quebrar publicação existente
* Não recalcular nada

---

# 🔷 FASE 0 — PRÉ-REFATORAÇÃO (Baseline)

### Objetivo

Congelar estado atual e validar integridade antes de mexer.

### Ações

* [ ] Rodar todos testes existentes
* [ ] Validar snapshots históricos
* [ ] Exportar exemplo real de snapshot consolidado
* [ ] Registrar hash de payload_json para comparação futura

### CHECKPOINT 0

Confirmar:

* Todos testes verdes
* Hash de snapshot registrado
* Nenhuma inconsistência

Não avançar sem baseline.

---

# 🔷 FASE 1 — INTRODUÇÃO DE EvaluationRound (SEM USO)

### Objetivo

Criar nova entidade sem alterar fluxo atual.

### Microtarefas

* [ ] Criar migration evaluation_rounds
* [ ] Criar model EvaluationRound
* [ ] Criar RoundSnapshot
* [ ] Criar relações:

  * Project → EvaluationRounds
  * EvaluationRound → Inspections (nullable por enquanto)
* [ ] Não alterar Inspection ainda

### CHECKPOINT 1

Validar:

* Migration reversível
* Nenhuma tabela antiga alterada
* Nenhum teste falhou
* Sistema funciona igual antes

Se qualquer regressão → rollback.

---

# 🔷 FASE 2 — VINCULAR INSPECTIONS A ROUNDS

### Objetivo

Introduzir vínculo sem ativar regra nova.

### Microtarefas

* [ ] Adicionar coluna inspection.evaluation_round_id nullable
* [ ] Criar default round automática para novos projetos
* [ ] Ao criar nova Inspection, associar round ativa
* [ ] Manter lógica antiga intacta

### CHECKPOINT 2

Validar:

* Inspections novas possuem round
* Inspections antigas continuam funcionando
* Publicação antiga ainda funciona
* Snapshots antigos intactos
* Testes continuam verdes

---

# 🔷 FASE 3 — MIGRAÇÃO DE DADOS EXISTENTES

### Objetivo

Criar rodadas retroativas.

### Microtarefas

* [ ] Para cada projeto existente:

  * Criar EvaluationRound "Rodada Inicial"
  * status = closed
  * visibility = private
* [ ] Associar inspeções existentes
* [ ] Criar RoundSnapshot usando snapshot consolidado já existente
* [ ] NÃO recalcular

### CHECKPOINT 3

Validar:

* Hash antigo do snapshot == hash novo
* Nenhum score mudou
* Nenhum registro foi duplicado
* Publicação antiga ainda retorna mesmos dados

Se houver qualquer divergência → abortar.

---

# 🔷 FASE 4 — DESACOPLAR PUBLICAÇÃO DE INSPECTION

### Objetivo

Migrar publicação para EvaluationRound.

### Microtarefas

* [ ] Criar RoundPublicationService
* [ ] Ajustar endpoints para usar round
* [ ] Atualizar diretório público para listar rounds
* [ ] Manter endpoints antigos temporariamente compatíveis

### CHECKPOINT 4

Validar:

* Diretório público mostra mesmas informações que antes
* Nenhuma inspeção individual exposta
* Publicação funciona via round
* Nenhum snapshot recalculado

---

# 🔷 FASE 5 — FECHAMENTO DE RODADA

### Objetivo

Mover consolidação para Round.

### Microtarefas

* [ ] Criar RoundConsolidationService
* [ ] Ao fechar rodada:

  * Consolidar inspections
  * Criar RoundSnapshot
* [ ] Bloquear edição após closed
* [ ] Manter Inspection intacta

### CHECKPOINT 5

Validar:

* Consolidado round == consolidado anterior
* Diagnóstico salvo corretamente
* Inspections bloqueadas
* Snapshots intactos

---

# 🔷 FASE 6 — COMPARAÇÃO ENTRE ROUNDS

### Microtarefas

* [ ] Criar RoundComparisonService
* [ ] Permitir comparar 2 ou N rounds
* [ ] Garantir uso exclusivo de RoundSnapshot

### CHECKPOINT 6

Validar:

* Comparação usa apenas snapshots
* Nenhum recalculo ocorre
* Histórico ordenado corretamente

---

# 🔷 FASE 7 — REMOÇÃO DO MODELO ANTIGO

Somente após todos checkpoints:

* [ ] Remover publicação por inspection
* [ ] Remover endpoints antigos
* [ ] Atualizar testes
* [ ] Garantir retrocompatibilidade final

### CHECKPOINT FINAL

* Todos testes verdes
* Snapshots antigos intactos
* Diretório funcionando
* Comparação funcionando
* Nenhuma fórmula alterada
* Nenhuma regra de domínio duplicada