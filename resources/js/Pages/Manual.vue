<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import { ref, computed } from 'vue';

const activeSection = ref(null);
const toggle = (id) => { activeSection.value = activeSection.value === id ? null : id; };

const roles = [
    { name: 'Dono (owner)', desc: 'Controle total: criar, editar, excluir, convidar membros, criar rodadas, publicar resultados.' },
    { name: 'Avaliador (evaluator)', desc: 'Criar inspeções, preencher questionários, visualizar resultados.' },
    { name: 'Observador (observer)', desc: 'Apenas visualizar o projeto e seus resultados.' },
];

const medals = [
    { icon: '🥇', name: 'Ouro', desc: 'Score mais alto — excelente transparência', bg: 'bg-yellow-400', text: 'text-yellow-900', shadow: 'shadow-yellow-400/30' },
    { icon: '🥈', name: 'Prata', desc: 'Boa conformidade', bg: 'bg-gray-300', text: 'text-gray-700', shadow: 'shadow-gray-300/30' },
    { icon: '🥉', name: 'Bronze', desc: 'Conformidade parcial', bg: 'bg-amber-600', text: 'text-amber-100', shadow: 'shadow-amber-600/30' },
    { icon: '⚠️', name: 'Incipiente', desc: 'Necessita melhorias significativas', bg: 'bg-red-400', text: 'text-red-900', shadow: 'shadow-red-400/30' },
];

const answerLevels = [
    { label: 'Suficiente', value: 100, color: 'bg-green-100 text-green-800 border-green-200', desc: 'A informação está presente e é adequada.' },
    { label: 'Insuficiente', value: 50, color: 'bg-yellow-100 text-yellow-800 border-yellow-200', desc: 'A informação existe, mas é incompleta ou inadequada.' },
    { label: 'Inexistente', value: 0, color: 'bg-red-100 text-red-800 border-red-200', desc: 'A informação não é fornecida.' },
    { label: 'Não se aplica', value: '—', color: 'bg-surface-100 text-surface-600 border-surface-200', desc: 'A pergunta não se aplica ao contexto.' },
];

const dimensions = [
    { num: 1, name: 'Pessoas/Atores', desc: 'Identificação dos atores envolvidos: controlador, operador e agências reguladoras.', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { num: 2, name: 'Propósito de Uso', desc: 'Objetivos, base legal, período de manipulação e responsável legal.', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { num: 3, name: 'Dados Pessoais', desc: 'Quais dados são coletados, sua origem, composição e permissões.', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4' },
    { num: 4, name: 'Compartilhamento', desc: 'Políticas de transferência e compartilhamento com terceiros.', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
    { num: 5, name: 'Agenciamento', desc: 'Meios para o indivíduo exercer seus direitos sobre os dados.', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
];

const workflow = [
    { step: 1, title: 'Criar Projeto', desc: 'Registre o software a ser avaliado com nome, descrição e URL.', icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6' },
    { step: 2, title: 'Convidar Equipe', desc: 'Envie convites por e-mail para avaliadores e observadores.', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
    { step: 3, title: 'Criar Rodada', desc: 'Organize as inspeções em rodadas de avaliação temáticas.', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' },
    { step: 4, title: 'Criar Inspeção', desc: 'Cada membro cria sua inspeção individual na rodada.', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { step: 5, title: 'Preencher Questionário', desc: 'Responda as 46 perguntas em escala (Suficiente / Insuficiente / Inexistente).', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' },
    { step: 6, title: 'Fechar e Consolidar', desc: 'Feche inspeções e rodadas para gerar snapshots de resultado.', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { step: 7, title: 'Visualizar Resultados', desc: 'Scores individuais, da equipe e consolidados por rodada com medalhas.', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { step: 8, title: 'Publicar e Gerar Selo', desc: 'Publique no Diretório Público e gere selos verificáveis para embed.', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064' },
];

const sections = [
    {
        id: 'cadastro', title: '1. Cadastro e Autenticação', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        content: [
            { subtitle: 'Criar uma Conta', text: 'Na página inicial, clique em "Cadastre-se". Preencha Nome, E-mail e Senha (mínimo 8 caracteres). Confirme a senha e envie o formulário.' },
            { subtitle: 'Login', text: 'Na página inicial, clique em "Entrar". Informe e-mail e senha cadastrados. O sistema suporta "Lembrar de mim" e recuperação de senha por e-mail.' },
            { subtitle: 'Editar Perfil', text: 'Acessível em "Perfil" no menu superior. Permite alterar Nome, E-mail e Senha. Inclui opção de excluir a conta permanentemente.' },
        ]
    },
    {
        id: 'projetos', title: '2. Gestão de Projetos', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        content: [
            { subtitle: 'Criar Projeto', text: 'No Workspace, clique em "Novo Projeto". Preencha: Nome (obrigatório — nome do software avaliado), Descrição, URL do software, Ícone e Cor de destaque. Você será automaticamente adicionado como dono.' },
            { subtitle: 'Tela de Detalhes', text: 'Exibe informações gerais, membros e seus papéis, convites pendentes, rodadas de avaliação e inspeções associadas ao projeto.' },
            { subtitle: 'Editar e Excluir', text: 'O dono pode editar dados ou excluir o projeto a qualquer momento (exclusão reversível via soft delete).' },
        ]
    },
    {
        id: 'convites', title: '3. Convites e Membros', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        content: [
            { subtitle: 'Convidar Membros', text: 'Na tela do projeto, insira o e-mail do convidado e selecione o papel (Avaliador ou Observador). O sistema envia um e-mail com link de aceitação válido por 7 dias.' },
            { subtitle: 'Aceitar Convite', text: 'Se o convidado já tem conta, é adicionado automaticamente ao projeto. Se não tem conta, o link direciona para criar uma conta vinculada ao e-mail do convite.' },
            { subtitle: 'Gerenciar', text: 'O dono pode alterar papéis entre Avaliador e Observador. O papel de Dono não pode ser alterado. Convites podem ser reenviados (renova prazo) ou cancelados.' },
        ]
    },
    {
        id: 'rodadas', title: '4. Rodadas de Avaliação', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        content: [
            { subtitle: 'Criar Rodada', text: 'Na tela do projeto, clique em "Nova Rodada de Avaliação". Informe um nome descritivo (ex: "Avaliação Q1 2026"). A rodada é criada com status Rascunho.' },
            { subtitle: 'Ciclo de Vida', text: 'Rascunho (draft) → Fechada (closed). No estado de rascunho, inspeções podem ser criadas e preenchidas. Ao fechar, o snapshot consolidado é gerado automaticamente.' },
            { subtitle: 'Fechar Rodada', text: 'Clique em "Revisar e Fechar" para ver um preview do resultado. Opcionalmente adicione um diagnóstico textual. Marque se deseja publicar imediatamente no Diretório Público e escolha a visibilidade (Apenas Score ou Relatório Completo).' },
        ]
    },
    {
        id: 'inspecoes', title: '5. Inspeções', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        content: [
            { subtitle: 'O que é', text: 'Uma inspeção representa a avaliação individual de um membro sobre o software. Cada inspeção é vinculada a uma Rodada de Avaliação e a uma versão do questionário.' },
            { subtitle: 'Criar Inspeção', text: 'Na tela do projeto ou rodada, clique em "Nova Inspeção" e selecione a rodada. A inspeção é criada com status Rascunho.' },
            { subtitle: 'Ciclo de Vida', text: 'Rascunho → Ativa (clique em "Ativar") → Fechada (clique em "Fechar"). Na fase Ativa, preencha o questionário. Ao fechar, snapshots individuais e consolidados são gerados. Apenas o responsável (criador) pode mudar o status. Inspeções fechadas não podem ser reabertas.' },
        ]
    },
    {
        id: 'questionario', title: '6. Preenchimento do Questionário', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        content: [
            { subtitle: 'Estrutura', text: 'O questionário contém 46 perguntas organizadas em 2 seções (Existência e Qualidade da Informação com 26 perguntas; Formato de Apresentação com 20 perguntas), cada uma avaliando as 5 dimensões de transparência.' },
            { subtitle: 'Como Preencher', text: 'Acesse a inspeção com status Ativa. O questionário é apresentado em cards por seção e categoria. Para cada pergunta, selecione Suficiente (100 pts), Insuficiente (50 pts) ou Inexistente (0 pts). Opcionalmente, adicione observações justificando sua escolha.' },
        ]
    },
    {
        id: 'resultados', title: '7. Resultados', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        content: [
            { subtitle: 'Individual', text: 'Mostra o resultado da sua avaliação pessoal: score por seção, por dimensão e medalha final. Acesse via tela do projeto → inspeção → "Ver Meus Resultados".' },
            { subtitle: 'Equipe (Consolidado)', text: 'Média consolidada de todas as respostas de todos os avaliadores na inspeção. Disponível apenas quando a inspeção estiver fechada.' },
            { subtitle: 'Rodada', text: 'Consolidação de todas as inspeções dentro de uma rodada. Gerado ao fechar a rodada. Inclui score global, medalha e detalhamento por seção e dimensão.' },
            { subtitle: 'Comparação Evolutiva', text: 'Compare duas inspeções ou duas rodadas do mesmo projeto lado-a-lado para acompanhar a evolução da transparência. Ambas devem estar fechadas. Indicadores visuais de progresso (↑ melhoria / ↓ regressão).' },
        ]
    },
    {
        id: 'diretorio', title: '8. Diretório Público', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064',
        content: [
            { subtitle: 'Publicar', text: 'Após fechar a rodada, clique em "Publicar no Diretório Público". Escolha: Apenas Score (exibe pontuações e medalhas) ou Relatório Completo (detalhamento completo por pergunta). A publicação gera um slug único (URL amigável).' },
            { subtitle: 'Consultar', text: 'Acesse /tools sem necessidade de login. Filtre por medalha, ano ou versão do questionário. Ordene por score ou data. Clique para ver o relatório público.' },
            { subtitle: 'Segurança', text: 'O sistema sanitiza automaticamente os dados públicos, removendo user_id, observações e comentários para proteger a privacidade dos avaliadores.' },
        ]
    },
    {
        id: 'selos', title: '9. Selos Verificáveis (Badges)', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        content: [
            { subtitle: 'Gerar Selo', text: 'Na tela da rodada publicada, clique em "Gerar Selo". O sistema cria um selo com token público único.' },
            { subtitle: 'Estilos', text: 'Default (card completo com nome, medalha, score e data), Compact (versão compacta) e Minimal (apenas medalha e score em linha).' },
            { subtitle: 'Incorporar', text: 'Cole o snippet \x3Cscript src="https://suaurl.com/badge/TOKEN.js"\x3E\x3C/script\x3E em qualquer site. O script carrega dados atualizados via API (cache de 5 min) e renderiza automaticamente.' },
            { subtitle: 'Revogar', text: 'O dono pode revogar o selo a qualquer momento, tornando-o inativo.' },
        ]
    },
    {
        id: 'exportacao', title: '10. Exportação de Dados', icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        content: [
            { subtitle: 'Exportar Projeto', text: 'Na tela do projeto, clique em "Exportar Projeto". Gera um arquivo JSON com todos os dados: projeto, membros, rodadas, inspeções, respostas e perguntas.' },
            { subtitle: 'Exportar Tudo', text: 'Em "Perfil", clique em "Exportar Todos os Dados". Gera um arquivo ZIP com um JSON para cada projeto que você participa.' },
        ]
    },
];
</script>

<template>
    <Head title="Manual de Uso - Privacy Tool v2" />

    <component :is="$page.props.auth.user ? AuthenticatedLayout : PublicLayout" title="Manual de Uso">
        <template v-if="$page.props.auth.user" #header>
            <h2 class="font-semibold text-xl text-surface-800 leading-tight">Manual de Uso</h2>
        </template>

        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">

                <!-- Hero -->
                <div class="bg-white rounded-3xl shadow-sm border border-surface-200 overflow-hidden mb-12">
                    <div class="relative p-8 md:p-12 lg:p-16">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full -mr-32 -mt-32 blur-3xl opacity-60"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-brand-100 rounded-full -ml-24 -mb-24 blur-3xl opacity-40"></div>
                        <div class="relative z-10 max-w-3xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Documentação Completa
                            </div>
                            <h1 class="text-4xl md:text-5xl font-extrabold text-surface-900 tracking-tight mb-6">
                                Manual de <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-brand-400">Uso</span>
                            </h1>
                            <p class="text-lg text-surface-600 leading-relaxed mb-4">
                                Guia completo de todas as funcionalidades do Privacy Tool v2 — a ferramenta de inspeção de transparência de dados pessoais baseada na LGPD e no Método Mitra.
                            </p>
                            <p class="text-sm text-surface-400">46 critérios · 5 dimensões · 2 seções de avaliação · Sistema de medalhas</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-12">
                    <h3 class="text-sm font-semibold text-surface-400 uppercase tracking-wider mb-4">Navegação Rápida</h3>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in sections" :key="s.id" @click="activeSection = s.id; $nextTick(() => { const el = document.getElementById(s.id); if (el) { const y = el.getBoundingClientRect().top + window.scrollY - 100; window.scrollTo({ top: y, behavior: 'smooth' }); } })"
                            class="px-3 py-1.5 text-sm rounded-lg border transition-all duration-200"
                            :class="activeSection === s.id ? 'bg-brand-50 border-brand-200 text-brand-700 font-semibold' : 'bg-surface-50 border-surface-200 text-surface-600 hover:bg-brand-50 hover:text-brand-600'"
                        >{{ s.title }}</button>
                    </div>
                </div>

                <!-- Workflow Overview -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-3 text-center">Fluxo de Trabalho</h2>
                    <p class="text-surface-500 text-center mb-10 max-w-2xl mx-auto">Do cadastro à publicação — o caminho completo para avaliar a transparência do seu software.</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="w in workflow" :key="w.step" class="relative bg-white p-5 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="w.icon"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-brand-500 uppercase">Passo {{ w.step }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-surface-900 mb-1">{{ w.title }}</h4>
                            <p class="text-xs text-surface-500 leading-relaxed">{{ w.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- Roles -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">Papéis do Sistema</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div v-for="r in roles" :key="r.name" class="bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <h4 class="text-lg font-bold text-surface-900 mb-3">{{ r.name }}</h4>
                            <p class="text-surface-600 text-sm leading-relaxed">{{ r.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- Medal System -->
                <div class="bg-surface-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden mb-16">
                    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_30%_20%,#16a34a_0%,transparent_50%)] opacity-20"></div>
                    <div class="relative z-10 text-center max-w-2xl mx-auto">
                        <h2 class="text-3xl font-bold mb-4">Sistema de Medalhas</h2>
                        <p class="text-surface-300 mb-10">O resultado consolida um score que classifica a aplicação em quatro níveis de transparência:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div v-for="m in medals" :key="m.name" class="flex flex-col items-center">
                                <div :class="[m.bg, m.text, m.shadow]" class="w-16 h-16 rounded-full flex items-center justify-center text-2xl mb-3 shadow-lg">{{ m.icon }}</div>
                                <span class="font-bold text-lg">{{ m.name }}</span>
                                <span class="text-xs text-surface-400 mt-1">{{ m.desc }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Scale -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">Escala de Respostas</h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="a in answerLevels" :key="a.label" class="bg-white p-5 rounded-2xl border border-surface-200 shadow-sm">
                            <div :class="a.color" class="inline-block px-3 py-1 rounded-full text-sm font-semibold border mb-3">{{ a.label }}</div>
                            <div class="text-2xl font-extrabold text-surface-900 mb-2">{{ a.value }}<span v-if="typeof a.value === 'number'" class="text-sm font-normal text-surface-400 ml-1">pts</span></div>
                            <p class="text-xs text-surface-500 leading-relaxed">{{ a.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- 5 Dimensions -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">5 Dimensões de Transparência</h2>
                    <div class="grid gap-4">
                        <div v-for="d in dimensions" :key="d.num" class="flex flex-col md:flex-row gap-5 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">{{ d.num }}</div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-surface-900 mb-1">{{ d.name }}</h4>
                                <p class="text-surface-600 text-sm">{{ d.desc }}</p>
                            </div>
                            <div class="flex-shrink-0 hidden md:flex items-center">
                                <svg class="w-8 h-8 text-surface-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="d.icon"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questionnaire Structure -->
                <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6 md:p-8 mb-16">
                    <h2 class="text-2xl font-bold text-surface-900 mb-6">Estrutura do Questionário</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-surface-50 rounded-xl p-5 border border-surface-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">I</div>
                                <div>
                                    <h4 class="font-bold text-surface-900">Existência e Qualidade da Informação</h4>
                                    <span class="text-xs text-surface-400">26 perguntas · 5 categorias</span>
                                </div>
                            </div>
                            <p class="text-sm text-surface-600">Avalia se as informações de transparência existem e se têm qualidade suficiente para o entendimento do usuário.</p>
                        </div>
                        <div class="bg-surface-50 rounded-xl p-5 border border-surface-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">II</div>
                                <div>
                                    <h4 class="font-bold text-surface-900">Formato de Apresentação</h4>
                                    <span class="text-xs text-surface-400">20 perguntas · 5 categorias</span>
                                </div>
                            </div>
                            <p class="text-sm text-surface-600">Avalia se as informações são apresentadas de forma acessível, objetiva e compreensível ao indivíduo.</p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sections (Accordion) -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-3 text-center">Funcionalidades Detalhadas</h2>
                    <p class="text-surface-500 text-center mb-10">Clique em cada seção para expandir os detalhes.</p>
                    <div class="space-y-3">
                        <div v-for="s in sections" :key="s.id" :id="s.id" class="bg-white rounded-2xl border border-surface-200 shadow-sm overflow-hidden transition-all duration-300" :class="activeSection === s.id ? 'ring-2 ring-brand-200' : ''">
                            <button @click="toggle(s.id)" class="w-full flex items-center gap-4 p-5 md:p-6 text-left hover:bg-surface-50 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
                                </div>
                                <span class="flex-1 text-lg font-bold text-surface-900">{{ s.title }}</span>
                                <svg class="w-5 h-5 text-surface-400 transition-transform duration-200" :class="activeSection === s.id ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div v-show="activeSection === s.id" class="px-5 md:px-6 pb-6 border-t border-surface-100">
                                <div class="pt-5 space-y-5">
                                    <div v-for="(c, ci) in s.content" :key="ci" class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-surface-900 mb-1">{{ c.subtitle }}</h5>
                                            <p class="text-sm text-surface-600 leading-relaxed">{{ c.text }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Practices Tip -->
                <div class="bg-gradient-to-r from-brand-50 to-green-50 rounded-2xl border border-brand-100 p-6 md:p-8 mb-16">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-brand-900 mb-2">Dica para Melhores Resultados</h4>
                            <p class="text-sm text-brand-800 leading-relaxed">Para resultados mais confiáveis, recomenda-se que <strong>múltiplos avaliadores</strong> preencham o questionário independentemente dentro da mesma rodada. O resultado consolidado (média das respostas) oferece uma visão mais imparcial do nível de transparência do software avaliado.</p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-surface-900 mb-6">Pronto para avaliar seu software?</h3>
                    <div class="flex justify-center gap-4">
                        <Link v-if="!$page.props.auth.user" :href="route('register')">
                            <Button variant="primary" size="lg">Começar Agora</Button>
                        </Link>
                        <Link v-else :href="route('dashboard')">
                            <Button variant="primary" size="lg">Ir para o Dashboard</Button>
                        </Link>
                        <Link :href="route('metodo.mitra')">
                            <Button variant="outline" size="lg">Conhecer o Método Mitra</Button>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </component>
</template>
