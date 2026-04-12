<script setup>
    import { ref, onMounted, onUnmounted, watch } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';
    import Sidebar from '@shared/Partials/Sidebar.vue';
    import NotificationDropdown from '@/components/NotificationDropdown.vue';
    import ToastList from '@/Components/ToastList.vue';

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

    // Global Search Logic
    const searchQuery = ref('');
    const searchResults = ref({ files: [], events: [], users: [] });
    const isSearching = ref(false);
    const showResults = ref(false);
    const searchContainerRef = ref(null);
    let searchTimeout = null;

    const handleSearch = () => {
        if (searchTimeout) clearTimeout(searchTimeout);
        
        if (searchQuery.value.length < 2) {
            searchResults.value = { files: [], events: [], users: [] };
            showResults.value = false;
            return;
        }

        isSearching.value = true;
        showResults.value = true;

        searchTimeout = setTimeout(async () => {
            try {
                const response = await axios.get(route('api.search.global'), {
                    params: { q: searchQuery.value }
                });
                searchResults.value = response.data;
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                isSearching.value = false;
            }
        }, 300);
    };

    const hasResults = () => {
        return searchResults.value.files.length > 0 || 
               searchResults.value.events.length > 0 || 
               searchResults.value.users.length > 0;
    };

    const closeSearch = (e) => {
        if (searchContainerRef.value && !searchContainerRef.value.contains(e.target)) {
            showResults.value = false;
        }
    };

    onMounted(() => {
        window.addEventListener('click', closeSearch);
        window.addEventListener('click', closeDropdown);
    });

    onUnmounted(() => {
        window.removeEventListener('click', closeSearch);
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
            <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-gray-800 bg-white dark:bg-surface-dark px-6 py-3 z-10">
                <div class="flex items-center gap-4 lg:gap-8 flex-1">
                <button class="md:hidden p-2 text-text-muted-light shrink-0">
                <span class="material-symbols-outlined">menu</span>
                </button>

                <!-- Search Bar -->
                <div class="relative flex-1 max-w-[500px]" ref="searchContainerRef">
                    <label class="flex flex-col w-full h-10">
                        <div class="flex w-full flex-1 items-stretch rounded-xl h-full overflow-hidden bg-[#f3f5f9] dark:bg-gray-800 transition-all focus-within:ring-2 focus-within:ring-primary/20 border border-transparent focus-within:border-primary/30 shadow-sm">
                            <div class="text-[#4c669a] dark:text-gray-400 flex border-none items-center justify-center pl-4">
                                <span v-if="!isSearching" class="material-symbols-outlined text-[20px]">search</span>
                                <span v-else class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                            </div>
                            <input 
                                v-model="searchQuery"
                                @input="handleSearch"
                                @focus="showResults = searchQuery.length >= 2"
                                class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-main-light dark:text-text-main-dark focus:outline-0 border-none bg-transparent placeholder:text-slate-400 dark:placeholder:text-gray-500 px-3 text-sm font-medium leading-normal" 
                                placeholder="Search files, events, or users..." 
                            />
                        </div>
                    </label>

                    <!-- Search Results Dropdown -->
                    <div v-if="showResults" 
                         class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-surface-dark border border-slate-100 dark:border-gray-800 rounded-2xl shadow-2xl overflow-hidden z-[100] transform transition-all duration-200">
                        
                        <div class="max-h-[70vh] overflow-y-auto scroll-smooth py-2">
                            <!-- Files -->
                            <div v-if="searchResults.files.length > 0">
                                <div class="px-4 pt-3 pb-1 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]">description</span> Files
                                </div>
                                <div class="px-2">
                                    <Link v-for="file in searchResults.files" :key="file.id"
                                          :href="route('file-archives', { search: file.title, type: file.is_general ? 'general' : 'personal' })"
                                          @click="showResults = false"
                                          class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-gray-800/60 group transition-all">
                                        <div class="size-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">file_present</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ file.title }}</p>
                                            <p class="text-[11px] text-slate-400">{{ file.is_general ? 'General Files' : 'My Drive' }}</p>
                                        </div>
                                        <span class="material-symbols-outlined text-[18px] opacity-0 group-hover:opacity-100 transition-all text-primary translate-x-2 group-hover:translate-x-0">chevron_right</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- Events -->
                            <div v-if="searchResults.events.length > 0" class="mt-2">
                                <div class="px-4 pt-3 pb-1 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2 border-t border-slate-50 dark:border-gray-800/50">
                                    <span class="material-symbols-outlined text-[16px]">event</span> Events
                                </div>
                                <div class="px-2">
                                    <Link v-for="event in searchResults.events" :key="event.id"
                                          :href="route('areas', { event: event.slug })"
                                          @click="showResults = false"
                                          class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-gray-800/60 group transition-all">
                                        <div class="size-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-500 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ event.title }}</p>
                                            <p class="text-[11px] text-slate-400">Accreditation Session</p>
                                        </div>
                                        <span class="material-symbols-outlined text-[18px] opacity-0 group-hover:opacity-100 transition-all text-primary translate-x-2 group-hover:translate-x-0">chevron_right</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- Users -->
                            <div v-if="searchResults.users.length > 0" class="mt-2">
                                <div class="px-4 pt-3 pb-1 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2 border-t border-slate-50 dark:border-gray-800/50">
                                    <span class="material-symbols-outlined text-[16px]">group</span> People
                                </div>
                                <div class="px-2">
                                    <Link v-for="user in searchResults.users" :key="user.id"
                                          :href="route('profile', { id: user.id })"
                                          @click="showResults = false"
                                          class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-gray-800/60 group transition-all">
                                        <div class="size-8 rounded-full overflow-hidden shrink-0 shadow-sm">
                                            <img v-if="user.google_info?.avatar" :src="user.google_info.avatar" class="size-full object-cover" />
                                            <div v-else class="size-full bg-primary text-white flex items-center justify-center text-[10px] font-bold">
                                                {{ getInitials(user.name) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ user.name }}</p>
                                            <p class="text-[11px] text-slate-400 italic truncate">{{ user.college?.name || 'IDO Staff' }}</p>
                                        </div>
                                        <span class="material-symbols-outlined text-[18px] opacity-0 group-hover:opacity-100 transition-all text-primary translate-x-2 group-hover:translate-x-0">chevron_right</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- No Results -->
                            <div v-if="!isSearching && !hasResults()" class="p-10 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-30 dark:opacity-20">
                                    <span class="material-symbols-outlined text-[48px]">search_off</span>
                                    <p class="text-sm font-medium">No results found for "{{ searchQuery }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="flex gap-3 items-center">
                    <NotificationDropdown />
                    
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

            <!-- Global Notifications -->
            <ToastList />
        </div>
    </div>
</template>