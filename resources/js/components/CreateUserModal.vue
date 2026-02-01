<script setup>
    const emit = defineEmits(['close']);

    // Close / abort
    const closeModal = async () => {
        emit('close');
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
            <div class="p-6 overflow-y-auto custom-scrollbar">
                <form class="space-y-5">
                    <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="full_name">Full Name</label>
                    <input class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" id="full_name" placeholder="e.g. Juan D. Dela Cruz" type="text"/>
                    </div>
                    <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="email">Email Address</label>
                    <div class="relative">
                    <input class="w-full pl-4 pr-32 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" id="email" placeholder="username" type="email"/>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm font-medium border-l border-slate-200 dark:border-slate-700 pl-3">
                                                @cvsu.edu.ph
                                            </div>
                    </div>
                    <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400">Must be a valid Cavite State University email domain.</p>
                    </div>
                    <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="role">System Role</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="role">
                    <option disabled="" selected="" value="">Select a role...</option>
                    <option value="ido">IDO Staff</option>
                    <option value="admin">College Admin</option>
                    <option value="faculty">Faculty</option>
                    <option value="taskforce">Program Taskforce</option>
                    </select>
                    </div>
                    <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="college">College / Unit</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="college">
                    <option disabled="" selected="" value="">Select college...</option>
                    <option>College of Engineering and Information Technology (CEIT)</option>
                    <option>College of Arts and Sciences (CAS)</option>
                    <option>College of Education (CED)</option>
                    <option>College of Nursing (CON)</option>
                    <option>CCAT Campus</option>
                    </select>
                    </div>
                    <div class="pt-1">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="program">Program</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white cursor-pointer" id="program">
                    <option disabled="" selected="" value="">Select program...</option>
                    <option>BS Information Technology</option>
                    <option>BS Computer Science</option>
                    <option>BS Civil Engineering</option>
                    </select>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Send account creation email</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">User will receive login credentials via email.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                        <input class="sr-only peer" type="checkbox"/>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button class="px-6 py-2.5 bg-primary hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                    Create User
                </button>
                </div>
            </div>
    </div>
</template>