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
    }
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
        <label v-if="label" class="block text-sm font-medium text-surface-700 mb-1">
            {{ label }}
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
