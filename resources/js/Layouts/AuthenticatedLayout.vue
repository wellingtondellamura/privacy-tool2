<script setup>
import { ref, watch, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import AlertModal from '@/Components/AlertModal.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const showingNavigationDropdown = ref(false);

const user = usePage().props.auth.user;
const page = usePage();

const flashModal = ref({
    show: false,
    title: '',
    message: '',
    variant: 'primary'
});

const closeFlash = () => {
    flashModal.value.show = false;
};

// Success/info messages are shown as a toast that disappears by itself;
// errors and warnings keep the modal, since they need attention.
const toast = ref({ show: false, message: '', variant: 'success' });
let toastTimer = null;

const showToast = (message, variant) => {
    toast.value = { show: true, message, variant };
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => (toast.value.show = false), 4000);
};

// Server errors that are not tied to a form field (e.g. "round already closed")
// were silently dropped before; field errors stay with their forms.
const GENERAL_ERROR_KEYS = ['status', 'message', 'token', 'access', 'inspection', 'questionnaire', 'evaluation_round_id', 'results', 'comparison', 'publication', 'role'];

const showFlash = (flash) => {
    if (!flash) return;
    if (flash.error) {
        flashModal.value = { show: true, title: t('common.error'), message: flash.error, variant: 'danger' };
    } else if (flash.warning) {
        flashModal.value = { show: true, title: t('common.warning'), message: flash.warning, variant: 'warning' };
    } else if (flash.success) {
        showToast(flash.success, 'success');
    } else if (flash.info) {
        showToast(flash.info, 'info');
    }
};

const showGeneralErrors = (errors) => {
    const key = GENERAL_ERROR_KEYS.find((k) => errors?.[k]);
    if (key) {
        flashModal.value = { show: true, title: t('common.error'), message: errors[key], variant: 'danger' };
    }
};

watch(() => page.props.flash, showFlash, { deep: true });
watch(() => page.props.errors, showGeneralErrors, { deep: true });

// Check on mount for initial flash
onMounted(() => {
    showFlash(page.props.flash);
    showGeneralErrors(page.props.errors);
});
</script>

<template>
    <div class="min-h-screen bg-surface-50">
        <nav
            class="border-b border-surface-200 bg-white shadow-sm"
        >
            <!-- Primary Navigation Menu -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo
                                    class="block h-10 w-auto transition-transform duration-smooth hover:scale-105"
                                />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div
                            class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                        >
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                {{ $t('nav.dashboard') }}
                            </NavLink>
                            <NavLink
                                :href="route('metodo.mitra')"
                                :active="route().current('metodo.mitra')"
                            >
                                {{ $t('nav.mitra_method') }}
                            </NavLink>
                            <NavLink
                                :href="route('manual')"
                                :active="route().current('manual')"
                            >
                                {{ $t('nav.manual') }}
                            </NavLink>
                            <NavLink
                                :href="route('public.tools.index')"
                                :active="route().current('public.tools.*')"
                            >
                                {{ $t('nav.public_directory') }}
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <LocaleSwitcher class="me-3" />
                        <!-- Settings Dropdown -->
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-surface-500 transition duration-smooth hover:text-surface-700 hover:bg-surface-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                                        >
                                            {{ user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        :href="route('profile.edit')"
                                    >
                                        {{ $t('nav.profile') }}
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        {{ $t('nav.logout') }}
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="
                                showingNavigationDropdown =
                                    !showingNavigationDropdown
                            "
                            class="inline-flex items-center justify-center rounded-md p-2 text-surface-400 transition duration-150 ease-in-out hover:bg-surface-100 hover:text-surface-500 focus:bg-surface-100 focus:text-surface-500 focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden"
            >
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        {{ $t('nav.dashboard') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('metodo.mitra')"
                        :active="route().current('metodo.mitra')"
                    >
                        {{ $t('nav.mitra_method') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('manual')"
                        :active="route().current('manual')"
                    >
                        {{ $t('nav.manual') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('public.tools.index')"
                        :active="route().current('public.tools.*')"
                    >
                        {{ $t('nav.public_directory') }}
                    </ResponsiveNavLink>
                </div>

                <!-- Responsive Settings Options -->
                <div
                    class="border-t border-surface-200 pb-1 pt-4"
                >
                    <div class="px-4">
                        <div
                            class="text-base font-medium text-surface-800"
                        >
                            {{ user.name }}
                        </div>
                        <div class="text-sm font-medium text-surface-500">
                            {{ user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            {{ $t('nav.profile') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            {{ $t('nav.logout') }}
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header
            class="bg-white shadow-sm border-b border-surface-100"
            v-if="$slots.header"
        >
            <div class="mx-auto max-w-7xl px-4 py-4 sm:py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
        <!-- Toast (success / info) -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="toast.show"
                role="status"
                aria-live="polite"
                class="fixed z-50 inset-x-4 bottom-4 sm:inset-x-auto sm:right-6 sm:bottom-6 sm:max-w-sm flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg text-sm font-medium"
                :class="toast.variant === 'success' ? 'bg-emerald-600 text-white' : 'bg-surface-900 text-white'"
            >
                <svg v-if="toast.variant === 'success'" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="flex-1">{{ toast.message }}</span>
                <button type="button" class="shrink-0 opacity-80 hover:opacity-100" :aria-label="$t('common.close')" @click="toast.show = false">&times;</button>
            </div>
        </transition>

        <!-- Flash Alert Modal -->
        <AlertModal
            :show="flashModal.show"
            :title="flashModal.title"
            :message="flashModal.message"
            :variant="flashModal.variant"
            @close="closeFlash"
        />
    </div>
</template>
