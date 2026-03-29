<script setup>
    import AppLayout from '@shared/Layouts/App.vue';
    import { Link, router } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';
    import { useDebounceFn } from '@vueuse/core';

    defineOptions({
        layout: AppLayout
    });

    const props = defineProps({
        logs: Object,
        filters: Object,
        logTypes: Array,
        roles: Array
    });

    const search = ref(props.filters.search || '');
    const type = ref(props.filters.type || '');
    const role = ref(props.filters.role || '');
    const isLoading = ref(false);

    const updateFilters = useDebounceFn(() => {
        router.get(route('activity-logs'), {
            search: search.value,
            type: type.value,
            role: role.value
        }, {
            preserveState: true,
            replace: true,
            onStart: () => { isLoading.value = true; },
            onFinish: () => { isLoading.value = false; }
        });
    }, 500);

    watch([search, type, role], () => {
        updateFilters();
    });

    const resetFilters = () => {
        search.value = '';
        type.value = '';
        role.value = '';
    };

    const getLogNameLabel = (name) => {
        if (!name || name === 'default') return 'System Event';
        return name.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    };
</script>

<template>
    <main class="layout-container flex h-full grow flex-col overflow-y-auto bg-gray-50 dark:bg-background-dark">
        <div class="px-4 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5">
            <div class="layout-content-container flex flex-col max-w-[1200px] flex-1 w-full">
                <!-- Breadcrumbs -->
                <div class="flex flex-wrap gap-2 px-4 pt-4 pb-2">
                    <Link class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" :href="route('dashboard')">Home</Link>
                    <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
                    <span class="text-[#4c669a] text-sm font-medium leading-normal">Admin</span>
                    <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
                    <span class="text-[#0d121b] dark:text-white text-sm font-medium leading-normal">Activity Logs</span>
                </div>

                <!-- Page Heading -->
                <div class="flex flex-col md:flex-row justify-between gap-6 px-4 py-6">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-[#0d121b] dark:text-white text-3xl md:text-4xl font-bold leading-tight tracking-[-0.033em]">System Activity Logs</h1>
                        <p class="text-[#4c669a] dark:text-gray-400 text-base font-normal leading-normal max-w-2xl">
                            Monitor system integrity. View a detailed history of authentication events, user role modifications, and repository actions.
                        </p>
                    </div>
                </div>

                <!-- Filters and Search Toolbar -->
                <div class="flex flex-col gap-4 px-4 pb-6">
                    <div class="bg-white dark:bg-[#151c2b] rounded-2xl p-6 shadow-sm border border-[#e7ebf3] dark:border-gray-800 flex flex-col lg:flex-row gap-6 justify-between items-end">
                        <!-- Search Bar -->
                        <div class="w-full lg:w-1/3">
                            <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">Search Logs</label>
                            <div class="flex w-full items-stretch rounded-xl h-11 border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all" :class="{'opacity-50 pointer-events-none': isLoading}">
                                <div class="text-[#4c669a] flex items-center justify-center pl-3 pr-2">
                                    <span class="material-symbols-outlined text-[20px]">search</span>
                                </div>
                                <input v-model="search" :disabled="isLoading" class="flex w-full min-w-0 flex-1 bg-transparent text-[#0d121b] dark:text-white focus:outline-0 h-full placeholder:text-[#9ca3af] text-sm font-medium" placeholder="Search user name, email, or action..."/>
                            </div>
                        </div>

                        <!-- Filter Selection -->
                        <div class="flex flex-wrap gap-4 w-full lg:w-auto flex-1 justify-end">
                            <!-- Action Type Filter -->
                            <div class="w-full sm:w-auto min-w-[180px]">
                                <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">Action Type</label>
                                <select v-model="type" :disabled="isLoading" class="w-full h-11 rounded-xl border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 px-4 text-sm font-medium text-[#0d121b] dark:text-white focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">All Actions</option>
                                    <option v-for="t in logTypes" :key="t" :value="t">{{ getLogNameLabel(t) }}</option>
                                </select>
                            </div>

                            <!-- Role Filter -->
                            <div class="w-full sm:w-auto min-w-[180px]">
                                <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">User Role</label>
                                <select v-model="role" :disabled="isLoading" class="w-full h-11 rounded-xl border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 px-4 text-sm font-medium text-[#0d121b] dark:text-white focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">All Roles</option>
                                    <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name.charAt(0).toUpperCase() + r.name.slice(1).replace('_', ' ') }}</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            <div class="flex items-end">
                                <button @click="resetFilters" :disabled="isLoading" class="flex h-11 px-4 shrink-0 items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-[#4c669a] hover:text-primary transition-all font-bold text-sm gap-2 disabled:opacity-50" title="Reset Filters">
                                    <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                                    <span>Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="px-4 pb-4">
                    <div class="w-full overflow-hidden rounded-2xl border border-[#e7ebf3] dark:border-gray-800 bg-white dark:bg-[#151c2b] shadow-sm relative">
                        <!-- Loading Overlay -->
                        <div v-if="isLoading" class="absolute inset-x-0 top-0 h-1 bg-primary/20 overflow-hidden z-[10]">
                            <div class="h-full bg-primary animate-[loading_2s_infinite_ease-in-out]"></div>
                        </div>
                        <div v-if="isLoading" class="absolute inset-0 bg-white/40 dark:bg-background-dark/40 backdrop-blur-[1px] flex items-center justify-center z-[5]">
                            <div class="p-3 rounded-full bg-white dark:bg-surface-dark shadow-xl border border-gray-100 dark:border-gray-800">
                                <span class="material-symbols-outlined animate-spin text-primary">progress_activity</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" :class="{'opacity-60 grayscale-[0.3]': isLoading}">
                                <thead>
                                    <tr class="border-b border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/50 text-[#4c669a] dark:text-gray-400">
                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Timestamp</th>
                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">User / Causer</th>
                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Action Category</th>
                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider w-1/3">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e7ebf3] dark:divide-gray-800 text-sm">
                                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-[#0d121b] dark:text-gray-200">{{ new Date(log.created_at).toLocaleDateString() }}</span>
                                                <span class="text-xs text-[#4c669a]">{{ new Date(log.created_at).toLocaleTimeString() }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0 overflow-hidden shadow-sm">
                                                    <img v-if="log.causer?.google_info?.avatar" :src="log.causer.google_info.avatar" :alt="log.causer.name" class="w-full h-full object-cover">
                                                    <span v-else>{{ log.causer?.name?.substring(0, 2).toUpperCase() || 'SYS' }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-[#0d121b] dark:text-white">{{ log.causer?.name || 'System Auto' }}</p>
                                                    <p class="text-xs text-[#4c669a]">{{ log.causer?.email || 'automated-task@cvsu.edu.ph' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="log.causer?.roles?.[0]" class="inline-flex items-center rounded-lg bg-indigo-50 dark:bg-indigo-900/20 px-2.5 py-1 text-xs font-bold text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-700/10 uppercase tracking-tighter">
                                                {{ log.causer.roles[0].name.replace('_', ' ') }}
                                            </span>
                                            <span v-else class="text-xs text-gray-400 italic">N/A</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-900/20 px-3 py-1 text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 shadow-sm">
                                                <span class="size-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                {{ getLogNameLabel(log.log_name) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-[#0d121b] dark:text-gray-300 font-medium leading-relaxed">
                                            {{ log.description }}
                                        </td>
                                    </tr>
                                    <tr v-if="logs.data.length === 0">
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-4 text-[#4c669a]">
                                                <span class="material-symbols-outlined text-5xl opacity-20">history_toggle_off</span>
                                                <p class="font-bold">No activity logs found matching your criteria.</p>
                                                <button @click="resetFilters" class="text-primary hover:underline font-bold text-sm">Clear all filters</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="flex items-center justify-between border-t border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/30 px-6 py-4">
                            <div class="flex flex-1 items-center justify-between flex-wrap gap-4">
                                <div>
                                    <p class="text-sm text-[#4c669a] dark:text-gray-400 font-medium">
                                        Showing <span class="font-bold text-[#0d121b] dark:text-white">{{ logs.from || 0 }}</span> to <span class="font-bold text-[#0d121b] dark:text-white">{{ logs.to || 0 }}</span> of <span class="font-bold text-[#0d121b] dark:text-white">{{ logs.total }}</span> entries
                                    </p>
                                </div>
                                <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-xl shadow-sm bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <template v-for="(link, index) in logs.links" :key="index">
                                        <Link 
                                            v-if="link.url"
                                            :href="link.url" 
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-bold transition-all transition-colors duration-200"
                                            :class="[
                                                link.active 
                                                    ? 'z-10 bg-primary text-white shadow-lg shadow-primary/20' 
                                                    : 'text-[#4c669a] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                                            ]"
                                        >
                                            <span v-html="link.label"></span>
                                        </Link>
                                        <span 
                                            v-else 
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-[#4c669a] opacity-40 cursor-not-allowed bg-gray-50/50 dark:bg-gray-900/50"
                                        ></span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>