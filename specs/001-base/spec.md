# 📘 Privacy Tool — Especificação Formal (Spec-Driven Development)

**Versão:** 2.0.0
**Status:** Especificação Técnica Base
**Paradigma:** Backend-first, determinístico, auditável
**Stack alvo:** Laravel + Inertia + Vue + Breeze

---

# 1. OBJETIVO DO SISTEMA

Construir uma plataforma colaborativa de inspeção de transparência no uso de dados pessoais baseada na LGPD e no TR-Model.

O sistema deve permitir:

1. Criação de projetos.
2. Convite de membros.
3. Execução colaborativa de inspeções.
4. Consolidação determinística de resultados.
5. Comparação temporal entre inspeções.
6. Geração de relatórios auditáveis.
7. Histórico imutável de resultados.

---

# 2. PRINCÍPIOS ARQUITETURAIS

## 2.1 Backend como Fonte da Verdade

* Toda regra de cálculo reside no backend.
* Frontend não pode recalcular scores.
* Snapshots são imutáveis.

---

## 2.2 Determinismo

Dado o mesmo conjunto de respostas:

* O resultado deve ser idêntico.
* A consolidação deve ser reprodutível.
* O histórico não pode ser alterado retroativamente.

---

## 2.3 Versionamento Estrutural

* Questionário deve ser versionado.
* Cada inspeção referencia uma versão congelada.
* Alterações futuras não afetam inspeções passadas.

---

## 2.4 Imutabilidade de Resultados

Após fechamento:

* Respostas não podem ser alteradas.
* Snapshots não podem ser recalculados.
* Apenas comparação pode ser feita.

---

# 3. MODELO DE DOMÍNIO

## 3.1 Usuário

Representa pessoa autenticada na plataforma.

### Atributos:

* id
* name
* email
* password
* email_verified_at
* created_at
* updated_at

---

## 3.2 Projeto

Representa inspeção recorrente de um site ou ferramenta.

### Atributos:

* id
* name
* description
* url
* owner_id
* created_at
* updated_at
* deleted_at

---

## 3.3 Membro do Projeto

Relacionamento usuário-projeto.

### Atributos:

* project_id
* user_id
* role (owner, evaluator, observer)

---

## 3.4 Convite

Permite entrada de novos membros.

### Atributos:

* project_id
* email
* token
* expires_at
* accepted_at

---

## 3.5 Versão do Questionário

Define conjunto estruturado de seções e perguntas.

### Atributos:

* id
* version_number
* is_active

---

## 3.6 Seção

Agrupador principal (5 fixas na versão 1).

---

## 3.7 Categoria

Dimensão interna da seção (2 por seção).

---

## 3.8 Pergunta

Item avaliativo.

Total fixo na versão 1: 46.

---

## 3.9 Inspeção

Execução do questionário dentro de um projeto.

### Atributos:

* id
* project_id
* questionnaire_version_id
* status (draft, active, closed)
* started_at
* closed_at

---

## 3.10 Resposta

Resposta individual por usuário.

### Atributos:

* inspection_id
* question_id
* user_id
* answer
* observation

---

## 3.11 Snapshot de Resultado

Resultado calculado no fechamento.

### Atributos:

* inspection_id
* user_id (null para consolidado)
* payload_json
* created_at

---

# 4. REGRAS DE NEGÓCIO (DETERMINÍSTICAS)

## RN-01 — Pontuação por Resposta

### Existência e Qualidade:

* Suficiente = 100
* Insuficiente = 50
* Inexistente = 0
* Outro = 0

### Formato:

* Apropriado = 100
* Necessita melhorias = 50
* Inapropriado = 0
* Outro = 0

---

## RN-02 — Score da Categoria

[
scoreCategoria = round\left(\frac{\sum pontos}{totalPerguntasCategoria \times 100} \times 100\right)
]

---

## RN-03 — Percentual Respondido da Categoria

[
percentualCategoria = \frac{respondidas}{totalPerguntasCategoria} \times 100
]

---

## RN-04 — Score da Seção

[
scoreSecao = round\left(\frac{scoreCat1 + scoreCat2}{2}\right)
]

---

## RN-05 — Percentual Respondido da Seção

[
percentualSecao = \frac{percentualCat1 + percentualCat2}{2}
]

---

## RN-06 — Medalhas

* 91–100 → Ouro
* 61–90 → Prata
* 41–60 → Bronze
* 0–40 → Incipiente/Inexistente

---

## RN-07 — Fechamento

Ao fechar inspeção:

* Calcular resultados individuais.
* Calcular consolidado da equipe.
* Persistir snapshots.
* Alterar status para closed.
* Bloquear edição posterior.

---

# 5. CONSOLIDAÇÃO COLABORATIVA

## 5.1 Média da Equipe

Para cada seção:

[
scoreEquipe = média(scores individuais)
]

---

## 5.2 Divergência

Para cada pergunta:

* Converter respostas em score numérico.
* Calcular variância.
* Classificar:

  * 0–10 → baixa
  * 11–30 → média
  * > 30 → alta

Persistir no snapshot consolidado.

---

# 6. COMPARAÇÃO TEMPORAL

Dado dois snapshots consolidados:

* Calcular delta por seção.
* Calcular delta por categoria.
* Calcular delta por pergunta.
* Persistir ComparisonSnapshot.

---

# 7. API CONTRACTS (Backend First)

## 7.1 Projetos

POST /projects
GET /projects
GET /projects/{id}
PUT /projects/{id}
DELETE /projects/{id}

---

## 7.2 Convites

POST /projects/{id}/invite
POST /invitations/{token}/accept

---

## 7.3 Inspeções

POST /projects/{id}/inspections
GET /inspections/{id}
POST /inspections/{id}/response
POST /inspections/{id}/close

---

## 7.4 Resultados

GET /inspections/{id}/results
GET /inspections/{id}/team-results
GET /inspections/{id}/comparison/{otherId}

---

# 8. AUTORIZAÇÃO

## Regras:

* Apenas membro acessa projeto.
* Apenas evaluator responde.
* Apenas owner fecha.
* Observer apenas visualiza após fechamento.

---

# 9. FLUXO DE ESTADOS

Inspeção:

draft → active → closed

Não permitir transição reversa.

---

# 10. TESTABILIDADE

Antes de implementar frontend:

Backend deve permitir:

* Criar projeto via API.
* Criar inspeção.
* Inserir respostas via API.
* Fechar inspeção.
* Consultar snapshot.
* Consultar consolidação.
* Comparar inspeções.

Cobertura mínima obrigatória:

* 80% nos serviços de cálculo.

---

# 11. CRITÉRIOS DE ACEITAÇÃO

O backend está correto quando:

* Resultados são matematicamente reproduzíveis.
* Snapshots não mudam após fechamento.
* Versionamento impede alteração retroativa.
* Consolidação reflete média real.
* Divergência corresponde à variância correta.
* Comparação retorna deltas corretos.

---

# 12. FORA DE ESCOPO (NESTA VERSÃO)

* Interface adaptativa.
* IA para recomendação.
* Ponderação por perfil.
* Integrações externas.
* API pública.

---

# 13. ORDEM DE IMPLEMENTAÇÃO (BACKEND-FIRST)

1. Modelagem de banco
2. Autenticação
3. Projetos e membros
4. Questionário versionado
5. Inspeção e respostas
6. AggregationService
7. Snapshot
8. DivergenceService
9. ComparisonService
10. Testes completos
11. Somente então iniciar frontend

---

# RESULTADO ESPERADO

Ao concluir o backend:

* O sistema deve ser utilizável via API.
* Todas regras devem estar validadas.
* Snapshots devem estar íntegros.
* Consolidação deve estar funcional.
* Comparação deve estar validada.
