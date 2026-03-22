<script setup>
    import { ref, computed } from 'vue';
    import { router, Head } from '@inertiajs/vue3';

    import AppLayout from '@shared/Layouts/App.vue';

    import CreateUserModal from '@/components/CreateUserModal.vue';
    import EditUserModal from '@/components/EditUserModal.vue';

    defineOptions({
        layout: AppLayout
    });

    const props = defineProps({
        users: [Object, Array],
        userStats: {
            type: Object,
            required: true
        },
        roles: Array,
        colleges: Array,
        programs: Array
    })

    const showCreateUserModal = ref(false);
    const editingUser = ref(null);

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
        router.reload({ only: ['users', 'userStats'] });
    }

    const searchQuery = ref('');
    const roleFilter = ref('All Roles');
    const statusFilter = ref('All Status');

    const filteredUsers = computed(() => {
        return props.users.filter(user => {
            if (roleFilter.value !== 'All Roles') {
                if (!user.roles || user.roles.length === 0) return false;
                if (roleFilter.value === 'IDO Staff' && user.roles[0].name !== 'ido_staff') return false;
                if (roleFilter.value === 'College Officer' && user.roles[0].name !== 'college_officer') return false;
                if (roleFilter.value === 'Taskforce' && user.roles[0].name !== 'taskforce') return false;
            }
            if (statusFilter.value !== 'All Status') {
                if (statusFilter.value === 'Active' && (!user.is_active || user.role_status !== 'approved')) return false;
                if (statusFilter.value === 'Pending' && user.role_status !== 'pending') return false;
                if (statusFilter.value === 'Inactive' && (user.is_active && user.role_status !== 'pending' && user.role_status !== 'rejected')) return false;
                if (statusFilter.value === 'Rejected' && user.role_status !== 'rejected') return false;
            }
            if (searchQuery.value) {
                const sq = searchQuery.value.toLowerCase();
                return (user.name && user.name.toLowerCase().includes(sq)) || 
                       (user.email && user.email.toLowerCase().includes(sq)) || 
                       (user.college && user.college.name.toLowerCase().includes(sq));
            }
            return true;
        });
    });
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Active Users</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.active }}</span>
                            <span class="text-green-600 text-sm font-medium bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded flex items-center">
                                <span class="material-symbols-outlined text-[14px] mr-0.5">trending_up</span> 5%
                            </span>
                        </div>
                    </div>
                    <div @click="statusFilter = 'Pending'" class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1 relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <span class="material-symbols-outlined text-6xl text-primary">person_add</span>
                        </div>
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Pending Requests</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.pending || 0 }}</span>
                            <span class="text-primary text-sm font-medium">Needs Approval</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Faculty Admins</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ userStats.officers || 0 }}</span>
                            <span class="text-slate-400 text-sm font-medium">College Officers</span>
                        </div>
                    </div>
                </div>
                <!-- Main Table Section -->
                <div class="bg-white dark:bg-[#1a2234] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
                    <!-- Filters Toolbar -->
                    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center bg-slate-50/50 dark:bg-slate-800/20">
                        <!-- Search -->
                        <div class="relative w-full lg:w-96">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input v-model="searchQuery" class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500" placeholder="Search by name, email, or college..." type="text"/>
                        </div>
                        <!-- Filter Actions -->
                        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Filter:</span>
                                <select v-model="roleFilter" class="bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 cursor-pointer">
                                    <option>All Roles</option>
                                    <option>IDO Staff</option>
                                    <option>College Officer</option>
                                    <option>Taskforce</option>
                                </select>
                                <select v-model="statusFilter" class="bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 cursor-pointer">
                                    <option>All Status</option>
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Rejected</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                            <div class="h-6 w-px bg-slate-300 dark:bg-slate-600 hidden sm:block"></div>
                            <button class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-blue-400 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined text-[20px]">filter_list</span>
                                <span>Advanced</span>
                            </button>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                    <th class="p-4 w-12">
                                    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
                                    </th>
                                    <th class="p-4">User Details</th>
                                    <th class="p-4">Role</th>
                                    <th class="p-4">Office</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Last Active</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm text-slate-700 dark:text-slate-300">
                                <!-- Row 1 -->
                                <tr
                                    v-for="user in filteredUsers" :key="user.id"
                                    class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                    <td class="p-4">
                                        <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center text-sm font-bold">
                                                <img v-if="user?.google_info?.avatar" :src="user?.google_info?.avatar" class="size-9 rounded-full object-cover" />
                                                <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900 dark:text-white">{{ user.name }}</div>
                                                <div class="text-slate-500 dark:text-slate-400 text-xs">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div v-if="user.roles && user.roles.length > 0" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300 border border-purple-100 dark:border-purple-800">
                                            <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                            {{ user.roles[0].name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                        </div>
                                        <div v-else class="text-xs text-slate-500 px-2 py-1">No Role</div>
                                    </td>
                                    <td class="p-4">
                                        <div v-if="user.college">{{ user.college.name }} ({{ user.college.code }})</div>
                                        <div v-else class="text-slate-400 text-xs italic">Unassigned</div>
                                    </td>
                                    <td class="p-4 flex flex-col items-start gap-1">
                                        <span v-if="user.role_status === 'approved' && user.is_active" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300 border border-green-100 dark:border-green-800">
                                            <span class="size-1.5 rounded-full bg-green-500"></span> Active
                                        </span>
                                        <span v-else-if="user.role_status === 'approved' && !user.is_active" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            <span class="size-1.5 rounded-full bg-slate-400"></span> Inactive
                                        </span>
                                        <span v-if="user.role_status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                                            <span class="material-symbols-outlined text-[14px]">hourglass_top</span> Pending
                                        </span>
                                        <span v-else-if="user.role_status === 'rejected'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border border-red-100 dark:border-red-800">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span> Rejected
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-500 dark:text-slate-400">{{ new Date(user.updated_at).toLocaleDateString() }}</td>
                                    <td class="p-4 text-right">
                                        <div v-if="user.role_status === 'pending'" class="flex items-center justify-end gap-1">
                                            <button class="flex items-center gap-1 px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-[14px]">check</span> Approve
                                            </button>
                                            <button class="p-1 rounded text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-colors" title="Reject">
                                                <span class="material-symbols-outlined text-[20px]">close</span>
                                            </button>
                                        </div>
                                        <div v-else class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editUser(user)" class="p-1.5 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit User">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button class="p-1.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete User">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Showing <span class="font-medium text-slate-900 dark:text-white">1</span> to <span class="font-medium text-slate-900 dark:text-white">5</span> of <span class="font-medium text-slate-900 dark:text-white">1,284</span> results</span>
                        <div class="flex items-center gap-1">
                            <button class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 disabled:opacity-50 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </button>
                            <button class="px-3 py-1 rounded bg-primary text-white text-sm font-medium">1</button>
                            <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">2</button>
                            <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">3</button>
                                <span class="px-2 text-slate-400">...</span>
                            <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">24</button>
                            <button class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CreateUserModal 
            v-if="showCreateUserModal"
            @close="closeCreateUserModal"
        />
        <EditUserModal 
            v-if="editingUser"
            :user="editingUser"
            :roles="roles"
            :colleges="colleges"
            :programs="programs"
            @close="closeEditModal"
            @updated="onUserUpdated"
        />
    </main>
</template>