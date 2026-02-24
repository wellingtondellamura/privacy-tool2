<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const user = usePage().props.auth.user;
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex flex-col md:flex-row">
        
        <!-- Left Sidebar: Progress and Navigation -->
        <aside class="w-full md:w-64 lg:w-72 bg-white border-r border-surface-200 flex flex-col shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-surface-100 shrink-0">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-brand-600 transition-transform duration-smooth hover:scale-105" />
                </Link>
            </div>
            <div class="flex-grow overflow-y-auto p-4 space-y-6">
                <slot name="sidebar" />
            </div>
        </aside>

        <!-- Center: Main Application Area -->
        <main class="flex-grow overflow-y-auto flex flex-col">
            <header class="h-16 bg-white border-b border-surface-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>
                <div class="ml-4 flex items-center gap-4">
                    <span class="text-sm font-medium text-surface-700 hidden sm:block">{{ user.name }}</span>
                    <!-- Mobile trigger for right panel if needed -->
                </div>
            </header>

            <div class="flex-grow p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto w-full">
                <slot />
            </div>
        </main>

        <!-- Right Panel: Global Status and Actions -->
        <aside class="w-full md:w-64 lg:w-72 bg-white border-l border-surface-200 shrink-0 flex flex-col">
            <div class="p-6">
                <slot name="panel" />
            </div>
        </aside>

    </div>
</template>
