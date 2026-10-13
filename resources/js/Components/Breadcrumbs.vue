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
    <nav class="flex text-xs sm:text-sm text-surface-500 mb-2 min-w-0" aria-label="Breadcrumb">
        <ol class="inline-flex flex-wrap items-center gap-y-1 min-w-0">
            <li v-for="(item, index) in items" :key="index" class="inline-flex items-center min-w-0">
                
                <!-- Separator -->
                <svg v-if="index > 0" class="w-4 h-4 shrink-0 text-surface-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>

                <div class="flex items-center">
                    <Link v-if="item.url && index !== items.length - 1" :href="item.url" class="font-medium text-surface-500 hover:text-brand-600 transition-colors truncate max-w-[10rem] sm:max-w-none" :title="item.label">
                        {{ item.label }}
                    </Link>
                    <span v-else class="text-surface-700 font-medium truncate max-w-[10rem] sm:max-w-none" aria-current="page">
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>
