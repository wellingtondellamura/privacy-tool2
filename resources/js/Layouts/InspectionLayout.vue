<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const user = usePage().props.auth.user;

// On phones the navigation sidebar is a drawer; from md up it is a fixed column.
const sidebarOpen = ref(false);
const sidebarEl = ref(null);
const openButton = ref(null);
const closeButton = ref(null);

const closeSidebar = () => {
    sidebarOpen.value = false;
    openButton.value?.focus();
};

const drawerIsModal = () => sidebarOpen.value && window.matchMedia('(max-width: 767px)').matches;

const focusableInDrawer = () => {
    if (!sidebarEl.value) return [];
    return [...sidebarEl.value.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )].filter((node) => node.offsetParent !== null || node === document.activeElement);
};

const onKeydown = (event) => {
    if (!drawerIsModal()) return;

    if (event.key === 'Escape') {
        event.preventDefault();
        closeSidebar();
        return;
    }

    if (event.key !== 'Tab') return;

    const focusable = focusableInDrawer();
    if (focusable.length === 0) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
};

watch(sidebarOpen, (open) => {
    if (open) {
        requestAnimationFrame(() => closeButton.value?.focus());
    }
});

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>

<template>
    <div class="min-h-screen md:h-screen md:overflow-hidden bg-surface-50 flex flex-col md:flex-row">

        <!-- Drawer backdrop (phones) -->
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150"
            leave-to-class="opacity-0"
        >
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-surface-900/40 md:hidden" @click="closeSidebar"></div>
        </transition>

        <!-- Left Sidebar: Progress and Navigation -->
        <aside
            ref="sidebarEl"
            class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white border-r border-surface-200 flex flex-col shrink-0 transition-transform duration-200
                   md:static md:z-auto md:translate-x-0 md:w-64 lg:w-72 md:h-full"
            :class="sidebarOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full'"
            id="inspection-sidebar"
        >
            <div class="h-16 flex items-center justify-between px-6 border-b border-surface-100 shrink-0">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-brand-600 transition-transform duration-smooth hover:scale-105" />
                </Link>
                <button ref="closeButton" type="button" class="md:hidden p-2 -mr-2 text-surface-500 hover:text-surface-900" :aria-label="$t('common.close')" @click="closeSidebar">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex-grow overflow-y-auto p-4 space-y-6">
                <slot name="sidebar" :close="closeSidebar" />
            </div>
        </aside>

        <!-- Center: Main Application Area -->
        <main class="flex-grow flex flex-col min-w-0 md:h-full">
            <header class="sticky top-0 z-30 md:static h-16 bg-white border-b border-surface-200 flex items-center justify-between gap-2 px-4 sm:px-6 lg:px-8 shrink-0">
                <button
                    ref="openButton"
                    type="button"
                    class="md:hidden -ml-1 p-2 rounded-lg text-surface-600 hover:bg-surface-100"
                    :aria-label="$t('inspection.open_categories')"
                    :aria-expanded="sidebarOpen"
                    aria-controls="inspection-sidebar"
                    @click="sidebarOpen = true"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>
                <div class="ml-2 hidden sm:flex items-center gap-4">
                    <span class="text-sm font-medium text-surface-700">{{ user.name }}</span>
                </div>
            </header>

            <div id="inspection-scroll" class="flex-grow md:overflow-y-auto p-4 sm:p-6 lg:p-8" :class="$slots['mobile-bar'] ? 'pb-28 md:pb-8' : ''">
                <div class="max-w-4xl mx-auto w-full">
                    <slot />
                </div>

                <!-- Status panel below the content on phones/tablets -->
                <div class="md:hidden max-w-4xl mx-auto w-full mt-8 bg-white rounded-xl border border-surface-200 p-4">
                    <slot name="panel" />
                </div>
            </div>
        </main>

        <!-- Right Panel: Global Status and Actions (md and up) -->
        <aside class="hidden md:flex w-64 lg:w-72 bg-white border-l border-surface-200 shrink-0 flex-col md:h-full">
            <div class="flex-grow overflow-y-auto p-6">
                <slot name="panel" />
            </div>
        </aside>

        <!-- Sticky action bar (phones) -->
        <div v-if="$slots['mobile-bar']" class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur border-t border-surface-200 px-4 py-3 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
            <slot name="mobile-bar" />
        </div>
    </div>
</template>
