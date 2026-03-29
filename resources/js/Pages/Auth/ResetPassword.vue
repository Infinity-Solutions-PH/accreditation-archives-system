<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { useForm, Head } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });

    const props = defineProps({
        email: String,
        token: String,
    });

    const form = useForm({
        token: props.token,
        email: props.email,
        password: '',
        password_confirmation: '',
    });

    const submit = () => {
        form.post(route('password.store'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>

<template>
    <Head>
        <title>Reset Password</title>
    </Head>
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Hero Image -->
        <div class="hidden lg:flex w-1/2 relative bg-primary/5 overflow-hidden">
            <div class="absolute inset-0 z-10 bg-primary/10 mix-blend-multiply"></div>
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <img alt="University Background" class="w-full h-full object-cover" src="https://cvsu.edu.ph/wp-content/uploads/2023/09/CvSU-Front-09-2023-scaled.jpg"/>
            <div class="absolute bottom-0 left-0 w-full p-12 z-30 text-white">
                <div class="mb-4 size-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-white text-3xl">key</span>
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-4">Set Your New Password</h2>
                <p class="text-lg text-white/90 max-w-md leading-relaxed">Choose a strong, secure password to protect your archival contributions and university data access.</p>
            </div>
        </div>
        
        <!-- Right Side: Reset Form -->
        <div class="w-full lg:w-1/2 flex flex-col relative bg-background-light dark:bg-background-dark">
            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-24 overflow-y-auto w-full">
                <div class="w-full max-w-[440px] space-y-8">
                    <!-- Header Section -->
                    <div class="text-left space-y-2">
                        <div class="hidden lg:flex items-center gap-3 mb-6">
                            <div class="size-10 p-2 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <img src="https://library.cvsu.edu.ph/landing/storage/images/CvSU-logo-64x64.webp" alt="CvSU Logo">
                            </div>
                            <h2 class="text-xl font-bold tracking-tight dark:text-white">CvSU Archives</h2>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#0d121b] dark:text-white">Secure Reset</h1>
                        <p class="text-slate-500 dark:text-slate-400">Please enter your new password to complete the account recovery process.</p>
                    </div>

                    <!-- Main Form -->
                    <form class="space-y-5" @submit.prevent="submit">
                        <!-- Error Alerts -->
                        <div v-if="Object.keys(form.errors).length > 0" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-800 dark:text-red-300 text-sm space-y-1">
                            <div v-for="(error, key) in form.errors" :key="key" class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                <span>{{ error }}</span>
                            </div>
                        </div>

                        <!-- Email (Hidden/Disabled) -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="email">Confirmed Email</label>
                            <div class="relative">
                                <input 
                                    v-model="form.email" 
                                    class="form-input flex w-full rounded-lg text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 border border-[#cfd7e7] dark:border-slate-700 h-12 px-4 transition-all opacity-70 cursor-not-allowed" 
                                    id="email" 
                                    required 
                                    type="email"
                                    readonly
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">verified</span>
                                </div>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="password">New Password</label>
                            <div class="relative group">
                                <input 
                                    v-model="form.password" 
                                    class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 placeholder:text-slate-400 px-4 text-base transition-all" 
                                    id="password" 
                                    placeholder="Set a strong password" 
                                    required 
                                    type="password"
                                    autocomplete="new-password"
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px] group-focus-within:text-primary transition-colors">lock</span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="password_confirmation">Confirm Password</label>
                            <div class="relative group">
                                <input 
                                    v-model="form.password_confirmation" 
                                    class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 placeholder:text-slate-400 px-4 text-base transition-all" 
                                    id="password_confirmation" 
                                    placeholder="Repeat your password" 
                                    required 
                                    type="password"
                                    autocomplete="new-password"
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px] group-focus-within:text-primary transition-colors">lock_reset</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Button -->
                        <button 
                            :disabled="form.processing" 
                            class="w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-lg shadow-lg shadow-primary/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed group"
                            type="submit"
                        >
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin">progress_activity</span>
                            <span v-else>Update Password</span>
                            <span v-if="!form.processing" class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">security</span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="pt-6 text-center">
                        <p class="text-xs text-slate-400 dark:text-slate-600">
                            © 2024 Cavite State University. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
