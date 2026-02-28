# Privacy Tool — UX/UI Specification (raw-ux)

## 1) Premissa de Design

**Torne o design ultramoderno, sereno e com qualidade tátil. Desenvolvido por um designer premiado. Microinterações suaves.**

Esta premissa define uma experiência que transmite:
- **Confiança** (clareza, previsibilidade, consistência)
- **Calma** (baixa carga cognitiva, foco, ritmo visual)
- **Precisão tátil** (feedback imediato, elementos “tocáveis”, sensação de controle)
- **Refinamento** (detalhe visual e interativo sem excesso)

---

## 2) Objetivo da Experiência

Criar uma interface que permita ao usuário:
1. Entender rapidamente o propósito da inspeção.
2. Preencher o formulário com segurança e sem fricção.
3. Interpretar os resultados com clareza e agir a partir deles.
4. Sentir fluidez e confiança em cada etapa.

---

## 3) Princípios de UX (Norteadores)

## 3.1 Clareza antes de densidade
- Mostrar somente o necessário por etapa.
- Reduzir ruído visual e textual.
- Priorizar hierarquia de informação forte (título, contexto, ação).

## 3.2 Calma cognitiva
- Evitar múltiplos estímulos concorrentes.
- Preservar espaços em branco para legibilidade e ritmo.
- Organizar conteúdo em blocos previsíveis.

## 3.3 Interação tátil e responsiva
- Estados de hover/focus/pressed distintos e imediatos.
- Alvos de toque confortáveis e consistentes.
- Feedback instantâneo para qualquer ação relevante.

## 3.4 Progressão guiada
- Sempre indicar onde o usuário está, o que falta e o próximo passo.
- Reforçar sensação de avanço com sinais visuais de progresso.

## 3.5 Transparência de decisão
- Explicar regras de cálculo, impactos e limitações.
- Evitar “caixa-preta” nos resultados e recomendações.

---

## 4) Aplicação das Heurísticas de Nielsen

## 4.1 Visibilidade do status do sistema
- Exibir progresso por seção e progresso global em tempo real.
- Sinalizar salvamento automático (discreto, não intrusivo).
- Informar estados de carregamento com skeletons e mensagens claras.

## 4.2 Compatibilidade com o mundo real
- Linguagem simples e orientada ao usuário (sem jargão técnico desnecessário).
- Rótulos de resposta semanticamente claros.
- Estrutura por seções com nomes próximos da linguagem de auditoria real.

## 4.3 Controle e liberdade do usuário
- Permitir voltar, revisar e editar respostas sem penalidade.
- Confirmar ações destrutivas (reiniciar, sair sem concluir).
- Oferecer rotas claras de desfazer/recuperar quando possível.

## 4.4 Consistência e padrões
- Mesmo padrão visual para botões, badges, cards e alertas.
- Mesmo comportamento para componentes equivalentes.
- Vocabulário consistente para estados e mensagens.

## 4.5 Prevenção de erros
- Destacar perguntas não respondidas antes de concluir.
- Bloquear apenas o que for crítico; orientar em vez de punir.
- Evitar cliques acidentais em ações irreversíveis.

## 4.6 Reconhecimento em vez de memorização
- Manter contexto visível (seção ativa, categoria ativa, progresso).
- Mostrar títulos e descrições de seção sempre no topo contextual.
- Usar ícones como suporte, não como único canal semântico.

## 4.7 Flexibilidade e eficiência de uso
- Permitir navegação rápida entre seções para usuários experientes.
- Manter preenchimento simples para iniciantes.
- Facilitar retomada de sessão sem reentrada de dados.

## 4.8 Design estético e minimalista
- Remover elementos não funcionais.
- Reduzir texto redundante.
- Valorizar contraste, ritmo e alinhamento para leitura calma.

## 4.9 Ajudar a reconhecer, diagnosticar e recuperar erros
- Mensagens orientadas por causa + ação recomendada.
- Erros em linguagem humana (“o que aconteceu”, “o que fazer agora”).
- Estado do formulário preservado sempre que possível.

## 4.10 Ajuda e documentação
- Disponibilizar visão geral da metodologia em linguagem objetiva.
- Incluir exemplos curtos de interpretação de score.
- Oferecer guia rápido “como preencher bem”.

---

## 5) Diretrizes Visuais para “Ultra-Modern, Calm, Tactile”

## 5.1 Composição visual
- Layout em camadas suaves (surface, conteúdo, ação).
- Cantos arredondados consistentes para sensação tátil.
- Densidade moderada com foco em leitura e escaneabilidade.

## 5.2 Hierarquia e tipografia
- Escala tipográfica previsível (título, subtítulo, corpo, meta).
- Peso tipográfico para guiar atenção (não usar cor como único destaque).
- Comprimento de linha confortável para leitura contínua.

## 5.3 Cor e contraste
- Paleta calma com acentos funcionais (sucesso, alerta, atenção).
- Contraste suficiente para todos os textos críticos.
- Cor sempre acompanhada de reforço semântico (ícone/label).

## 5.4 Espaçamento e ritmo
- Sistema de espaçamento regular.
- Respiros entre blocos para reduzir esforço visual.
- Agrupamento por proximidade para reforçar relação semântica.

## 5.5 Superfícies e profundidade
- Profundidade sutil (sem excesso de sombras).
- Estados de interação perceptíveis e elegantes.
- Camadas com propósito (não decorativas).

---

## 6) Microinterações (Smooth Micro-interactions)

## 6.1 Princípios
- Curta duração, resposta imediata, sem distrair do fluxo principal.
- Priorizar percepção de continuidade entre estados.
- Usar animação para **explicar transição**, não apenas enfeitar.

## 6.2 Eventos críticos
- Seleção de resposta: feedback visual instantâneo.
- Conclusão de categoria/seção: reforço positivo discreto.
- Mudança de seção: transição suave preservando orientação espacial.
- Cálculo de resultado: progressão percebida + estado final claro.

## 6.3 Boas práticas de movimento
- Evitar movimentos longos e repetitivos.
- Preservar desempenho e previsibilidade.
- Respeitar preferências de redução de movimento do sistema.

---

## 7) Usabilidade por Etapa da Jornada

## 7.1 Tela inicial
- Explicar valor da ferramenta em poucos segundos.
- CTA principal inequívoco para iniciar inspeção.
- Acesso secundário para metodologia e contexto.

## 7.2 Formulário
- Navegação lateral clara com status de conclusão.
- Conteúdo principal focado na categoria ativa.
- Opção “Outro” com campo contextual imediato.
- Progresso visível e incentivo à completude.

## 7.3 Resultados
- Síntese da seção ativa no topo (score + classificação + contexto).
- Visualizações legíveis e autoexplicativas.
- Respostas detalhadas com boa escaneabilidade.
- Caminhos claros para nova inspeção e exportação.

---

## 8) Acessibilidade e Inclusão

- Navegação completa por teclado.
- Estados de foco visíveis em todos os controles interativos.
- Labels claros e associações corretas em campos.
- Contraste adequado em textos e componentes críticos.
- Mensagens compreensíveis para tecnologias assistivas.
- Não depender exclusivamente de cor para significado.

---

## 9) Escrita UX (Microcopy)

## 9.1 Tom de voz
- Claro, humano, confiável, sem burocracia excessiva.
- Objetivo em instruções; empático em alertas.

## 9.2 Padrões de mensagem
- **Ação primária**: verbos diretos (“Calcular”, “Continuar”, “Exportar”).
- **Alerta**: motivo + impacto + decisão.
- **Erro**: causa provável + próximo passo.
- **Sucesso**: confirmação curta e contextual.

## 9.3 Exemplos de estrutura
- “Há perguntas sem resposta. Você pode continuar, mas o resultado ficará menos preciso.”
- “Inspeção concluída. Revise os pontos críticos antes de exportar.”

---

## 10) Modelo de Qualidade da Experiência

## 10.1 Métricas principais
- Taxa de conclusão da inspeção.
- Tempo médio até cálculo de resultado.
- Percentual de perguntas respondidas por inspeção.
- Taxa de abandono por etapa.
- Frequência de reinício/saída antes de concluir.

## 10.2 Indicadores qualitativos
- Clareza percebida das instruções.
- Confiança no score apresentado.
- Facilidade de navegação entre seções.
- Utilidade percebida das recomendações e resumo final.

---

## 11) Checklist de Boas Práticas (Pronto para Revisão)

- [ ] Hierarquia visual clara em todas as telas
- [ ] Progresso sempre visível e atualizado
- [ ] Estados de interação completos (default, hover, focus, active, disabled)
- [ ] Alertas destrutivos com confirmação
- [ ] Diferenciação entre não respondido e baixa pontuação
- [ ] Contraste e foco compatíveis com acessibilidade
- [ ] Textos curtos, claros e orientados à ação
- [ ] Microinterações suaves, úteis e não intrusivas
- [ ] Consistência entre padrões visuais e comportamentais
- [ ] Feedback imediato em todas as ações críticas

---

## 12) Diretriz para Evolução com Generative UI

A experiência pode evoluir para interface adaptativa sem perder previsibilidade:
- Adaptar prioridade visual conforme contexto de uso.
- Destacar lacunas críticas em tempo real.
- Sugerir revisão contextual de respostas ambíguas.
- Permitir alternar entre modo adaptativo e modo padrão.
- Registrar adaptações relevantes para auditoria e transparência.

---

## 13) Definição de Sucesso de UX

A interface é considerada bem-sucedida quando:
1. O usuário completa a inspeção com baixa fricção.
2. O entendimento do resultado é imediato e acionável.
3. A experiência transmite calma, controle e confiança.
4. O produto mantém consistência, acessibilidade e refinamento em toda a jornada.
