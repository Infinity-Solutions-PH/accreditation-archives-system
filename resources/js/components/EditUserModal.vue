<script setup>
    import { ref, reactive, watch } from 'vue';
    import api from '@/axios.js';

    const props = defineProps({
        user: Object,
        roles: Array,
        colleges: Array,
        programs: Array
    });

    const emit = defineEmits(['close', 'updated']);
    
    const isSaving = ref(false);

    const form = reactive({
        role_status: props.user.role_status || 'pending',
        role: props.user.roles?.[0]?.name || '',
        college_id: props.user.college_id || '',
        program_id: props.user.program_id || '',
        is_active: props.user.is_active === 1
    });

    const closeModal = () => emit('close');

    const saveUser = async () => {
        if (isSaving.value) return;
        isSaving.value = true;

        try {
            await api.put(`/user-management/${props.user.id}/role-status`, {
                role_status: form.role_status,
                role: form.role || null,
                college_id: form.college_id || null,
                program_id: form.program_id || null,
                is_active: form.is_active
            });
            emit('updated');
        } catch (error) {
            console.error(error);
        } finally {
            isSaving.value = false;
        }
    }
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-lg rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit User Defaults</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Manage user privileges and assignments</p>
                    </div>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form class="space-y-5" @submit.prevent="saveUser">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="role_status">Account Status</label>
                        <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="role_status" v-model="form.role_status">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="role">System Role</label>
                        <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="role" v-model="form.role">
                            <option value="">No Role</option>
                            <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="college">College / Unit</label>
                        <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="college" v-model="form.college_id">
                            <option value="">No College Assigned</option>
                            <option v-for="college in colleges" :key="college.id" :value="college.id">{{ college.name }}</option>
                        </select>
                    </div>
                    
                    <div class="pt-1">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="program">Program</label>
                        <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="program" v-model="form.program_id">
                            <option value="">No Program Assigned</option>
                            <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.name }}</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Active Account</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Allow this user to sign in to the platform.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input class="sr-only peer" type="checkbox" v-model="form.is_active" />
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button @click="saveUser" :disabled="isSaving" class="px-6 py-2.5 bg-primary hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                    Save Changes
                </button>
            </div>
            </div>
    </div>
</template>
