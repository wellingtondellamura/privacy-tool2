<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    // { pt_BR, en, es } modules with { intro, sections }
    documents: { type: Object, required: true },
    titleKey: { type: String, required: true },
    // Values from config/legal.php
    legal: { type: Object, required: true },
    appUrl: { type: String, default: '' },
});

const { t, locale } = useI18n();

const doc = computed(() => props.documents[locale.value] ?? props.documents.pt_BR);

const updatedAt = computed(() => {
    const date = new Date(`${props.legal.updated_at}T12:00:00`);
    return date.toLocaleDateString(locale.value.replace('_', '-'), { day: 'numeric', month: 'long', year: 'numeric' });
});

// Placeholder values; null means the deployment has not configured it yet.
const values = computed(() => {
    const l = props.legal;
    const details = [
        l.controller_document ? `, ${t('legal.document_prefix')} ${l.controller_document}` : '',
        l.controller_address ? `, ${t('legal.address_prefix')} ${l.controller_address}` : '',
    ].join('');

    return {
        url: { text: props.appUrl || null },
        controller: { text: l.controller_name || null },
        controller_details: { text: details, optional: true },
        contact: { text: l.contact_email || null, href: l.contact_email ? `mailto:${l.contact_email}` : null },
        dpo: l.dpo_name
            ? { text: `${l.dpo_name} (${l.dpo_email || l.contact_email || t('legal.not_configured')})` }
            : { text: l.dpo_email || null, href: l.dpo_email ? `mailto:${l.dpo_email}` : null },
    };
});

// Splits a paragraph into plain text and placeholder segments, so nothing is rendered as HTML.
function segments(text) {
    return text.split(/(\{[a-z_]+\})/).filter(Boolean).map((part) => {
        const match = part.match(/^\{([a-z_]+)\}$/);
        if (!match || !(match[1] in values.value)) {
            return { text: part };
        }
        const value = values.value[match[1]];
        if (value.text === null) {
            return { text: t('legal.not_configured'), missing: true };
        }
        return { text: value.text, href: value.href };
    });
}
</script>

<template>
    <Head :title="t(titleKey)" />

    <div class="min-h-screen bg-surface-50">
        <header class="bg-white border-b border-surface-100 sticky top-0 z-10 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center gap-4">
                <Link href="/" class="flex items-center gap-2 text-brand-600 hover:text-brand-700 transition-colors duration-smooth">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-sm font-medium">{{ t('common.back') }}</span>
                </Link>
                <div class="h-4 w-px bg-surface-200"></div>
                <span class="text-sm text-surface-500">Mitra Privacy Tool</span>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-surface-900 mb-2">{{ t(titleKey) }}</h1>
                <p class="text-sm text-surface-500">
                    {{ t('legal.version') }} {{ legal.version }} · {{ t('legal.last_updated') }}: {{ updatedAt }}
                </p>
                <p v-if="locale !== 'pt_BR'" class="text-sm text-surface-500 mt-1">{{ t('legal.translation_notice') }}</p>
                <div class="mt-4 h-1 w-16 bg-brand-500 rounded-full"></div>
            </div>

            <div class="space-y-4 mb-10">
                <p v-for="(paragraph, i) in doc.intro" :key="`intro-${i}`" class="text-surface-600 leading-relaxed">
                    <template v-for="(seg, j) in segments(paragraph)" :key="j">
                        <a v-if="seg.href" :href="seg.href" class="text-brand-600 underline">{{ seg.text }}</a>
                        <span v-else-if="seg.missing" class="text-amber-700 font-medium">{{ seg.text }}</span>
                        <template v-else>{{ seg.text }}</template>
                    </template>
                </p>
            </div>

            <div class="space-y-10">
                <section v-for="(section, s) in doc.sections" :key="s" :id="`section-${s + 1}`">
                    <h2 class="text-xl font-semibold text-surface-800 mb-3">{{ s + 1 }}. {{ section.title }}</h2>
                    <div class="space-y-4">
                        <template v-for="(block, b) in section.blocks" :key="b">
                            <ul v-if="typeof block === 'object'" class="list-disc pl-6 space-y-2 text-surface-600 leading-relaxed">
                                <li v-for="(item, k) in block.list" :key="k">
                                    <template v-if="typeof item === 'object'">
                                        <strong class="font-semibold text-surface-700">{{ item.label }}:</strong>
                                        {{ ' ' }}
                                        <template v-for="(seg, j) in segments(item.text)" :key="j">
                                            <a v-if="seg.href" :href="seg.href" class="text-brand-600 underline">{{ seg.text }}</a>
                                            <span v-else-if="seg.missing" class="text-amber-700 font-medium">{{ seg.text }}</span>
                                            <template v-else>{{ seg.text }}</template>
                                        </template>
                                    </template>
                                    <template v-else>{{ item }}</template>
                                </li>
                            </ul>
                            <p v-else class="text-surface-600 leading-relaxed">
                                <template v-for="(seg, j) in segments(block)" :key="j">
                                    <a v-if="seg.href" :href="seg.href" class="text-brand-600 underline">{{ seg.text }}</a>
                                    <span v-else-if="seg.missing" class="text-amber-700 font-medium">{{ seg.text }}</span>
                                    <template v-else>{{ seg.text }}</template>
                                </template>
                            </p>
                        </template>
                    </div>
                </section>
            </div>

            <div class="mt-16 pt-8 border-t border-surface-100 text-center">
                <p class="text-sm text-surface-400">
                    Mitra Privacy Tool &copy; {{ new Date().getFullYear() }} ·
                    <Link :href="route('credits')" class="hover:text-surface-600 transition-colors">{{ t('footer.credits_link') }}</Link>
                </p>
            </div>
        </main>
    </div>
</template>
