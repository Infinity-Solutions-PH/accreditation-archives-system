<script setup>
    import { Link } from '@inertiajs/vue3';
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });

    const props = defineProps({
        areas: Array,
        event: Object
    });
</script>

<template>
    <!-- Scrollable Content -->
    <main class="flex-1 overflow-y-auto scroll-smooth w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav aria-label="Breadcrumb" class="flex mb-6">
            <ol class="flex items-center space-x-2">
                <li>
                    <Link :href="$page.props.auth.is_accreditor ? route('accreditor.dashboard') : route('events.index')" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium">Events</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">{{ event.title }}</span>
                </li>
                <!-- <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">File Repository</span>
                </li> -->
            </ol>
        </nav>
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2 max-w-2xl">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ event.title }}</h1>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    Manage and track accreditation documents. Access AACCUP areas and monitor compliance standards.
                </p>
            </div>
            <!-- <div class="flex items-center gap-3 shrink-0">
                <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">table_view</span>
                    Export to Excel
                </button>
                <button class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-md shadow-blue-500/20">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    Upload File
                </button>
            </div> -->
        </div>
        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="w-full lg:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-400">search</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white" placeholder="Search areas or files..." type="text"/>
                </div>
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <button class="group flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-transparent dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300">
                        Status: All <span class="material-symbols-outlined text-slate-500 text-[18px]">expand_more</span>
                    </button>
                    <button class="group flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-transparent dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300">
                        Type: All <span class="material-symbols-outlined text-slate-500 text-[18px]">expand_more</span>
                    </button>
                    <div class="w-px h-8 bg-slate-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-lg border border-slate-200 dark:border-slate-700">
                        <a class="p-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 transition-all flex items-center focus:bg-white focus:text-primary dark:focus:bg-slate-700 dark:focus:text-white" href="#folder-view">
                            <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        </a>
                        <a class="p-1.5 rounded-md text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 transition-all flex items-center focus:bg-white focus:text-primary dark:focus:bg-slate-700 dark:focus:text-white" href="#list-view">
                            <span class="material-symbols-outlined text-[20px]">list</span>
                        </a>
                    </div>
                    <button class="p-2 text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8" id="folder-container">
            <div v-for="area in areas" :key="area.id"
                class="group cursor-pointer bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-5 hover:border-primary/50 hover:shadow-lg transition-all">
                <Link :href="route('areas.slug', { event: event.slug, area: area.slug })">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[32px] fill-[1]">folder</span>
                        </div>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">AACCUP</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">{{ area.code.toUpperCase() }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-snug line-clamp-2 min-h-[2.5rem]">{{ area.name }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">{{ area.files_count }} Files</span>
                        <span class="material-symbols-outlined text-[18px] text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                </Link>
            </div>
        </div>
    </main>
</template>