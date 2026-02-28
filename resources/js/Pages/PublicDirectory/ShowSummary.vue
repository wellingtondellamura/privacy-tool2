<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tool: Object,
});
</script>

<template>
    <Head :title="tool.name + ' - Resumo de Privacidade'" />

    <div class="min-h-screen bg-white">
        <!-- Hero Section -->
        <div class="bg-indigo-900 py-16 text-white text-center">
            <div class="max-w-3xl mx-auto px-4">
                <h1 class="text-4xl font-extrabold sm:text-5xl uppercase tracking-wider mb-4">{{ tool.name }}</h1>
                <p class="text-indigo-200 text-lg mb-8 italic">Relatório de Privacidade Publicamente Acessível</p>
                <div class="flex justify-center items-center gap-12">
                    <div class="text-center">
                        <div class="text-6xl font-black mb-2">{{ tool.score }}%</div>
                        <div class="text-indigo-300 uppercase tracking-widest text-xs font-bold">Score Global</div>
                    </div>
                    <div class="h-16 w-px bg-indigo-800"></div>
                    <div class="text-center" v-if="tool.medal">
                        <div class="text-2xl font-bold mb-2">{{ tool.medal.name || tool.medal }}</div>
                        <div class="text-indigo-300 uppercase tracking-widest text-xs font-bold">Medalha</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 border-b-2 border-indigo-100 pb-2 mb-6 uppercase tracking-tight">Resumo por Seção</h2>
                <div class="grid gap-4">
                    <div v-for="section in tool.sections" :key="section.name" class="flex items-center justify-between p-6 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="font-bold text-gray-800 text-lg">{{ section.name }}</span>
                        <div class="flex items-center gap-6">
                            <span v-if="section.medal" class="text-xs font-bold uppercase tracking-wider bg-white px-2 py-1 rounded shadow-sm">{{ section.medal }}</span>
                            <span class="text-2xl font-black text-indigo-600">{{ section.score }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-2xl p-8 text-white flex flex-col sm:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-1">Deseja o relatório completo?</h3>
                    <p class="text-gray-400 text-sm">Esta é uma visualização resumida (score_public).</p>
                </div>
                <Link :href="route('public.tools.index')" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-lg hover:bg-indigo-50 transition-colors">
                    Voltar ao Diretório
                </Link>
            </div>
        </main>
    </div>
</template>
