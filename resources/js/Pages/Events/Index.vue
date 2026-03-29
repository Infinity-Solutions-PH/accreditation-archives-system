<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';

const props = defineProps({
    events: Object,
    colleges: Array,
    programs: Array,
});

defineOptions({
    layout: AppLayout
});

const showCreateModal = ref(false);
const editingEvent = ref(null);

const form = useForm({
    title: '',
    description: '',
    college_id: '',
    program_id: '',
    level: '',
    expires_at: '',
    status: 'active'
});

const openCreateModal = () => {
    editingEvent.value = null;
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (event) => {
    editingEvent.value = event;
    form.title = event.title;
    form.description = event.description;
    form.college_id = event.college_id;
    form.program_id = event.program_id;
    form.level = event.level;
    form.expires_at = event.expires_at ? event.expires_at.split('T')[0] : '';
    form.status = event.status;
    showCreateModal.value = true;
};

const submit = () => {
    if (editingEvent.value) {
        form.put(route('events.update', editingEvent.value.id), {
            onSuccess: () => {
                showCreateModal.value = false;
            }
        });
    } else {
        form.post(route('events.store'), {
            onSuccess: () => {
                showCreateModal.value = false;
            }
        });
    }
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
const accreditors = ref([]);
const isFetchingAccreditors = ref(false);

const accreditorForm = useForm({
    name: '',
    email: '',
});

const openAccreditorsModal = async (event) => {
    selectedEventForAccreditors.value = event;
    showAccreditorsModal.value = true;
    fetchAccreditors();
};

const fetchAccreditors = async () => {
    if (!selectedEventForAccreditors.value) return;
    isFetchingAccreditors.value = true;
    try {
        const response = await axios.get(route('events.accreditors.index', selectedEventForAccreditors.value.id));
        accreditors.value = response.data;
    } catch (error) {
        console.error('Failed to fetch accreditors:', error);
    } finally {
        isFetchingAccreditors.value = false;
    }
};

const addAccreditor = () => {
    accreditorForm.post(route('events.accreditors.store', selectedEventForAccreditors.value.id), {
        onSuccess: () => {
            accreditorForm.reset();
            fetchAccreditors();
        },
    });
};

const removeAccreditor = async (accreditorId) => {
    if (!confirm('Are you sure you want to remove this accreditor?')) return;
    try {
        await axios.delete(route('events.accreditors.destroy', accreditorId));
        fetchAccreditors();
    } catch (error) {
        console.error('Failed to remove accreditor:', error);
    }
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
            <button v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))" @click="openCreateModal"
                class="flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined">add_circle</span>
                Create Event
            </button>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div v-for="event in events.data" :key="event.id" 
                class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                
                <div v-if="$page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))" class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
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
                    <Link :href="route('file-archives', { event_id: event.id })" 
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

            <!-- Empty State -->
            <div v-if="events.data.length === 0" class="col-span-full py-20 bg-slate-50 dark:bg-slate-900/20 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-slate-300 text-6xl mb-4">event_busy</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Events Found</h3>
                <p class="text-slate-500 max-w-xs mx-auto">Click "Create Event" to start a new accreditation session.</p>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-surface-dark w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ editingEvent ? 'Edit Event' : 'Create New Event' }}
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 mt-1">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Title</label>
                        <input v-model="form.title" type="text" required placeholder="AACCUP 3rd Survey Visit"
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Description</label>
                        <textarea v-model="form.description" rows="3" placeholder="Additional details about this visit..."
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">College</label>
                            <select v-model="form.college_id" required
                                class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                                <option value="" disabled>Select College</option>
                                <option v-for="college in colleges" :key="college.id" :value="college.id">{{ college.code }}</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Program</label>
                            <select v-model="form.program_id" required :disabled="!form.college_id"
                                class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                                <option value="" disabled>Select Program</option>
                                <option v-for="program in programs.filter(p => p.college_id == form.college_id)" :key="program.id" :value="program.id">
                                    {{ program.code }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Level</label>
                            <select v-model="form.level" required
                                class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                                <option value="" disabled>Select Level</option>
                                <option value="Candidate Status">Candidate Status</option>
                                <option value="Level I">Level I</option>
                                <option value="Level II">Level II</option>
                                <option value="Level III">Level III</option>
                                <option value="Level IV">Level IV</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Expiry Date</label>
                            <input v-model="form.expires_at" type="date" required
                                class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                    </div>

                    <div v-if="editingEvent" class="space-y-1.5 pt-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                        <div class="flex gap-4">
                            <label v-for="s in ['active', 'completed', 'cancelled']" :key="s" class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" :value="s" class="text-primary focus:ring-primary">
                                <span class="text-sm capitalize">{{ s }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showCreateModal = false"
                            class="flex-1 px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 px-4 py-3 rounded-xl bg-primary text-white font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20 disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : (editingEvent ? 'Update Event' : 'Create Event') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Manage Accreditors Modal -->
        <div v-if="showAccreditorsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-surface-dark w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Manage Accreditors</h3>
                        <p class="text-xs text-slate-500">{{ selectedEventForAccreditors?.title }}</p>
                    </div>
                    <button @click="showAccreditorsModal = false" class="text-slate-400 hover:text-slate-600 mt-1">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-8">
                    <!-- Add Accreditor Form -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Add External Accreditor</h4>
                        <form @submit.prevent="addAccreditor" class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Full Name</label>
                                <input v-model="accreditorForm.name" type="text" required placeholder="Dr. John Doe"
                                    class="w-full px-4 py-2 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:ring-primary">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Institutional Email</label>
                                <div class="flex gap-2">
                                    <input v-model="accreditorForm.email" type="email" required placeholder="john@university.edu"
                                        class="flex-1 px-4 py-2 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:ring-primary">
                                    <button type="submit" :disabled="accreditorForm.processing"
                                        class="px-4 py-2 bg-primary text-white rounded-xl font-bold text-sm hover:bg-blue-700 disabled:opacity-50 shrink-0 shadow-lg shadow-blue-500/20">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p v-if="accreditorForm.errors.email" class="text-xs text-red-500 mt-1">{{ accreditorForm.errors.email }}</p>
                    </div>

                    <!-- Accreditors List -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Active Assignments</h4>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-[10px] font-bold text-slate-500">{{ accreditors.length }} Total</span>
                        </div>
                        
                        <div v-if="isFetchingAccreditors" class="flex justify-center py-8">
                            <div class="animate-spin text-primary">
                                <span class="material-symbols-outlined text-4xl">progress_activity</span>
                            </div>
                        </div>

                        <div v-else-if="accreditors.length === 0" class="py-12 bg-slate-50 dark:bg-slate-800/20 rounded-2xl border-2 border-dashed border-slate-100 dark:border-slate-800 flex flex-col items-center justify-center text-center">
                            <span class="material-symbols-outlined text-slate-300 text-4xl mb-2">person_search</span>
                            <p class="text-sm text-slate-500">No accreditors assigned to this event yet.</p>
                        </div>

                        <div v-else class="space-y-2">
                            <div v-for="accreditor in accreditors" :key="accreditor.id" 
                                class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700 rounded-xl group">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center text-primary font-bold">
                                        {{ accreditor.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white leading-none">{{ accreditor.name }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ accreditor.email }}</p>
                                    </div>
                                </div>
                                <button @click="removeAccreditor(accreditor.id)" 
                                    class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex justify-end shrink-0">
                    <button @click="showAccreditorsModal = false"
                        class="px-6 py-2 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 font-bold text-slate-700 dark:text-slate-200 text-sm hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </main>
</template>
