<script setup>
    import { ref, onMounted } from 'vue';
    import { useForm, Link, router } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';
    import AppLayout from '@shared/Layouts/App.vue';
    import AccreditorsModal from '@/Components/AccreditorsModal.vue';
    import EventModal from '@/Components/EventModal.vue';

    const props = defineProps({
        events: Object,
        colleges: Array,
        programs: Array,
        activeTab: String,
    });

    defineOptions({
        layout: AppLayout
    });

    const viewMode = ref(new URLSearchParams(window.location.search).get('view') || 'grid');

    const switchTab = (tab) => {
        router.get(route('events.index'), { tab, view: viewMode.value }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const switchView = (mode) => {
        viewMode.value = mode;
        router.get(route('events.index'), { tab: props.activeTab, view: mode }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const showCreateModal = ref(false);
    const editingEvent = ref(null);

    const openCreateModal = () => {
        editingEvent.value = null;
        showCreateModal.value = true;
    };

    const openEditModal = (event) => {
        editingEvent.value = event;
        showCreateModal.value = true;
    };

    const getStatusClass = (status) => {
        switch (status) {
            case 'active': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
            case 'completed': return 'bg-blue-100 text-blue-700 border-blue-200';
            case 'cancelled': return 'bg-slate-100 text-slate-700 border-slate-200';
            default: return 'bg-slate-100 text-slate-700 border-slate-200';
        }
    };

    // Accreditor Management
    const showAccreditorsModal = ref(false);
    const selectedEventForAccreditors = ref(null);

    const openAccreditorsModal = (event) => {
        selectedEventForAccreditors.value = event;
        showAccreditorsModal.value = true;
    };
</script>

<template>
    <main class="flex-1 overflow-y-auto w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Accreditation Events</h1>
                <p class="text-slate-600 dark:text-slate-400">Manage accreditation visits, timelines, and virtual drive assignments.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- View Toggle -->
                <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button 
                        @click="switchView('grid')"
                        :class="['p-2 rounded-lg transition-all flex items-center gap-2', viewMode === 'grid' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
                        title="Grid View"
                    >
                        <span class="material-symbols-outlined text-[20px]">grid_view</span>
                    </button>
                    <button 
                        @click="switchView('list')"
                        :class="['p-2 rounded-lg transition-all flex items-center gap-2', viewMode === 'list' ? 'bg-white dark:bg-slate-700 shadow-sm text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
                        title="List View"
                    >
                        <span class="material-symbols-outlined text-[20px]">list</span>
                    </button>
                </div>

                <button v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))" @click="openCreateModal"
                    class="flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all shadow-lg shadow-blue-500/20">
                    <span class="material-symbols-outlined">add_circle</span>
                    Create Event
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-8 mb-8 border-b border-slate-200 dark:border-slate-800">
            <button 
                @click="switchTab('active')"
                :class="['pb-4 px-2 text-sm font-bold transition-all relative', activeTab === 'active' ? 'text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
            >
                Active Visits
                <div v-if="activeTab === 'active'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full animate-in fade-in duration-300"></div>
            </button>
            <button 
                @click="switchTab('archived')"
                :class="['pb-4 px-2 text-sm font-bold transition-all relative', activeTab === 'archived' ? 'text-primary' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']"
            >
                Archives
                <div v-if="activeTab === 'archived'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full animate-in fade-in duration-300"></div>
            </button>
        </div>

        <!-- Events View -->
        <div v-if="events.data.length > 0">
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div v-for="event in events.data" :key="event.id" 
                    class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    
                    <div v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))" class="absolute top-0 right-0 p-4 transition-opacity">
                        <button @click="openEditModal(event)" class="text-slate-400 hover:text-primary p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                    </div>

                    <div class="flex items-start gap-4 mb-5">
                        <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-3xl">event_available</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-snug truncate pr-6">{{ event.title }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border', getStatusClass(event.status)]">
                                    {{ event.status }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">{{ event.level }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-2 h-10">
                        {{ event.description || 'No description provided.' }}
                    </p>

                    <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">College / Program</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 text-right">{{ event.college?.code || 'N/A' }} - {{ event.program?.code || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Expiry Date</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                {{ event.expires_at ? new Date(event.expires_at).toLocaleDateString() : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <Link :href="route('areas', { event: event.slug })" 
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-slate-700 dark:text-slate-200 text-sm font-bold transition-colors">
                            View Drive
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </Link>
                        <button v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))"
                            @click="openAccreditorsModal(event)"
                            class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-slate-700 dark:text-slate-200 transition-colors"
                            title="Manage Accreditors">
                            <span class="material-symbols-outlined">group</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div v-else class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-2 duration-300 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Event Details</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">College/Program</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="event in events.data" :key="event.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined">event_available</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ event.title }}</p>
                                            <p class="text-xs text-slate-500">{{ event.level }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ event.college?.code || 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-500 truncate max-w-[150px]">{{ event.program?.code || 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span :class="['w-fit px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border', getStatusClass(event.status)]">
                                            {{ event.status }}
                                        </span>
                                        <span class="text-[10px] text-slate-500">Exp: {{ event.expires_at ? new Date(event.expires_at).toLocaleDateString() : 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 transition-opacity">
                                        <button v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))"
                                            @click="openEditModal(event)" 
                                            class="p-2 text-slate-400 hover:text-primary rounded-lg hover:bg-white dark:hover:bg-slate-700 shadow-sm border border-transparent hover:border-slate-100 dark:hover:border-slate-600 transition-all">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <button v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))"
                                            @click="openAccreditorsModal(event)" 
                                            class="p-2 text-slate-400 hover:text-primary rounded-lg hover:bg-white dark:hover:bg-slate-700 shadow-sm border border-transparent hover:border-slate-100 dark:hover:border-slate-600 transition-all">
                                            <span class="material-symbols-outlined text-[20px]">group</span>
                                        </button>
                                        <Link :href="route('areas', { event: event.slug })" 
                                            class="p-2 text-primary hover:text-white bg-primary/5 hover:bg-primary rounded-lg border border-primary/10 transition-all">
                                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-20 bg-slate-50 dark:bg-slate-900/20 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center text-center">
            <span class="material-symbols-outlined text-slate-300 text-6xl mb-4">event_busy</span>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Events Found</h3>
            <p class="text-slate-500 max-w-xs mx-auto">Click "Create Event" to start a new accreditation session.</p>
        </div>

        <EventModal 
            :show="showCreateModal"
            :event="editingEvent"
            :colleges="colleges"
            :programs="programs"
            @close="showCreateModal = false"
        />

        <AccreditorsModal 
            :show="showAccreditorsModal"
            :event="selectedEventForAccreditors"
            @close="showAccreditorsModal = false"
        />
    </main>
</template>
