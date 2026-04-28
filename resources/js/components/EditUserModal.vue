<script setup>
    import { ref, reactive, computed, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import api from '@/axios.js';

    const props = defineProps({
        user: Object,
        roles: Array,
        colleges: Array,
        programs: Array,
        events: Array
    });

    const emit = defineEmits(['close', 'updated']);
    
    // Detect if this is an accreditor based on the presence of expires_at or resource type
    const isAccreditor = computed(() => {
        return 'expires_at' in props.user || !props.user.roles;
    });

    // Form logic
    const form = useForm({
        name: props.user.name || '',
        email: props.user.email || '',
        password: '',
        role_status: props.user.role_status || 'pending',
        role: props.user.roles?.[0]?.name || '',
        college_id: props.user.college_id || '',
        program_id: props.user.program_id || '',
        is_active: props.user.is_active === true || props.user.is_active === 1,
        expires_at: props.user.expires_at ? props.user.expires_at.split('T')[0] : '',
        selected_events: props.user.events ? props.user.events.map(e => e.id) : []
    });

    const showPassword = ref(false);

    const generatePassword = () => {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        form.password = password;
        showPassword.value = true;
    };

    const closeModal = () => emit('close');

    const saveChanges = () => {
        const url = isAccreditor.value 
            ? route('accreditors.update', props.user.id)
            : route('user-management.role-status', props.user.id);

        form.put(url, {
            preserveScroll: true,
            onSuccess: () => {
                emit('updated');
                emit('close');
            },
            onError: (errors) => {
                console.error('Update failed:', errors);
            }
        });
    }

    // Reactive filtering for programs based on selected college
    const filteredPrograms = computed(() => {
        if (!form.college_id) return [];
        return props.programs.filter(p => p.college_id === parseInt(form.college_id));
    });

    import { watch } from 'vue';
    watch(() => form.role, (newRole) => {
        if (newRole === 'ido_staff' || newRole === 'admin') {
            form.college_id = '';
            form.program_id = '';
        }
    });
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-lg rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined">{{ isAccreditor ? 'shield_person' : 'manage_accounts' }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit {{ isAccreditor ? 'Accreditor' : 'User' }} Information</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Manage account details and assignments</p>
                    </div>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar max-h-[70vh]">
                <form class="space-y-5" @submit.prevent="saveChanges">
                    
                    <!-- Common Fields (If Accreditor) -->
                    <template v-if="isAccreditor">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Full Name</label>
                            <input 
                                v-model="form.name"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                type="text"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Email Address</label>
                            <input 
                                v-model="form.email"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                type="email" 
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 font-bold uppercase tracking-tight text-[11px]">Set New Password (Optional)</label>
                                <button type="button" @click="generatePassword" class="text-[10px] font-bold text-primary hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">autorenew</span> Generate
                                </button>
                            </div>
                            <div class="relative">
                                <input 
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                    placeholder="Leave blank to keep current" 
                                />
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                >
                                    <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Assigned Events</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                                <div v-for="event in events" :key="event.id" class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        :id="'edit-event-'+event.id" 
                                        :value="event.id" 
                                        v-model="form.selected_events"
                                        class="rounded border-slate-300 text-primary focus:ring-primary"
                                    />
                                    <label :for="'edit-event-'+event.id" class="text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                        {{ event.title }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Access Expiration</label>
                            <input 
                                v-model="form.expires_at"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                type="date"
                            />
                            <p v-if="form.errors.expires_at" class="mt-1 text-xs text-red-500">{{ form.errors.expires_at }}</p>
                        </div>
                    </template>

                    <!-- Normal User Specific Fields -->
                    <template v-else>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">System Role</label>
                            <select class="w-full px-4 py-1.5 h-11 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all dark:text-white cursor-pointer" v-model="form.role">
                                <option value="">No Role</option>
                                <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</option>
                            </select>
                        </div>
                        
                        <div v-if="form.role !== 'ido_staff' && form.role !== 'admin'">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">College / Unit</label>
                            <select class="w-full px-4 py-1.5 h-11 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all dark:text-white cursor-pointer" v-model="form.college_id">
                                <option value="">No College Assigned</option>
                                <option v-for="college in colleges" :key="college.id" :value="college.id">{{ college.name }}</option>
                            </select>
                        </div>
                        
                        <div v-if="form.college_id && form.role !== 'ido_staff' && form.role !== 'admin'" class="pt-1">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Program</label>
                            <select class="w-full px-4 py-1.5 h-11 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all dark:text-white cursor-pointer" v-model="form.program_id">
                                <option value="">No Program Assigned</option>
                                <option v-for="program in filteredPrograms" :key="program.id" :value="program.id">{{ program.name }}</option>
                            </select>
                        </div>
                    </template>

                    <!-- Status Common Fields -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5 font-bold uppercase tracking-tight text-[11px]">Account Approval Status</label>
                        <select class="w-full px-4 py-1.5 h-11 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 transition-all dark:text-white cursor-pointer" v-model="form.role_status">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight text-[11px]">Active Access</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Enable or disable system login.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input class="sr-only peer" type="checkbox" v-model="form.is_active" />
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary shadow-sm transition-all"></div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button @click="saveChanges" :disabled="form.processing" class="flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all active:scale-95">
                    <span v-if="form.processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>
    </div>
</template>
