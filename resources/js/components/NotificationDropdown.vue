<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const isOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const dropdownRef = ref(null);
const loading = ref(false);

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('notifications.recent'));
        notifications.value = response.data.recent;
        unreadCount.value = response.data.unreadCount;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    } finally {
        loading.value = false;
    }
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    }
};

const closeDropdown = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

const markAsRead = async (id) => {
    try {
        await router.post(route('notifications.mark-as-read', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                fetchNotifications();
            }
        });
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const markAllRead = async () => {
    try {
        await router.post(route('notifications.mark-all-read'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                fetchNotifications();
            }
        });
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

onMounted(() => {
    fetchNotifications();
    window.addEventListener('click', closeDropdown);
    // Refresh unread count periodically or on event
    const interval = setInterval(fetchNotifications, 60000); // every minute
    onUnmounted(() => clearInterval(interval));
});

onUnmounted(() => {
    window.removeEventListener('click', closeDropdown);
});

const formatTime = (time) => {
    const date = new Date(time);
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <button 
            @click.stop="toggleDropdown"
            class="relative flex items-center justify-center rounded-lg size-10 hover:bg-gray-100 dark:hover:bg-gray-800 text-text-main-light dark:text-text-main-dark transition-all duration-300 group"
            :class="{ 'animate-[pulse_1.5s_infinite]': unreadCount > 0 }"
        >
            <span class="material-symbols-outlined text-[24px]" :class="{ 'text-primary': unreadCount > 0 }">notifications</span>
            <span v-if="unreadCount > 0" class="absolute top-2 right-2 flex size-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full size-2 bg-red-500 border border-white dark:border-gray-900"></span>
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="isOpen" 
             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden z-50 transform origin-top-right transition-all">
            
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-text-main-light dark:text-white uppercase tracking-wider">Notifications</h3>
                    <span v-if="unreadCount > 0" class="px-1.5 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-black">
                        {{ unreadCount }} NEW
                    </span>
                </div>
                <button 
                    v-if="unreadCount > 0"
                    @click="markAllRead"
                    class="text-[11px] font-bold text-primary hover:underline transition-all uppercase tracking-tighter"
                >
                    Mark all as read
                </button>
            </div>

            <div class="max-h-[400px] overflow-y-auto scroll-smooth">
                <div v-if="loading && notifications.length === 0" class="p-10 text-center">
                    <span class="material-symbols-outlined animate-spin text-primary opacity-50">progress_activity</span>
                </div>
                
                <div v-else-if="notifications.length === 0" class="p-10 text-center">
                    <div class="flex flex-col items-center gap-2 opacity-30 dark:opacity-20">
                        <span class="material-symbols-outlined text-[48px]">notifications_off</span>
                        <p class="text-sm font-medium">No recent notifications</p>
                    </div>
                </div>

                <div v-else class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    <div v-for="notif in notifications" :key="notif.id" 
                         class="group relative flex gap-4 p-5 hover:bg-slate-50 dark:hover:bg-gray-800/40 transition-all cursor-pointer border-l-4"
                         :class="notif.read_at ? 'border-transparent' : 'border-primary bg-blue-50/10 dark:bg-primary/5'"
                         @click="markAsRead(notif.id)"
                    >
                        <div class="size-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                             :class="{
                                 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': notif.data.type === 'upload',
                                 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400': notif.data.type === 'event',
                                 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400': notif.data.type === 'success',
                                 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400': notif.data.type === 'reminder',
                                 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400': notif.data.type === 'onboarding'
                             }"
                        >
                            <span class="material-symbols-outlined text-[22px]">{{ notif.data.icon || 'notifications' }}</span>
                        </div>
                        
                        <div class="flex-1 flex flex-col gap-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-text-main-light dark:text-white truncate tracking-tight">{{ notif.data.title }}</span>
                                <span class="text-[10px] font-medium text-text-muted-light dark:text-text-muted-dark whitespace-nowrap">{{ formatTime(notif.created_at) }}</span>
                            </div>
                            <p class="text-[13px] leading-snug text-slate-600 dark:text-slate-400 line-clamp-2">{{ notif.data.message }}</p>
                            
                            <Link v-if="notif.data.url" 
                                  :href="notif.data.url"
                                  class="mt-2 text-[11px] font-bold text-primary flex items-center gap-1.5 hover:gap-2 transition-all opacity-0 group-hover:opacity-100"
                            >
                                View Details <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </Link>
                        </div>

                        <!-- Read dot -->
                        <div v-if="!notif.read_at" class="absolute top-5 right-2 size-1.5 rounded-full bg-primary shadow-[0_0_8px_rgba(var(--color-primary),0.5)]"></div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-slate-50/50 dark:bg-slate-800/30">
                <Link :href="route('notifications.index')" 
                      @click="isOpen = false"
                      class="block w-full text-center py-2 text-xs font-black text-text-main-light dark:text-white hover:text-primary transition-colors uppercase tracking-widest">
                    View full history
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}
</style>
