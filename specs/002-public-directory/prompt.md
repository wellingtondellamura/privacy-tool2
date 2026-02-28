## Módulo 002 — Public Directory

Você é um agente especialista em:

* Laravel
* Arquitetura orientada a domínio
* Implementação incremental segura
* Execução baseada em SDD + BDD
* Preservação de integridade histórica

---

# 📁 CONTEXTO NORMATIVO

Arquivos oficiais da funcionalidade:

* `specs/002-public-directory/spec.md`
* `specs/002-public-directory/features/`

Hierarquia obrigatória:

1. spec.md
2. features/
3. spec.md global (caso exista)
4. demais especificações

Você NÃO pode reinterpretar spec.md.
Você NÃO pode alterar regras.
Você NÃO pode ampliar escopo.

---

# 🎯 OBJETIVO

Implementar o módulo de publicação e diretório público de forma incremental, segura e validada.

---

# 🚨 REGRAS ABSOLUTAS

Você NÃO pode:

* Alterar snapshot
* Recalcular score
* Alterar inspection.status
* Permitir publicação de inspection != closed
* Expor respostas individuais
* Expor user_id
* Expor observações textuais
* Quebrar regras existentes
* Alterar contratos existentes

---

# 🧱 ESTRATÉGIA DE EXECUÇÃO

## Ordem obrigatória de implementação:

1. Modelagem de domínio
2. Policies
3. Services
4. Endpoints autenticados
5. Endpoints públicos
6. Filtros e ordenação
7. Validação de integridade
8. Hardening

Cada fase possui checkpoint obrigatório.

---

# 🔹 FASE 1 — MODELAGEM DE DOMÍNIO

## Microtarefas

* [ ] Criar migration InspectionPublication
* [ ] Criar enum visibility
* [ ] Criar Model InspectionPublication
* [ ] Criar relação 1:1 com Inspection
* [ ] Criar unique index inspection_id
* [ ] Adicionar slug público na Inspection (se necessário)
* [ ] Criar factory básica

---

## CHECKPOINT 1

Validar:

* Migration roda e rollback funciona
* Nenhuma tabela existente foi alterada indevidamente
* Nenhum teste existente falhou
* Model se relaciona corretamente

Se falhar → corrigir antes de avançar.

---

# 🔹 FASE 2 — POLICY E AUTORIZAÇÃO

## Microtarefas

* [ ] Criar PublicationPolicy
* [ ] Permitir apenas owner publicar
* [ ] Bloquear non-owner
* [ ] Bloquear publicação se status != closed
* [ ] Registrar Gate

---

## CHECKPOINT 2

Validar via testes:

* Owner pode publicar closed
* Non-owner recebe 403
* Active inspection não pode publicar
* Nenhuma rota pública exposta ainda

---

# 🔹 FASE 3 — PUBLICATION SERVICE

## Microtarefas

* [ ] Criar PublicationService
* [ ] Implementar publish()
* [ ] Implementar updateVisibility()
* [ ] Implementar revoke()
* [ ] Garantir uso exclusivo de snapshot consolidado
* [ ] Garantir que nenhum cálculo seja executado

---

## CHECKPOINT 3

Validar:

* Publicação cria registro
* published_at é preenchido
* Snapshot não é alterado
* Nenhum score é recalculado
* Testes existentes continuam verdes

---

# 🔹 FASE 4 — ENDPOINTS AUTENTICADOS

## Microtarefas

* [ ] POST /inspections/{id}/publish
* [ ] PUT /inspections/{id}/publish
* [ ] DELETE /inspections/{id}/publish
* [ ] Validação de enum
* [ ] Validação de policy
* [ ] Retornar DTO seguro

---

## CHECKPOINT 4

Rodar cenários em:

```
specs/002-public-directory/features/publication.feature
```

Todos devem passar.

---

# 🔹 FASE 5 — DIRETÓRIO PÚBLICO

## Microtarefas

* [ ] Criar rota GET /tools
* [ ] Criar rota GET /tools/{slug}
* [ ] Filtrar visibility != private
* [ ] Implementar paginação
* [ ] Implementar ordenação por score desc
* [ ] Implementar filtros por medalha
* [ ] Implementar filtro por ano
* [ ] Criar DTO público seguro
* [ ] Remover qualquer campo sensível

---

## CHECKPOINT 5

Rodar cenários em:

```
specs/002-public-directory/features/public_directory.feature
```

Validar:

* Private retorna 404
* score_public retorna resumo
* full_public retorna relatório consolidado
* Nenhum user_id aparece na resposta

---

# 🔹 FASE 6 — INTEGRIDADE

## Microtarefas

* [ ] Garantir que publicação nunca altera snapshot
* [ ] Garantir que publication não altera inspection.status
* [ ] Garantir que slug é determinístico
* [ ] Garantir que múltiplas publicações não criam duplicação

---

## CHECKPOINT 6

Rodar cenários em:

```
specs/002-public-directory/features/publication_integrity.feature
```

Confirmar:

* Snapshot imutável
* Sem recalculo
* Sem vazamento de dados

---

# 🔹 FASE 7 — HARDENING

## Microtarefas

* [ ] Adicionar índices para performance
* [ ] Adicionar cache opcional para /tools
* [ ] Garantir que visibility private não retorna 403 (deve ser 404)
* [ ] Validar ausência de N+1
* [ ] Garantir que slug não colide

---

## CHECKPOINT FINAL GLOBAL

Antes de finalizar:

* Rodar todos testes existentes
* Rodar testes do módulo 002
* Confirmar que snapshots antigos continuam intactos
* Confirmar que nenhuma regra matemática foi alterada
* Confirmar que nenhuma regra de autorização foi comprometida
* Confirmar que não há campos sensíveis nas respostas públicas

Se qualquer validação falhar → rollback da fase correspondente.

---

# 🔁 MODO DE OPERAÇÃO OBRIGATÓRIO

Você deve:

1. Executar Fase 1
2. Executar Checkpoint 1
3. Confirmar integridade
4. Avançar
5. Nunca implementar todas as fases simultaneamente
6. Nunca ignorar um checkpoint

---

# 🧠 INSTRUÇÃO FINAL

Comece lendo:

```
specs/002-public-directory/spec.md
```

Depois:

```
specs/002-public-directory/features/
```

Implemente Fase 1.
Pare.
Valide.
Reporte.
Aguarde confirmação.
Continue.

Execute incrementalmente.
Preserve determinismo.
Preserve imutabilidade.
Nunca exponha dados sensíveis.
