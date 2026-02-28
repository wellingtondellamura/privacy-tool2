Você é especialista em:

* Refatoração de sistemas Laravel
* Preservação de determinismo
* Migração de dados segura
* Execução incremental com rollback
* Garantia de integridade histórica

---

# CONTEXTO

O sistema atual está estável.

Objetivo:

Refatorar para introduzir EvaluationRound conforme:

```
specs/003-inspection-rounds/spec.md
specs/003-inspection-rounds/plan.md
specs/003-inspection-rounds/features/

```

Sem quebrar:

* Snapshots
* Publicação
* Comparação
* Regras matemáticas
* Integridade histórica

---

# REGRAS ABSOLUTAS

Você NÃO pode:

* Recalcular snapshots existentes
* Alterar fórmulas
* Modificar score
* Alterar dados históricos
* Expor dados sensíveis
* Remover código antigo antes da migração estar validada

---

# MODO DE OPERAÇÃO

Execute FASE por FASE.

Para cada fase:

1. Implementar microtarefas
2. Rodar testes existentes
3. Validar integridade via:

   * Comparação de hash do snapshot
   * Testes Gherkin
   * Testes unitários
4. Confirmar que não há regressão
5. Somente então avançar

Nunca implementar múltiplas fases simultaneamente.

---

# VALIDAÇÃO CRÍTICA

Após cada checkpoint:

* Confirmar que snapshot payload_json permanece byte-identical
* Confirmar que número de registros não mudou indevidamente
* Confirmar que nenhuma rota pública mudou comportamento
* Confirmar que nenhuma policy foi relaxada

Se qualquer divergência for detectada:

* Abort
* Reverter fase
* Corrigir

---

# ORDEM OBRIGATÓRIA

1. Fase 0 — Baseline
2. Fase 1 — Criar entidade
3. Fase 2 — Vincular inspections
4. Fase 3 — Migrar dados
5. Fase 4 — Migrar publicação
6. Fase 5 — Implementar fechamento
7. Fase 6 — Implementar comparação
8. Fase 7 — Limpeza final

---

# INSTRUÇÃO FINAL

Comece pela Fase 0.

Não avance sem validar.

Preserve integridade.

Execute incrementalmente.

Nunca comprometa determinismo.

---

# 🔥 Resultado Esperado

Após refatoração:

```text
Project
 └── EvaluationRound
       ├── Inspections
       ├── RoundSnapshot
       └── Publication
```

Sistema mais maduro.
Histórico semântico.
Comparação robusta.
Publicação institucional.
Zero perda de integridade.
