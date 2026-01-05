<script setup>
    import Sidebar from '@shared/Partials/Sidebar.vue';
</script>

<template>
    <div class="flex h-screen w-full overflow-hidden">
        <!-- SideNavBar -->
        <Sidebar />
        <!-- Main Content Layout -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-background-light dark:bg-background-dark relative">
            <!-- TopNavBar -->
            <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#e7ebf3] dark:border-gray-800 bg-surface-light dark:bg-surface-dark px-6 py-3 z-10">
                <div class="flex items-center gap-4 lg:gap-8">
                <button class="md:hidden p-2 text-text-muted-light">
                <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-text-main-light dark:text-text-main-dark">
                <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Dashboard Overview</h2>
                </div>
                <!-- Search Bar -->
                <label class="flex flex-col min-w-40 h-10 max-w-96 lg:w-96">
                <div class="flex w-full flex-1 items-stretch rounded-lg h-full overflow-hidden bg-[#e7ebf3] dark:bg-gray-800 transition-colors focus-within:ring-2 focus-within:ring-primary/50">
                <div class="text-[#4c669a] dark:text-gray-400 flex border-none items-center justify-center pl-4">
                <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-main-light dark:text-text-main-dark focus:outline-0 border-none bg-transparent placeholder:text-[#4c669a] dark:placeholder:text-gray-500 px-3 text-sm font-normal leading-normal" placeholder="Search files, users, or logs..." value=""/>
                </div>
                </label>
                </div>
                <div class="flex gap-3 items-center">
                <button class="relative flex items-center justify-center rounded-lg size-10 hover:bg-gray-100 dark:hover:bg-gray-800 text-text-main-light dark:text-text-main-dark transition-colors">
                <span class="material-symbols-outlined text-[24px]">notifications</span>
                <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-900"></span>
                </button>
                <button class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-8" data-alt="User Avatar Profile" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBgr9tWD_qhuI66VZmtATV3mWUGORRdLjKn52gPz7u8_Fdwjdx3fYMiIIacLVslMiQN50vq4cSO1aiRUzrnva-CD-SAjaj8uvYRN3iPC6kStgRpIqEO3WzUZS10HIwabpGymzwjsFAPwX6avmXt9DtRl4uRhJzPakYYVuZ1NtghcUxTYE1TWv0uKUJA7XvHBXxOnN_NdaB5RPAvnPqoh-ort85zkgv9cHmMJs4XQrAVFbARsY-o7mPZlzYqeamAfJLKLs42E-1V2QPG");'></div>
                <span class="text-sm font-medium hidden sm:block">Admin</span>
                </button>
                </div>
            </header>
            <slot />
        </div>
    </div>
</template>