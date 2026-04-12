<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    event: Object
});

const emit = defineEmits(['close']);

const accreditors = ref([]);
const isFetchingAccreditors = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const isSubmitting = ref(false);

const accreditorForm = useForm({
    accreditor_id: '',
    name: '', 
    email: '',
});

// Reset state when modal opens
watch(() => props.show, (isVisible) => {
    if (isVisible) {
        resetForm();
        fetchAccreditors();
    }
});

const resetForm = () => {
    accreditorForm.reset();
    searchQuery.value = '';
    searchResults.value = [];
    accreditors.value = [];
};

const fetchAccreditors = async () => {
    if (!props.event) return;
    isFetchingAccreditors.value = true;
    try {
        const response = await axios.get(route('events.accreditors.index', props.event.id));
        accreditors.value = response.data;
    } catch (error) {
        console.error('Failed to fetch accreditors:', error);
    } finally {
        isFetchingAccreditors.value = false;
    }
};

const searchAccreditors = useDebounceFn(async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get(route('accreditors.search'), {
            params: { 
                query: searchQuery.value,
                exclude_event_id: props.event.id
            }
        });
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isSearching.value = false;
    }
}, 300);

const selectAccreditor = (accreditor) => {
    accreditorForm.accreditor_id = accreditor.id;
    accreditorForm.name = accreditor.name;
    accreditorForm.email = accreditor.email;
    searchQuery.value = accreditor.name;
    searchResults.value = [];
};

const addAccreditor = async () => {
    if (!accreditorForm.accreditor_id) {
        if (searchQuery.value.includes('@')) {
            accreditorForm.email = searchQuery.value;
            accreditorForm.name = searchQuery.value.split('@')[0];
        } else {
            alert('Please select an existing accreditor or enter a valid email address.');
            return;
        }
    }

    isSubmitting.value = true;
    accreditorForm.clearErrors();

    try {
        const response = await axios.post(route('events.accreditors.store', props.event.id), {
            accreditor_id: accreditorForm.accreditor_id,
            name: accreditorForm.name,
            email: accreditorForm.email
        });

        // Update the list immediately with the new accreditor
        if (response.data.accreditor) {
            // Check if already in list to avoid duplicates (though backend handles it)
            const exists = accreditors.value.some(a => a.id === response.data.accreditor.id);
            if (!exists) {
                accreditors.value.unshift(response.data.accreditor);
            }
        }

        // Reset form
        accreditorForm.reset();
        searchQuery.value = '';
        searchResults.value = [];
        
        // Optional: refresh the whole list to be sure
        fetchAccreditors(); 

    } catch (error) {
        if (error.response?.status === 422) {
            accreditorForm.setError(error.response.data.errors);
        } else {
            console.error('Failed to add accreditor:', error);
            alert('An unexpected error occurred. Please try again.');
        }
    } finally {
        isSubmitting.value = false;
    }
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

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="bg-white dark:bg-surface-dark w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col max-h-[90vh] animate-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Manage Accreditors</h3>
                    <p class="text-xs text-slate-500">{{ event?.title }}</p>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 mt-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                <!-- Add Accreditor Form -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Search & Add Accreditor</h4>
                    <div class="relative">
                        <form @submit.prevent="addAccreditor" class="flex gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">search</span>
                                </div>
                                <input 
                                    v-model="searchQuery" 
                                    @input="searchAccreditors"
                                    type="text" 
                                    placeholder="Search by name or email..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:ring-primary shadow-sm outline-none"
                                >
                                
                                <!-- Suggestions Dropdown -->
                                <div v-if="searchResults.length > 0" class="absolute z-[60] left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto">
                                    <button 
                                        v-for="result in searchResults" 
                                        :key="result.id"
                                        @click="selectAccreditor(result)"
                                        type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-3 border-b last:border-0 border-slate-100 dark:border-slate-700 transition-all"
                                    >
                                        <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                            {{ result.name.charAt(0) }}
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ result.name }}</span>
                                            <span class="text-[10px] text-slate-500 truncate">{{ result.email }}</span>
                                        </div>
                                    </button>
                                </div>

                                <!-- Loading State in Input -->
                                <div v-if="isSearching" class="absolute right-3 top-1/2 -translate-y-1/2 text-primary animate-spin">
                                    <span class="material-symbols-outlined text-[18px]">progress_activity</span>
                                </div>
                            </div>
                                <button 
                                    type="submit" 
                                    :disabled="isSubmitting || (!accreditorForm.accreditor_id && !searchQuery.includes('@'))"
                                    class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold text-sm hover:bg-blue-700 disabled:opacity-50 shrink-0 shadow-lg shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-2"
                                >
                                    <span v-if="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                    <span>{{ isSubmitting ? 'Saving...' : (accreditorForm.accreditor_id ? 'Assign' : 'Add New') }}</span>
                                </button>
                        </form>
                        <p v-if="accreditorForm.accreditor_id" class="mt-2 text-[10px] text-emerald-600 font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            Selected: {{ accreditorForm.name }} ({{ accreditorForm.email }})
                            <button @click="accreditorForm.reset(); searchQuery = ''" class="ml-2 text-slate-400 hover:text-rose-500 underline uppercase tracking-tighter">Clear</button>
                        </p>
                    </div>
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
                            class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700 rounded-xl group hover:border-primary/30 transition-colors">
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
                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100 focus:opacity-100">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex justify-end shrink-0">
                <button @click="closeModal"
                    class="px-6 py-2 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 font-bold text-slate-700 dark:text-slate-200 text-sm hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
