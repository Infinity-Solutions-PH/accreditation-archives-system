<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });
</script>

<template>
    <Head>
        <title>Pending Approval - CvSU Archives</title>
    </Head>
    <div class="flex min-h-screen w-full items-center justify-center bg-background-light dark:bg-background-dark p-6">
        <div class="w-full max-w-md bg-white dark:bg-surface-dark border border-[#cfd7e7] dark:border-slate-700 rounded-2xl shadow-xl p-8 flex flex-col items-center text-center">
            
            <div class="size-20 bg-amber-100 dark:bg-amber-900/30 text-amber-500 rounded-full flex items-center justify-center mb-6 border-4 border-amber-50 dark:border-amber-900/10">
                <span class="material-symbols-outlined text-4xl">hourglass_empty</span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-[#0d121b] dark:text-white mb-3">Approval Pending</h2>
            
            <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-8">
                Your account request has been received and is currently under review by our administrators. You will be able to access the system once your role is verified.
            </p>

            <Link :href="route('logout')" method="post" as="button" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-white text-[#0d121b] font-semibold rounded-lg transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Sign Out
            </Link>
        </div>
    </div>
</template>
