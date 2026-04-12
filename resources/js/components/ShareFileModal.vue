<script setup>
    import { ref, computed, watch } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import api from '@/axios.js';

    const props = defineProps({
        file: {
            type: Object,
            required: true
        },
        activeEvents: {
            type: Array,
            default: () => []
        },
        areas: {
            type: Array,
            default: () => []
        }
    });

    const emit = defineEmits(['close', 'shared']);
    const page = usePage();

    // Tabs: 'event' or 'user'
    const activeTab = ref('event');

    // -- EVENT SHARING STATE --
    const selectedEventId = ref(null);
    const selectedAreaId = ref(null);
    const isSharingEvent = ref(false);
    const eventError = ref('');
    const eventSuccess = ref('');

    // Check if the file is already assigned to the selected event
    const isAlreadySharedToEvent = computed(() => {
        if (!selectedEventId.value || !props.file.accreditation_events) return false;
        
        // We look through the file's attached events to see if it matches the chosen event AND area
        return props.file.accreditation_events.some(
            (ev) => ev.id === parseInt(selectedEventId.value) && ev.pivot?.area_id === parseInt(selectedAreaId.value)
        );
    });

    const shareToEvent = async () => {
        if (!selectedEventId.value || !selectedAreaId.value) return;

        isSharingEvent.value = true;
        eventError.value = '';
        eventSuccess.value = '';

        try {
            await api.post('/events/share', {
                file_id: props.file.id,
                accreditation_event_id: selectedEventId.value,
                area_id: selectedAreaId.value
            });

            eventSuccess.value = 'File seamlessly shared to Accreditation Event!';
            emit('shared');
            
            setTimeout(() => {
                emit('close');
            }, 1500);
        } catch (error) {
            eventError.value = error.response?.data?.message || 'Failed to share to event.';
        } finally {
            isSharingEvent.value = false;
        }
    };

    // -- USER SHARING STATE --
    const searchQuery = ref('');
    const isSearchingUsers = ref(false);
    const userResults = ref([]);
    const isSharingUser = ref(false);
    const userError = ref('');
    const userSuccess = ref('');

    let searchTimeout = null;

    const performUserSearch = async () => {
        if (!searchQuery.value.trim()) {
            userResults.value = [];
            return;
        }

        isSearchingUsers.value = true;
        try {
            const { data } = await api.get('/users/search-shareable', {
                params: { query: searchQuery.value }
            });
            userResults.value = data;
        } catch (error) {
            console.error('Failed searching users:', error);
        } finally {
            isSearchingUsers.value = false;
        }
    }

    watch(searchQuery, (newVal) => {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performUserSearch();
        }, 400); // 400ms debounce
    });

    const isAlreadySharedToUser = (userId) => {
        // If the API hasn't loaded 'shared_with_users' relation, we fall back to hoping it succeeds.
        if (!props.file.shared_with_users) return false;
        return props.file.shared_with_users.some(u => u.id === parseInt(userId));
    }

    const shareToSpecificUser = async (userId) => {
        isSharingUser.value = true;
        userError.value = '';
        userSuccess.value = '';

        try {
            await api.post('/files/share-to-user', {
                file_id: props.file.id,
                user_id: userId
            });

            userSuccess.value = 'Securely linked to user!';
            
            // Optimistically update local state so the button flips to 'Shared'
            if (!props.file.shared_with_users) {
                props.file.shared_with_users = [];
            }
            props.file.shared_with_users.push({ id: userId });

            emit('shared');
        } catch (error) {
            userError.value = error.response?.data?.message || 'Failed to share with user.';
        } finally {
            isSharingUser.value = false;
        }
    }
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="$emit('close')">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-xl shadow-2xl border overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 text-primary p-2 rounded-lg">
                        <span class="material-symbols-outlined text-[24px]">share</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Share File</h3>
                        <p class="text-xs text-gray-500 font-medium truncate max-w-xs">{{ file.title }}</p>
                    </div>
                </div>

                <button @click="$emit('close')" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 shrink-0">
                <button 
                    class="flex-1 py-4 text-sm font-bold flex justify-center items-center gap-2 border-b-2 transition"
                    :class="activeTab === 'event' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    @click="activeTab = 'event'">
                    <span class="material-symbols-outlined text-[20px]">meeting_room</span>
                    Accreditation Event
                </button>
                <button 
                    class="flex-1 py-4 text-sm font-bold flex justify-center items-center gap-2 border-b-2 transition"
                    :class="activeTab === 'user' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    @click="activeTab = 'user'">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    Specific User
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-6 overflow-y-auto">
                
                <!-- EVENT TAB -->
                <div v-show="activeTab === 'event'" class="space-y-5 animate-fade-in">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Select Active Event</label>
                        <select v-model="selectedEventId" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white">
                            <option :value="null" disabled>Choose an event...</option>
                            <option v-for="event in activeEvents" :key="event.id" :value="event.id">
                                {{ event.title }} ({{ event.program?.code }} - {{ event.level }})
                            </option>
                        </select>
                        <p v-if="activeEvents.length === 0" class="text-xs text-red-500 mt-1">No active events found.</p>
                    </div>

                    <div v-if="selectedEventId">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Assign to Area</label>
                        <select v-model="selectedAreaId" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white">
                            <option :value="null" disabled>Choose an area...</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">
                                Area {{ area.code ?? area.id }} <!-- Adjust based on literal Area model format -->
                            </option>
                        </select>
                    </div>

                    <!-- Alerts -->
                    <div v-if="eventError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        {{ eventError }}
                    </div>
                    <div v-if="eventSuccess" class="p-3 bg-emerald-50 text-emerald-600 text-sm rounded-lg border border-emerald-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        {{ eventSuccess }}
                    </div>

                    <button 
                        v-if="selectedEventId && selectedAreaId"
                        type="button"
                        @click="shareToEvent"
                        :disabled="isSharingEvent || isAlreadySharedToEvent"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg text-sm font-bold transition-colors shadow-md shadow-blue-500/20">
                        <span class="material-symbols-outlined text-[20px]">{{ isAlreadySharedToEvent ? 'check' : (isSharingEvent ? 'sync' : 'share') }}</span>
                        {{ isAlreadySharedToEvent ? 'Already Mapped to this Area' : (isSharingEvent ? 'Mapping...' : 'Share to Accreditation Event') }}
                    </button>
                </div>

                <!-- USER TAB -->
                <div v-show="activeTab === 'user'" class="space-y-4 flex flex-col h-full min-h-[300px] animate-fade-in flex-1">
                    
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                        </div>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all dark:text-white"
                            placeholder="Search by name or email (e.g. IDO Staff)..." />
                    </div>

                    <!-- Alerts -->
                    <div v-if="userError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 flex items-center gap-2 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        {{ userError }}
                    </div>
                    <div v-if="userSuccess" class="p-3 bg-emerald-50 text-emerald-600 text-sm rounded-lg border border-emerald-100 flex items-center gap-2 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        {{ userSuccess }}
                    </div>

                    <!-- User Results List -->
                    <div class="flex-1 overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 relative min-h-[200px]">
                        
                        <div v-if="isSearchingUsers" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-10">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        </div>

                        <div v-if="!searchQuery && userResults.length === 0" class="flex flex-col items-center justify-center p-8 text-gray-400 h-full">
                            <span class="material-symbols-outlined text-[48px] mb-2 opacity-50">group</span>
                            <p class="text-sm font-medium">Type to search for peer users</p>
                            <p class="text-xs text-gray-400 mt-1">Admins and Accreditors are excluded.</p>
                        </div>

                        <div v-else-if="userResults.length === 0 && searchQuery && !isSearchingUsers" class="flex flex-col items-center justify-center p-8 text-gray-500 h-full">
                            <p class="text-sm font-medium">No active users match this query.</p>
                        </div>

                        <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="user in userResults" :key="user.id" class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-cover bg-center bg-gray-200 border border-gray-100 dark:border-gray-700 shrink-0"
                                        :style='{backgroundImage: `url(${user.avatar || "https://lh3.googleusercontent.com/a/default-user=s96-c"})`}'>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ user.name }}</span>
                                        <span class="text-xs text-gray-500">{{ user.email }} • {{ user.role }} ({{ user.college }})</span>
                                    </div>
                                </div>

                                <button 
                                    @click="shareToSpecificUser(user.id)"
                                    :disabled="isAlreadySharedToUser(user.id) || isSharingUser"
                                    class="px-3 py-1.5 rounded-md text-xs font-bold transition-all ml-3 shrink-0"
                                    :class="isAlreadySharedToUser(user.id) 
                                        ? 'bg-gray-100 text-gray-500 cursor-not-allowed border border-gray-200' 
                                        : 'bg-primary/10 text-primary hover:bg-primary hover:text-white border border-transparent'">
                                    {{ isAlreadySharedToUser(user.id) ? 'Shared' : 'Share' }}
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
