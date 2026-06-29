<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { useValidationRules } from '@/Composables/useValidationRules';
import { 
    CameraIcon, 
    EnvelopeIcon, 
    LockClosedIcon, 
    EyeIcon, 
    EyeSlashIcon, 
    ArrowRightOnRectangleIcon,
    ShieldCheckIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const { rules } = useValidationRules();
const validationRules = computed(() => ({
    email: rules.email,
    password: { required: rules.required },
}));

const v$ = useVuelidate(validationRules, form);

const showPassword = ref(false);

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-white text-slate-900 font-sans">
        <!-- Login Container -->
        <main class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px] transition-all duration-500 hover:shadow-[0_20px_50px_rgba(37,99,235,0.15)]">
            
            <!-- Left Side: Artistic Visual -->
            <section class="relative w-full md:w-1/2 overflow-hidden bg-slate-900 group hidden md:block">
                <div 
                    class="absolute inset-0 z-0 scale-105 group-hover:scale-110 transition-transform duration-700 ease-out bg-cover bg-center" 
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC49PS0yZiF3HR8VCDp_epD5dlUQrVOMWuztN2JV2d3ktGE618xcwphutDhrwesSSaBsLoEviweNl4PnYE2BMcNXmC4ngv7IHO6zDaxFksky6RkLW8BXQA2xDCs-GEvXhJrnsrx7AarqkvQ91E7eGE_4wZ7b1naCH6_HbeexpLPafweYB1aeG8XN-Z4L1gIwFCK4kua9Clf-q5iNapKhQ5Jdg8rhERlNH4IMT64nMovI0dBhHTJHuPbJhaWGLHGSGc4QAJGLvaWS2Y')"
                ></div>
                <!-- Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/30 to-transparent z-10"></div>
                <!-- Content on Image -->
                <div class="absolute bottom-0 left-0 p-12 z-20 text-white">
                    <h2 class="text-3xl font-bold tracking-tight mb-3">Capture the Moment</h2>
                    <p class="text-base text-blue-100 opacity-90 max-w-xs leading-relaxed">
                        Management tools designed specifically for the professional photography workflow.
                    </p>
                </div>
            </section>

            <!-- Right Side: Login Form -->
            <section class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white relative">
                
                <!-- Status Message -->
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ status }}
                </div>

                <!-- App Identity -->
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-md">
                        <CameraIcon class="w-6 h-6 text-white" />
                    </div>
                    <h1 class="text-3xl font-bold text-blue-600 tracking-tight">FotoApp</h1>
                </div>

                <!-- Header Text -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-1">Welcome back</h2>
                    <p class="text-base text-slate-500">Sign in to your account</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label 
                            for="email" 
                            class="block text-sm font-medium mb-2 transition-colors"
                            :class="v$.email.$error ? 'text-red-600' : 'text-slate-900'"
                        >
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <EnvelopeIcon 
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 transition-colors" 
                                :class="v$.email.$error ? 'text-red-400' : 'text-slate-400'"
                            />
                            <input 
                                id="email" 
                                type="email" 
                                v-model="form.email"
                                @blur="v$.email.$touch()"
                                placeholder="name@studio.com" 
                                class="w-full h-12 pl-10 pr-4 bg-slate-50 rounded-xl text-base transition-all placeholder:text-slate-400"
                                :class="v$.email.$error 
                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500 text-red-900' 
                                    : 'border-transparent focus:ring-2 focus:ring-blue-600 focus:border-transparent focus:bg-white'"
                                required 
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                        <p v-if="v$.email.$error" class="mt-2 text-sm text-red-600">
                            {{ v$.email.$errors[0].$message }}
                        </p>
                        <p v-else-if="form.errors.email" class="mt-2 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label 
                                for="password" 
                                class="block text-sm font-medium transition-colors"
                                :class="v$.password.$error ? 'text-red-600' : 'text-slate-900'"
                            >
                                Password <span class="text-red-500">*</span>
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-blue-600 text-sm font-semibold hover:underline"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <div class="relative">
                            <LockClosedIcon 
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 transition-colors" 
                                :class="v$.password.$error ? 'text-red-400' : 'text-slate-400'"
                            />
                            <input 
                                id="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                v-model="form.password"
                                @blur="v$.password.$touch()"
                                placeholder="••••••••" 
                                class="w-full h-12 pl-10 pr-12 bg-slate-50 rounded-xl text-base transition-all placeholder:text-slate-400"
                                :class="v$.password.$error 
                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500 text-red-900' 
                                    : 'border-transparent focus:ring-2 focus:ring-blue-600 focus:border-transparent focus:bg-white'"
                                required 
                                autocomplete="current-password"
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none p-1"
                            >
                                <EyeSlashIcon v-if="showPassword" class="w-5 h-5" />
                                <EyeIcon v-else class="w-5 h-5" />
                            </button>
                        </div>
                        <p v-if="v$.password.$error" class="mt-2 text-sm text-red-600">
                            {{ v$.password.$errors[0].$message }}
                        </p>
                        <p v-else-if="form.errors.password" class="mt-2 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            type="checkbox" 
                            v-model="form.remember"
                            class="w-4 h-4 text-blue-600 bg-slate-50 border-slate-300 rounded focus:ring-blue-600 cursor-pointer"
                        />
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 cursor-pointer select-none">
                            Keep me signed in
                        </label>
                    </div>

                    <!-- Sign In Button -->
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full h-12 bg-blue-600 hover:bg-blue-700 disabled:opacity-70 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 active:scale-[0.98] mt-8 flex items-center justify-center gap-2"
                    >
                        <span v-if="form.processing">Authenticating...</span>
                        <template v-else>
                            Sign In
                            <ArrowRightOnRectangleIcon class="w-5 h-5" />
                        </template>
                    </button>
                </form>

                <!-- Footer / Security Note -->
                <div class="mt-12 text-center">
                    <p class="text-xs font-semibold text-slate-400 flex items-center justify-center gap-1.5">
                        <ShieldCheckIcon class="w-4 h-4" />
                        Powered by FotoApp Admin Professional
                    </p>
                    <div class="mt-4 inline-block px-4 py-1.5 bg-slate-100 rounded-full">
                        <span class="text-xs font-semibold text-slate-500 italic">Access restricted to authorized studio personnel</span>
                    </div>
                </div>
            </section>
        </main>

        <!-- Background Decorations -->
        <div class="fixed top-0 right-0 -z-10 w-96 h-96 bg-blue-600/5 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="fixed bottom-0 left-0 -z-10 w-64 h-64 bg-indigo-600/5 rounded-full blur-2xl opacity-30 pointer-events-none"></div>
    </div>
</template>
