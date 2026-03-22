<script setup>
    import AppLayout from '@shared/Layouts/App.vue';
    import { Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AppLayout
    });

    defineProps({
        logs: Object,
        filters: Object,
    });
</script>

<template>
    <main class="layout-container flex h-full grow flex-col overflow-y-auto">
        <div class="px-4 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col max-w-[1200px] flex-1 w-full">
        <!-- Breadcrumbs -->
        <div class="flex flex-wrap gap-2 px-4 pt-4 pb-2">
        <a class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Home</a>
        <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
        <a class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Admin</a>
        <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
        <span class="text-[#0d121b] dark:text-white text-sm font-medium leading-normal">Activity Logs</span>
        </div>
        <!-- Page Heading -->
        <div class="flex flex-col md:flex-row justify-between gap-6 px-4 py-6">
        <div class="flex flex-col gap-2">
        <h1 class="text-[#0d121b] dark:text-white text-3xl md:text-4xl font-bold leading-tight tracking-[-0.033em]">System Activity Logs</h1>
        <p class="text-[#4c669a] dark:text-gray-400 text-base font-normal leading-normal max-w-2xl">
                                        Monitor system integrity. View a detailed history of all document uploads, role modifications, and authentication events.
                                    </p>
        </div>
        <div class="flex items-start md:items-center">
        <button class="flex items-center justify-center gap-2 overflow-hidden rounded-lg h-10 px-6 bg-primary hover:bg-blue-700 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors shadow-md shadow-blue-500/20">
        <span class="material-symbols-outlined text-[20px]">download</span>
        <span class="truncate">Export Logs</span>
        </button>
        </div>
        </div>
        <!-- Filters and Search Toolbar -->
        <div class="flex flex-col gap-4 px-4 pb-6">
        <div class="bg-white dark:bg-[#151c2b] rounded-xl p-4 shadow-sm border border-[#e7ebf3] dark:border-gray-800 flex flex-col lg:flex-row gap-4 justify-between items-center">
        <!-- Search Bar -->
        <div class="w-full lg:w-1/3">
        <label class="flex flex-col w-full h-10">
        <div class="flex w-full flex-1 items-stretch rounded-lg h-full border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all">
        <div class="text-[#4c669a] flex items-center justify-center pl-3 pr-2">
        <span class="material-symbols-outlined text-[20px]">search</span>
        </div>
        <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden bg-transparent text-[#0d121b] dark:text-white focus:outline-0 h-full placeholder:text-[#9ca3af] text-sm font-normal leading-normal" placeholder="Search user, action ID..."/>
        </div>
        </label>
        </div>
        <!-- Filter Chips -->
        <div class="flex flex-wrap gap-3 w-full lg:w-auto justify-start lg:justify-end">
        <div class="flex items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-500 hidden sm:block">Filters:</span>
        </div>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">calendar_today</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Last 30 Days</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">category</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Action: All</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">admin_panel_settings</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Role: All</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-dashed border-[#4c669a]/40 text-[#4c669a] hover:text-primary hover:border-primary hover:bg-primary/5 transition-all" title="Reset Filters">
        <span class="material-symbols-outlined text-[20px]">restart_alt</span>
        </button>
        </div>
        </div>
        </div>
        <!-- Data Table -->
        <div class="px-4 pb-4">
        <div class="w-full overflow-hidden rounded-xl border border-[#e7ebf3] dark:border-gray-800 bg-white dark:bg-[#151c2b] shadow-sm">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
        <thead>
        <tr class="border-b border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/50">
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 cursor-pointer hover:text-primary group select-none">
        <div class="flex items-center gap-1">
                                                            Timestamp
                                                            <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">arrow_downward</span>
        </div>
        </th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">User</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">Role</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">Action Type</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 w-1/3">Description</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 text-right">Details</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-[#e7ebf3] dark:divide-gray-800 text-sm">
        <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">{{ new Date(log.created_at).toLocaleDateString() }}</span>
        <span class="text-xs">{{ new Date(log.created_at).toLocaleTimeString() }}</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center shrink-0" :style="`background-image: url('https://ui-avatars.com/api/?name=${log.causer?.name || 'System'}')`"></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">{{ log.causer?.name || 'System' }}</p>
        <p class="text-xs text-[#4c669a]">{{ log.causer?.email || 'N/A' }}</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10">{{ log.causer?.roles?.[0]?.name || 'N/A' }}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20">
        <span class="size-1.5 rounded-full bg-amber-500"></span>
        {{ log.log_name || 'System Event' }}
        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
        {{ log.description }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="flex items-center justify-between border-t border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/30 px-6 py-3">
        <div class="hidden sm:flex flex-1 items-center justify-between">
        <div>
        <p class="text-sm text-[#4c669a] dark:text-gray-400">
            Showing <span class="font-medium text-[#0d121b] dark:text-white">{{ logs.from || 0 }}</span> to <span class="font-medium text-[#0d121b] dark:text-white">{{ logs.to || 0 }}</span> of <span class="font-medium text-[#0d121b] dark:text-white">{{ logs.total }}</span> results
        </p>
        </div>
        <div>
        <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
            <template v-for="(link, index) in logs.links" :key="index">
                <Link 
                    v-if="link.url"
                    :href="link.url" 
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset focus:z-20 focus:outline-offset-0"
                    :class="[
                        link.active 
                            ? 'z-10 bg-primary text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' 
                            : 'text-[#0d121b] dark:text-gray-300 ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                    ]"
                >
                    <span v-html="link.label"></span>
                </Link>
                <span 
                    v-else 
                    v-html="link.label"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#4c669a] ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-offset-0 opacity-50 cursor-not-allowed"
                ></span>
            </template>
        </nav>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
    </main>
</template>