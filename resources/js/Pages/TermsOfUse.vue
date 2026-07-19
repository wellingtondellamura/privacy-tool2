<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="$t('terms.title')" />

    <div class="min-h-screen bg-surface-50">
        <!-- Header -->
        <header class="bg-white border-b border-surface-100 sticky top-0 z-10 shadow-sm">
            <div class="max-w-4xl mx-auto px-6 py-4 flex items-center gap-4">
                <Link href="/" class="flex items-center gap-2 text-brand-600 hover:text-brand-700 transition-colors duration-smooth">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-sm font-medium">{{ $t('common.back') }}</span>
                </Link>
                <div class="h-4 w-px bg-surface-200"></div>
                <span class="text-sm text-surface-500">Mitra Privacy Tool</span>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-4xl mx-auto px-6 py-12">

            <!-- WIP Notice -->
            <div class="mb-8 flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">{{ $t('terms.wip_title') }}</p>
                    <p class="text-sm text-amber-700 mt-0.5">{{ $t('terms.wip_notice') }}</p>
                </div>
            </div>

            <!-- Document Header -->
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-surface-900 mb-2">{{ $t('terms.title') }}</h1>
                <p class="text-sm text-surface-400">{{ $t('terms.last_updated') }}: —</p>
                <div class="mt-4 h-1 w-16 bg-brand-500 rounded-full"></div>
            </div>

            <!-- Sections -->
            <div class="prose prose-surface max-w-none space-y-10">

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">1. {{ $t('terms.section1_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section1_content') }}</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">2. {{ $t('terms.section2_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section2_content') }}</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">3. {{ $t('terms.section3_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section3_content') }}</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">4. {{ $t('terms.section4_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section4_content') }}</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">5. {{ $t('terms.section5_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section5_content') }}</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">6. {{ $t('terms.section6_title') }}</h2>
                    <p class="text-surface-600 leading-relaxed">{{ $t('terms.section6_content') }}</p>
                </section>

            </div>

            <!-- Footer -->
            <div class="mt-16 pt-8 border-t border-surface-100 text-center">
                <p class="text-sm text-surface-400">Mitra Privacy Tool &copy; {{ new Date().getFullYear() }}</p>
            </div>
        </main>
    </div>
</template>
