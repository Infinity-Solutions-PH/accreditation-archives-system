<script setup>
    import SidebarLink from '@shared/Partials/SidebarLink.vue';
    import { usePage } from '@inertiajs/vue3';

    const page = usePage();
</script>

<template>
    <aside class="w-64 flex-shrink-0 border-r border-[#e7ebf3] dark:border-gray-800 bg-surface-light dark:bg-surface-dark flex flex-col transition-all duration-300 hidden md:flex">
        <div class="flex h-full flex-col justify-between p-4">
            <div class="flex flex-col gap-6">
                <!-- Logo Area -->
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-center bg-no-repeat bg-cover rounded-full size-10 shadow-sm" data-alt="University Logo Abstract" style='background-image: url("https://library.cvsu.edu.ph/landing/storage/images/CvSU-logo-64x64.webp");'></div>
                    <h1 class="text-text-main-light dark:text-text-main-dark text-lg font-bold leading-normal">CvSU Admin</h1>
                </div>
                <!-- Navigation Links -->
                <nav class="flex flex-col gap-1">
                    <!-- Overview Section (Only for Admins/Staff) -->
                    <div v-if="page.props.auth?.roles?.some(r => ['admin', 'ido_staff', 'college_officer'].includes(r))" class="mb-2">
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Overview</p>
                        <SidebarLink :href="route('dashboard')">
                            <template #icon>dashboard</template>
                            <template #label>Dashboard</template>
                        </SidebarLink>
                        <SidebarLink :href="route('user-management')">
                            <template #icon>group</template>
                            <template #label>User Management</template>
                        </SidebarLink>
                        <SidebarLink :href="route('taskforce')" v-if="page.props.auth?.roles?.some(r => ['admin', 'ido_staff'].includes(r))">
                            <template #icon>safety_divider</template>
                            <template #label>Taskforce</template>
                        </SidebarLink>
                        <SidebarLink :href="route('activity-logs')" v-if="page.props.auth?.roles?.includes('admin')">
                            <template #icon>history</template>
                            <template #label>Activity Logs</template>
                        </SidebarLink>
                    </div>

                    <!-- Repository Section -->
                    <div>
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Repository</p>
                        <SidebarLink :href="route('file-archives', { type: 'personal' })">
                            <template #icon>hard_drive</template>
                            <template #label>My Drive</template>
                        </SidebarLink>
                        <SidebarLink :href="route('file-archives', { type: 'general' })">
                            <template #icon>description</template>
                            <template #label>General Files</template>
                        </SidebarLink>
                        <SidebarLink :href="route('events.index')">
                            <template #icon>event_note</template>
                            <template #label>Accreditation</template>
                        </SidebarLink>
                        <SidebarLink :href="route('areas')" v-if="page.props.auth?.roles?.some(r => ['admin', 'ido_staff'].includes(r))">
                            <template #icon>folder_open</template>
                            <template #label>AACCUP Areas</template>
                        </SidebarLink>
                    </div>
                </nav>
            </div>
        </div>
    </aside>
</template>