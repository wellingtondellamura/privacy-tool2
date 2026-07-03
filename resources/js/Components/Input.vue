<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: null,
    },
    placeholder: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const classes = computed(() => {
    let base = 'block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm transition-colors duration-smooth';
    let disabled = props.disabled ? 'bg-surface-50 text-surface-500 cursor-not-allowed' : '';
    let errorColor = props.error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '';
    
    return `${base} ${disabled} ${errorColor}`;
});
</script>

<template>
    <div>
        <label v-if="label" class="flex items-center gap-1.5 text-sm font-medium text-surface-700 mb-1">
            {{ label }}
            <!-- Tooltip icon for password fields -->
            <span v-if="tooltip" class="relative group inline-flex items-center">
                <svg
                    class="w-3.5 h-3.5 text-surface-400 cursor-help hover:text-brand-500 transition-colors duration-smooth"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-label="tooltip"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Tooltip bubble -->
                <span
                    class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-52
                           bg-surface-900 text-white text-xs rounded-lg px-3 py-2 shadow-lg
                           opacity-0 group-hover:opacity-100 transition-opacity duration-200
                           whitespace-normal text-center z-50"
                >
                    {{ tooltip }}
                    <!-- Arrow -->
                    <span class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-surface-900"></span>
                </span>
            </span>
        </label>
        <div class="relative rounded-md shadow-sm">
            <input 
                :type="type" 
                :value="modelValue" 
                @input="$emit('update:modelValue', $event.target.value)" 
                :disabled="disabled"
                :placeholder="placeholder"
                :class="classes"
            />
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600" id="email-error">
            {{ error }}
        </p>
    </div>
</template>

