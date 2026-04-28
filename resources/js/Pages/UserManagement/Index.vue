<script setup>
    import { ref, watch } from 'vue';
    import { router, Head, Link } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';

    import AppLayout from '@shared/Layouts/App.vue';

    import CreateUserModal from '@/components/CreateUserModal.vue';
    import EditUserModal from '@/components/EditUserModal.vue';
    import ConfirmationModal from '@/components/ConfirmationModal.vue';
    import api from '@/axios.js';

    defineOptions({
        layout: AppLayout
    });

    const props = defineProps({
        users: Object, // Laravel Paginator
        userStats: {
            type: Object,
            required: true
        },
        roles: Array,
        colleges: Array,
        programs: Array,
        events: Array,
        filters: Object
    })

    const showCreateUserModal = ref(false);
    const editingUser = ref(null);
    const isLoading = ref(false);

    const openCreateUserModal = () => {
        showCreateUserModal.value = true;
    }

    const closeCreateUserModal = () => {
        showCreateUserModal.value = false;
    }

    const editUser = (user) => {
        editingUser.value = user;
    }

    const closeEditModal = () => {
        editingUser.value = null;
    }

    const onUserUpdated = () => {
        closeEditModal();
        closeConfirmationModal();
        router.reload({ only: ['users', 'userStats'] });
    }

    // Confirmation Logic
    const confirmingAction = ref(null); // { user, type: 'approve' | 'reject' }
    const isProcessingAction = ref(false);

    const handleApprove = (user) => {
        confirmingAction.value = {
            user,
            type: 'approve',
            title: 'Approve User Request',
            message: `Are you sure you want to approve ${user.name}'s account access? This will allow them to login and access assigned programs.`,
            confirmText: 'Approve User',
            confirmButtonClass: 'bg-green-600 hover:bg-green-700'
        };
    };

    const handleReject = (user) => {
        confirmingAction.value = {
            user,
            type: 'reject',
            title: 'Reject User Request',
            message: `Are you sure you want to reject ${user.name}'s request? They will not be able to access the system until approved.`,
            confirmText: 'Reject Request',
            confirmButtonClass: 'bg-red-600 hover:bg-red-700'
        };
    };

    const closeConfirmationModal = () => {
        confirmingAction.value = null;
    };

    const executeAction = () => {
        if (!confirmingAction.value || isProcessingAction.value) return;
        
        const { user, type } = confirmingAction.value;
        const status = type === 'approve' ? 'approved' : 'rejected';
        
        isProcessingAction.value = true;
        
        router.put(route('user-management.role-status', user.id), {
            role_status: status,
            is_active: type === 'approve'
        }, {
            preserveScroll: true,
            onSuccess: () => {
                onUserUpdated();
            },
            onError: (errors) => {
                console.error('Failed to update user status:', errors);
            },
            onFinish: () => {
                isProcessingAction.value = false;
            }
        });
    };

    const searchQuery = ref(props.filters.search || '');
    const roleFilter = ref(props.filters.role || 'All Roles');
    const statusFilter = ref(props.filters.status || 'All Status');
    const activeTab = ref(props.filters.tab || 'users');

    // Debounced server-side filtering matching Activity Logs logic
    const updateFilters = useDebounceFn(() => {
        router.get(route('user-management'), {
            search: searchQuery.value,
            role: roleFilter.value,
            status: statusFilter.value,
            tab: activeTab.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => { isLoading.value = true; },
            onFinish: () => { isLoading.value = false; }
        });
    }, 500);

    watch([searchQuery, roleFilter, statusFilter, activeTab], () => {
        updateFilters();
    });

    const resetFilters = () => {
        searchQuery.value = '';
        roleFilter.value = 'All Roles';
        statusFilter.value = 'All Status';
    };
</script>

<template>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <Head title="User Management" />
        <!-- Scrollable Page Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
            <div class="max-w-7xl mx-auto flex flex-col gap-6">
                <!-- Breadcrumbs -->
                <nav class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400">
                    <a class="hover:text-primary transition-colors" href="#">Home</a>
                    <span class="mx-2">/</span>
                    <a class="hover:text-primary transition-colors" href="#">Administration</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 dark:text-white">User Management</span>
                </nav>
                <!-- Page Heading & Primary Action -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white tracking-tight">User Management</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-base max-w-2xl">Manage user accounts, assign roles, and approve access requests for college accreditation officers.</p>
                    </div>
                    <button type="button" @click="openCreateUserModal" class="flex items-center gap-2 bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all active:scale-95 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Create New User
                </button>
                </div>
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Active Users</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.active }}</span>
                        </div>
                    </div>
                    <div @click="statusFilter = 'Pending'; activeTab = 'users'" class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1 relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <span class="material-symbols-outlined text-4xl text-primary">person_add</span>
                        </div>
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Pending Requests</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.pending || 0 }}</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Faculty Admins</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.officers || 0 }}</span>
                        </div>
                    </div>
                    <div @click="activeTab = 'accreditors'" class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1 relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <span class="material-symbols-outlined text-4xl text-primary">verified_user</span>
                        </div>
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Accreditors</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.accreditors || 0 }}</span>
                        </div>
                    </div>
                </div>
                <!-- Tab Switcher -->
                <div class="flex border-b border-slate-200 dark:border-slate-700">
                    <button 
                        @click="activeTab = 'users'"
                        class="px-6 py-3 text-sm font-bold transition-all border-b-2"
                        :class="activeTab === 'users' ? 'text-primary border-primary bg-primary/5' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300'"
                    >
                        System Users
                    </button>
                    <button 
                        @click="activeTab = 'accreditors'"
                        class="px-6 py-3 text-sm font-bold transition-all border-b-2"
                        :class="activeTab === 'accreditors' ? 'text-primary border-primary bg-primary/5' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300'"
                    >
                        Accreditors
                    </button>
                </div>

                <!-- Main Table Section -->
                <div class="bg-white dark:bg-[#1a2234] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
                <!-- Filters and Search Toolbar -->
                <div class="flex flex-col gap-4">
                    <div class="bg-white dark:bg-[#151c2b] rounded-2xl p-6 shadow-sm border border-[#e7ebf3] dark:border-gray-800 flex flex-col lg:flex-row gap-6 justify-between items-end">
                        <!-- Search Bar -->
                        <div class="w-full lg:w-1/3">
                            <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">Search Users</label>
                            <div class="flex w-full items-stretch rounded-xl h-11 border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all" :class="{'opacity-50 pointer-events-none': isLoading}">
                                <div class="text-[#4c669a] flex items-center justify-center pl-3 pr-2">
                                    <span class="material-symbols-outlined text-[20px]">search</span>
                                </div>
                                <input v-model="searchQuery" :disabled="isLoading" class="flex w-full min-w-0 flex-1 bg-transparent text-[#0d121b] dark:text-white focus:outline-0 h-full placeholder:text-[#9ca3af] text-sm font-medium" placeholder="Search by name, email, or college..."/>
                            </div>
                        </div>

                        <!-- Filter Selection -->
                        <div class="flex flex-wrap gap-4 w-full lg:w-auto flex-1 justify-end">
                            <!-- Role Filter -->
                            <div v-if="activeTab === 'users'" class="w-full sm:w-auto min-w-[180px]">
                                <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">User Role</label>
                                <select v-model="roleFilter" :disabled="isLoading" class="w-full h-11 rounded-xl border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 px-4 text-sm font-medium text-[#0d121b] dark:text-white focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option>All Roles</option>
                                    <option>IDO Staff</option>
                                    <option>College Officer</option>
                                    <option>Taskforce</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="w-full sm:w-auto min-w-[180px]">
                                <label class="block text-xs font-bold text-[#4c669a] uppercase tracking-wider mb-2">Account Status</label>
                                <select v-model="statusFilter" :disabled="isLoading" class="w-full h-11 rounded-xl border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 px-4 text-sm font-medium text-[#0d121b] dark:text-white focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option>All Status</option>
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Rejected</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            <div class="flex items-end">
                                <button @click="resetFilters" :disabled="isLoading" class="flex h-11 px-4 shrink-0 items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-[#4c669a] hover:text-primary transition-all font-bold text-sm gap-2 disabled:opacity-50" title="Reset Filters">
                                    <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                                    <span>Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                    <!-- Table -->
                    <div class="overflow-x-auto relative min-h-[400px]">
                        <!-- Loading Overlay -->
                        <div v-if="isLoading" class="absolute inset-x-0 top-0 h-1 bg-primary/20 overflow-hidden z-[10]">
                            <div class="h-full bg-primary animate-[loading_2s_infinite_ease-in-out]"></div>
                        </div>
                        <div v-if="isLoading" class="absolute inset-0 bg-white/40 dark:bg-slate-900/40 backdrop-blur-[1px] flex items-center justify-center z-[5]">
                            <div class="p-3 rounded-full bg-white dark:bg-slate-800 shadow-xl border border-slate-100 dark:border-slate-700">
                                <span class="material-symbols-outlined animate-spin text-primary">progress_activity</span>
                            </div>
                        </div>

                        <table class="w-full text-left border-collapse transition-all duration-300" :class="{'opacity-50 grayscale-[0.5] pointer-events-none': isLoading}">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                    <th class="p-4 w-12 text-center">
                                        #
                                    </th>
                                    <th class="p-4">User Details</th>
                                    <th v-if="activeTab === 'users'" class="p-4">Role</th>
                                    <th class="p-4">{{ activeTab === 'users' ? 'Office' : 'Affiliation' }}</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">{{ activeTab === 'users' ? 'Last Active' : 'Access Expiry' }}</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm text-slate-700 dark:text-slate-300">
                                <!-- Row 1 -->
                                <tr
                                    v-for="(item, index) in users.data" :key="item.id"
                                    class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                    <td class="p-4 text-center text-xs text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3" @click="activeTab === 'accreditors' ? editUser(item) : null">
                                            <div class="size-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center text-sm font-bold shadow-sm">
                                                <img v-if="item?.google_info?.avatar" :src="item.google_info.avatar" class="size-9 rounded-full object-cover" />
                                                <span v-else>{{ item?.name?.charAt(0).toUpperCase() || '?' }}</span>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ item?.name || 'Unknown User' }}</div>
                                                <div class="text-slate-500 dark:text-slate-400 text-xs">{{ item?.email || 'No Email' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Role / Level -->
                                    <td v-if="activeTab === 'users'" class="p-4">
                                        <div v-if="item.roles && item.roles.length > 0" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs text-nowrap font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300 border border-purple-100 dark:border-purple-800">
                                            <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                            {{ item.roles[0].name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                        </div>
                                        <div v-else class="text-xs text-slate-500 px-2 py-1">No Role</div>
                                    </td>
                                    <!-- Office / Affiliation -->
                                    <td class="p-4">
                                        <div v-if="item?.college" class="text-xs font-medium">
                                            {{ item.college.code }} <span class="text-slate-400 font-normal hidden md:inline"> - {{ item.college.name }}</span>
                                        </div>
                                        <div v-else class="text-slate-400 text-xs italic">Unassigned</div>
                                    </td>
                                    <!-- Status -->
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <span v-if="item?.role_status === 'approved' && item?.is_active" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                            <span v-else-if="item?.role_status === 'approved' && !item?.is_active" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span class="size-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                            <span v-else-if="item?.role_status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-100 dark:border-amber-800 shadow-sm">
                                                <span class="material-symbols-outlined text-[14px]">bolt</span> Pending
                                            </span>
                                            <span v-else-if="item?.role_status === 'rejected'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-300 border border-rose-100 dark:border-rose-800">
                                                <span class="material-symbols-outlined text-[14px]">cancel</span> Rejected
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Date Column -->
                                    <td class="p-4">
                                        <div v-if="activeTab === 'users'" class="text-xs text-slate-500 font-medium">
                                            {{ item?.updated_at ? new Date(item.updated_at).toLocaleDateString() : 'N/A' }}
                                        </div>
                                        <div v-else class="flex flex-col gap-0.5">
                                            <div class="text-xs font-bold" :class="new Date(item.expires_at) < new Date() ? 'text-rose-500' : 'text-slate-700 dark:text-slate-200'">
                                                {{ item.expires_at_human }}
                                            </div>
                                            <div v-if="new Date(item.expires_at) < new Date()" class="text-[10px] text-rose-400 uppercase font-black">Expired</div>
                                        </div>
                                    </td>
                                    <!-- Actions -->
                                    <td class="p-4 text-right">
                                        <div v-if="item.role_status === 'pending' && activeTab === 'users'" class="flex items-center justify-end gap-1">
                                            <button 
                                                @click="handleApprove(item)"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-500/20 active:scale-95"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">how_to_reg</span> Approve
                                            </button>
                                            <button 
                                                @click="handleReject(item)"
                                                class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all active:scale-95" 
                                                title="Reject"
                                            >
                                                <span class="material-symbols-outlined text-[20px]">person_remove</span>
                                            </button>
                                        </div>
                                        <div v-else class="flex items-center justify-end gap-1 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editUser(item)" class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-all" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </button>
                                            <button class="p-2 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all" title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div v-if="users.total > 0" class="p-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">
                            Showing <span class="font-medium text-slate-900 dark:text-white">{{ users.from }}</span> to <span class="font-medium text-slate-900 dark:text-white">{{ users.to }}</span> of <span class="font-medium text-slate-900 dark:text-white">{{ users.total.toLocaleString() }}</span> results
                        </span>
                        <div class="flex items-center gap-1">
                            <template v-for="(link, index) in users.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 rounded text-sm font-medium transition-colors flex items-center justify-center min-w-[32px]',
                                        link.active
                                            ? 'bg-primary text-white'
                                            : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="px-3 py-1 text-sm font-medium text-slate-400 cursor-not-allowed min-w-[32px]"
                                >
                                    {{ link.label }}
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CreateUserModal 
            v-if="showCreateUserModal"
            :roles="roles"
            :colleges="colleges"
            :programs="programs"
            :events="events"
            @close="closeCreateUserModal"
        />
        <EditUserModal 
            v-if="editingUser"
            :user="editingUser"
            :roles="roles"
            :colleges="colleges"
            :programs="programs"
            :events="events"
            @close="closeEditModal"
            @updated="onUserUpdated"
        />

        <ConfirmationModal
            v-if="confirmingAction"
            :show="!!confirmingAction"
            :title="confirmingAction.title"
            :message="confirmingAction.message"
            :confirmText="confirmingAction.confirmText"
            :confirmButtonClass="confirmingAction.confirmButtonClass"
            :isProcessing="isProcessingAction"
            @close="closeConfirmationModal"
            @confirm="executeAction"
        />
    </main>
</template>