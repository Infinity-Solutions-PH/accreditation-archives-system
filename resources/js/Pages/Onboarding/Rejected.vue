<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });
</script>

<template>
    <Head>
        <title>Access Rejected - CvSU Archives</title>
    </Head>
    <div class="flex min-h-screen w-full items-center justify-center bg-background-light dark:bg-background-dark p-6">
        <div class="w-full max-w-md bg-white dark:bg-surface-dark border border-red-200 dark:border-red-900/40 rounded-2xl shadow-xl p-8 flex flex-col items-center text-center">
            
            <div class="size-20 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mb-6 border-4 border-red-50 dark:border-red-900/10">
                <span class="material-symbols-outlined text-4xl">block</span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-red-600 dark:text-red-400 mb-3">Access Denied</h2>
            
            <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                Your request to access the accreditation system was rejected. Please contact the administrator via email at <strong>admin@cvsu.edu.ph</strong> for further assistance.
            </p>

            <Link :href="route('logout')" method="post" as="button" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-white text-[#0d121b] font-semibold rounded-lg transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Return to Login
            </Link>
        </div>
    </div>
</template>
