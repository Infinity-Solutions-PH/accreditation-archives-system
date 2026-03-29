<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';

const props = defineProps({
    user: Object
});

defineOptions({
    layout: AppLayout
});

const showEditModal = ref(false);
const form = useForm({
    name: props.user.name,
    current_password: '',
    password: '',
    password_confirmation: ''
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const submitUpdate = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            form.reset('current_password', 'password', 'password_confirmation');
        }
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
                            <!-- Avatar Logic -->
                            <div v-if="user.google_info?.avatar" 
                                 class="size-24 rounded-full bg-center bg-cover border-4 border-white dark:border-surface-dark shadow-md"
                                 :style="{ backgroundImage: `url(${user.google_info.avatar})` }">
                            </div>
                            <div v-else 
                                 class="size-24 rounded-full bg-primary flex items-center justify-center border-4 border-white dark:border-surface-dark shadow-md text-white text-3xl font-bold">
                                {{ getInitials(user.name) }}
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
                        <button 
                            @click="showEditModal = true"
                            class="bg-gray-100 dark:bg-gray-800 text-text-main-light dark:text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Edit Account Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Security Card -->
                <div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-start gap-4 transition-all duration-300">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-xl text-primary">
                        <span class="material-symbols-outlined text-[24px]">verified_user</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-bold text-text-main-light dark:text-white">Account Security</h3>
                        <p v-if="user.google_info" class="text-sm text-text-muted-light dark:text-text-muted-dark">
                            Your account is secured via Google Authentication. Password changes are managed through Google.
                        </p>
                        <p v-else class="text-sm text-text-muted-light dark:text-text-muted-dark">
                            Your account is not linked to Google. You can manage your security settings and password directly within the IDO Archives system.
                        </p>
                    </div>
                </div>
                <!-- Recent Activity Card -->
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

        <!-- Edit Account Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden" @click.stop>
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold">Edit Account Details</h3>
                    <button @click="showEditModal = false" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitUpdate" class="p-6 flex flex-col gap-5">
                    <!-- Name Field -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-bold text-text-muted-light dark:text-text-muted-dark">Full Name</label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-background-dark focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            :class="{'border-red-500': form.errors.name}"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</p>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-2"></div>

                    <!-- Change Password Fields -->
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-bold text-text-muted-light dark:text-text-muted-dark">New Password <span class="font-normal opacity-50">(Optional)</span></label>
                            <input 
                                v-model="form.password" 
                                type="password" 
                                placeholder="Min. 8 characters"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-background-dark focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                :class="{'border-red-500': form.errors.password}"
                            />
                            <p v-if="form.errors.password" class="text-xs text-red-500 font-medium">{{ form.errors.password }}</p>
                        </div>
                        <div v-if="form.password" class="flex flex-col gap-1.5">
                            <label class="text-sm font-bold text-text-muted-light dark:text-text-muted-dark">Confirm New Password</label>
                            <input 
                                v-model="form.password_confirmation" 
                                type="password" 
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-background-dark focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-800 my-2"></div>

                    <!-- Confirmation Password -->
                    <div class="flex flex-col gap-1.5 p-4 rounded-xl bg-primary/5 border border-primary/10">
                        <label class="text-sm font-bold text-primary">Current Password</label>
                        <p class="text-[11px] text-primary/70 mb-2">Required to save any changes.</p>
                        <input 
                            v-model="form.current_password" 
                            type="password" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-primary/20 bg-white dark:bg-background-dark focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            :class="{'border-red-500': form.errors.current_password}"
                        />
                        <p v-if="form.errors.current_password" class="text-xs text-red-500 font-medium whitespace-pre-wrap">{{ form.errors.current_password }}</p>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button 
                            type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-sm font-bold text-text-muted-light hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button 
                            type="submit" :disabled="form.processing"
                            class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>
