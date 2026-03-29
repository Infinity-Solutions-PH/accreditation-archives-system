<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';

const props = defineProps({
    notifications: Object
});

defineOptions({
    layout: AppLayout
});

const markAsRead = (id) => {
    router.post(route('notifications.mark-as-read', id), {}, {
        preserveScroll: true
    });
};

const markAllRead = () => {
    router.post(route('notifications.mark-all-read'), {}, {
        preserveScroll: true
    });
};

const formatTime = (time) => {
    const date = new Date(time);
    return date.toLocaleDateString(undefined, { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

const getIcon = (type) => {
    switch (type) {
        case 'upload': return 'upload_file';
        case 'event': return 'event';
        case 'success': return 'check_circle';
        case 'reminder': return 'timer';
        case 'onboarding': return 'person_add';
        default: return 'notifications';
    }
};

const getTypeColor = (type) => {
    switch (type) {
        case 'upload': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400';
        case 'event': return 'text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400';
        case 'success': return 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'reminder': return 'text-indigo-600 bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400';
        case 'onboarding': return 'text-purple-600 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-400';
        default: return 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>
    <main class="flex-1 overflow-y-auto scroll-smooth w-full max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex mb-6">
            <ol class="flex items-center space-x-2">
                <li>
                    <Link class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium" :href="route('dashboard')">Home</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">Notifications</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">System Notifications</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 text-base">Stay updated with the latest activity across your accreditation drives.</p>
            </div>
            
            <button 
                @click="markAllRead"
                class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold transition-all shadow-sm active:scale-95"
            >
                <span class="material-symbols-outlined text-[20px]">done_all</span>
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-surface-dark rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
            <div v-if="notifications.data.length === 0" class="py-20 text-center">
                <div class="flex flex-col items-center gap-4 opacity-30 dark:opacity-20">
                    <span class="material-symbols-outlined text-[64px]">notifications_none</span>
                    <p class="text-lg font-bold">No notifications found.</p>
                    <Link :href="route('dashboard')" class="text-primary hover:underline font-bold">Back to Dashboard</Link>
                </div>
            </div>
            
            <div v-else class="divide-y divide-slate-100 dark:divide-slate-800/50">
                <div v-for="notif in notifications.data" :key="notif.id" 
                     class="group relative flex flex-col sm:flex-row gap-4 p-6 hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-all border-l-4"
                     :class="notif.read_at ? 'border-transparent' : 'border-primary bg-primary/5'"
                >
                    <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 shadow-sm"
                         :class="getTypeColor(notif.data.type)"
                    >
                        <span class="material-symbols-outlined text-[28px]">{{ getIcon(notif.data.type) }}</span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">
                            <h3 class="font-black text-slate-900 dark:text-white text-base tracking-tight">{{ notif.data.title }}</h3>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ formatTime(notif.created_at) }}</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4 max-w-2xl">{{ notif.data.message }}</p>
                        
                        <div class="flex items-center gap-3">
                            <Link v-if="notif.data.url" 
                                  :href="notif.data.url"
                                  class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-xs font-bold transition-all hover:bg-blue-700 shadow-md shadow-primary/20"
                            >
                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                View Details
                            </Link>
                            
                            <button 
                                v-if="!notif.read_at"
                                @click="markAsRead(notif.id)"
                                class="text-xs font-bold text-slate-500 hover:text-primary transition-colors px-2 py-2"
                            >
                                Mark as read
                            </button>
                        </div>
                    </div>

                    <!-- Read Status Indicator -->
                    <div v-if="!notif.read_at" class="hidden sm:block absolute top-1/2 -translate-y-1/2 right-6">
                        <div class="size-2 rounded-full bg-primary animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.links && notifications.data.length > 0" class="px-6 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/20 flex items-center justify-center">
                <div class="flex items-center gap-2">
                    <template v-for="link in notifications.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            :class="[
                                'px-4 py-2 rounded-xl text-sm font-bold transition-all active:scale-95',
                                link.active ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else v-html="link.label" class="px-4 py-2 rounded-xl text-sm text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/50 cursor-not-allowed opacity-50 border border-transparent"></span>
                    </template>
                </div>
            </div>
        </div>
    </main>
</template>
