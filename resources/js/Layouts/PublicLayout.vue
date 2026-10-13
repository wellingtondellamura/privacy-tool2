<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Button from '@/Components/Button.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';

defineProps({
    title: String,
});

// Page links collapse into a menu on phones
const menuOpen = ref(false);
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-surface-50 flex flex-col font-sans selection:bg-brand-500 selection:text-white">

        <!-- Navigation -->
        <header class="w-full bg-white border-b border-surface-200 shrink-0 sticky top-0 z-50 shadow-sm">
          <div class="px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center gap-3">
            <div class="flex items-center gap-3 shrink-0">
                <Link href="/">
                    <ApplicationLogo class="h-8 w-auto text-brand-600 fill-current" />
                </Link>
            </div>
            <nav class="flex items-center gap-3 sm:gap-4 min-w-0">
                <Link
                    :href="route('metodo.mitra')"
                    class="hidden sm:block text-sm font-medium transition-colors whitespace-nowrap"
                    :class="route().current('metodo.mitra') ? 'text-brand-600' : 'text-surface-500 hover:text-brand-600'"
                >
                    {{ $t('nav.mitra_method') }}
                </Link>
                <Link
                    :href="route('manual')"
                    class="hidden md:block text-sm font-medium transition-colors"
                    :class="route().current('manual') ? 'text-brand-600' : 'text-surface-500 hover:text-brand-600'"
                >
                    {{ $t('nav.manual') }}
                </Link>
                <Link
                    :href="route('public.tools.index')"
                    class="hidden md:block text-sm font-medium transition-colors"
                    :class="route().current('public.tools.*') ? 'text-brand-600' : 'text-surface-500 hover:text-brand-600'"
                >
                    {{ $t('nav.public_directory') }}
                </Link>

                <div class="w-px h-4 bg-surface-200 hidden md:block mx-2"></div>

                <LocaleSwitcher class="hidden sm:block" />

                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors duration-smooth"
                >
                    {{ $t('nav.dashboard') }}
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium text-surface-600 hover:text-brand-600 transition-colors duration-smooth whitespace-nowrap"
                    >
                        {{ $t('nav.login') }}
                    </Link>

                    <Link
                        :href="route('register')"
                        class="hidden sm:block"
                    >
                        <Button variant="primary" size="sm" class="shadow-sm whitespace-nowrap">{{ $t('nav.register') }}</Button>
                    </Link>
                </template>

                <button
                    type="button"
                    class="md:hidden -mr-1 p-2 rounded-lg text-surface-600 hover:bg-surface-100"
                    :aria-label="$t('welcome.menu')"
                    :aria-expanded="menuOpen"
                    aria-controls="public-mobile-menu"
                    @click="menuOpen = !menuOpen"
                >
                    <svg v-if="!menuOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </nav>
          </div>

          <!-- Phone menu -->
          <nav v-if="menuOpen" id="public-mobile-menu" class="md:hidden border-t border-surface-100 px-4 py-3 space-y-1" @click="menuOpen = false">
              <Link :href="route('metodo.mitra')" class="block rounded-lg px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50">{{ $t('nav.mitra_method') }}</Link>
              <Link :href="route('manual')" class="block rounded-lg px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50">{{ $t('nav.manual') }}</Link>
              <Link :href="route('public.tools.index')" class="block rounded-lg px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50">{{ $t('nav.public_directory') }}</Link>
              <Link v-if="!$page.props.auth.user" :href="route('register')" class="block rounded-lg px-3 py-2 text-sm font-semibold text-brand-700 hover:bg-surface-50 sm:hidden">{{ $t('nav.register') }}</Link>
              <div class="px-3 pt-2 sm:hidden" @click.stop>
                  <LocaleSwitcher />
              </div>
          </nav>
        </header>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-surface-900 text-surface-300 py-12 shrink-0">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-8 items-center mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <ApplicationLogo class="h-6 w-auto text-brand-400 fill-current" />
                        </div>
                        <p class="text-sm max-w-sm leading-relaxed text-surface-400">
                            {{ $t('footer.description') }}
                        </p>
                    </div>
                    <div class="flex flex-col md:items-end gap-3 text-sm">
                        <span class="font-semibold text-white mb-2">{{ $t('footer.references') }}</span>
                        <a href="https://each.usp.br/cond_met_pand/trmodel/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2">
                            <span>{{ $t('footer.trmodel_link') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <a href="https://www.gov.br/esporte/pt-br/acesso-a-informacao/lgpd" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2">
                            <span>{{ $t('footer.lgpd_link') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>

                <hr class="border-surface-800 mb-8" />

                <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-surface-500">
                    <p>&copy; {{ new Date().getFullYear() }} {{ $t('footer.copyright') }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
