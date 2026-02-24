<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
        // Example: [{ label: 'Workspace', url: route('projects.index') }, { label: 'Project Name' }]
    }
});
</script>

<template>
    <nav class="flex text-sm text-surface-500 mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li v-for="(item, index) in items" :key="index" class="inline-flex items-center">
                
                <!-- Separator -->
                <svg v-if="index > 0" class="w-4 h-4 text-surface-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>

                <div class="flex items-center">
                    <Link v-if="item.url && index !== items.length - 1" :href="item.url" class="inline-flex items-center font-medium text-surface-500 hover:text-brand-600 transition-colors">
                        {{ item.label }}
                    </Link>
                    <span v-else class="text-surface-700 font-medium" aria-current="page">
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>
