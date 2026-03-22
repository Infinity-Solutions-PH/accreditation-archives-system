<script setup>
    import AppLayout from '@shared/Layouts/App.vue';
    import { Head } from '@inertiajs/vue3';

    defineOptions({
        layout: AppLayout
    });

    defineProps({
        collegeStats: {
            type: Array,
            default: () => []
        }
    });
</script>

<template>
    <Head title="Accreditation Taskforce" />
    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
        <div class="max-w-[1200px] mx-auto flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-text-main-light dark:text-white tracking-tight text-[28px] md:text-[32px] font-bold leading-tight">Accreditation Taskforce</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-normal leading-normal">Overview of accreditation taskforce members across all colleges.</p>
            </div>

            <!-- Taskforce Grid View (Admin & IDO Staff Only) -->
            <div v-if="collegeStats && collegeStats.length > 0" class="flex flex-col gap-4 mt-2">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div v-for="college in collegeStats" :key="college.id" class="flex flex-col gap-3 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-primary uppercase tracking-wider mb-1">{{ college.code }}</span>
                                <h3 class="text-base font-bold text-text-main-light dark:text-white leading-tight line-clamp-2" :title="college.name">{{ college.name }}</h3>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1 mt-2">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">badge</span>
                                <span class="text-slate-600 dark:text-slate-300 font-medium truncate" :title="college.officer">{{ college.officer }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-auto pt-4 border-t border-[#cfd7e7] dark:border-gray-700">
                            <div class="flex flex-col items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-900/30">
                                <span class="text-xs font-medium text-green-600 dark:text-green-400 mb-0.5">Active</span>
                                <span class="text-lg font-bold text-green-700 dark:text-green-300">{{ college.active_users }}</span>
                            </div>
                            <div class="flex flex-col items-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5">Inactive</span>
                                <span class="text-lg font-bold text-slate-700 dark:text-slate-300">{{ college.inactive_users }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
</template>
