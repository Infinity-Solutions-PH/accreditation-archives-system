<script setup>
    import { ref, onMounted, onUnmounted, watch } from 'vue';
    import { router, Link, usePage } from '@inertiajs/vue3';

    import AppLayout from '@shared/Layouts/App.vue';
    import FileShareModal from '@/Components/FileShareModal.vue';
    import FileEditModal from '@/Components/FileEditModal.vue';

    import UploadModal from '@/components/UploadModal.vue';
    import FileViewerModal from '@/components/FileViewerModal.vue';
    import CommentPanel from '@/components/CommentPanel.vue';

    const props = defineProps({
        files: Object,
        area: Object,
        event: Object,
        colleges: Object,
        programs: Object,
        areas: Object,
        filters: Object,
    });

    const page = usePage();
    const auth = page.props.auth;
    const search = ref(props.filters.search || '');

    watch(search, (value) => {
        router.get(route('areas.slug', { event: props.event.slug, area: props.area.slug }), { search: value }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    });

    const canRemove = (file) => {
        if (auth.is_accreditor) return false;
        
        // Admin, Staff, Officer can remove anything
        const hasHigherRole = auth.roles.some(r => ['admin', 'ido_staff', 'college_officer', 'coordinator'].includes(r));
        if (hasHigherRole) return true;

        // Uploader can remove their own file
        return file.uploaded_by_id === auth.user.id;
    };

    const handleUnshare = (file) => {
        if (!confirm(`Are you sure you want to remove '${file.title}' from this area?`)) return;

        router.post(route('events.unshare', { event: props.event.slug, area: props.area.slug }), {
            file_id: file.id
        }, {
            preserveScroll: true
        });
    };

    const isShareModalOpen = ref(false);
    const isEditModalOpen = ref(false);
    const activeFile = ref(null);

    const openShareModal = (file) => {
        activeFile.value = file;
        isShareModalOpen.value = true;
    };

    const openEditModal = (file) => {
        activeFile.value = file;
        isEditModalOpen.value = true;
    };

    const copyPermanentLink = (file) => {
        const url = route('files.shared', file.uuid);
        navigator.clipboard.writeText(url);
        if (window.toast) {
            window.toast('Permanent link copied to clipboard!', 'success');
        }
    };

    const handleFileUpdated = (updatedFile) => {
        // Find and update in the local files data if necessary, 
        // though router.post usually handles the refresh.
        router.reload({ preserveScroll: true });
    };

    const showComments = ref(false);

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

    const isLoading = ref(false);

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
                    <Link :href="$page.props.auth.is_accreditor ? route('accreditor.dashboard') : route('events.index')" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium">Events</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <Link :href="route('areas', { event: event.slug })" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium">{{ event.title }}</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">{{ area.code }} Files</span>
                </li>
            </ol>
        </nav>
        <!-- Page Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2 max-w-2xl">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ area.code }}</h1>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    {{ area.description }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">table_view</span>
                    Export to Excel
                </button>
                <button 
                    v-if="event"
                    type="button"
                    @click="showComments = !showComments"
                    class="flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm"
                    :class="{'text-primary border-primary bg-primary/5': showComments}"
                >
                    <span class="material-symbols-outlined text-[20px]">forum</span>
                    Discussion
                </button>
                <button type="button"
                    @click="openUploadModal"
                    class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-md shadow-blue-500/20">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    Upload File
                </button>
            </div>
        </div>

        <!-- Comment Panel -->
        <CommentPanel 
            v-if="event"
            :event="event"
            :area="area"
            :is-open="showComments"
            @close="showComments = false"
        />
        <FileShareModal 
            v-if="activeFile"
            :is-open="isShareModalOpen"
            :file="activeFile"
            @close="isShareModalOpen = false"
        />

        <FileEditModal 
            v-if="activeFile"
            :is-open="isEditModalOpen"
            :file="activeFile"
            @close="isEditModalOpen = false"
            @updated="handleFileUpdated"
        />

        <!-- Text Search -->
        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6">
            <div class="flex items-center justify-between">
                <!-- Search -->
                <div class="w-full lg:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-400">search</span>
                    </div>
                    <input 
                        v-model="search"
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white" 
                        placeholder="Search file name or uploader..." 
                        type="text"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button @click="search = ''" v-if="search" class="text-sm text-slate-500 hover:text-primary">Clear Search</button>
                    <button @click="router.reload()" class="p-2 text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Files Table -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden relative">
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
            {{ (auth.roles.includes('taskforce') || auth.roles.includes('coordinator')) ? 'Program' : 'College / Program' }}
        </th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell" scope="col">Uploaded By</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right" scope="col">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <tr v-if="files.data.length === 0">
            <td colspan="6" class="p-12 text-center text-slate-500 dark:text-slate-400 italic">
                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-[48px] opacity-20">folder_off</span>
                    <span>No files available in this area for this event.</span>
                </div>
            </td>
        </tr>
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
            <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">
                {{ (auth.roles.includes('taskforce') || auth.roles.includes('coordinator')) ? (file.program?.code || 'N/A') : `${file.college?.code || 'Global'} - ${file.program?.code || 'General'}` }}
            </td>
            <td class="p-4 hidden lg:table-cell">
                <div class="flex items-center gap-2">
                    <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small"
                        :style='{backgroundImage: `url(${file.uploaded_by?.google_info?.avatar || "https://lh3.googleusercontent.com/aida-public/AB6AXuAyd5GytrCn8kduh_Iuz0ySh5VVrmNP9pRGZMCPzCw5qgasNtIJeBvV38fJsICfT0uXATEWKrP1qSMUXTaiHEQ8QlR55UnM8zPob4lCVCQMVGRZVHaAITVT4hDYMsn2SBAQG1hJU1-yzIM_hWYfqnjVd9KLcTp60WDFeiZjIEai35-EjfEXHTVciP8uvi348D8T_7Q-o3H1SQbjAtaRU8emjmcB_i11XzlzHfEy61ZQtfoVyE55JOhPta5juvgvhscAr4N_QxvipB-R"})`}'>
                    </div>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ file.uploaded_by?.name || 'Unknown' }}</span>
                </div>
            </td>
            <td class="p-4 text-right">
                <div class="flex items-center justify-end gap-1 transition-opacity">
                    <!-- Internal View -->
                    <button 
                        @click.stop="openFileViewerModal(file)"
                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors tooltip"
                        title="View Internally"
                    >
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>

                    <!-- Permanent Link -->
                    <button 
                        @click.stop="copyPermanentLink(file)"
                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors tooltip"
                        title="Copy Permanent Link"
                    >
                        <span class="material-symbols-outlined text-[20px]">link</span>
                    </button>

                    <!-- Download -->
                    <a 
                        :href="route('files.download', file.id)"
                        @click.stop
                        class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors tooltip"
                        title="Download"
                    >
                        <span class="material-symbols-outlined text-[20px]">download</span>
                    </a>

                    <!-- Share / Permissions -->
                    <button 
                        v-if="!auth.is_accreditor"
                        @click.stop="openShareModal(file)"
                        class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors tooltip"
                        title="Share & Permissions"
                    >
                        <span class="material-symbols-outlined text-[20px]">share</span>
                    </button>

                    <!-- Edit Meta/File -->
                    <button 
                        v-if="auth.roles.includes('admin') || file.uploaded_by_id === auth.user.id"
                        @click.stop="openEditModal(file)"
                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip"
                        title="Edit / Replace"
                    >
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </button>

                    <!-- Detach from Area (Legacy 'delete' icon for removal from event) -->
                    <button 
                        v-if="canRemove(file)"
                        @click.stop="handleUnshare(file)"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors tooltip"
                        title="Remove from Area"
                    >
                        <span class="material-symbols-outlined text-[20px]">folder_off</span>
                    </button>
                </div>
            </td>
        </tr>
        <!-- Row 2: Expired - VISUAL EMPHASIS -->
        <!-- <tr class="group bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-l-4 border-l-red-500">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
        <span class="material-symbols-outlined">description</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Faculty_Roster_2022.docx</span>
        <span class="text-xs text-slate-500">1.8 MB • Updated 1 year ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Human Resources</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                JD
                                            </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">John Doe</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-red-600 dark:text-red-400">Jan 15, 2023</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
        <span class="material-symbols-outlined text-[14px]">error</span>
                                            Expired
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400">
        <span class="material-symbols-outlined">table_chart</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Budget_Report_Q3.xlsx</span>
        <span class="text-xs text-slate-500">4.1 MB • Updated 5 hours ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Finance</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCCqPftJCGVtsv2aaqBryoqy7FYeoMpmXKtrAsqugr27LilYbkz4FgkilBkIa2qatEZDblkd8z3RMPkSAdFgLhf6HhHw0vQFYPo4ECYPgf7436FZkYJ5rI_IgHwdmVN-6izDRyrDjzeo_y8ctwDDTcyzphZRosqMJJd7Tk6zRM-1Ksu7AkKFTc0TtCYFAN7_fzRTI8SJk5NVtdDCRVYnhgjkrjk93UeIVy17lrNgmpGUMNdjB8qJwqWiGuCox42x3LbIh92Yaw35NF0");'>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Elena Cruz</span>
        </div>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">--</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
        <span class="material-symbols-outlined">picture_as_pdf</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Lab_Safety_Protocols.pdf</span>
        <span class="text-xs text-slate-500">1.2 MB • Updated 1 month ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Laboratory Services</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                RR
                                            </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Rene Reyes</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-amber-600 dark:text-amber-500">Tomorrow</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
        <span class="material-symbols-outlined text-[14px]">warning</span>
                                            Expiring Soon
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <tr class="group bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-l-4 border-l-red-500">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
        <span class="material-symbols-outlined">description</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Memo_Guidance_2020.docx</span>
        <span class="text-xs text-slate-500">850 KB • Updated 3 years ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Admin</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAKp0aCH5xDVWNGnFKBQKa6a_t-kMQuwdSxwiGAsWlyViE9A6eFrhxc2CvNSms75DjPRQAmXFLxWkFAIi6KkRmUCpGMtBrnedoo5ws5WryUKOq-T5VNUbQyjyhcjveWeu12TYIUGIJADpFkwIM8GtPsbZdRQmAn2NnA1jVRKDZM4X2DOYTF6QA6s4E4Vl6eD994okqvnX4CA3xteiCb5pvP_bLZlqbzPv7T6Yys5i9xQlGMbPn2WNAu1-yGqL91rLpP9jcS4gVYFahj");'>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Ana Lee</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-red-600 dark:text-red-400">Mar 10, 2021</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
        <span class="material-symbols-outlined text-[14px]">error</span>
                                            Expired
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr> -->
        </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark">
        <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <span>Rows per page:</span>
        <select class="form-select bg-slate-50 dark:bg-slate-800 border-none rounded text-sm py-1 pl-2 pr-6 focus:ring-1 focus:ring-primary">
        <option>10</option>
        <option>20</option>
        <option>50</option>
        </select>
        </div>
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
        <span>{{ files.total > 0 ? `${files.from}-${files.to} of ${files.total}` : '0 of 0' }}</span>
        <div class="flex items-center gap-1">
        <Link v-if="files.prev_page_url" :href="files.prev_page_url" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </Link>
        <button v-else class="p-1 rounded opacity-30 cursor-not-allowed" disabled>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>
        
        <Link v-if="files.next_page_url" :href="files.next_page_url" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </Link>
        <button v-else class="p-1 rounded opacity-30 cursor-not-allowed" disabled>
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>
        </div>
        </div>
        </div>
        </div>
        <UploadModal 
            v-if="showUploadModal"
            :colleges="colleges"
            :programs="programs"
            :areas="areas"
            :currentArea="area"
            @close="closeUploadModal"
        />
        <FileViewerModal 
            v-if="showFileViewerModal"
            :file="selectedFile"
            :currentArea="area"
            @close="closeFileViewerModal"
        />
    </main>
</template>