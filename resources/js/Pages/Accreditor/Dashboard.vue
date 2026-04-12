<script setup>
import AppLayout from '@shared/Layouts/App.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarDays, FolderArchive, ArrowRight, Clock, MapPin, Milestone, LayoutGrid, List } from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    events: Object,
    activeTab: String,
    accreditor: Object
});

const viewMode = ref(new URLSearchParams(window.location.search).get('view') || 'grid');

const switchView = (mode) => {
    viewMode.value = mode;
    router.get(route('accreditor.dashboard'), { tab: props.activeTab, view: mode }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="activeTab === 'active' ? 'Active Accreditations' : 'Accreditation Archives'" />

    <main class="flex-1 overflow-y-auto w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <component :is="activeTab === 'active' ? CalendarDays : FolderArchive" class="size-8 text-primary" />
                    {{ activeTab === 'active' ? 'Active Accreditations' : 'Accreditation Archives' }}
                </h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400">
                    Welcome back, {{ accreditor.name }}. You have access to the following events.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <!-- View Toggle -->
                <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button 
                        @click="switchView('grid')"
                        :class="['p-2 rounded-lg transition-all flex items-center gap-2', viewMode === 'grid' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
                        title="Grid View"
                    >
                        <LayoutGrid class="size-5" />
                    </button>
                    <button 
                        @click="switchView('list')"
                        :class="['p-2 rounded-lg transition-all flex items-center gap-2', viewMode === 'list' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
                        title="List View"
                    >
                        <List class="size-5" />
                    </button>
                </div>

                <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <Link 
                        :href="route('accreditor.dashboard', { tab: 'active', view: viewMode })"
                        class="px-6 py-2 text-sm font-semibold rounded-lg transition-all"
                        :class="activeTab === 'active' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    >
                        Current
                    </Link>
                    <Link 
                        :href="route('accreditor.dashboard', { tab: 'archives', view: viewMode })"
                        class="px-6 py-2 text-sm font-semibold rounded-lg transition-all"
                        :class="activeTab === 'archives' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    >
                        Archives
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="events.data.length">
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div 
                    v-for="event in events.data" 
                    :key="event.id"
                    class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                >
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <Milestone class="size-6" />
                            </div>
                            <div 
                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="activeTab === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
                            >
                                {{ activeTab === 'active' ? 'Active' : 'Archived' }}
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">{{ event.title }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 h-10">
                            {{ event.description || 'No description provided.' }}
                        </p>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <MapPin class="size-4 text-slate-400" />
                                <span>{{ event.college?.name }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <Clock class="size-4 text-slate-400" />
                                <span>Expires: {{ new Date(event.expires_at).toLocaleDateString() }}</span>
                            </div>
                        </div>

                        <Link 
                            :href="route('areas', { event: event.slug })"
                            class="w-full py-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-primary hover:text-white text-slate-700 dark:text-slate-200 font-semibold rounded-xl transition-all flex items-center justify-center gap-2"
                        >
                            View Details
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div v-else class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-2 duration-300 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Accreditation Details</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">College</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Expiry</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="event in events.data" :key="event.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                            <Milestone class="size-5" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ event.title }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span 
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                                    :class="activeTab === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
                                                >
                                                    {{ activeTab === 'active' ? 'Active' : 'Archived' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ event.college?.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 font-medium">
                                    {{ new Date(event.expires_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link 
                                        :href="route('areas', { event: event.slug })"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-primary hover:text-white rounded-lg text-sm font-bold transition-all shadow-sm"
                                    >
                                        Open Drive
                                        <ArrowRight class="size-4" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-20 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
            <div class="size-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6">
                <FolderArchive class="size-10 text-slate-400" />
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Events Found</h3>
            <p class="text-slate-500 dark:text-slate-400">You are not assigned to any events in this section.</p>
        </div>
    </main>
</template>
