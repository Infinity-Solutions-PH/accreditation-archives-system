<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import Sidebar from '@shared/Partials/Sidebar.vue';

    const isUserDropdownOpen = ref(false);
    const dropdownRef = ref(null);

    const toggleDropdown = () => {
        isUserDropdownOpen.value = !isUserDropdownOpen.value;
    };

    const closeDropdown = (e) => {
        if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
            isUserDropdownOpen.value = false;
        }
    };

    onMounted(() => {
        window.addEventListener('click', closeDropdown);
    });

    onUnmounted(() => {
        window.removeEventListener('click', closeDropdown);
    });

    const getInitials = (name) => {
        return name ? name.charAt(0).toUpperCase() : '?';
    };
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
                    
                    <!-- User Dropdown Menu -->
                    <div class="relative" ref="dropdownRef">
                        <button 
                            @click.stop="toggleDropdown"
                            class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                            <div class="size-8 rounded-full flex items-center justify-center text-xs font-bold font-primary overflow-hidden shadow-sm"
                                 :class="!$page.props.auth.user.google_info?.avatar ? 'bg-primary text-white' : ''">
                                <img v-if="$page.props.auth.user.google_info?.avatar" :src="$page.props.auth.user.google_info.avatar" class="size-full object-cover" />
                                <span v-else>{{ getInitials($page.props.auth.user.name) }}</span>
                            </div>
                            <span class="text-sm font-medium hidden sm:block text-text-main-light dark:text-white">{{ $page.props.auth.user.name }}</span>
                            <span class="material-symbols-outlined text-[18px] text-text-muted-light dark:text-text-muted-dark transition-transform duration-200"
                                  :class="isUserDropdownOpen ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <!-- Dropdown Content -->
                        <div v-if="isUserDropdownOpen" 
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-surface-dark rounded-xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden py-1 z-50 transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                                <p class="text-xs font-bold text-text-muted-light dark:text-text-muted-dark uppercase tracking-widest">Signed in as</p>
                                <p class="text-sm font-bold text-text-main-light dark:text-white truncate mt-0.5">{{ $page.props.auth.user.email }}</p>
                            </div>
                            
                            <Link :href="route('profile')" 
                                  @click="isUserDropdownOpen = false"
                                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-text-main-light dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                <span class="material-symbols-outlined text-[20px] text-text-muted-light group-hover:text-primary">account_circle</span>
                                My Profile
                            </Link>
                            
                            <Link :href="route('settings')" 
                                  @click="isUserDropdownOpen = false"
                                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-text-main-light dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                <span class="material-symbols-outlined text-[20px] text-text-muted-light group-hover:text-primary">settings</span>
                                Settings
                            </Link>

                            <div class="h-px bg-gray-100 dark:bg-gray-800 my-1"></div>

                            <Link :href="route('logout')" 
                                  method="post" 
                                  as="button"
                                  class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors group">
                                <span class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform">logout</span>
                                Sign Out
                            </Link>
                        </div>
                    </div>
                </div>
            </header>
            <slot />
        </div>
    </div>
</template>