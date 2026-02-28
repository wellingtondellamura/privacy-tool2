<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tools: Object,
    filters: Object,
});
</script>

<template>
    <Head title="Diretório Público de Ferramentas" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Diretório Público</h1>
                <p class="mt-4 text-xl text-gray-500">Explore ferramentas avaliadas quanto à sua conformidade e privacidade.</p>
            </header>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="tool in tools.data" :key="tool.slug" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 transition-all hover:shadow-lg">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ tool.project_name }}</h2>
                                <a :href="tool.project_url" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-sm truncate block mt-1">{{ tool.project_url }}</a>
                            </div>
                            <div class="flex flex-col items-center">
                                <template v-if="tool.medal">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        {{ tool.medal.name || tool.medal }}
                                    </span>
                                </template>
                                <span class="text-2xl font-black text-gray-900 mt-2">{{ tool.score }}%</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between text-sm text-gray-500 border-t border-gray-50 pt-4">
                            <span>Publicado em {{ tool.published_at }}</span>
                            <Link :href="route('public.tools.show', tool.slug)" class="font-semibold text-indigo-600 hover:text-indigo-500">
                                Ver Detalhes &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination placeholder to be implemented in Phase 7 -->
            <div class="mt-12 flex justify-center" v-if="tools.links && tools.links.length > 3">
                <!-- Pagination components -->
            </div>
        </div>
    </div>
</template>
