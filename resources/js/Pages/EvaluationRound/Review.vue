<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    round: {
        type: Object,
        required: true,
    },
    preview: {
        type: Object,
        required: true,
    }
});

const form = useForm({
    diagnosis: props.round.diagnosis || '',
    publish_immediately: false,
    visibility: 'score_public',
});

const showPreview = ref(false);
const { t } = useI18n();

const submit = () => {
    form.post(route('rounds.close', props.round.id));
};

const toRoman = (num) => {
    if (isNaN(num)) return '';
    const map = { M: 1000, CM: 900, D: 500, CD: 400, C: 100, XC: 90, L: 50, XL: 40, X: 10, IX: 9, V: 5, IV: 4, I: 1 };
    let result = '';
    for (let key in map) {
        while (num >= map[key]) {
            result += key;
            num -= map[key];
        }
    }
    return result;
};

const getAlpha = (index) => String.fromCharCode(65 + index);

const getMedalImage = (medalName) => {
    const images = {
        'gold': '/images/badges-gold.png',
        'silver': '/images/badges-silver.png',
        'bronze': '/images/badges-bronze.png',
    };
    return images[medalName] || null;
};

const translateStatus = (status) => {
    const map = {
        'draft': t('round.status_draft'),
        'active': t('round.status_active'),
        'closed': t('round.status_closed'),
    };
    return map[status] || status;
};

</script>

<template>
    <Head :title="t('review.page_title', { name: round.name })" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumbs :items="[
                        { label: $t('nav.workspace'), url: route('projects.index') },
                        { label: round.project.name, url: route('projects.show', round.project.id) },
                        { label: round.name, url: route('rounds.show', round.id) },
                        { label: $t('review.header_title') }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                        {{ $t('review.header_title') }}
                    </h2>
                    <p class="text-sm text-surface-500 mt-1">{{ $t('review.header_description') }}</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Inspections Summary & Diagnosis -->
                    <div class="lg:col-span-2 space-y-6">
                        <Card :title="$t('review.summary_title')">
                            <ul class="divide-y divide-surface-100">
                                <li v-for="inspection in round.inspections" :key="inspection.id" class="px-6 py-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-surface-900">{{ $t('review.inspection_label', { id: inspection.sequential_id }) }}</p>
                                        <p class="text-xs text-surface-500">{{ inspection.user?.name }}</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div v-if="inspection.status === 'closed' && inspection.result_snapshots?.[0]" class="text-right mr-4">
                                            <span class="block text-xs font-bold text-brand-600">{{ inspection.result_snapshots[0].payload_json.global_score }} pts</span>
                                            <span class="block text-[10px] text-surface-400 uppercase tracking-tighter">{{ inspection.result_snapshots[0].payload_json.medal.name }}</span>
                                        </div>
                                        <Badge :variant="inspection.status === 'closed' ? 'success' : 'surface'">
                                            {{ translateStatus(inspection.status) }}
                                        </Badge>
                                    </div>
                                </li>
                            </ul>
                            <div v-if="round.inspections.some(i => i.status !== 'closed')" class="bg-amber-50 p-4 border-t border-amber-100 flex gap-3 text-amber-800 text-xs italic">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                {{ $t('review.warning_unclosed') }}
                            </div>
                        </Card>

                        <Card :title="$t('review.diagnosis_title')">
                            <div class="p-6">
                                <label class="block text-sm font-medium text-surface-700 mb-2">
                                    {{ $t('review.diagnosis_label') }}
                                </label>
                                <textarea 
                                    v-model="form.diagnosis" 
                                    rows="10" 
                                    class="w-full rounded-lg border-surface-200 focus:border-brand-500 focus:ring-brand-500 text-sm"
                                    :placeholder="$t('review.diagnosis_placeholder')"
                                ></textarea>
                                <p class="text-[11px] text-surface-400 mt-2 italic">
                                    {{ $t('review.diagnosis_help') }}
                                </p>
                            </div>
                        </Card>
                    </div>

                    <!-- Right: Preview and Actions -->
                    <div class="space-y-6">
                        <Card class="bg-gradient-to-br from-surface-50 to-white overflow-hidden relative border-surface-200">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-300 to-brand-600"></div>
                            <template #header>
                                <div class="px-6 py-4 flex justify-between items-center">
                                    <h3 class="font-semibold text-surface-800">{{ $t('review.preview_title') }}</h3>
                                    <Button variant="ghost" size="xs" @click="showPreview = !showPreview">
                                        {{ showPreview ? $t('review.hide_details') : $t('review.show_details') }}
                                    </Button>
                                </div>
                            </template>
                            
                            <div class="p-6 text-center">
                                <div class="relative inline-block mb-4">
                                    <img v-if="getMedalImage(preview.medal.name)" 
                                         :src="getMedalImage(preview.medal.name)" 
                                         class="w-32 h-32 object-contain drop-shadow-md mx-auto" />
                                    <div v-else class="w-32 h-32 bg-surface-100 rounded-full flex items-center justify-center mx-auto text-surface-300 italic text-[10px]">
                                        {{ $t('review.no_data') }}
                                    </div>
                                </div>
                                <div class="text-4xl font-bold text-surface-900 leading-none">
                                    {{ preview.global_score }}
                                </div>
                                <div class="mt-2 text-[10px] font-bold text-surface-400 uppercase tracking-widest">
                                    {{ $t('review.avg_score') }}
                                </div>
                            </div>

                            <div v-if="showPreview" class="border-t border-surface-100 divide-y divide-surface-100 max-h-96 overflow-y-auto">
                                <div v-for="(section, sIndex) in preview.sections" :key="section.id" class="px-6 py-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-surface-700">{{ toRoman(sIndex + 1) }}. {{ section.name }}</span>
                                        <span class="text-xs font-bold text-brand-600">{{ section.score }}</span>
                                    </div>
                                    <div class="w-full bg-surface-200 h-1 rounded-full">
                                        <div class="bg-brand-400 h-1 rounded-full" :style="`width: ${section.score}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <Card :title="$t('review.actions_title')">
                            <div class="p-6 space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" v-model="form.publish_immediately" id="publish" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500" />
                                    </div>
                                    <div class="text-sm">
                                        <label for="publish" class="font-medium text-surface-700">{{ $t('review.publish_immediately') }}</label>
                                        <p class="text-xs text-surface-500">{{ $t('review.publish_description') }}</p>
                                    </div>
                                </div>

                                <div v-if="form.publish_immediately" class="pl-8 space-y-2">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" value="score_public" v-model="form.visibility" class="text-brand-600 focus:ring-brand-500" />
                                        <span class="text-xs text-surface-600 group-hover:text-surface-900 transition-colors">{{ $t('review.visibility_score') }}</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" value="full_public" v-model="form.visibility" class="text-brand-600 focus:ring-brand-500" />
                                        <span class="text-xs text-surface-600 group-hover:text-surface-900 transition-colors">{{ $t('review.visibility_full') }}</span>
                                    </label>
                                </div>

                                <hr class="border-surface-100 my-4" />

                                <Button 
                                    variant="danger" 
                                    class="w-full justify-center" 
                                    :disabled="form.processing"
                                    @click="submit"
                                >
                                    {{ form.processing ? $t('common.processing') : $t('review.close_button') }}
                                </Button>
                                <Link :href="route('rounds.show', round.id)" class="block text-center text-xs text-surface-400 hover:text-surface-600 underline">
                                    {{ $t('review.cancel_link') }}
                                </Link>
                            </div>
                        </Card>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
