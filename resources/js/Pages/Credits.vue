<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    // Values from config/credits.php
    credits: { type: Object, required: true },
});

const { t } = useI18n();

function reference(pub) {
    return `${pub.authors} ${pub.title}. ${pub.source}`;
}

// Index of the publication whose reference was just copied
const copied = ref(null);

async function copyReference(pub, index) {
    try {
        await navigator.clipboard.writeText(reference(pub));
        copied.value = index;
        setTimeout(() => { if (copied.value === index) copied.value = null; }, 2000);
    } catch {
        // Clipboard unavailable (insecure context); the reference stays selectable on the page.
    }
}
</script>

<template>
    <PublicLayout :title="t('credits.title')">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-surface-900 mb-3">{{ t('credits.title') }}</h1>
                <p class="text-surface-600 leading-relaxed">{{ t('credits.intro') }}</p>
                <div class="mt-4 h-1 w-16 bg-brand-500 rounded-full"></div>
            </div>

            <section class="mb-12">
                <h2 class="text-xl font-semibold text-surface-800 mb-4">{{ t('credits.authors') }}</h2>
                <ul class="grid gap-4 sm:grid-cols-3">
                    <li
                        v-for="author in credits.authors"
                        :key="author.name"
                        class="bg-white border border-surface-200 rounded-xl p-5 shadow-sm flex flex-col"
                    >
                        <span class="font-semibold text-surface-900">{{ author.name }}</span>
                        <span v-if="author.role_key" class="text-sm text-surface-500 mt-1">{{ t(author.role_key) }}</span>
                        <div class="mt-auto pt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                            <a
                                v-if="author.lattes"
                                :href="author.lattes"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-brand-600 hover:text-brand-700 underline"
                            >
                                {{ t('credits.lattes') }}
                            </a>
                            <a
                                v-if="author.orcid"
                                :href="author.orcid"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-brand-600 hover:text-brand-700 underline"
                            >
                                ORCID
                            </a>
                        </div>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-surface-800 mb-4">{{ t('credits.publications') }}</h2>
                <ol class="space-y-4">
                    <li
                        v-for="(pub, i) in credits.publications"
                        :key="pub.title"
                        class="bg-white border border-surface-200 rounded-xl p-5 shadow-sm"
                    >
                        <p class="text-surface-700 leading-relaxed text-sm sm:text-base">
                            {{ pub.authors }}
                            <strong class="font-semibold text-surface-900">{{ pub.title }}</strong>.
                            {{ pub.source }}
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm">
                            <a
                                v-if="pub.url"
                                :href="pub.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-brand-600 hover:text-brand-700 underline"
                            >
                                {{ t('credits.access') }}
                            </a>
                            <button
                                type="button"
                                class="text-surface-500 hover:text-brand-600 transition-colors"
                                @click="copyReference(pub, i)"
                            >
                                <span aria-live="polite">{{ copied === i ? t('credits.copied') : t('credits.copy_reference') }}</span>
                            </button>
                        </div>
                    </li>
                </ol>
            </section>
        </div>
    </PublicLayout>
</template>
