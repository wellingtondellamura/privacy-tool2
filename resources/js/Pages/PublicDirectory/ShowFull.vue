<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tool: Object,
});
</script>

<template>
    <Head title="Relatório Completo" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ tool.name }} - Relatório de Privacidade</h1>
            <!-- Content -->
        </div>
    </div>
</template>
