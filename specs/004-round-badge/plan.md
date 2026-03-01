# 📘 PARTE 3 — PLANO INCREMENTAL COM CHECKPOINTS

---

# 🔷 FASE 0 — BASELINE

* [ ] Rodar todos testes existentes
* [ ] Registrar hash de RoundSnapshot.payload_json

### CHECKPOINT 0

Todos testes verdes.
Hash registrado.

---

# 🔷 FASE 1 — MODELAGEM

* [ ] Criar migration round_badges
* [ ] Criar model RoundBadge
* [ ] Criar relação com EvaluationRound
* [ ] Criar índice unique token

### CHECKPOINT 1

Validar:

* Migration reversível
* Nenhuma regressão
* Nenhuma alteração em snapshot

---

# 🔷 FASE 2 — SERVICE

* [ ] Criar RoundBadgeService
* [ ] Implementar create()
* [ ] Implementar revoke()
* [ ] Garantir uso exclusivo de snapshot

### CHECKPOINT 2

Rodar testes de criação.
Confirmar que não há recalculo.

---

# 🔷 FASE 3 — ENDPOINTS PRIVADOS

* [ ] POST /rounds/{id}/badge
* [ ] DELETE /rounds/{id}/badge
* [ ] Policy owner-only

### CHECKPOINT 3

Validar via Gherkin de criação e revogação.

---

# 🔷 FASE 4 — ENDPOINTS PÚBLICOS

* [ ] GET /badge/{token}
* [ ] GET /badge/{token}.js
* [ ] Implementar 404 para inválidos
* [ ] Aplicar rate limit

### CHECKPOINT 4

Rodar Gherkin de acesso público.
Validar ausência de dados sensíveis.

---

# 🔷 FASE 5 — HARDENING

* [ ] Cache 5 min
* [ ] Sanitização
* [ ] Confirmar ausência de user_id
* [ ] Confirmar uso exclusivo de RoundSnapshot

### CHECKPOINT FINAL

* Todos testes verdes
* Nenhum snapshot alterado
* Nenhuma regra matemática alterada
* Nenhum dado sensível exposto
