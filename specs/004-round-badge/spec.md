## Módulo: RoundBadge (Selo Embeddable)

**Versão:** 1.0
**Dependência:** EvaluationRound já implementado
**Premissas:**

* RoundSnapshot é imutável
* Publication já existe
* Determinismo é obrigatório

---

# 1️⃣ OBJETIVO

Permitir que uma EvaluationRound publicada gere um selo embeddable verificável que:

* Exiba o score consolidado
* Linke para a página pública da rodada
* Use exclusivamente RoundSnapshot
* Seja automaticamente invalidado se a rodada deixar de ser pública

---

# 2️⃣ MODELO DE DOMÍNIO

## 2.1 Nova Entidade: RoundBadge

```php
RoundBadge
- id
- evaluation_round_id (unique)
- public_token (unique, 32+ chars)
- style (enum: default, compact, minimal)
- is_active (boolean, default true)
- created_at
- updated_at
```

---

## 2.2 Relações

```text
EvaluationRound
 ├── RoundSnapshot
 ├── Publication
 └── RoundBadge (0..1)
```

---

# 3️⃣ REGRAS DE NEGÓCIO

---

## RN-BADGE-01 — Elegibilidade

RoundBadge só pode ser criado se:

* EvaluationRound.status = published
* EvaluationRound.visibility != private

---

## RN-BADGE-02 — Token Público

* Deve ser criptograficamente seguro
* Deve ser único
* Não pode ser previsível
* Não pode usar ID incremental

---

## RN-BADGE-03 — Fonte de Dados

Badge deve usar exclusivamente:

* RoundSnapshot.payload_json

Nunca recalcular.

---

## RN-BADGE-04 — Conteúdo Permitido

Badge pode exibir:

* Nome do projeto
* Nome da rodada
* Score geral
* Medalha
* Data
* Link para página pública

Nunca pode exibir:

* Identidade de avaliadores
* Diagnóstico completo
* Observações textuais individuais
* Dados internos

---

## RN-BADGE-05 — Invalidação Automática

Se:

* Round.visibility mudar para private
* Round.status deixar de ser published
* RoundBadge.is_active = false

Então:

* Endpoint do badge deve retornar 404

---

## RN-BADGE-06 — Revogação

Owner pode:

* Revogar selo (is_active = false)
* Gerar novo token

Token antigo deve se tornar inválido.

---

## RN-BADGE-07 — Imutabilidade

Badge nunca altera:

* Snapshot
* Round
* Publication

---

# 4️⃣ ENDPOINTS

## Autenticados

```
POST   /rounds/{id}/badge
DELETE /rounds/{id}/badge
PUT    /rounds/{id}/badge/style
```

---

## Públicos

```
GET /badge/{token}
GET /badge/{token}.js
```

---

# 5️⃣ SCRIPT EMBEDDABLE

Formato de uso:

```html
<script src="https://privacytool.com/badge/{token}.js"></script>
```

Script deve:

* Buscar dados JSON seguros
* Renderizar DOM
* Inserir link público
* Não expor token internamente

---

# 6️⃣ SEGURANÇA

* Rate limiting no endpoint público
* Cache controlado
* Sanitização de strings
* CSP apropriado
* Assinatura opcional HMAC futura

---

# 7️⃣ INVARIANTES

* Badge nunca recalcula score
* Badge nunca depende de Inspection
* Badge sempre reflete RoundSnapshot
* Badge não sobrevive se round deixar de ser pública
