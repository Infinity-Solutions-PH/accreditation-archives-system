<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import AppLayout from '@shared/Layouts/App.vue';
    import FileViewerModal from '@/components/FileViewerModal.vue';

    const props = defineProps({
        files: Object,
        colleges: Object,
        programs: Object,
        areas: Object,
        activeEvents: Array
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
                    <Link class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium" :href="route('dashboard')">Home</Link>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">Shared Drive</span>
                </li>
            </ol>
        </nav>
        
        <!-- Page Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2 max-w-2xl">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Shared with Me</h1>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    Documents securely shared directly with you by other peers or administrators.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <Link :href="route('file-archives', { type: 'general' })"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition-colors border border-slate-200 dark:border-slate-700 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">folder_open</span>
                    Go to General Repository
                </Link>
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
                    <input class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white" placeholder="Search shared files..." type="text"/>
                </div>
            </div>
        </div>

        <!-- Standard Table -->
        <div class="overflow-hidden bg-white dark:bg-surface-dark rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none relative">
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
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">attachment</span>
                            </th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">File Name</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell" scope="col">Original Uploader</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell" scope="col">Shared By</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Date Shared</th>
                            <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <!-- Empty State -->
                        <tr v-if="files.data.length === 0">
                            <td colspan="6" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                    <span class="material-symbols-outlined text-[48px] mb-3 opacity-50">folder_shared</span>
                                    <p class="text-sm font-medium">No files have been shared with you yet.</p>
                                </div>
                            </td>
                        </tr>

                        <!-- Rows -->
                        <tr v-for="file in files.data" :key="file.id" 
                            class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" 
                            @click="openFileViewerModal(file)">
                            
                            <td class="p-4 text-center">
                                <div class="size-2 rounded-full mx-auto" :class="'bg-emerald-500'"></div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 shrink-0 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <span class="material-symbols-outlined">{{ getFileIcon(file.extension) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">{{ file.title }}</span>
                                        <span class="text-xs text-slate-500">{{ file.size_human }} • {{ file.extension?.toUpperCase() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 hidden md:table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="size-6 rounded-full bg-cover bg-center bg-gray-200"
                                        :style='{backgroundImage: `url(${file.uploaded_by?.google_info?.avatar || "https://lh3.googleusercontent.com/aida-public/AB6AXuAyd5GytrCn8kduh_Iuz0ySh5VVrmNP9pRGZMCPzCw5qgasNtIJeBvV38fJsICfT0uXATEWKrP1qSMUXTaiHEQ8QlR55UnM8zPob4lCVCQMVGRZVHaAITVT4hDYMsn2SBAQG1hJU1-yzIM_hWYfqnjVd9KLcTp60WDFeiZjIEai35-EjfEXHTVciP8uvi348D8T_7Q-o3H1SQbjAtaRU8emjmcB_i11XzlzHfEy61ZQtfoVyE55JOhPta5juvgvhscAr4N_QxvipB-R"})`}'>
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ file.uploaded_by?.name || 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="p-4 hidden lg:table-cell text-sm font-medium text-slate-700 dark:text-slate-300">
                                <!-- Pivot properties access via file.pivot -->
                                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-slate-400">person</span> Shared direct</span>
                            </td>
                            <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ new Date(file.pivot?.created_at || file.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="text-slate-400 hover:text-primary p-1.5 rounded transition hover:bg-slate-100 dark:hover:bg-slate-700">
                                        <span class="material-symbols-outlined text-[20px]">download</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div v-if="files.links && files.data.length > 0" class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark">
                <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span>Showing {{ files.from }} to {{ files.to }} of {{ files.total }} results</span>
                </div>
                <div class="flex items-center gap-2">
                    <template v-for="link in files.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" v-html="link.label"
                            :class="[
                                'px-3 py-1 rounded text-sm transition-colors',
                                link.active ? 'bg-primary text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'
                            ]"
                        />
                        <span v-else v-html="link.label" class="px-3 py-1 rounded text-sm text-slate-400 cursor-not-allowed opacity-50"></span>
                    </template>
                </div>
            </div>
        </div>

        <FileViewerModal 
            v-if="showFileViewerModal"
            :file="selectedFile"
            @close="closeFileViewerModal"
        />
    </main>
</template>