<script setup>
    import { ref, computed, watch } from 'vue';
    import { useForm } from '@inertiajs/vue3';

    const props = defineProps({
        roles: Array,
        colleges: Array,
        programs: Array,
        events: Array
    });

    const emit = defineEmits(['close']);
    
    const activeTab = ref('normal'); // 'normal' or 'accreditor'

    // Form for Normal User
    const form = useForm({
        name: '',
        email: '',
        role: '',
        college_id: '',
        program_id: '',
        send_email: true
    });

    // Form for Accreditor
    const accreditorForm = useForm({
        name: '',
        email: '',
        password: '',
        selected_events: [], // IDs of events
        expires_at: '',
        send_email: true
    });

    const showAccreditorPassword = ref(false);

    const generateAccreditorPassword = () => {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        accreditorForm.password = password;
        showAccreditorPassword.value = true;
    };

    // Reactive filtering for programs based on selected college
    const filteredPrograms = computed(() => {
        if (!form.college_id) return [];
        return props.programs.filter(p => p.college_id === parseInt(form.college_id));
    });

    // Handle college change to reset program
    const onCollegeChange = () => {
        form.program_id = '';
    };

    watch(() => form.role, (newRole) => {
        if (newRole === 'ido_staff' || newRole === 'admin') {
            form.college_id = '';
            form.program_id = '';
        }
    });

    // Auto-fill expiration logic for Accreditor
    watch(() => accreditorForm.selected_events, (newEvents) => {
        if (newEvents.length === 0) return;
        
        const selectedEventData = props.events.filter(e => newEvents.includes(e.id));
        const maxExpiry = selectedEventData.reduce((max, e) => {
            const expiry = new Date(e.expires_at);
            return (expiry > max) ? expiry : max;
        }, new Date(0));

        if (maxExpiry.getTime() > 0) {
            accreditorForm.expires_at = maxExpiry.toISOString().split('T')[0];
        }
    });

    const closeModal = () => {
        emit('close');
    }

    const saveNewUser = () => {
        if (activeTab.value === 'normal') {
            if (!form.email) {
                form.errors.email = 'Email address is required.';
                return;
            }

            /* 
            COMMENTED OUT AS PER REQUEST: Fix email suffixing, allow other domains.
            const fullEmail = form.email.trim().toLowerCase() + '@cvsu.edu.ph';
            const originalEmail = form.email;
            form.email = fullEmail;
            */

            form.post(route('user-management.store'), {
                onSuccess: () => {
                    form.reset();
                    emit('close');
                }
            });
        } else {
            accreditorForm.post(route('accreditors.store'), {
                onSuccess: () => {
                    accreditorForm.reset();
                    emit('close');
                }
            });
        }
    }
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-lg rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/20">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined">{{ activeTab === 'normal' ? 'person_add' : 'shield_person' }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Create New Account</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add a new {{ activeTab }} to the system</p>
                    </div>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Tabs Selection -->
            <div class="flex border-b border-slate-200 dark:border-slate-800">
                <button 
                    @click="activeTab = 'normal'" 
                    class="flex-1 py-3 text-sm font-semibold transition-all border-b-2"
                    :class="activeTab === 'normal' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    Normal User
                </button>
                <button 
                    @click="activeTab = 'accreditor'" 
                    class="flex-1 py-3 text-sm font-semibold transition-all border-b-2"
                    :class="activeTab === 'accreditor' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                >
                    Accreditor
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto custom-scrollbar max-h-[65vh]">
                <!-- TAB: Normal User -->
                <form v-if="activeTab === 'normal'" @submit.prevent="saveNewUser" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
                        <input 
                            v-model="form.name"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                            placeholder="e.g. Juan D. Dela Cruz" 
                            type="text"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
                        <div class="relative">
                            <input 
                                v-model="form.email"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                placeholder="name@domain.com" 
                                type="email" 
                            />
                            <!-- 
                            COMMENTED OUT BUT KEPT: Domain restriction UI
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm font-medium border-l border-slate-200 dark:border-slate-700 pl-3">
                                @cvsu.edu.ph
                            </div>
                            -->
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium">Use a valid institutional or personal email address.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">System Role</label>
                        <select 
                            v-model="form.role"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer"
                        >
                            <option value="" disabled>Select a role...</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</p>
                    </div>

                    <div v-if="form.role !== 'ido_staff' && form.role !== 'admin'">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">College / Unit</label>
                        <select 
                            v-model="form.college_id"
                            @change="onCollegeChange"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer"
                        >
                            <option value="" disabled>Select college...</option>
                            <option v-for="college in colleges" :key="college.id" :value="college.id">
                                {{ college.name }} ({{ college.code }})
                            </option>
                        </select>
                        <p v-if="form.errors.college_id" class="mt-1 text-xs text-red-500">{{ form.errors.college_id }}</p>
                    </div>

                    <div v-if="form.college_id && form.role !== 'ido_staff' && form.role !== 'admin'" class="pt-1">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Program</label>
                        <select 
                            v-model="form.program_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer"
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

                <!-- TAB: Accreditor -->
                <form v-else @submit.prevent="saveNewUser" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Accreditor Full Name</label>
                        <input 
                            v-model="accreditorForm.name"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                            placeholder="e.g. Dr. Jane Smith" 
                            type="text"
                        />
                        <p v-if="accreditorForm.errors.name" class="mt-1 text-xs text-red-500">{{ accreditorForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
                        <input 
                            v-model="accreditorForm.email"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                            placeholder="accreditor@external.com" 
                            type="email" 
                        />
                        <p v-if="accreditorForm.errors.email" class="mt-1 text-xs text-red-500">{{ accreditorForm.errors.email }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Set Account Password</label>
                            <button type="button" @click="generateAccreditorPassword" class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">autorenew</span> Generate
                            </button>
                        </div>
                        <div class="relative">
                            <input 
                                v-model="accreditorForm.password"
                                :type="showAccreditorPassword ? 'text' : 'password'"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                placeholder="••••••••" 
                            />
                            <button 
                                type="button" 
                                @click="showAccreditorPassword = !showAccreditorPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                            >
                                <span class="material-symbols-outlined text-[20px]">{{ showAccreditorPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </div>
                        <p v-if="accreditorForm.errors.password" class="mt-1 text-xs text-red-500">{{ accreditorForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Select Accreditation Events</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <div v-for="event in events" :key="event.id" class="flex items-center gap-2">
                                <input 
                                    type="checkbox" 
                                    :id="'event-'+event.id" 
                                    :value="event.id" 
                                    v-model="accreditorForm.selected_events"
                                    class="rounded border-slate-300 text-primary focus:ring-primary"
                                />
                                <label :for="'event-'+event.id" class="text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                    {{ event.title }}
                                </label>
                            </div>
                            <div v-if="!events.length" class="text-xs text-slate-500 italic p-2 text-center">No active events available.</div>
                        </div>
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400">Multiple events can be selected for access.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Account Expiration</label>
                        <input 
                            v-model="accreditorForm.expires_at"
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                            type="date"
                        />
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-medium italic">Automatically set to the furthest event expiration date but can be manual edited.</p>
                        <p v-if="accreditorForm.errors.expires_at" class="mt-1 text-xs text-red-500">{{ accreditorForm.errors.expires_at }}</p>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Send invite email</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Accreditor will receive login credentials.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="accreditorForm.send_email" class="sr-only peer" type="checkbox"/>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Footer Footer (Global) -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button 
                    @click="saveNewUser" 
                    :disabled="form.processing || accreditorForm.processing"
                    class="px-6 py-2.5 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
                >
                    <span v-if="form.processing || accreditorForm.processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    {{ form.processing || accreditorForm.processing ? 'Saving...' : 'Create ' + (activeTab === 'normal' ? 'User' : 'Accreditor') }}
                </button>
            </div>
        </div>
    </div>
</template>