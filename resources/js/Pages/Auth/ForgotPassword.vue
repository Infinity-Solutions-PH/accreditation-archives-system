<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { useForm, Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });

    defineProps({
        status: String,
    });

    const form = useForm({
        email: '',
    });

    const submit = () => {
        form.post(route('password.email'));
    };
</script>

<template>
    <Head>
        <title>Forgot Password</title>
    </Head>
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Hero Image (Consistent with Login) -->
        <div class="hidden lg:flex w-1/2 relative bg-primary/5 overflow-hidden">
            <div class="absolute inset-0 z-10 bg-primary/10 mix-blend-multiply"></div>
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <img alt="University Background" class="w-full h-full object-cover shadow-2xl" src="https://cvsu.edu.ph/wp-content/uploads/2023/09/CvSU-Front-09-2023-scaled.jpg"/>
            <div class="absolute bottom-0 left-0 w-full p-12 z-30 text-white">
                <div class="mb-4 size-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-white text-3xl">lock_reset</span>
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-4">Account Recovery</h2>
                <p class="text-lg text-white/90 max-w-md leading-relaxed">Forgot your credentials? No worries. We'll help you securely reset your password to get you back to the archives.</p>
            </div>
        </div>
        
        <!-- Right Side: Request Form -->
        <div class="w-full lg:w-1/2 flex flex-col relative bg-background-light dark:bg-background-dark">
            <!-- Navigation -->
            <div class="w-full p-6 flex justify-between items-center lg:absolute lg:top-0 lg:right-0 z-10">
                <Link :href="route('auth')" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-all group">
                    <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-1">arrow_back</span>
                    Back to Login
                </Link>
            </div>

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-24 overflow-y-auto">
                <div class="w-full max-w-[440px] space-y-8">
                    <!-- Header Section -->
                    <div class="text-left space-y-2">
                        <div class="hidden lg:flex items-center gap-3 mb-6 text-[#0d121b] dark:text-white">
                            <div class="size-10 p-2 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <img src="https://library.cvsu.edu.ph/landing/storage/images/CvSU-logo-64x64.webp" alt="CvSU Logo">
                            </div>
                            <h2 class="text-xl font-bold tracking-tight">CvSU Archives</h2>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#0d121b] dark:text-white">Forgot Password?</h1>
                        <p class="text-slate-500 dark:text-slate-400">Enter your university email and we'll send you a secure link to reset your password.</p>
                    </div>

                    <!-- Main Form -->
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Success Status -->
                        <div v-if="status" class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-800 dark:text-green-300 text-sm flex items-center gap-3 animate-in fade-in duration-500">
                            <span class="material-symbols-outlined">check_circle</span>
                            {{ status }}
                        </div>

                        <!-- Error Message -->
                        <div v-if="form.errors.email" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-800 dark:text-red-300 text-sm flex items-center gap-3 animate-in fade-in duration-500">
                            <span class="material-symbols-outlined">error</span>
                            {{ form.errors.email }}
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="email">University Email</label>
                            <div class="relative">
                                <input 
                                    v-model="form.email" 
                                    class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 placeholder:text-slate-400 px-4 text-base font-normal transition-all" 
                                    id="email" 
                                    placeholder="username@cvsu.edu.ph" 
                                    required 
                                    type="email"
                                    :disabled="form.processing"
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">alternate_email</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            :disabled="form.processing" 
                            class="w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-lg shadow-lg shadow-primary/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed group"
                            type="submit"
                        >
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin">progress_activity</span>
                            <span v-else>Email Password Reset Link</span>
                            <span v-if="!form.processing" class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">send</span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="pt-6 text-center">
                        <p class="text-xs text-slate-400 dark:text-slate-600 leading-relaxed">
                            © 2024 Cavite State University. All rights reserved.
                            <br/>
                            <a class="hover:text-primary transition-colors" href="#">Security Policy</a> · <a class="hover:text-primary transition-colors" href="#">Support</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
