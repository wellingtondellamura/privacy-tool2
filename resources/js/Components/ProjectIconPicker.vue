<script setup>
import { PROJECT_ICONS, PROJECT_COLORS } from '@/Constants/ProjectOptions';
import { useI18n } from 'vue-i18n';
import ProjectIcon from './ProjectIcon.vue';

const { t } = useI18n();

const props = defineProps({
    icon: String,
    color: String,
    errorIcon: String,
    errorColor: String
});

const emit = defineEmits(['update:icon', 'update:color']);

const selectIcon = (iconName) => {
    emit('update:icon', iconName);
};

const selectColor = (colorName) => {
    emit('update:color', colorName);
};
</script>

<template>
    <div class="space-y-6">
        <!-- Icon Selection -->
        <div>
            <label class="block text-sm font-medium text-surface-700 mb-2">{{ $t('component.project_icon') }}</label>
            <div class="grid grid-cols-8 gap-2">
                <button
                    v-for="iconName in PROJECT_ICONS"
                    :key="iconName"
                    type="button"
                    @click="selectIcon(iconName)"
                    :class="[
                        'flex items-center justify-center p-2 rounded-lg border-2 transition-all',
                        icon === iconName 
                            ? 'border-brand-500 bg-brand-50' 
                            : 'border-surface-100 hover:border-brand-200 hover:bg-surface-50'
                    ]"
                >
                    <ProjectIcon :icon="iconName" :color="color || 'gray'" size="xs" />
                </button>
            </div>
            <p v-if="errorIcon" class="text-xs text-red-600 mt-1">{{ errorIcon }}</p>
        </div>

        <!-- Color Selection -->
        <div>
            <label class="block text-sm font-medium text-surface-700 mb-2">{{ $t('component.project_color') }}</label>
            <div class="flex flex-wrap gap-3">
                <button
                    v-for="colorOption in PROJECT_COLORS"
                    :key="colorOption.name"
                    type="button"
                    @click="selectColor(colorOption.name)"
                    :class="[
                        'h-8 w-8 rounded-full border-2 transition-all shadow-sm',
                        color === colorOption.name 
                            ? 'border-surface-900 ring-2 ring-brand-200' 
                            : 'border-transparent hover:scale-110',
                        colorOption.bg
                    ]"
                    :title="$t(colorOption.labelKey)"
                ></button>
            </div>
            <p v-if="errorColor" class="text-xs text-red-600 mt-1">{{ errorColor }}</p>
        </div>
    </div>
</template>
