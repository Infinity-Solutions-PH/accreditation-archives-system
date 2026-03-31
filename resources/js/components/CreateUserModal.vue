<script setup>
    import { ref, computed } from 'vue';
    import { useForm } from '@inertiajs/vue3';

    const props = defineProps({
        roles: Array,
        colleges: Array,
        programs: Array
    });

    const emit = defineEmits(['close']);
    
    const form = useForm({
        name: '',
        email: '',
        role: '',
        college_id: '',
        program_id: '',
        send_email: true
    });

    // Reactive filtering for programs based on selected college
    const filteredPrograms = computed(() => {
        if (!form.college_id) return [];
        return props.programs.filter(p => p.college_id === parseInt(form.college_id));
    });

    // Handle college change to reset program
    const onCollegeChange = () => {
        form.program_id = '';
    };

    const closeModal = () => {
        emit('close');
    }

    const saveNewUser = () => {
        // Validation check for empty email before processing
        if (!form.email) {
            form.errors.email = 'Email username is required.';
            return;
        }

        // Format email to include domain
        const fullEmail = form.email.trim().toLowerCase() + '@cvsu.edu.ph';
        
        // We temporarily set it to fullEmail then submit
        const originalEmail = form.email;
        form.email = fullEmail;

        form.post(route('api.user-management.store'), {
            onSuccess: () => {
                form.reset();
                emit('close');
            },
            onError: () => {
                // If there's an error, revert email for UI consistency if needed
                form.email = originalEmail;
            }
        });
    }
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-lg rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined">person_add</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Create New User</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add a new user to the archive system</p>
                    </div>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar max-h-[70vh]">
                <form @submit.prevent="saveNewUser" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="full_name">Full Name</label>
                        <input 
                            v-model="form.name"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                            id="full_name" 
                            placeholder="e.g. Juan D. Dela Cruz" 
                            type="text"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="email">Email Address</label>
                        <div class="relative">
                            <input 
                                v-model="form.email"
                                class="w-full pl-4 pr-32 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                id="email" 
                                placeholder="username" 
                                type="text" 
                            />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm font-medium border-l border-slate-200 dark:border-slate-700 pl-3">
                                @cvsu.edu.ph
                            </div>
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">Must be a valid Cavite State University email domain.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="role">System Role</label>
                        <select 
                            v-model="form.role"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" 
                            id="role"
                        >
                            <option value="" disabled>Select a role...</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="college">College / Unit</label>
                        <select 
                            v-model="form.college_id"
                            @change="onCollegeChange"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" 
                            id="college"
                        >
                            <option value="" disabled>Select college...</option>
                            <option v-for="college in colleges" :key="college.id" :value="college.id">
                                {{ college.name }} ({{ college.code }})
                            </option>
                        </select>
                        <p v-if="form.errors.college_id" class="mt-1 text-xs text-red-500">{{ form.errors.college_id }}</p>
                    </div>

                    <div v-if="form.college_id" class="pt-1">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="program">Program</label>
                        <select 
                            v-model="form.program_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" 
                            id="program"
                        >
                            <option value="">Select program (Optional)</option>
                            <option v-for="program in filteredPrograms" :key="program.id" :value="program.id">
                                {{ program.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.program_id" class="mt-1 text-xs text-red-500">{{ form.errors.program_id }}</p>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Send account creation email</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">User will receive login credentials via email.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.send_email" class="sr-only peer" type="checkbox"/>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all"></div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button 
                    @click="saveNewUser" 
                    :disabled="form.processing"
                    class="px-6 py-2.5 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
                >
                    <span v-if="form.processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    {{ form.processing ? 'Saving...' : 'Create User' }}
                </button>
            </div>
        </div>
    </div>
</template>