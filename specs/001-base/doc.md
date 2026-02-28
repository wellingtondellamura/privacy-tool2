# Privacy Tool — Raw Documentation (As-Is)

## 1) Visão Geral

O Privacy Tool é uma aplicação web para inspeção de transparência no uso de dados pessoais em softwares/sites, com base na LGPD e no TR-Model.  
Fluxo principal: Início → Formulário → Resultados → Exportação estruturada.

---

## 2) Arquitetura Funcional

### Estrutura funcional
- Experiência centrada em formulário de avaliação
- Cálculo de resultado com regras determinísticas
- Navegação em etapas (início, avaliação, resultados)
- Exportação dos resultados para compartilhamento e registro

### Características atuais
- Funcionamento sem dependência de cadastro de usuário
- Armazenamento temporário do progresso durante a avaliação
- Processamento imediato dos resultados ao concluir o preenchimento

---

## 3) Etapas da Jornada

- Tela inicial
- Tela de formulário de inspeção
- Tela de resultados da inspeção

---

## 4) Domínio do Formulário

### 4.1 Seções
1. Pessoas/Atores
2. Propósito de uso
3. Dados pessoais
4. Compartilhamento
5. Agenciamento

### 4.2 Categorias
- Existência e Qualidade da Informação
- Formato de Apresentação

### 4.3 Quantidade de perguntas
- Pessoas/Atores: 7
- Propósito de uso: 10
- Dados pessoais: 10
- Compartilhamento: 12
- Agenciamento: 7
- Total: 46 perguntas

### 4.4 Questões completas (texto oficial da ferramenta)

#### Pessoas/Atores

**Existência e Qualidade da Informação**
1. Informações sobre os atores tais como: Nome, endereço, telefone, e-mail e responsável pela empresa?
2. Informações que indicam quais são as agências de proteção de dados que regulamentam o uso dos dados pessoais pelos atores?
3. Informações sobre o papel (função) de cada ator no uso dos dados pessoais?

**Formato de Apresentação**
1. Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações dos atores.
2. Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise dos atores envolvidos no uso dos dados pessoais.
3. Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia de grandes volumes de textos.
4. As informações existentes descartam a necessidade do indivíduo buscar informações em outras fontes.

#### Propósito de uso

**Existência e Qualidade da Informação**
1. Descrição do objetivo de uso dos dados pessoais.
2. Informação sobre a lei/regulamentação que torna o uso dos dados pessoais legal.
3. Informações sobre quais dados pessoais serão utilizados para atingir os objetivos apontados.
4. Informação do ator responsável legal pelo uso dos dados pessoais.
5. Informações sobre a existência, ou não, da utilização ou processamento de dados pessoais feitas exclusivamente por computador, sem a supervisão humana.
6. Informações sobre o período de manipulação dos dados pessoais para o propósito indicado.

**Formato de Apresentação**
1. Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre o(s) propósito(s) de uso dos dados.
2. Simplicidade, objetividade e relevância das informações, de forma a auxiliar efetivamente na análise do(s) propósito(s) de uso dos dados pessoais.
3. Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.
4. As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.

#### Dados pessoais

**Existência e Qualidade da Informação**
1. Informações de quais dados pessoais são utilizados.
2. Descrição de como os dados pessoais são compostos (detalhes que possam explicar melhor os dados pessoais).
3. Informações sobre a origem dos dados (dispositivos, compra de terceiros, compartilhamento etc).
4. Em caso de obrigatoriedade da disponibilização dos dados pelos indivíduos, informações sobre o que pode ocorrer no caso da não coleta dos dados.
5. Informações sobre o objetivo do uso do dado pessoal e como (qual processo) é feito com o dado pessoal.
6. Informações sobre a permissão concedida pelo indivíduo para o uso dos dados pessoais.

**Formato de Apresentação**
1. Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre os dados pessoais manipulados
2. Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise do propósito de uso os dados pessoais
3. Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.
4. As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.

#### Compartilhamento

**Existência e Qualidade da Informação**
1. Informações de quais dados pessoais são transferidos ou compartilhados com terceiros.
2. Informações sobre o motivo da transferência e/ou compartilhamento dos dados pessoais.
3. Informações sobre a base legal (lei/regulamentação) que garante a legalidade do compartilhamento dos dados.
4. Dados completos do destinatário dos dados pessoais, de forma que permita a identificação e o contato com o destinatário.
5. Dados da organização que monitora o uso dos dados pessoais no país ou região do destinatário, de forma que permita a identificação e o contato com o órgão.
6. Relação de quais dados foram transferidos ou compartilhados e como foram obtidos.
7. Informações para relembrar como você permitiu e/ou autorizou o compartilhamento dos dados pessoais.
8. Informações sobre os eventos que causam a transferência/compartilhamento dos dados pessoais.

**Formato de Apresentação**
1. Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre a transferência/compartilhamento dos dados.
2. Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise da transferência/compartilhamento dos dados.
3. Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.
4. As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.

#### Agenciamento

**Existência e Qualidade da Informação**
1. Informações de como o indivíduo pode solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados.
2. Informações sobre meios de contato, telefones, e-mails sobre os atores envolvidos no uso dos dados pessoais.
3. Informações e/ou recursos para o indivíduo solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados diretamente no software, sem a necessidade de entrar em contato.

**Formato de Apresentação**
1. Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre agências de controle e ações para questionar ou verificar o uso dos dados.
2. Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise das agências de controle e ações para questionar ou verificar o uso dos dados.
3. Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.
4. As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.

---

## 5) Opções de Resposta

### Existência e Qualidade da Informação
- Suficiente
- Insuficiente
- Inexistente
- Outro

### Formato de Apresentação
- Apropriado
- Inapropriado
- Necessita melhorias
- Outro

Se a resposta for Outro, o sistema exibe campo de texto complementar.

---

## 6) Regras de Negócio

### RN-01 — Pontuação por resposta
- Suficiente / Apropriado = 100
- Insuficiente / Necessita melhorias = 50
- Inexistente / Inapropriado / Outro = 0

### RN-02 — Score da categoria
$$
scoreCategoria = round\left(\frac{\sum pontos}{totalPerguntasCategoria \times 100} \times 100\right)
$$

### RN-03 — Percentual respondido da categoria
$$
percentualCategoria = \frac{respondidas}{totalPerguntasCategoria} \times 100
$$

### RN-04 — Score da seção
Média simples dos 2 scores das categorias:
$$
scoreSecao = round\left(\frac{scoreCat1 + scoreCat2}{2}\right)
$$

### RN-05 — Percentual respondido da seção
Média simples dos percentuais das 2 categorias:
$$
percentualSecao = \frac{percentualCat1 + percentualCat2}{2}
$$

### RN-06 — Medalhas por seção
- 91–100: Ouro
- 61–90: Prata
- 41–60: Bronze
- 0–40: Incipiente / Inexistente

### RN-07 — Persistência e limpeza
- Salva automaticamente as respostas e observações durante o preenchimento
- Recupera o progresso ao retornar para a inspeção em andamento
- Limpa os dados ao iniciar nova inspeção, retornar ao início, reiniciar a avaliação ou finalizar a exportação

---

## 7) Fluxos Funcionais

### Fluxo A — Iniciar inspeção
1. Usuário clica em Inspeção na home
2. Sistema limpa dados anteriores
3. Sistema abre o formulário de inspeção

### Fluxo B — Preenchimento
1. Usuário escolhe seção no menu lateral
2. Escolhe categoria por badges
3. Responde perguntas por radio
4. Se marcar Outro, preenche texto adicional
5. Sistema salva automaticamente o progresso
6. Sistema atualiza progresso por seção e total

### Fluxo C — Calcular
1. Usuário clica em Calcular
2. Sistema exibe aviso sobre respostas incompletas
3. Usuário confirma
4. Sistema abre a tela de resultados
5. Sistema calcula scores

### Fluxo D — Resultados
1. Usuário seleciona seção
2. Sistema mostra score, medalha e percentual respondido
3. Sistema mostra gráficos por categoria
4. Sistema mostra respostas detalhadas

### Fluxo E — Exportar / reiniciar
1. Usuário baixa o relatório de respostas em formato estruturado
2. Sistema gera o arquivo de exportação da inspeção
3. Sistema limpa os dados temporários da avaliação
4. Usuário pode iniciar nova inspeção

---

## 8) Persistência e Estado

### Dados persistidos durante a avaliação
- Respostas objetivas por pergunta
- Observações livres quando a opção “Outro” é utilizada

### Operações de estado
- Registrar respostas automaticamente
- Restaurar progresso de inspeção em andamento
- Limpar estado ao reiniciar ou encerrar inspeção
- Exportar o resultado consolidado

---

## 9) Modelo Conceitual de Dados

- **Seção**: agrupador principal da avaliação (5 seções)
- **Categoria**: dimensão de análise dentro de cada seção (2 categorias)
- **Pergunta**: item avaliativo vinculado a uma seção e categoria
- **Resposta**: opção selecionada para cada pergunta
- **Observação complementar**: texto livre associado à opção “Outro”

Convenção operacional: cada resposta é registrada por seção, categoria e posição da pergunta.

---

## 10) Requisitos Funcionais (As-Is)

- RF-01: Exibir landing com CTA para iniciar inspeção
- RF-02: Exibir formulário por seção/categoria
- RF-03: Permitir resposta com escala por categoria
- RF-04: Exibir campo complementar para Outro
- RF-05: Persistir respostas automaticamente
- RF-06: Exibir progresso por seção e total
- RF-07: Permitir cálculo com respostas incompletas (com aviso)
- RF-08: Exibir score e medalha por seção
- RF-09: Exibir gráficos por categoria
- RF-10: Exibir respostas detalhadas
- RF-11: Exportar respostas em formato estruturado
- RF-12: Reiniciar inspeção limpando estado local
- RF-13: Suportar tema claro/escuro

---

## 11) Requisitos Não Funcionais

- RNF-01: Interface responsiva
- RNF-02: Cálculo determinístico e reproduzível
- RNF-03: Baixa fricção de uso, sem pré-requisitos para iniciar
- RNF-04: Interação acessível e compreensível
- RNF-05: Clareza visual para leitura de progresso e resultados
- RNF-06: Consistência de experiência entre diferentes tamanhos de tela

---

