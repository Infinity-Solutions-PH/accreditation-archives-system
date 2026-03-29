<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });
</script>

<template>
    <Head>
        <title>No Active Accreditation - CvSU Archives</title>
    </Head>
    <div class="flex min-h-screen w-full items-center justify-center bg-background-light dark:bg-background-dark p-6">
        <div class="w-full max-w-md bg-white dark:bg-surface-dark border border-[#cfd7e7] dark:border-slate-700 rounded-2xl shadow-xl p-8 flex flex-col items-center text-center animate-in fade-in zoom-in duration-500">
            
            <div class="size-20 bg-amber-100 dark:bg-amber-900/10 text-amber-500 rounded-full flex items-center justify-center mb-6 border-4 border-amber-50 dark:border-amber-900/5">
                <span class="material-symbols-outlined text-4xl">event_busy</span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-[#0d121b] dark:text-white mb-3">No Active Accreditation</h2>
            
            <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-8">
                Your account is verified, but there are currently no active accreditation events assigned to you or available for your review. Please contact the IDO administrator if you believe this is an error.
            </p>

            <Link 
                :href="route('logout')" 
                method="post" 
                as="button" 
                class="w-full py-3 bg-slate-900 dark:bg-white dark:text-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all flex items-center justify-center gap-2 active:scale-[0.98] shadow-lg shadow-slate-200 dark:shadow-none"
            >
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Secure Sign Out
            </Link>
            
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 w-full text-center text-xs text-slate-400">
                Cavite State University Accreditation System
            </div>
        </div>
    </div>
</template>
