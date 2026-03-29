<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';

const props = defineProps({
    user: Object
});

defineOptions({
    layout: AppLayout
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="My Profile" />

    <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gray-50 dark:bg-background-dark">
        <div class="max-w-4xl mx-auto flex flex-col gap-8">
            <!-- Header section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold text-text-main-light dark:text-white">Account Profile</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark">Manage your personal information and account settings.</p>
            </div>

            <!-- Profile Card -->
            <div class="bg-white dark:bg-surface-dark rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden transition-all duration-300">
                <div class="h-32 bg-gradient-to-r from-primary to-indigo-600"></div>
                
                <div class="px-8 pb-8">
                    <div class="relative flex justify-between items-end -mt-12 mb-6">
                        <div class="p-1 bg-white dark:bg-surface-dark rounded-full">
                            <div class="size-24 rounded-full bg-center bg-cover border-4 border-white dark:border-surface-dark shadow-md"
                                 :style="{ backgroundImage: `url(${user.google_info?.avatar || 'https://ui-avatars.com/api/?name=' + user.name})` }">
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold uppercase tracking-wider">
                                {{ user.role_status }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                        <!-- Left Column -->
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-widest">Full Name</label>
                                <p class="text-lg font-semibold text-text-main-light dark:text-white">{{ user.name }}</p>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-widest">Email Address</label>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[18px]">mail</span>
                                    <p class="text-text-main-light dark:text-white font-medium">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-widest">Assigned Roles</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span v-for="role in user.roles" :key="role.id" 
                                          class="px-2 py-1 rounded bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-wider">
                                        {{ role.name.replace('_', ' ') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-widest">Department / College</label>
                                <p class="text-text-main-light dark:text-white font-semibold">{{ user.college?.name || 'Not Assigned' }}</p>
                                <p v-if="user.program" class="text-sm text-text-muted-light dark:text-text-muted-dark">{{ user.program.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center">
                        <div class="flex flex-col gap-1">
                            <p class="text-xs text-text-muted-light dark:text-text-muted-dark italic">Account created on {{ formatDate(user.created_at) }}</p>
                        </div>
                        <button class="bg-gray-100 dark:bg-gray-800 text-text-main-light dark:text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Edit Account Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-start gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-xl text-primary">
                        <span class="material-symbols-outlined text-[24px]">verified_user</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-bold text-text-main-light dark:text-white">Account Security</h3>
                        <p class="text-sm text-text-muted-light dark:text-text-muted-dark">Your account is secured via Google Authentication. Password changes are managed through Google.</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-start gap-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-xl text-indigo-600">
                        <span class="material-symbols-outlined text-[24px]">history</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-bold text-text-main-light dark:text-white">Recent Activity</h3>
                        <p class="text-sm text-text-muted-light dark:text-text-muted-dark">View your recent document uploads and system interactions.</p>
                        <a href="#" class="text-primary text-xs font-bold hover:underline mt-2">View Activity History</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
