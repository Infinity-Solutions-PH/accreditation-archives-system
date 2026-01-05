<script setup>
    import SidebarLink from '@shared/Partials/SidebarLink.vue';
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
                <nav class="flex flex-col gap-2">
                    <SidebarLink :href="route('dashboard')">
                        <template #icon>dashboard</template>
                        <template #label>Dashboard</template>
                    </SidebarLink>
                    <SidebarLink :href="route('user-management')">
                        <template #icon>group</template>
                        <template #label>User Management</template>
                    </SidebarLink>
                    <SidebarLink :href="route('file-archives')">
                        <template #icon>folder_open</template>
                        <template #label>File Archives</template>
                    </SidebarLink>
                    <SidebarLink :href="route('activity-logs')">
                        <template #icon>history</template>
                        <template #label>Activity Logs</template>
                    </SidebarLink>
                    <SidebarLink :href="route('settings')">
                        <template #icon>settings</template>
                        <template #label>Settings</template>
                    </SidebarLink>
                </nav>
            </div>
            <!-- Bottom Action -->
            <div class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors" href="#">
                    <span class="material-symbols-outlined text-[24px]">logout</span>
                    <p class="text-sm font-medium leading-normal">Sign Out</p>
                </a>
            </div>
        </div>
    </aside>
</template>