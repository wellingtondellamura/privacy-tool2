<script setup>
import { computed } from 'vue';
import { PROJECT_COLORS } from '@/Constants/ProjectOptions';

const props = defineProps({
    icon: {
        type: String,
        default: 'briefcase'
    },
    color: {
        type: String,
        default: 'gray'
    },
    size: {
        type: String,
        default: 'md'
    }
});

const colorScheme = computed(() => {
    return PROJECT_COLORS.find(c => c.name === props.color) || PROJECT_COLORS[0];
});

const sizeClasses = computed(() => {
    return {
        'xs': 'h-6 w-6 text-[10px]',
        'sm': 'h-8 w-8 text-xs',
        'md': 'h-10 w-10 text-sm',
        'lg': 'h-12 w-12 text-base',
    }[props.size];
});

// Helper for Lucide-like icons if needed, but since we are minimalist, we can use a simple SVG switch or a mapping
</script>

<template>
    <div 
        :class="[
            'flex items-center justify-center rounded-xl transition-all duration-300 shadow-sm shrink-0',
            colorScheme.bg,
            colorScheme.text,
            sizeClasses
        ]"
    >
        <div class="flex items-center justify-center w-full h-full p-[18%]">
            <span v-if="icon === 'server'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3V7.5a3 3 0 013-3h13.5a3 3 0 013 3v3.75a3 3 0 01-3 3m-13.5 0a3 3 0 00-3 3v.75c0 1.243 1.007 2.25 2.25 2.25h13.5c1.243 0 2.25-1.007 2.25-2.25v-.75a3 3 0 00-3-3m-9.75 3.75h.008v.008H9.75v-.008zm.375 0h.008v.008H10.125v-.008zm3.75 0h.008v.008H13.875v-.008zm.375 0h.008v.008H14.25v-.008z" /></svg></span>
            <span v-else-if="icon === 'database'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg></span>
            <span v-else-if="icon === 'code-bracket'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" /></svg></span>
            <span v-else-if="icon === 'cpu-chip'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5m-15 3H3m18 0h-1.5m-15 3H3m18 0h-1.5m-15 3H3m18 0h-1.5M6.75 6.75h10.5v10.5H6.75V6.75zm.75 2.25v6h9v-6h-9zM9 3v1.5m3-1.5v1.5m3-1.5v1.5M9 21v-1.5m3 1.5v-1.5m3 1.5v-1.5M3 9h1.5m-1.5 3h1.5m-1.5 3h1.5m1.5 3V21M3 15h1.5m15-6H18m1.5 3H18m1.5 3H18m-1.5 3V21" /></svg></span>
            <span v-else-if="icon === 'window'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg></span>
            <span v-else-if="icon === 'command-line'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" /></svg></span>
            <span v-else-if="icon === 'puzzle-piece'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.08V4.5a2.25 2.25 0 00-4.5 0v1.58c0 .13.02.26.07.38a2.25 2.25 0 01-1.97 3.01H6.26a2.24 2.24 0 00-2.21 1.83l-.33 1.95a2.25 2.25 0 001.07 2.33l1.53.86a2.25 2.25 0 011.08 2.6c-.1.35-.35.63-.7.75v.01l-1.95.66a2.25 2.25 0 00-1.5 2.12v1.43c0 1.243 1.007 2.25 2.25 2.25h1.43a2.25 2.25 0 002.12-1.5l.67-1.96a2.25 2.25 0 013.35-1.28l1.53.85a2.25 2.25 0 002.33-1.07l.86-1.53a2.25 2.25 0 012.6-1.08c.35.1.63.35.75.7l.01.01 1.1 3.25a2.25 2.25 0 002.12 1.5h1.43c1.243 0 2.25-1.007 2.25-2.25v-1.43a2.25 2.25 0 00-1.5-2.12l-3.25-1.1c-.35-.12-.6-.37-.7-.72a2.25 2.25 0 011.08-2.6l1.53-.86a2.25 2.25 0 001.07-2.33l-.33-1.95a2.24 2.24 0 00-2.21-1.83h-1.58c-.13 0-.26-.02-.38-.07a2.25 2.25 0 01-3.01-1.97z" /></svg></span>
            <span v-else-if="icon === 'shield-check'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg></span>
            <span v-else-if="icon === 'lock-closed'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg></span>
            <span v-else-if="icon === 'fingerprint'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.507 10.29m-6.34 1.744a3.75 3.75 0 00-4.712 3.749m11.056-11.411A15 15 0 0012 3m0 0c-3.284 0-6.425.529-9.358 1.507m12.358-1.507A4.5 4.5 0 0012 3.75m0 0a4.5 4.5 0 01-4.5 4.5m4.5-4.5a4.5 4.5 0 00-4.5 4.5m4.5-4.5V1.5m0 2.25a4.5 4.5 0 00-4.5 4.5m4.5-4.5a4.5 4.5 0 014.5 4.5m-4.5-4.5a4.5 4.5 0 004.5 4.5m-4.5-4.5V1.5m0 2.25c.84 0 1.638.169 2.364.475m-14.728 0A15.015 15.015 0 0112 1.5m0 16.5a15 15 0 01-9.358-3.243m12.358 3.243A4.5 4.5 0 0012 17.25m0 0a4.5 4.5 0 01-4.5-4.5m4.5 4.5a4.5 4.5 0 014.5-4.5m-4.5 4.5v2.25m0-2.25a4.5 4.5 0 00-4.5-4.5m4.5 4.5a4.5 4.5 0 014.5 4.5m-4.5-4.5a4.5 4.5 0 004.5 4.5m-4.5-4.5v2.25m0-2.25a15 15 0 01 9.358-12.757" /></svg></span>
            <span v-else-if="icon === 'globe-alt'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.015 9.015 0 018.716 2.253M12 3a9.015 9.015 0 00-8.716 2.253m17.432 0c.326.69.518 1.461.518 2.275a9.015 9.015 0 01-8.716 2.253m8.716-2.253c0 .013 0 .027 0 .041m-17.432 0c0-.014 0-.028 0-.041m0 0c0-.814.192-1.585.518-2.275m0 0a9.015 9.015 0 018.716-2.253" /></svg></span>
            <span v-else-if="icon === 'cloud-arrow-up'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg></span>
            <span v-else-if="icon === 'rocket-launch'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg></span>
            <span v-else-if="icon === 'cog'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-7.5 7.5V21m0-16.5V3m0 0a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm-3.712 1.338a1.5 1.5 0 11-2.122 2.122 1.5 1.5 0 012.122-2.122zm10.55 10.55a1.5 1.5 0 11-2.122 2.122 1.5 1.5 0 012.122-2.122zm-12.672 0a1.5 1.5 0 112.122 2.122 1.5 1.5 0 01-2.122-2.122zm10.55-10.55a1.5 1.5 0 112.122-2.122 1.5 1.5 0 01-2.122 2.122z" /></svg></span>
            <span v-else-if="icon === 'chart-pie'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg></span>
            <span v-else-if="icon === 'key'" class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg></span>
            <span v-else class="w-full h-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg></span>
        </div>
    </div>
</template>
