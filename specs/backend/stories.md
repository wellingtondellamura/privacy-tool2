# 📘 Privacy Tool — Specification by Example (BDD)

Versão: 2.0
Escopo: Backend-first
Paradigma: Determinístico, versionado e auditável

---

# 🔷 FEATURE 1 — Autenticação

## Cenário 1.1 — Registro de usuário

**Given** um visitante não autenticado
**When** ele envia nome, e-mail válido e senha válida
**Then** um usuário deve ser criado
**And** o e-mail deve ser marcado como não verificado
**And** ele deve receber e-mail de verificação

---

## Cenário 1.2 — Login válido

**Given** um usuário registrado com e-mail verificado
**When** ele envia credenciais corretas
**Then** deve ser autenticado
**And** deve receber sessão válida

---

# 🔷 FEATURE 2 — Projetos

## Cenário 2.1 — Criar projeto

**Given** um usuário autenticado
**When** ele envia nome e URL válidos
**Then** um projeto deve ser criado
**And** o usuário deve ser registrado como membro com papel owner
**And** owner deve ser único no projeto

---

## Cenário 2.2 — Acesso restrito ao projeto

**Given** um usuário que não é membro do projeto
**When** ele tenta acessar o projeto
**Then** deve receber 403 Forbidden

---

# 🔷 FEATURE 3 — Convites

## Cenário 3.1 — Convidar novo membro

**Given** um owner autenticado
**When** ele convida um e-mail válido
**Then** deve ser criado um convite com token único
**And** deve ser enviado e-mail

---

## Cenário 3.2 — Aceitar convite com usuário inexistente

**Given** um convite válido
**And** o e-mail não possui conta
**When** o convite é aceito
**Then** uma conta deve ser criada
**And** o usuário deve ser associado ao projeto

---

## Cenário 3.3 — Aceitar convite expirado

**Given** um convite com expires_at no passado
**When** o token é utilizado
**Then** deve retornar erro de convite expirado

---

# 🔷 FEATURE 4 — Versionamento do Questionário

## Cenário 4.1 — Criar inspeção usa versão ativa

**Given** existe uma versão ativa do questionário
**When** uma inspeção é criada
**Then** ela deve referenciar exatamente essa versão

---

## Cenário 4.2 — Nova versão não afeta inspeções antigas

**Given** uma inspeção criada com versão 1
**And** uma nova versão 2 é criada
**When** consultar a inspeção antiga
**Then** ela deve continuar referenciando versão 1

---

# 🔷 FEATURE 5 — Respostas Individuais

## Cenário 5.1 — Salvar resposta válida

**Given** inspeção ativa
**And** usuário com papel evaluator
**When** ele responde uma pergunta válida
**Then** a resposta deve ser persistida
**And** deve substituir resposta anterior se existir

---

## Cenário 5.2 — Observer não pode responder

**Given** inspeção ativa
**And** usuário com papel observer
**When** ele tenta salvar resposta
**Then** deve receber 403

---

## Cenário 5.3 — Não permitir resposta após fechamento

**Given** inspeção com status closed
**When** um evaluator tenta salvar resposta
**Then** deve receber erro de edição bloqueada

---

# 🔷 FEATURE 6 — Cálculo Determinístico

## Cenário 6.1 — Pontuação por resposta

**Given** resposta "Suficiente"
**When** convertida para score
**Then** deve retornar 100

**Given** resposta "Insuficiente"
**Then** deve retornar 50

**Given** resposta "Inexistente"
**Then** deve retornar 0

---

## Cenário 6.2 — Score de categoria

**Given** categoria com 4 perguntas
**And** respostas com score total 300
**When** calcular scoreCategoria
**Then** resultado deve ser round((300 / 400) * 100)

---

## Cenário 6.3 — Score de seção

**Given** scoreCat1 = 80
**And** scoreCat2 = 60
**When** calcular scoreSecao
**Then** resultado deve ser round((80 + 60)/2) = 70

---

## Cenário 6.4 — Medalha

**Given** scoreSecao = 95
**Then** medalha deve ser Ouro

**Given** scoreSecao = 70
**Then** medalha deve ser Prata

**Given** scoreSecao = 50
**Then** medalha deve ser Bronze

**Given** scoreSecao = 30
**Then** medalha deve ser Incipiente

---

# 🔷 FEATURE 7 — Fechamento de Inspeção

## Cenário 7.1 — Fechar inspeção

**Given** inspeção ativa
**And** respostas individuais registradas
**When** owner executa fechamento
**Then** sistema deve calcular resultado individual
**And** gerar snapshot individual
**And** gerar snapshot consolidado se todos responderam
**And** alterar status para closed

---

## Cenário 7.2 — Snapshot imutável

**Given** inspeção fechada
**When** novas respostas são inseridas diretamente no banco (simulação indevida)
**Then** snapshot persistido não deve ser alterado

---

# 🔷 FEATURE 8 — Consolidação da Equipe

## Cenário 8.1 — Média por seção

**Given** dois usuários com scoreSecao 80 e 60
**When** calcular scoreEquipe
**Then** resultado deve ser 70

---

## Cenário 8.2 — Divergência baixa

**Given** todos usuários responderam com score 100
**When** calcular variância
**Then** classificação deve ser baixa

---

## Cenário 8.3 — Divergência alta

**Given** respostas 0, 100, 0, 100
**When** calcular variância
**Then** classificação deve ser alta

---

# 🔷 FEATURE 9 — Comparação Temporal

## Cenário 9.1 — Delta positivo

**Given** inspeção A com score 60
**And** inspeção B com score 80
**When** comparar B com A
**Then** delta deve ser +20

---

## Cenário 9.2 — Delta negativo

**Given** inspeção A com score 80
**And** inspeção B com score 60
**When** comparar B com A
**Then** delta deve ser -20

---

## Cenário 9.3 — Comparação inválida

**Given** inspeções pertencem a projetos diferentes
**When** tentar comparar
**Then** retornar erro 400

---

# 🔷 FEATURE 10 — Autorização

## Cenário 10.1 — Apenas owner pode fechar

**Given** inspeção ativa
**And** usuário evaluator
**When** tenta fechar
**Then** receber 403

---

## Cenário 10.2 — Observer pode visualizar após fechamento

**Given** inspeção fechada
**And** usuário observer
**When** acessa resultados
**Then** deve visualizar snapshot consolidado

---

# 🔷 FEATURE 11 — Estado da Inspeção

## Cenário 11.1 — Transição válida

**Given** status draft
**When** ativar inspeção
**Then** status deve ser active

**Given** status active
**When** fechar
**Then** status deve ser closed

---

## Cenário 11.2 — Transição inválida

**Given** status closed
**When** tentar reabrir
**Then** retornar erro de transição inválida

---

# 🔷 FEATURE 12 — Integridade Matemática

## Cenário 12.1 — Reprodutibilidade

**Given** conjunto fixo de respostas
**When** recalcular via serviço
**Then** resultado deve ser idêntico ao snapshot

---

# 🔷 CRITÉRIOS GLOBAIS DE ACEITAÇÃO

O backend está correto quando:

1. Todos cenários acima passam.
2. Nenhuma regra é executada no frontend.
3. Snapshots não são recalculados retroativamente.
4. Versionamento preserva histórico.
5. Autorização impede acesso indevido.
6. Cálculos são determinísticos.
