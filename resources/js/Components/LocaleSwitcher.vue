<script setup>
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';

const { locale } = useI18n();
const page = usePage();

const switchLocale = (event) => {
    const newLocale = event.target.value;
    locale.value = newLocale;

    if (page.props.auth.user) {
        router.patch(route('profile.locale'), { locale: newLocale }, {
            preserveState: true,
            preserveScroll: true,
        });
    } else {
        localStorage.setItem('locale', newLocale);
        router.reload();
    }
};
</script>

<template>
    <select
        :value="locale"
        @change="switchLocale"
        class="text-sm border-gray-300 rounded-md bg-transparent focus:border-brand-500 focus:ring-brand-500"
    >
        <option value="pt_BR">🇧🇷 Português</option>
        <option value="en">🇺🇸 English</option>
    </select>
</template>
