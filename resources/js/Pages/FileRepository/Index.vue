<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import AppLayout from '@shared/Layouts/App.vue';
    import UploadModal from '@/components/UploadModal.vue';
    import FileViewerModal from '@/components/FileViewerModal.vue';
    import ShareFileModal from '@/components/ShareFileModal.vue';

    const props = defineProps({
        files: Object,
        colleges: Array,
        programs: Array,
        areas: Array,
        activeEvents: Array,
        currentType: String,
        currentEvent: Object,
        filters: Object // Added filters prop
    });

    function getFileIcon(extension) {
        const ext = extension?.toLowerCase();
        if (!ext) return 'insert_drive_file';
        if (ext === 'pdf') return 'picture_as_pdf';
        if (ext === 'doc' || ext === 'docx') return 'description';
        if (ext === 'xls' || ext === 'xlsx') return 'table_chart';
        if (['png','jpg','jpeg','gif'].includes(ext)) return 'image';
        if (['mp4','mov','webm'].includes(ext)) return 'videocam';
        if (['mp3','wav','ogg'].includes(ext)) return 'audiotrack';
        return 'insert_drive_file';
    }

    defineOptions({
        layout: AppLayout
    });

    const showUploadModal = ref(false);

    const openUploadModal = () => {
        showUploadModal.value = true;
    }

    const closeUploadModal = () => {
        showUploadModal.value = false;
    }

    const showFileViewerModal = ref(false);
    const selectedFile = ref(null);

    const openFileViewerModal = (file) => {
        selectedFile.value = file;
        showFileViewerModal.value = true;
    }

    const closeFileViewerModal = () => {
        showFileViewerModal.value = false;
    }

    const showShareModal = ref(false);
    const selectedFileToShare = ref(null);

    const openShareModal = (file) => {
        selectedFileToShare.value = file;
        showShareModal.value = true;
    }

    const isLoading = ref(false);
    
    // Filtering Logic
    const filters = ref({
        search: props.filters?.search || '',
        status: props.filters?.status || 'All',
        college_id: props.filters?.college_id || 'All',
        program_id: props.filters?.program_id || 'All'
    });

    const applyFilters = () => {
        router.get(route('file-archives'), {
            type: props.currentType,
            event_id: props.currentEvent?.id,
            ...filters.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    let searchTimeout = null;
    const handleSearch = () => {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    };

    const resetFilters = () => {
        filters.value = {
            search: '',
            status: 'All',
            college_id: 'All',
            program_id: 'All'
        };
        applyFilters();
    };

    let unregisterStart = null;
    let unregisterFinish = null;

    onMounted(() => {
        unregisterStart = router.on('start', () => { isLoading.value = true; });
        unregisterFinish = router.on('finish', () => { isLoading.value = false; });
    });

    onUnmounted(() => {
        if (unregisterStart) unregisterStart();
        if (unregisterFinish) unregisterFinish();
    });
</script>

<template>
    <main class="flex-1 overflow-y-auto scroll-smooth w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex mb-6">
            <ol class="flex items-center space-x-2">
                <li>
                    <Link class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium" :href="route('dashboard')">Home</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">
                        {{ currentType === 'personal' ? 'My Drive' : (currentType === 'shared' ? 'Shared with Me' : (currentType === 'event' && currentEvent ? currentEvent.title : 'General Files')) }}
                    </span>
                </li>
            </ol>
        </nav>
        <!-- Page Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2 max-w-2xl">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ currentType === 'all' ? 'All Files' : (currentType === 'personal' ? 'My Drive' : (currentType === 'shared' ? 'Shared with Me' : (currentType === 'event' && currentEvent ? currentEvent.title : 'General Files'))) }}
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    {{ currentType === 'all' 
                        ? 'Centralized access to all documents uploaded across the repository.'
                        : (currentType === 'personal' 
                        ? 'Your isolated personal storage for preparing and storing drafts.' 
                        : (currentType === 'shared'
                            ? 'Documents securely shared directly with you by other peers or administrators.'
                            : (currentType === 'event' && currentEvent
                                ? `Virtual drive for ${currentEvent.program?.code} - ${currentEvent.level}. Organized by AACCUP Areas.`
                                : 'Collaborative storage accessible to your college taskforce.'))) }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <!-- Toggle (Only show if not in event view and not admin) -->
                <div v-if="currentType !== 'event' && currentType !== 'all'" class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <Link :href="route('file-archives', { type: 'general' })"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                            currentType === 'general' 
                                ? 'bg-white dark:bg-slate-700 text-primary shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        ]">
                        General Drive
                    </Link>
                    <Link :href="route('file-archives', { type: 'shared' })"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                            currentType === 'shared' 
                                ? 'bg-white dark:bg-slate-700 text-primary shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        ]">
                        Shared Drive
                    </Link>
                    <Link :href="route('file-archives', { type: 'personal' })"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                            currentType === 'personal' 
                                ? 'bg-white dark:bg-slate-700 text-primary shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' 
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        ]">
                        My Drive
                    </Link>
                </div>

                <button v-if="currentType !== 'shared'" type="button"
                    @click="openUploadModal"
                    class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-md shadow-blue-500/20">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    Upload File
                </button>
            </div>
        </div>
        <!-- Filter & Search Toolbar -->
        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="w-full lg:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-400">search</span>
                    </div>
                    <input 
                        v-model="filters.search"
                        @input="handleSearch"
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white" 
                        placeholder="Search by filename or uploader..." type="text"/>
                </div>
                <!-- Filters -->
                <div class="flex flex-wrap gap-2 w-full lg:w-auto overflow-x-auto pb-1 lg:pb-0">
                    <!-- Status Filter -->
                    <div class="relative group">
                        <select v-model="filters.status" @change="applyFilters"
                            class="appearance-none bg-slate-100 dark:bg-slate-800 pl-3 pr-8 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 border border-transparent dark:border-slate-700 focus:ring-primary focus:border-primary outline-none">
                            <option value="All">Status: All</option>
                            <option value="Completed">Completed</option>
                            <option value="Uploading">Uploading</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                    </div>

                    <!-- College Filter (Admin/Staff Only) -->
                    <div v-if="$page.props.auth.roles?.some(role => ['admin', 'ido_staff'].includes(role))" class="relative group">
                        <select v-model="filters.college_id" @change="applyFilters"
                            class="appearance-none bg-slate-100 dark:bg-slate-800 pl-3 pr-8 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 border border-transparent dark:border-slate-700 focus:ring-primary focus:border-primary outline-none">
                            <option value="All">College: All</option>
                            <option v-for="college in colleges" :key="college.id" :value="college.id">{{ college.code }}</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                    </div>

                    <!-- Program Filter -->
                    <div class="relative group">
                        <select v-model="filters.program_id" @change="applyFilters"
                            class="appearance-none bg-slate-100 dark:bg-slate-800 pl-3 pr-8 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 border border-transparent dark:border-slate-700 focus:ring-primary focus:border-primary outline-none">
                            <option value="All">Program: All</option>
                            <option v-for="program in (filters.college_id !== 'All' ? programs.filter(p => p.college_id == filters.college_id) : programs)" 
                                    :key="program.id" :value="program.id">{{ program.code }}</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-[18px] pointer-events-none">expand_more</span>
                    </div>

                    <div class="w-px h-8 bg-slate-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>
                    <button @click="resetFilters" class="p-2 text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary" title="Reset Filters">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Event View: Area Grouping -->
        <div v-if="currentType === 'event'" class="space-y-12">
            <div v-for="area in areas" :key="area.id" class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-2">
                    <div class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                        {{ area.code }}
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ area.name }}</h2>
                    <span class="text-xs text-slate-500 ml-auto">{{ files.data.filter(f => f.assigned_area_id == area.id).length }} files</span>
                </div>

                <div class="overflow-hidden bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="p-4 text-xs font-semibold text-slate-500 uppercase">File Name</th>
                                <th class="p-4 text-xs font-semibold text-slate-500 uppercase">Program</th>
                                <th class="p-4 text-xs font-semibold text-slate-500 uppercase">Uploader</th>
                                <th class="p-4 text-xs font-semibold text-slate-500 uppercase">Date</th>
                                <th class="p-4 text-xs font-semibold text-slate-500 uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="file in files.data.filter(f => f.assigned_area_id == area.id)" :key="file.id">
                                <td class="p-4 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-slate-400">{{ getFileIcon(file.extension) }}</span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ file.title }}</span>
                                        <span class="text-[11px] text-slate-500">{{ file.size_human }} • {{ file.extension.toUpperCase() }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ file.program?.code || 'N/A' }}</td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ file.uploaded_by_name || file.uploaded_by?.name }}</td>
                                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ new Date(file.created_at).toLocaleDateString() }}</td>
                                <td class="p-4 text-right">
                                    <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
                                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="files.data.filter(f => f.assigned_area_id == area.id).length === 0">
                                <td colspan="5" class="p-8 text-center text-slate-400 text-sm italic">No files shared to this area yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Standard Table (Non-Event View) -->
        <div v-else class="overflow-hidden bg-white dark:bg-surface-dark rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none relative">
            <!-- Loading Overlay -->
            <div v-if="isLoading" class="absolute inset-x-0 top-0 h-1 bg-primary/20 overflow-hidden z-[10]">
                <div class="h-full bg-primary animate-[loading_2s_infinite_ease-in-out]"></div>
            </div>
            <div v-if="isLoading" class="absolute inset-0 bg-white/40 dark:bg-background-dark/40 backdrop-blur-[1px] flex items-center justify-center z-[5]">
                <div class="p-3 rounded-full bg-white dark:bg-surface-dark shadow-xl border border-gray-100 dark:border-gray-800">
                    <span class="material-symbols-outlined animate-spin text-primary">progress_activity</span>
                </div>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" :class="{'opacity-60 grayscale-[0.3]': isLoading}">
        <thead>
        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
        <th class="p-4 w-12 text-center" scope="col">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">File Name</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell" scope="col">
            {{ currentType === 'shared' ? 'Original Uploader' : 'College / Program' }}
        </th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell" scope="col">
            {{ currentType === 'shared' ? 'Shared By' : 'Uploaded By' }}
        </th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">
            {{ currentType === 'shared' ? 'Date Shared' : 'Expiry Date' }}
        </th>
        <th v-if="currentType !== 'shared'" class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Status</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right" scope="col">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <!-- Row 1: Active -->
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" v-for="file in files.data" :key="file.id" @click="openFileViewerModal(file)">
            <td class="p-4 text-center">
                <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
            </td>
            <td class="p-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 shrink-0 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined">{{ getFileIcon(file.extension) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">{{ file.title }}</span>
                        <span class="text-xs text-slate-500">{{ file.size_human }} • {{ file.created_at !== file.updated_at ? `Updated ${file.updated_at_timeago}` : `Created ${file.created_at_timeago}` }}</span>
                    </div>
                </div>
            </td>
            <td v-if="currentType !== 'shared'" class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">
                {{ `${file.college?.code || 'N/A'} - ${file.program?.code || 'N/A'}` }}
            </td>
            <td v-else class="p-4 hidden md:table-cell">
                <div class="flex items-center gap-2">
                    <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small"
                        :style='{backgroundImage: `url(${file.uploaded_by?.google_info?.avatar || "https://lh3.googleusercontent.com/aida-public/AB6AXuAyd5GytrCn8kduh_Iuz0ySh5VVrmNP9pRGZMCPzCw5qgasNtIJeBvV38fJsICfT0uXATEWKrP1qSMUXTaiHEQ8QlR55UnM8zPob4lCVCQMVGRZVHaAITVT4hDYMsn2SBAQG1hJU1-yzIM_hWYfqnjVd9KLcTp60WDFeiZjIEai35-EjfEXHTVciP8uvi348D8T_7Q-o3H1SQbjAtaRU8emjmcB_i11XzlzHfEy61ZQtfoVyE55JOhPta5juvgvhscAr4N_QxvipB-R"})`}'>
                    </div>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ file.uploaded_by?.name || 'Unknown' }}</span>
                </div>
            </td>

            <td v-if="currentType !== 'shared'" class="p-4 hidden lg:table-cell">
                <div class="flex items-center gap-2">
                    <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small"
                        :style='{backgroundImage: `url(${file.uploaded_by?.google_info?.avatar || "https://lh3.googleusercontent.com/aida-public/AB6AXuAyd5GytrCn8kduh_Iuz0ySh5VVrmNP9pRGZMCPzCw5qgasNtIJeBvV38fJsICfT0uXATEWKrP1qSMUXTaiHEQ8QlR55UnM8zPob4lCVCQMVGRZVHaAITVT4hDYMsn2SBAQG1hJU1-yzIM_hWYfqnjVd9KLcTp60WDFeiZjIEai35-EjfEXHTVciP8uvi348D8T_7Q-o3H1SQbjAtaRU8emjmcB_i11XzlzHfEy61ZQtfoVyE55JOhPta5juvgvhscAr4N_QxvipB-R"})`}'>
                    </div>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ file.uploaded_by?.name || 'Unknown' }}</span>
                </div>
            </td>
            <td v-else class="p-4 hidden lg:table-cell text-sm font-medium text-slate-700 dark:text-slate-300">
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-slate-400">person</span> Shared direct</span>
            </td>

            <td v-if="currentType !== 'shared'" class="p-4 text-sm text-slate-600 dark:text-slate-400">Dec 31, 2024</td>
            <td v-else class="p-4 text-sm text-slate-600 dark:text-slate-400">
                {{ new Date(file.pivot?.created_at || file.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
            </td>

            <td v-if="currentType !== 'shared'" class="p-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            </td>
            <td class="p-4 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button v-if="currentType !== 'shared'" @click.stop="openShareModal(file)" title="Share to virtual drive"
                        class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">share_windows</span>
                    </button>
                    <button v-if="currentType === 'shared'" class="text-slate-400 hover:text-primary p-1.5 rounded transition hover:bg-slate-100 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                    </button>
                    <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                    </button>
                </div>
            </td>
        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (Only show if not in event view as event view uses full list) -->
            <div v-if="currentType !== 'event' && files.links" class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark">
                <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span>Showing {{ files.from }} to {{ files.to }} of {{ files.total }} results</span>
                </div>
                <div class="flex items-center gap-2">
                    <template v-for="link in files.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            :class="[
                                'px-3 py-1 rounded text-sm transition-colors',
                                link.active ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                            ]">
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else class="px-3 py-1 rounded text-sm text-slate-400 cursor-not-allowed opacity-50">
                            <span v-html="link.label"></span>
                        </span>
                    </template>
                </div>
            </div>
        </div>
        <UploadModal 
            v-if="showUploadModal"
            :colleges="colleges"
            :programs="programs"
            :areas="areas"
            :isGeneral="currentType !== 'personal'"
            @close="closeUploadModal"
        />
        <FileViewerModal v-if="showFileViewerModal"
            :file="selectedFile"
            @close="closeFileViewerModal"
        />
        <ShareFileModal 
            v-if="showShareModal"
            :file="selectedFileToShare"
            :activeEvents="activeEvents"
            :areas="areas"
            @close="showShareModal = false"
        />
    </main>
</template>
