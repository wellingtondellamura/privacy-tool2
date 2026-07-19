<script setup>
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';

const { locale } = useI18n();
const page = usePage();

const switchLocale = (newLocale) => {
    locale.value = newLocale;

    if (page.props.auth.user) {
        router.patch(route('profile.locale'), { locale: newLocale }, {
            preserveState: true,
            preserveScroll: true,
        });
    } else {
        router.post(route('locale.update'), { locale: newLocale }, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const getFlagUrl = (key) => {
    return `/images/ico/${key}.png`;
};
</script>

<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button class="flex items-center justify-center p-1 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                <img :src="getFlagUrl(locale)" class="h-5 w-auto rounded-sm shadow-sm" alt="Current Language" />
            </button>
        </template>

        <template #content>
            <button
                v-for="(label, key) in $page.props.available_locales"
                :key="key"
                @click="switchLocale(key)"
                class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
            >
                <img :src="getFlagUrl(key)" class="h-4 w-auto rounded-sm shadow-sm" :alt="label" />
                <span>{{ label }}</span>
            </button>
        </template>
    </Dropdown>
</template>
