<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { useForm, Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });

    const form = useForm({
        email: '',
        password: '',
    });

    const submit = () => {
        form.post(route('accreditor.login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<template>
    <Head>
        <title>Auth</title>
    </Head>
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Hero Image -->
        <div class="hidden lg:flex w-1/2 relative bg-primary/5 overflow-hidden">
            <div class="absolute inset-0 z-10 bg-primary/10 mix-blend-multiply"></div>
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <img alt="University Background" class="w-full h-full object-cover" data-alt="University campus building with students walking" src="https://cvsu.edu.ph/wp-content/uploads/2023/09/CvSU-Front-09-2023-scaled.jpg"/>
            <div class="absolute bottom-0 left-0 w-full p-12 z-30 text-white">
                <div class="mb-6 inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full border border-white/30 text-xs font-semibold uppercase tracking-wider">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    External Access Portal
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-4">Accreditor Verification</h2>
                <p class="text-lg text-white/90 max-w-md leading-relaxed">Secure access point for external evaluators to review CvSU's accreditation documents and archival records.</p>
            </div>
        </div>
        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col relative bg-background-light dark:bg-background-dark">
            <!-- Mobile/Top Minimal Header -->
            <div class="w-full p-6 flex justify-between items-center lg:absolute lg:top-0 lg:right-0 z-10">
                <div class="lg:hidden flex items-center gap-2">
                    <div class="size-8 rounded bg-accreditor/10 flex items-center justify-center text-accreditor">
                        <span class="material-symbols-outlined text-xl">verified</span>
                    </div>
                    <span class="font-bold text-lg dark:text-white">CvSU Archives</span>
                </div>
                <div class="flex gap-4 ml-auto items-center">
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-accreditor transition-colors flex items-center gap-1" href="#">
                        <span class="material-symbols-outlined text-base">help</span>
                            Support
                    </a>
                </div>
            </div>
            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-24 overflow-y-auto">
                <div class="w-full max-w-[440px] space-y-8">
                    <div class="text-left space-y-2">
                        <div class="hidden lg:flex items-center gap-3 mb-6 text-[#0d121b] dark:text-white">
                            <div class="size-10 rounded-lg bg-accreditor/10 flex items-center justify-center text-accreditor">
                                <span class="material-symbols-outlined text-2xl">shield_person</span>
                            </div>
                            <h2 class="text-xl font-bold tracking-tight">CvSU Accreditation Archives</h2>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#0d121b] dark:text-white">Accreditor Login</h1>
                        <p class="text-slate-500 dark:text-slate-400">Enter your assigned temporary credentials provided by the system administrator.</p>
                    </div>
                    <form class="space-y-5" @submit.prevent="submit">
                        <div v-if="form.errors.email" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            {{ form.errors.email }}
                        </div>
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="access-id">Accreditor Email / ID</label>
                            <div class="relative">
                                <input v-model="form.email" class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-accreditor/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-accreditor h-12 placeholder:text-[#4c669a] dark:placeholder:text-slate-500 px-4 text-base font-normal leading-normal transition-all" id="access-id" placeholder="e.g. email@example.com" required="" type="email"/>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="access-code">Temporary Access Code</label>
                            <div class="relative">
                                <input v-model="form.password" class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-accreditor/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-accreditor h-12 placeholder:text-[#4c669a] dark:placeholder:text-slate-500 px-4 text-base font-normal leading-normal transition-all" id="access-code" placeholder="Enter your 8-digit code" required="" type="password"/>
                                <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-accreditor transition-colors focus:outline-none" type="button">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <div class="flex items-start gap-1.5 mt-2">
                                <span class="material-symbols-outlined text-xs text-amber-500 mt-0.5">warning</span>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Temporary codes expire after the scheduled evaluation period.</p>
                            </div>
                        </div>
                        <button :disabled="form.processing" class="w-full h-12 bg-accreditor hover:bg-accreditor-hover text-white font-semibold rounded-lg shadow-sm shadow-accreditor/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" type="submit">
                            <span>Authenticate Access</span>
                            <span class="material-symbols-outlined text-sm">lock_open</span>
                        </button>
                    </form>
                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                            <span class="flex-shrink-0 mx-4 text-slate-400 text-sm font-medium">Regular Personnel</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 flex flex-col items-center gap-3">
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                            Are you a university staff member or official?
                        </p>
                        <Link class="w-full h-11 border-2 border-primary text-primary hover:bg-primary hover:text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2 group" :href="route('auth')">
                            <span class="material-symbols-outlined text-sm transition-transform group-hover:-translate-x-1">arrow_back</span>
                            Return to Main Login
                        </Link>
                    </div>
                    <div class="pt-6 text-center">
                        <p class="text-xs text-slate-400 dark:text-slate-600">
                            © 2024 Cavite State University. Official Accreditation Portal.
                            <br/>
                            <a class="hover:text-accreditor transition-colors" href="#">Security Policy</a> · <a class="hover:text-accreditor transition-colors" href="#">Usage Guidelines</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>