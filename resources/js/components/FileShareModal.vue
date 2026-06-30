<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { SUBFOLDERS, AREA_PARAMETERS, PARAMETER_FOLDERS } from '@/constants/foldering.js';

const props = defineProps({
    isOpen: Boolean,
    file: Object
});

const emit = defineEmits(['close']);
const page = usePage();

const activeTab = ref('events'); // 'events' or 'users'
const isLoading = ref(false);
const sharedUsers = ref([]);
const pendingRequests = ref([]);

const searchEventQuery = ref('');
const eventResults = ref([]);
const isSearchingEvents = ref(false);

const searchUserQuery = ref('');
const userResults = ref([]);
const isSearchingUsers = ref(false);

// Event Sharing Folder State
const selectedEventForShare = ref(null);
const areas = ref([]);
const selectedAreaId = ref('');
const selectedSubfolder = ref('');
const selectedParameter = ref('');
const selectedParameterFolder = ref('');
const isSharing = ref(false);

const fetchAreas = async () => {
    try {
        const response = await axios.get(route('api.areas'));
        areas.value = response.data;
    } catch (e) {
        console.error('Failed to fetch areas', e);
    }
};

const resetEventShareSelection = () => {
    selectedEventForShare.value = null;
    selectedAreaId.value = '';
    selectedSubfolder.value = '';
    selectedParameter.value = '';
    selectedParameterFolder.value = '';
};

watch(selectedAreaId, () => {
    selectedSubfolder.value = '';
    selectedParameter.value = '';
    selectedParameterFolder.value = '';
});

watch(selectedSubfolder, () => {
    selectedParameter.value = '';
    selectedParameterFolder.value = '';
});

watch(selectedParameter, () => {
    selectedParameterFolder.value = '';
});

const selectedArea = computed(() => {
    return areas.value.find(a => a.id === selectedAreaId.value);
});

const currentAreaParameters = computed(() => {
    if (!selectedArea.value) return [];
    return AREA_PARAMETERS[selectedArea.value.order_no] || [];
});

const isEventShareSubmitDisabled = computed(() => {
    if (isSharing.value) return true;
    if (!selectedAreaId.value || !selectedSubfolder.value) return true;
    if (selectedSubfolder.value === 'PARAMETER' && !selectedParameter.value) return true;
    if (selectedParameter.value && (selectedParameter.value.startsWith('Parameter A') || selectedParameter.value.startsWith('Parameter B')) && !selectedParameterFolder.value) return true;
    return false;
});

const fetchAccessData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('files.get-shares', props.file.id));
        sharedUsers.value = response.data.shared_users;
        pendingRequests.value = response.data.pending_requests;
    } catch (e) {
        console.error('Failed to fetch shares', e);
    } finally {
        isLoading.value = false;
    }
};

const searchEvents = async () => {
    if (!searchEventQuery.value) {
        eventResults.value = [];
        return;
    }
    isSearchingEvents.value = true;
    try {
        const response = await axios.get(route('files.search-events'), {
            params: { search: searchEventQuery.value }
        });
        eventResults.value = response.data;
    } catch (e) {
        console.error('Event search failed', e);
    } finally {
        isSearchingEvents.value = false;
    }
};

const searchUsers = async () => {
    if (!searchUserQuery.value) {
        userResults.value = [];
        return;
    }
    isSearchingUsers.value = true;
    try {
        const response = await axios.get(route('files.search-users'), {
            params: { search: searchUserQuery.value }
        });
        userResults.value = response.data;
    } catch (e) {
        console.error('User search failed', e);
    } finally {
        isSearchingUsers.value = false;
    }
};

let eventTimeout = null;
watch(searchEventQuery, () => {
    if (eventTimeout) clearTimeout(eventTimeout);
    eventTimeout = setTimeout(searchEvents, 400);
});

let userTimeout = null;
watch(searchUserQuery, () => {
    if (userTimeout) clearTimeout(userTimeout);
    userTimeout = setTimeout(searchUsers, 400);
});

onMounted(() => {
    if (props.isOpen) {
        fetchAccessData();
        fetchAreas();
    }
});

watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
        fetchAccessData();
        fetchAreas();
        resetEventShareSelection();
    }
});

const shareToEvent = (event) => {
    if (!event.is_selectable) return;
    selectedEventForShare.value = event;
};

const confirmShareToEvent = async () => {
    if (isEventShareSubmitDisabled.value) return;

    isSharing.value = true;
    try {
        await axios.post(route('events.share'), {
            file_id: props.file.id,
            accreditation_event_id: selectedEventForShare.value.id,
            area_id: selectedAreaId.value,
            subfolder: selectedSubfolder.value,
            parameter: selectedParameter.value || null,
            parameter_folder: selectedParameterFolder.value || null
        });
        
        if (window.toast) {
            window.toast('File shared to event virtual drive successfully!', 'success');
        }
        
        fetchAccessData();
        resetEventShareSelection();
        searchEventQuery.value = '';
        eventResults.value = [];
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to share to event.';
        if (window.toast) {
            window.toast(message, 'error');
        }
    } finally {
        isSharing.value = false;
    }
};

const shareToUser = async (userId) => {
    try {
        await axios.post(route('api.files.share_to_user'), {
            file_id: props.file.id,
            user_id: userId
        });
        window.toast('Shared with user successfully!', 'success');
        fetchAccessData();
        searchUserQuery.value = '';
    } catch (e) {
        window.toast('Failed to share with user', 'error');
    }
};

const approveRequest = (requestId) => {
    axios.post(route('files.approve-access', requestId)).then(() => {
        window.toast('Access approved!', 'success');
        fetchAccessData();
    });
};

const copyPermanentLink = () => {
    const url = route('files.shared', props.file.uuid);
    navigator.clipboard.writeText(url);
    if (window.toast) {
        window.toast('Permanent link copied to clipboard!', 'success');
    }
};

</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="emit('close')"></div>

        <!-- Modal Content -->
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex justify-between items-start mb-1">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-primary text-[28px]">share_reviews</span>
                            Share & Permissions
                        </h2>
                        <div class="flex items-center gap-2">
                            <p class="text-slate-500 text-sm truncate max-w-[300px] font-medium">{{ file.title }}</p>
                            <span class="text-slate-300">•</span>
                            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                <img v-if="file.uploaded_by?.google_info?.avatar" :src="file.uploaded_by.google_info.avatar" class="size-4 rounded-full" />
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300" :title="`Uploader: ${file.uploaded_by?.name}` || 'Unknown'">{{ file.uploaded_by?.name || 'Unknown' }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="emit('close')" class="size-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-slate-400">close</span>
                    </button>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="px-6 border-b border-slate-100 dark:border-slate-800 flex gap-8 shrink-0">
                <button 
                    @click="activeTab = 'events'"
                    class="py-4 text-sm font-bold transition-all relative"
                    :class="activeTab === 'events' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'"
                >
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">meeting_room</span>
                        Accreditation Events
                    </div>
                    <div v-if="activeTab === 'events'" class="absolute bottom-0 left-0 right-0 h-1 bg-primary rounded-t-full"></div>
                </button>
                <button 
                    @click="activeTab = 'users'"
                    class="py-4 text-sm font-bold transition-all relative"
                    :class="activeTab === 'users' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'"
                >
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        Specific Users
                    </div>
                    <div v-if="activeTab === 'users'" class="absolute bottom-0 left-0 right-0 h-1 bg-primary rounded-t-full"></div>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6 scrollbar-hide">
                
                <!-- Events Tab -->
                <div v-if="activeTab === 'events'" class="space-y-6">
                    <!-- Search Event (Only show when NO event is currently selected for sharing folders) -->
                    <div v-if="!selectedEventForShare" class="relative group">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                            <span v-if="!isSearchingEvents" class="material-symbols-outlined text-[22px]">search</span>
                            <span v-else class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                        </div>
                        <input 
                            v-model="searchEventQuery"
                            placeholder="Find an accreditation event to share with..."
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-4 py-3.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"
                        />
                        <div v-if="searchEventQuery && eventResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl z-10 overflow-hidden py-1">
                            <div v-for="event in eventResults" :key="event.id" 
                                @click="shareToEvent(event)"
                                class="p-3 hover:bg-slate-50 dark:hover:bg-slate-900 cursor-pointer transition-colors"
                                :class="!event.is_selectable ? 'opacity-50 grayscale' : ''"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ event.title }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ event.program_code }} • Level {{ event.level }}</p>
                                    </div>
                                    <span v-if="!event.is_selectable" class="text-[10px] bg-red-500/10 text-red-500 px-2 py-0.5 rounded-full font-bold">LOCKED</span>
                                    <span v-else class="text-[10px] text-primary font-bold">SELECTABLE</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Folder Selection Form (Show when an event is selected) -->
                    <div v-else class="p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-4 animate-in fade-in duration-200">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sharing Target Event</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ selectedEventForShare.title }}</p>
                            </div>
                            <button @click="resetEventShareSelection" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                Change Event
                            </button>
                        </div>

                        <!-- Area selection -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Target Area Folder</label>
                            <select v-model="selectedAreaId" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="" disabled>Select target area...</option>
                                <option v-for="area in areas" :key="area.id" :value="area.id">
                                    {{ area.code }} - {{ area.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Subfolder selection -->
                        <div v-if="selectedAreaId" class="space-y-1.5 animate-in fade-in duration-200">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Target Subfolder</label>
                            <select v-model="selectedSubfolder" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="" disabled>Select target subfolder...</option>
                                <option v-for="sub in SUBFOLDERS" :key="sub" :value="sub">
                                    {{ sub }}
                                </option>
                            </select>
                        </div>

                        <!-- Parameter selection -->
                        <div v-if="selectedSubfolder === 'PARAMETER' && currentAreaParameters.length > 0" class="space-y-1.5 animate-in fade-in duration-200">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Target Parameter</label>
                            <select v-model="selectedParameter" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="" disabled>Select parameter...</option>
                                <option v-for="param in currentAreaParameters" :key="param" :value="param">
                                    {{ param }}
                                </option>
                            </select>
                        </div>

                        <!-- Parameter Folder Selection -->
                        <div v-if="selectedParameter && (selectedParameter.startsWith('Parameter A') || selectedParameter.startsWith('Parameter B'))" class="space-y-1.5 animate-in fade-in duration-200">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Target Parameter Folder</label>
                            <select v-model="selectedParameterFolder" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="" disabled>Select a folder...</option>
                                <option v-for="folder in PARAMETER_FOLDERS" :key="folder" :value="folder">
                                    {{ folder }}
                                </option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button @click="resetEventShareSelection" type="button" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-sm font-semibold">
                                Cancel
                            </button>
                            <button @click="confirmShareToEvent" :disabled="isEventShareSubmitDisabled" class="flex-1 py-2.5 rounded-xl bg-primary hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white transition-colors text-sm font-bold shadow-lg shadow-blue-500/20">
                                {{ isSharing ? 'Sharing...' : 'Confirm & Share' }}
                            </button>
                        </div>
                    </div>

                    <!-- Current Event Shares -->
                    <div class="space-y-3">
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Currently Shared in Rooms</h3>
                        <div v-if="!file.accreditation_events?.length" class="text-center py-10 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                            <span class="material-symbols-outlined text-slate-300 text-[40px] mb-2">dashboard_customize</span>
                            <p class="text-xs text-slate-500">This file hasn't been shared to any accreditation events yet.</p>
                        </div>
                        <div v-for="event in file.accreditation_events" :key="event.id" class="flex items-center justify-between p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="size-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">meeting_room</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ event.title }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <!-- Avatar Stack -->
                                        <div class="flex -space-x-2 overflow-hidden">
                                            <div v-for="i in 3" :key="i" class="inline-block size-5 rounded-full ring-2 ring-white dark:ring-slate-800 bg-slate-200 flex items-center justify-center text-[8px] font-bold">
                                                {{ i }}
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-slate-500 font-medium">+15 reviewers can access</span>
                                    </div>
                                </div>
                            </div>
                            <button class="size-8 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">remove_circle</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div v-if="activeTab === 'users'" class="space-y-8">
                    <!-- Search -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                            <span v-if="!isSearchingUsers" class="material-symbols-outlined text-[22px]">person_search</span>
                            <span v-else class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                        </div>
                        <input 
                            v-model="searchUserQuery"
                            placeholder="Find a specific user to grant access..."
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-4 py-3.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"
                        />
                        <div v-if="searchUserQuery && userResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl z-10 overflow-hidden py-1">
                            <div v-for="user in userResults" :key="user.id" 
                                @click="shareToUser(user.id)"
                                class="p-3 hover:bg-slate-50 dark:hover:bg-slate-900 cursor-pointer transition-colors flex items-center gap-3"
                            >
                                <img v-if="user.google_info?.avatar" :src="user.google_info.avatar" class="size-8 rounded-full" />
                                <div v-else class="size-8 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-bold">{{ user.name.charAt(0) }}</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user.name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Requests (Always Global in this modal) -->
                    <div v-if="pendingRequests.length > 0">
                        <h3 class="text-[11px] font-black text-amber-600 uppercase tracking-widest px-1 mb-3">Pending Access Requests</h3>
                        <div class="space-y-3">
                            <div v-for="req in pendingRequests" :key="req.id" class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-200 dark:border-amber-800/50">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <img v-if="req.user?.google_info?.avatar" :src="req.user.google_info.avatar" class="size-9 rounded-full" />
                                        <div v-else class="size-9 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold">?</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ req.user?.name }}</p>
                                            <p class="text-[10px] text-slate-500">{{ req.user?.email }}</p>
                                        </div>
                                    </div>
                                    <button @click="approveRequest(req.id)" class="px-3 py-1 bg-emerald-600 text-white text-[11px] font-bold rounded-lg shadow-sm hover:bg-emerald-700">GRANT ACCESS</button>
                                </div>
                                <div v-if="req.reason" class="text-xs text-amber-800 dark:text-amber-400 bg-white/50 dark:bg-black/20 p-2.5 rounded-xl italic">
                                    "{{ req.reason }}"
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Shares -->
                    <div class="space-y-3">
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">People with direct access</h3>
                        <div v-if="!sharedUsers.length" class="text-center py-6 text-slate-400 text-xs italic">
                            No specific users added yet.
                        </div>
                        <div v-for="user in sharedUsers" :key="user.id" class="flex items-center justify-between p-3 hover:bg-slate-50 dark:hover:bg-slate-900/50 rounded-2xl transition-colors group">
                            <div class="flex items-center gap-3">
                                <img v-if="user.google_info?.avatar" :src="user.google_info.avatar" class="size-8 rounded-full" />
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user.name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ user.email }}</p>
                                </div>
                            </div>
                            <button class="opacity-0 group-hover:opacity-100 transition-opacity text-slate-400 hover:text-red-500">
                                <span class="material-symbols-outlined text-[18px]">person_remove</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer (Share Link) -->
            <div class="p-6 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-4 bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Permanent Secure Link</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400 truncate">{{ route('files.shared', file.uuid) }}</p>
                    </div>
                    <button @click="copyPermanentLink" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-xs font-bold shadow-lg shadow-primary/20 hover:bg-primary-hover transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">content_copy</span>
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
