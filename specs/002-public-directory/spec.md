## Módulo: Publicação de Inspeções e Diretório Público

**Versão:** 1.0
**Dependência:** Backend já validado e imutável
**Objetivo:** Permitir publicação controlada de inspeções fechadas

---

# 1️⃣ OBJETIVO DO MÓDULO

Permitir que o owner de um projeto:

* Publique uma inspeção fechada
* Escolha o nível de visibilidade
* Disponibilize publicamente score ou relatório consolidado
* Revogue a publicação a qualquer momento

Sem:

* Alterar snapshot
* Alterar score
* Alterar histórico
* Expor respostas individuais

---

# 2️⃣ NOVO MODELO DE DOMÍNIO

Criar entidade:

```php
InspectionPublication
```

### Campos:

* id
* inspection_id (unique)
* visibility (enum)

  * private
  * score_public
  * full_public
* published_at (nullable)
* published_by (user_id)
* created_at
* updated_at

---

# 3️⃣ REGRAS DE NEGÓCIO

## RN-PUB-01 — Elegibilidade

Somente inspeções com:

```text
status = closed
```

podem ser publicadas.

---

## RN-PUB-02 — Controle de Autoridade

Somente:

```text
ProjectMember.role = owner
```

pode:

* Criar publicação
* Alterar visibilidade
* Revogar publicação

---

## RN-PUB-03 — Snapshot como Fonte Única

Publicação deve:

* Ler exclusivamente o snapshot consolidado
* Nunca recalcular scores
* Nunca acessar respostas individuais

---

## RN-PUB-04 — Visibilidade

### visibility = private

* Nenhum dado exposto publicamente
* Inspeção invisível no diretório

---

### visibility = score_public

Expor apenas:

* Nome da ferramenta
* URL da ferramenta
* Score geral consolidado
* Medalhas por seção
* Data da inspeção
* Versão do questionário
* Número de avaliadores

Não expor:

* Divergência
* Percentuais detalhados
* Observações
* Identidade dos avaliadores
* Respostas individuais

---

### visibility = full_public

Expor:

* Todos dados do snapshot consolidado
* Scores por seção
* Percentuais
* Medalhas
* Divergência agregada
* Explicação metodológica

Nunca expor:

* user_id individuais
* Observações textuais individuais

---

## RN-PUB-05 — Revogação

Owner pode:

* Alterar de score_public → private
* Alterar de full_public → private
* Alterar de score_public → full_public
* Alterar de full_public → score_public

Mudança não altera snapshot.

---

## RN-PUB-06 — Histórico

Publicação não altera inspeção.
Publicação não cria nova versão.
Publicação não altera status.

---

# 4️⃣ ENDPOINTS

## Autenticados

```
POST /inspections/{id}/publish
PUT  /inspections/{id}/publish
DELETE /inspections/{id}/publish
```

---

## Públicos

```
GET /tools
GET /tools/{slug}
```

---

# 5️⃣ DIRETÓRIO PÚBLICO

## /tools

Lista paginada contendo apenas inspeções com:

```text
visibility != private
```

Ordenação padrão:

* Score desc
* Data desc

Filtros:

* Medalha
* Ano
* Versão

---

## /tools/{slug}

Slug baseado em:

* project name
* inspection id

Se visibility = score_public:

* Mostrar resumo

Se visibility = full_public:

* Mostrar relatório completo

Se private:

* Retornar 404

---

# 6️⃣ SEGURANÇA

* Nunca retornar inspeções private
* Nunca retornar respostas individuais
* Nunca retornar user_id
* Nunca retornar observações

---

# 7️⃣ INTEGRIDADE

Publicação:

* Não cria novo snapshot
* Não recalcula nada
* Apenas referencia snapshot existente

---
