# 📘 SDD — Evaluation Rounds, Diagnóstico e Publicação Versionada

**Versão:** 3.0
**Objetivo:** Introduzir Baterias de Avaliação (EvaluationRound)
**Natureza:** Refatoração estrutural sem quebrar histórico
**Premissa:** Tudo já está implementado e funcional

---

# 1️⃣ OBJETIVO DO MÓDULO

Transformar o conceito atual de publicação por Inspection em:

> Publicação por EvaluationRound (Bateria de Avaliação)

Cada rodada representa:

* Um ciclo de avaliação
* Um contexto semântico (ex: “Avaliação Inicial”)
* Um diagnóstico textual
* Um snapshot consolidado imutável
* Um conjunto de inspeções individuais

Permitir:

* Múltiplas rodadas por projeto
* Fechamento manual pelo owner
* Publicação granular
* Comparação histórica entre rodadas

---

# 2️⃣ NOVO MODELO DE DOMÍNIO

## 2.1 Nova Entidade: EvaluationRound

```php
EvaluationRound
- id
- project_id
- name (string)
- diagnosis (text)
- status (draft, closed, published)
- visibility (private, consolidated_only, consolidated_and_individual)
- closed_at (nullable)
- published_at (nullable)
- created_by
- created_at
- updated_at
```

---

## 2.2 Ajustes na Inspection

Adicionar:

```php
inspection.evaluation_round_id (nullable inicialmente)
```

Regra:

* Toda nova Inspection deve pertencer a uma EvaluationRound
* Inspections antigas devem ser migradas para uma rodada padrão automática

---

## 2.3 Snapshots

Novo modelo:

```php
RoundSnapshot
- evaluation_round_id
- payload_json (consolidado)
- created_at
```

Reutilizar snapshots individuais já existentes.

---

# 3️⃣ REGRAS DE NEGÓCIO

---

## RN-ROUND-01 — Criação

Owner pode criar nova EvaluationRound com status draft.

---

## RN-ROUND-02 — Inspeções

* Inspeções só podem ser criadas dentro de uma EvaluationRound draft.
* Um membro pode participar de múltiplas rodadas.
* Não há número mínimo obrigatório de inspeções.
* Owner decide quando fechar.

---

## RN-ROUND-03 — Fechamento

Owner pode fechar rodada se:

* Pelo menos 1 Inspection concluída existir.

Ao fechar:

1. Consolidar todas inspections closed da rodada.
2. Gerar RoundSnapshot.
3. Gerar snapshots individuais (se ainda não existirem).
4. Alterar status para closed.
5. Registrar closed_at.

Após closed:

* Inspections não podem ser alteradas.
* Rodada não pode ser reaberta.

---

## RN-ROUND-04 — Diagnóstico

* Pode ser editado enquanto status = draft ou closed.
* Após status = published, diagnóstico torna-se imutável.

---

## RN-ROUND-05 — Publicação

Owner pode publicar rodada closed.

visibility:

* private
* consolidated_only
* consolidated_and_individual

Publicação:

* Não altera snapshot
* Não recalcula nada
* Apenas altera visibility + published_at

---

## RN-ROUND-06 — Revogação

Owner pode:

* Alterar visibility
* Revogar publicação (voltar para private)

Revogação não altera snapshot.

---

## RN-ROUND-07 — Imutabilidade

Após status = closed:

* Inspections bloqueadas
* Snapshot bloqueado
* Estrutura de questionário bloqueada

Após status = published:

* Diagnóstico bloqueado
* Snapshot bloqueado
* Apenas visibility pode mudar

---

# 4️⃣ DIRETÓRIO PÚBLICO ATUALIZADO

---

## /tools

Lista:

Project + EvaluationRound

Ordenação padrão:

* Score desc
* Data desc

Filtros:

* Medalha
* Ano
* Projeto
* Versão do questionário

---

## /tools/{projectSlug}/{roundSlug}

Se private → 404

Se consolidated_only → mostrar:

* Nome da rodada
* Diagnóstico
* Score consolidado
* Medalhas por seção
* Percentual
* Data
* Versão

Se consolidated_and_individual → mostrar adicionalmente:

* Lista de scores individuais anonimizados
* Sem identificação
* Sem observações

---

# 5️⃣ COMPARAÇÃO HISTÓRICA

---

## RoundComparisonService

Comparação entre múltiplas rodadas:

```php
compare(Project $project, array $roundIds)
```

Regras:

* Apenas rodadas closed
* Mesmo projeto
* Usar snapshots consolidados
* Nunca recalcular

Retornar:

* Delta por seção
* Delta por categoria
* Delta global
* Série temporal ordenada

---

## Histórico gráfico

Permitir:

* Comparação entre 2 rodadas
* Comparação entre N rodadas
* Série histórica

---

# 6️⃣ MIGRAÇÃO DO SISTEMA EXISTENTE

---

## Migração obrigatória

1. Criar EvaluationRound padrão para cada projeto existente:

   * name = "Rodada Inicial"
   * status = closed
   * visibility = private
2. Associar inspeções existentes a essa rodada.
3. Gerar RoundSnapshot com base no snapshot consolidado existente.

Sem recalcular.

---

# 7️⃣ ESTADOS DA RODADA

```text
draft → closed → published
```

Transições:

* draft → closed (owner)
* closed → published (owner)
* published → closed (revogação apenas altera visibility)
* Nunca voltar para draft

---

# 8️⃣ SEGURANÇA

* Apenas owner pode fechar rodada
* Apenas owner pode publicar
* Publicação nunca expõe dados individuais identificáveis
* Diagnóstico não pode conter HTML perigoso (sanitizar)

---

# 9️⃣ INTEGRIDADE MATEMÁTICA

* Nenhum cálculo ocorre no momento da publicação
* Snapshot consolidado é única fonte
* Comparação usa apenas snapshots persistidos
* Nunca recalcular retroativamente

---

# 🔟 CONSISTÊNCIA COM IMPLEMENTAÇÃO EXISTENTE

Este SDD:

* Não quebra determinismo
* Não altera fórmula
* Não altera Inspection
* Apenas introduz camada superior
* Mantém snapshots existentes

---

# 1️⃣1️⃣ FUTURAS EXTENSÕES (NÃO IMPLEMENTAR AGORA)

* Anexos no diagnóstico
* Comentários públicos
* Selo embeddable
* API pública

---

# 1️⃣2️⃣ CRITÉRIOS DE ACEITAÇÃO

O sistema está correto quando:

* Rodadas podem ser criadas
* Inspections vinculam-se a rodadas
* Rodada pode ser fechada manualmente
* Snapshot consolidado é gerado
* Diagnóstico é persistido
* Rodadas podem ser publicadas
* Diretório lista rodadas públicas
* Comparação histórica funciona
* Nenhum snapshot antigo foi alterado
* Nenhuma fórmula foi modificada

---

# 🎯 RESULTADO

Agora o sistema passa a ter:

* Histórico semântico estruturado
* Evolução contextual por rodada
* Diagnóstico associado
* Comparação robusta entre ciclos
* Publicação institucional
* Base para autoridade pública

