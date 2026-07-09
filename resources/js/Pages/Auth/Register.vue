<script setup>
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const { t } = useI18n();

const currentStep = ref(1);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    profile: '',
    affiliation: '',
});

// Front-end step validation
const isStep1Valid = computed(() => {
    return form.name.trim().length > 0 && 
           form.email.trim().length > 0 && 
           form.email.includes('@');
});

const isStep2Valid = computed(() => {
    return form.password.length >= 8 && 
           form.password === form.password_confirmation;
});

const nextStep = () => {
    if (currentStep.value === 1 && isStep1Valid.value) {
        currentStep.value = 2;
    } else if (currentStep.value === 2 && isStep2Valid.value) {
        currentStep.value = 3;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const selectProfile = (val) => {
    if (form.profile === val) {
        form.profile = '';
    } else {
        form.profile = val;
    }
    form.clearErrors('profile');
};

const submit = () => {
    if (currentStep.value === 3 && form.terms) {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            onError: (errors) => {
                if (errors.name || errors.email) {
                    currentStep.value = 1;
                } else if (errors.password || errors.password_confirmation) {
                    currentStep.value = 2;
                }
            }
        });
    }
};
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex items-center justify-center p-4 sm:p-6 md:p-8 lg:p-12 font-sans relative overflow-hidden">
        <!-- Grid pattern matching Welcome page -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#cbd5e1_1px,transparent_1px),linear-gradient(to_bottom,#cbd5e1_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-25 pointer-events-none"></div>
        <!-- Glow background decorations (Ambient Lighting) -->
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-brand-500/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-indigo-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <Head :title="$t('auth.register_title')" />

        <!-- Centered Card Container -->
        <div class="w-full max-w-5xl bg-white rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(15,23,42,0.12)] overflow-hidden flex flex-col md:flex-row border border-surface-200 relative z-10 min-h-[580px] lg:min-h-[640px] transition-all duration-smooth">
            
            <!-- Left Onboarding Panel (Illustration & Steps info) -->
            <div class="md:w-[42%] bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 text-white flex flex-col justify-between p-8 lg:p-10 relative overflow-hidden hidden md:flex">
                <!-- Inner glow -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 blur-3xl rounded-full translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-300/10 blur-3xl rounded-full -translate-x-1/3 translate-y-1/3"></div>

                <!-- Header logo -->
                <div class="relative z-10 flex items-center">
                    <Link href="/" class="text-white hover:opacity-90 transition-opacity duration-smooth">
                        <ApplicationLogo class="h-8 w-auto text-white" />
                    </Link>
                </div>

                <!-- Step Illustration and Dynamic Sub-copy -->
                <div class="relative z-10 my-auto py-6">
                    <!-- SVG Illustration -->
                    <div class="mb-6">
                        <svg class="w-full max-w-[160px] lg:max-w-[190px] aspect-square mx-auto text-brand-200 opacity-95 drop-shadow-xl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="80" fill="url(#bgGradient)" />
                            <!-- Connecting lines -->
                            <path d="M60 100 L100 60 L140 100 L100 140 Z" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" class="animate-[spin_40s_linear_infinite]" style="transform-origin: 100px 100px;" />
                            <line x1="100" y1="20" x2="100" y2="180" stroke="currentColor" stroke-width="0.75" opacity="0.4" />
                            <line x1="20" y1="100" x2="180" y2="100" stroke="currentColor" stroke-width="0.75" opacity="0.4" />
                            <!-- Core shield/lock -->
                            <g transform="translate(75, 70)">
                                <path d="M25 0 L50 10 V35 C50 48.75 39.25 58.5 25 62 C10.75 58.5 0 48.75 0 35 V10 L25 0 Z" fill="url(#shieldGrad)" />
                                <!-- Dynamic Icons inside the shield based on steps -->
                                <path v-if="currentStep === 1" d="M15 22 h20 M15 32 h20 M15 42 h12" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
                                <path v-else-if="currentStep === 2" d="M25 18 v16 M20 28 h10 M17 40 h16" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
                                <path v-else d="M16 28 L22 34 L34 20" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <!-- Orbiting dots -->
                            <circle cx="100" cy="20" r="6" fill="#fff" class="animate-pulse" />
                            <circle cx="100" cy="180" r="6" fill="#fff" />
                            <circle cx="20" cy="100" r="6" fill="#cbd5e1" />
                            <circle cx="180" cy="100" r="6" fill="#fff" class="animate-pulse" />
                            
                            <defs>
                                <radialGradient id="bgGradient" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                                </radialGradient>
                                <linearGradient id="shieldGrad" x1="25" y1="0" x2="25" y2="62" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#ffffff" />
                                    <stop offset="100%" stop-color="#e2e8f0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>

                    <h2 class="text-xl lg:text-2xl font-extrabold tracking-tight mb-3 text-center leading-snug">
                        {{ $t('auth.welcome_title') }}
                    </h2>
                    <p class="text-brand-100 text-xs lg:text-sm leading-relaxed text-center opacity-85">
                        {{ $t('auth.welcome_description') }}
                    </p>
                </div>

                <!-- Onboarding steps guide -->
                <div class="relative z-10 space-y-3.5">
                    <div 
                        v-for="step in 3" 
                        :key="step"
                        class="flex items-center gap-3.5 transition-all duration-smooth"
                        :class="[currentStep === step ? 'opacity-100' : 'opacity-40']"
                    >
                        <div 
                            class="w-7.5 h-7.5 rounded-full border-2 flex items-center justify-center font-bold text-xs shrink-0"
                            :class="[
                                currentStep === step 
                                    ? 'border-white bg-white text-brand-700 shadow-md ring-4 ring-white/20' 
                                    : (currentStep > step ? 'border-emerald-400 bg-emerald-400 text-white' : 'border-brand-400 text-brand-200')
                            ]"
                        >
                            <span v-if="currentStep > step">✓</span>
                            <span v-else>{{ step }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold leading-none text-white">
                                {{ $t(`auth.step_${step}_title`) }}
                            </p>
                            <p class="text-[10px] text-brand-200 mt-1 leading-none">
                                {{ $t(`auth.step_${step}_subtitle`) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer info -->
                <div class="relative z-10 pt-4 mt-4 border-t border-white/10 text-[10px] text-brand-200 flex justify-between items-center">
                    <span>{{ $t('results.footer.developed_with') }}</span>
                    <span>v1.0</span>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="flex-1 flex flex-col justify-center py-10 px-6 sm:px-12 md:px-14 lg:px-16 bg-white relative">
                <!-- Loading Overlay (Visibility of System Status Heuristic) -->
                <transition name="fade">
                    <div 
                        v-if="form.processing" 
                        class="absolute inset-0 bg-white/95 backdrop-blur-md z-50 flex flex-col items-center justify-center p-6 text-center animate-fade-in"
                    >
                        <div class="relative flex items-center justify-center mb-6">
                            <div class="absolute w-20 h-20 rounded-full border-4 border-brand-500/20 animate-ping"></div>
                            <svg class="animate-spin h-12 w-12 text-brand-500 relative z-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <h3 class="text-xl font-bold text-surface-900 mb-2">
                            {{ $t('auth.creating_account_title') }}
                        </h3>
                        <p class="text-sm text-surface-500 max-w-xs leading-relaxed">
                            {{ $t('auth.creating_account_description') }}
                        </p>
                    </div>
                </transition>

                <!-- Mobile Header -->
                <div class="absolute top-6 left-6 right-6 flex items-center justify-between md:hidden">
                    <Link href="/">
                        <ApplicationLogo class="h-8 w-auto text-brand-900" />
                    </Link>
                    <div class="text-xs font-semibold text-surface-500 bg-surface-100 px-3 py-1 rounded-full">
                        Passo {{ currentStep }} de 3
                    </div>
                </div>

                <!-- Top Nav Info (Link to Login) -->
                <div class="hidden sm:block absolute top-6 right-8 lg:right-12 text-sm text-surface-500">
                    {{ $t('auth.has_account') }}
                    <Link 
                        :href="route('login')" 
                        class="font-semibold text-brand-600 hover:text-brand-500 transition-colors duration-smooth underline underline-offset-4"
                    >
                        {{ $t('auth.login_title') }}
                    </Link>
                </div>

                <!-- Form Content Wrapper -->
                <div class="mx-auto w-full max-w-md mt-6 md:mt-0">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                            {{ $t('auth.register_title') }}
                        </h1>
                        <p class="text-sm text-surface-500 mt-1">
                            {{ $t(`auth.step_${currentStep}_subtitle`) }}
                        </p>

                        <!-- Segmented Onboarding Progress Bar (Visual) -->
                        <div class="flex gap-1.5 mt-4">
                            <div 
                                v-for="s in 3" 
                                :key="s"
                                class="h-1.5 flex-1 rounded-full transition-all duration-smooth"
                                :class="[
                                    s <= currentStep ? 'bg-brand-500' : 'bg-surface-200'
                                ]"
                            ></div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Step 1: Personal Info -->
                        <div v-if="currentStep === 1" class="space-y-4 animate-fade-in">
                            <Input
                                :label="$t('auth.name')"
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                :error="form.errors.name"
                                :placeholder="$t('auth.name_placeholder')"
                                @input="form.clearErrors('name')"
                            />

                            <Input
                                :label="$t('auth.email_professional')"
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :error="form.errors.email"
                                :placeholder="$t('auth.email_placeholder')"
                                @input="form.clearErrors('email')"
                            />
                        </div>

                        <!-- Step 2: Security -->
                        <div v-if="currentStep === 2" class="space-y-4 animate-fade-in">
                            <Input
                                :label="$t('auth.password')"
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                :error="form.errors.password"
                                placeholder="••••••••"
                                :tooltip="$t('auth.password_tooltip')"
                                @input="form.clearErrors('password')"
                            />

                            <Input
                                :label="$t('auth.password_confirm')"
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                :error="form.errors.password_confirmation"
                                placeholder="••••••••"
                                :tooltip="$t('auth.password_tooltip')"
                                @input="form.clearErrors('password_confirmation')"
                            />
                        </div>

                        <!-- Step 3: User Profile & Affiliation -->
                        <div v-if="currentStep === 3" class="space-y-4 animate-fade-in">
                            <!-- Clickable profile cards -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-surface-700">
                                    {{ $t('auth.profile_label') }}
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button
                                        type="button"
                                        @click="selectProfile('student')"
                                        :class="[
                                            'flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-smooth select-none cursor-pointer',
                                            form.profile === 'student'
                                                ? 'border-brand-500 bg-brand-50/50 ring-2 ring-brand-500/20 text-brand-700 shadow-sm'
                                                : 'border-surface-200 hover:border-brand-300 hover:bg-surface-50 text-surface-600'
                                        ]"
                                    >
                                        <span class="text-xl mb-1 select-none">🎓</span>
                                        <span class="font-bold text-xs select-none">{{ $t('auth.profile_student') }}</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="selectProfile('professional')"
                                        :class="[
                                            'flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-smooth select-none cursor-pointer',
                                            form.profile === 'professional'
                                                ? 'border-brand-500 bg-brand-50/50 ring-2 ring-brand-500/20 text-brand-700 shadow-sm'
                                                : 'border-surface-200 hover:border-brand-300 hover:bg-surface-50 text-surface-600'
                                        ]"
                                    >
                                        <span class="text-xl mb-1 select-none">💼</span>
                                        <span class="font-bold text-xs select-none">{{ $t('auth.profile_professional') }}</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="selectProfile('researcher')"
                                        :class="[
                                            'flex flex-col items-center justify-center p-3 rounded-xl border text-center transition-all duration-smooth select-none cursor-pointer',
                                            form.profile === 'researcher'
                                                ? 'border-brand-500 bg-brand-50/50 ring-2 ring-brand-500/20 text-brand-700 shadow-sm'
                                                : 'border-surface-200 hover:border-brand-300 hover:bg-surface-50 text-surface-600'
                                        ]"
                                    >
                                        <span class="text-xl mb-1 select-none">🔬</span>
                                        <span class="font-bold text-xs select-none">{{ $t('auth.profile_researcher') }}</span>
                                    </button>
                                </div>
                                <p v-if="form.errors.profile" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.profile }}
                                </p>
                            </div>

                            <!-- Affiliation Input -->
                            <Input
                                :label="$t('auth.affiliation_label')"
                                id="affiliation"
                                type="text"
                                v-model="form.affiliation"
                                :error="form.errors.affiliation"
                                :placeholder="$t('auth.affiliation_placeholder')"
                                @input="form.clearErrors('affiliation')"
                            />

                            <!-- Terms Checkbox -->
                            <div class="flex flex-col gap-1.5 pt-1">
                                <div class="flex items-start gap-3">
                                    <Checkbox
                                        id="terms"
                                        :checked="form.terms"
                                        @update:checked="form.terms = $event"
                                        class="mt-1 shrink-0"
                                    />
                                    <label for="terms" class="text-xs text-surface-600 leading-normal cursor-pointer select-none">
                                        {{ $t('auth.terms_accept') }}
                                        <a
                                            :href="route('terms.use')"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="font-semibold text-brand-600 hover:text-brand-500 underline underline-offset-2 transition-colors duration-smooth"
                                        >
                                            {{ $t('auth.terms_of_use') }}
                                        </a>
                                        {{ $t('auth.and') }}
                                        <a
                                            :href="route('privacy.policy')"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="font-semibold text-brand-600 hover:text-brand-500 underline underline-offset-2 transition-colors duration-smooth"
                                        >
                                            {{ $t('auth.privacy_policy') }}
                                        </a>.
                                    </label>
                                </div>
                                <p v-if="form.errors.terms" class="text-xs text-red-600 pl-7">
                                    {{ form.errors.terms }}
                                </p>
                            </div>
                        </div>

                        <!-- Navigation Action Buttons -->
                        <div class="flex items-center justify-between gap-4 pt-4 border-t border-surface-100">
                            <Button
                                v-if="currentStep > 1"
                                type="button"
                                variant="secondary"
                                @click="prevStep"
                                class="px-5 cursor-pointer"
                            >
                                {{ $t('auth.back') }}
                            </Button>
                            <div v-else></div> <!-- spacer when back is hidden -->

                            <!-- Next Button (Step 1 or 2) -->
                            <Button
                                v-if="currentStep < 3"
                                type="button"
                                variant="primary"
                                @click="nextStep"
                                :disabled="currentStep === 1 ? !isStep1Valid : !isStep2Valid"
                                class="px-6 ml-auto cursor-pointer"
                            >
                                {{ $t('auth.next') }}
                            </Button>

                            <!-- Submit Button (Step 3) -->
                            <Button
                                v-else
                                type="submit"
                                variant="primary"
                                :disabled="form.processing || !form.terms"
                                class="px-6 ml-auto cursor-pointer"
                            >
                                {{ $t('auth.register_title') }}
                            </Button>
                        </div>
                    </form>

                    <!-- Mobile Link to Login -->
                    <div class="block sm:hidden text-center mt-8 text-sm text-surface-500">
                        {{ $t('auth.has_account') }}
                        <Link 
                            :href="route('login')" 
                            class="font-semibold text-brand-600 hover:text-brand-500 transition-colors duration-smooth"
                        >
                            {{ $t('auth.login_title') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

