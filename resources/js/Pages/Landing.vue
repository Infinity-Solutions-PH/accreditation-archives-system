<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import AppLayout from '@shared/Layouts/App.vue';
    import UploadModal from '@/components/UploadModal.vue';
    import ConfirmationModal from '@/components/ConfirmationModal.vue';
    import api from '@/axios.js';

    const props = defineProps({
        counts: Object,
        pendingUsers: Array,
        expiringFiles: Array,
        recentActivity: Array
    });

    const showUploadModal = ref(false);

    const openUploadModal = () => {
        showUploadModal.value = true;
    }

    const closeUploadModal = () => {
        showUploadModal.value = false;
    }

    defineOptions({
        layout: AppLayout
    });

    // Confirmation Logic
    const confirmingAction = ref(null); // { user, type: 'approve' | 'reject' }
    const isProcessingAction = ref(false);

    const handleApprove = (user) => {
        confirmingAction.value = {
            user,
            type: 'approve',
            title: 'Approve User Request',
            message: `Are you sure you want to approve ${user.name}'s account access?`,
            confirmText: 'Approve User',
            confirmButtonClass: 'bg-green-600 hover:bg-green-700'
        };
    };

    const handleReject = (user) => {
        confirmingAction.value = {
            user,
            type: 'reject',
            title: 'Reject User Request',
            message: `Are you sure you want to reject ${user.name}'s request?`,
            confirmText: 'Reject Request',
            confirmButtonClass: 'bg-red-600 hover:bg-red-700'
        };
    };

    const closeConfirmationModal = () => {
        confirmingAction.value = null;
    };

    const executeAction = async () => {
        if (!confirmingAction.value || isProcessingAction.value) return;
        
        const { user, type } = confirmingAction.value;
        const status = type === 'approve' ? 'approved' : 'rejected';
        
        isProcessingAction.value = true;
        try {
            await api.put(`/user-management/${user.id}/role-status`, {
                role_status: status,
                is_active: type === 'approve'
            });
            confirmingAction.value = null;
            router.reload({ only: ['counts', 'pendingUsers', 'recentActivity'] });
        } catch (error) {
            console.error('Failed to update user status:', error);
        } finally {
            isProcessingAction.value = false;
        }
    };
    const getLogNameLabel = (name) => {
        if (!name || name === 'default') return 'System Event';
        return name.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    };
</script>

<template>
    <!-- Scrollable Content -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-8 scroll-smooth">
        <div class="max-w-[1200px] mx-auto flex flex-col gap-6">
            <!-- PageHeading -->
            <div class="flex flex-wrap justify-between items-end gap-3">
                <div class="flex flex-col gap-1">
                    <h1 class="text-text-main-light dark:text-white tracking-tight text-[28px] md:text-[32px] font-bold leading-tight">Welcome back, Admin</h1>
                    <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-normal leading-normal">Here is what needs your attention today regarding the accreditation archives.</p>
                </div>
                <div class="flex gap-3">
                    <button class="flex items-center gap-2 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-text-main-light dark:text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Generate Report
                    </button>
                    <button
                        @click="openUploadModal"
                        class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/20">
                        <span class="material-symbols-outlined text-[18px]">upload_file</span>
                        Upload Document
                    </button>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Total Documents</p>
                        <span class="material-symbols-outlined text-primary bg-blue-50 dark:bg-blue-900/20 p-1.5 rounded-lg">description</span>
                    </div>
                    <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">{{ counts.totalDocuments?.toLocaleString() }}</p>
                    <span class="text-xs text-green-600 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">trending_up</span> Documents tracked</span>
                </div>
                <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Active Users</p>
                        <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 p-1.5 rounded-lg">group</span>
                    </div>
                    <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">{{ counts.activeUsers }}</p>
                    <span class="text-xs text-text-muted-light dark:text-text-muted-dark font-medium">Verified system access</span>
                </div>
                <div 
                    v-if="$page.props.auth?.roles?.some(r => ['admin', 'ido_staff', 'college_officer'].includes(r))"
                    class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border-l-4 border-l-orange-400 border-y border-r border-r-[#cfd7e7] border-y-[#cfd7e7] dark:border-y-gray-700 dark:border-r-gray-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Pending Approvals</p>
                        <span class="material-symbols-outlined text-orange-500 bg-orange-50 dark:bg-orange-900/20 p-1.5 rounded-lg">person_add</span>
                    </div>
                    <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">{{ counts.pendingApprovals }}</p>
                    <span class="text-xs text-orange-600 font-medium">Requires immediate action</span>
                </div>
                <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Expiring Soon</p>
                        <span class="material-symbols-outlined text-red-500 bg-red-50 dark:bg-red-900/20 p-1.5 rounded-lg">timer</span>
                    </div>
                    <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">{{ counts.expiringSoon }}</p>
                    <span class="text-xs text-red-600 font-medium">Documents expiring &lt; 30 days</span>
                </div>
            </div>
            <!-- Dashboard Main Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Column (Main) -->
            <div class="xl:col-span-2 flex flex-col gap-6">
            <!-- Charts Section -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm p-6">
            <div class="flex justify-between items-start mb-6">
            <div class="flex flex-col gap-1">
            <h3 class="text-text-main-light dark:text-white text-lg font-bold">Upload Activity</h3>
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm">Document submission trends over last 6 months</p>
            </div>
            <div class="flex items-center gap-2 px-2 py-1 bg-green-50 dark:bg-green-900/20 rounded text-green-700 dark:text-green-400 text-xs font-bold">
            <span class="material-symbols-outlined text-[16px]">arrow_upward</span> 12% vs last period
                                                </div>
            </div>
            <div class="grid min-h-[220px] grid-flow-col gap-4 sm:gap-8 grid-rows-[1fr_auto] items-end justify-items-center px-2">
            <!-- Bars -->
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[60%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[45%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[35%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[80%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[55%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[90%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <!-- Labels -->
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Jan</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Feb</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Mar</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Apr</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">May</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Jun</p>
            </div>
            </div>
            <!-- Pending Approvals Table -->
            <div 
                v-if="$page.props.auth?.roles?.some(r => ['admin', 'ido_staff', 'college_officer'].includes(r))"
                class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#cfd7e7] dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-text-main-light dark:text-white text-lg font-bold">Pending User Approvals</h3>
                    <Link class="text-primary text-sm font-bold hover:underline" :href="route('user-management')">View All</Link>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-background-light dark:bg-gray-800 text-text-muted-light dark:text-text-muted-dark font-medium uppercase text-xs">
                <tr>
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Department</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Date</th>
                <th class="px-6 py-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#e7ebf3] dark:divide-gray-700">
                    <tr v-if="pendingUsers.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-text-muted-light dark:text-text-muted-dark italic">No pending user approvals at this time.</td>
                    </tr>
                    <tr v-for="user in pendingUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center text-xs font-bold font-primary overflow-hidden">
                                <img v-if="user.google_info?.avatar" :src="user.google_info.avatar" class="size-full object-cover" />
                                <span v-else>{{ user.name.charAt(0) }}</span>
                            </div>
                            <div class="font-bold text-text-main-light dark:text-white">{{ user.name }}</div>
                        </td>
                        <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark text-xs truncate max-w-[150px]">{{ user.college?.name || 'Unassigned' }}</td>
                        <td class="px-6 py-4">
                            <span v-if="user.roles?.[0]" class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-[10px] font-bold uppercase tracking-wider">
                                {{ user.roles[0].name.replace('_', ' ') }}
                            </span>
                            <span v-else class="text-xs text-text-muted-light italic">None</span>
                        </td>
                        <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark text-xs">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <button @click="handleApprove(user)" class="size-8 flex items-center justify-center rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors active:scale-90" title="Approve">
                                <span class="material-symbols-outlined text-[18px]">check</span>
                            </button>
                            <button @click="handleReject(user)" class="size-8 flex items-center justify-center rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors active:scale-90" title="Deny">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
                </table>
                </div>
            </div>
            </div>
            <!-- Right Column (Side) -->
            <div class="flex flex-col gap-6">
            <!-- Expiring Documents Widget -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-[#cfd7e7] dark:border-gray-700">
            <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-red-500 text-[20px]">warning</span>
            <h3 class="text-text-main-light dark:text-white text-base font-bold">Expiring Documents</h3>
            </div>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">Files needing renewal within 30 days</p>
            </div>
            <div class="flex flex-col divide-y divide-[#e7ebf3] dark:divide-gray-700 max-h-[300px] overflow-y-auto">
                <div v-if="expiringFiles.length === 0" class="p-8 text-center text-text-muted-light dark:text-text-muted-dark text-xs italic">
                    No documents expiring soon.
                </div>
                <div v-for="file in expiringFiles" :key="file.id" class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 p-2 rounded shrink-0">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-text-main-light dark:text-white truncate">{{ file.title }}</p>
                        <p class="text-xs" :class="new Date(file.expiration) < new Date(Date.now() + 7 * 24 * 60 * 60 * 1000) ? 'text-red-600' : 'text-orange-600'">
                            Expires {{ new Date(file.expiration).toLocaleDateString() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="p-3 border-t border-[#cfd7e7] dark:border-gray-700 text-center">
                <a class="text-xs font-bold text-primary hover:underline" href="#">View Repository</a>
            </div>
            </div>
            <!-- Activity Log Feed -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden flex-1">
                <div class="p-5 border-b border-[#cfd7e7] dark:border-gray-700">
                    <h3 class="text-text-main-light dark:text-white text-base font-bold">Recent Activity</h3>
                </div>
                <div class="p-5 overflow-y-auto">
                    <div class="relative pl-4 border-l-2 border-[#e7ebf3] dark:border-gray-700 flex flex-col gap-6">
                        <div v-if="recentActivity.length === 0" class="text-xs text-text-muted-light italic">No recent activity recorded.</div>
                        <div v-for="activity in recentActivity" :key="activity.id" class="relative group">
                            <div class="absolute -left-[21px] top-1 bg-primary rounded-full size-2.5 outline outline-4 outline-white dark:outline-surface-dark group-hover:scale-125 transition-transform"></div>
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-800 uppercase tracking-tighter">
                                        {{ getLogNameLabel(activity.log_name) }}
                                    </span>
                                    <p class="text-xs text-text-muted-light dark:text-text-muted-dark font-medium">{{ activity.created_at_human }}</p>
                                </div>
                                <p class="text-sm text-text-main-light dark:text-white leading-relaxed">
                                    <span class="font-bold border-b border-dotted border-gray-400 dark:border-gray-600">{{ activity.causer_name }}</span> 
                                    <span class="ml-1 text-[#4c669a] dark:text-gray-300 font-medium">{{ activity.description }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3 border-t border-[#cfd7e7] dark:border-gray-700 text-center">
                    <Link :href="route('activity-logs')" class="text-xs font-bold text-primary hover:underline">View All Activities</Link>
                </div>
            </div>
            </div>
            </div>
        </div>
        <UploadModal 
            v-if="showUploadModal"
            @close="closeUploadModal"
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