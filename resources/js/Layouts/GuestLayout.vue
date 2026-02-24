<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-surface-50 pt-6 sm:pt-0"
    >
        <div class="mb-6 flex flex-col items-center">
            <Link href="/">
                <ApplicationLogo class="h-16 w-16 fill-current text-brand-600" />
            </Link>
            <h1 class="mt-4 text-2xl font-semibold text-surface-900 tracking-tight">Privacy Tool</h1>
            <p class="mt-1 text-surface-500 text-sm">Transparência no uso de dados pessoais</p>
        </div>

        <div
            class="mt-2 w-full overflow-hidden bg-white px-8 py-8 shadow-tactile sm:max-w-md sm:rounded-2xl border border-surface-100 transition-shadow duration-smooth hover:shadow-tactile-hover"
        >
            <slot />
        </div>
    </div>
</template>
