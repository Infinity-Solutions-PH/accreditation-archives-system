<script setup>
    import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
    import { router, Link, usePage } from '@inertiajs/vue3';
    import axios from 'axios';

    import AppLayout from '@shared/Layouts/App.vue';
    import FileShareModal from '@/Components/FileShareModal.vue';
    import FileEditModal from '@/Components/FileEditModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';

    import UploadModal from '@/components/UploadModal.vue';
    import FileViewerModal from '@/components/FileViewerModal.vue';
    import CommentPanel from '@/components/CommentPanel.vue';
    import { SUBFOLDERS, AREA_PARAMETERS, PARAMETER_FOLDERS } from '@/constants/foldering.js';

    const props = defineProps({
        files: Object,
        area: Object,
        event: Object,
        colleges: Object,
        programs: Object,
        areas: Object,
        filters: Object,
        fileCounts: Array,
        isAvpHidden: Boolean,
    });

    const page = usePage();
    const auth = page.props.auth;
    const search = ref(props.filters.search || '');
    const sort_field = ref(props.filters.sort_field || 'created_at');
    const sort_order = ref(props.filters.sort_order || 'desc');
    const currentSubfolder = ref(props.filters.subfolder || '');
    const currentParameter = ref(props.filters.parameter || '');
    const currentParameterFolder = ref(props.filters.parameter_folder || '');

    const applyFilters = () => {
        router.get(route('areas.slug', { event: props.event.slug, area: props.area.slug }), {
            search: search.value,
            sort_field: sort_field.value,
            sort_order: sort_order.value,
            subfolder: currentSubfolder.value || undefined,
            parameter: currentParameter.value || undefined,
            parameter_folder: currentParameterFolder.value || undefined
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    };

    let searchTimeout = null;
    watch(search, () => {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });

    const toggleSort = (field) => {
        if (sort_field.value === field) {
            sort_order.value = sort_order.value === 'asc' ? 'desc' : 'asc';
        } else {
            sort_field.value = field;
            sort_order.value = field === 'created_at' ? 'desc' : 'asc';
        }
        applyFilters();
    };

    const getSortIcon = (field) => {
        if (sort_field.value !== field) {
            return 'swap_vert';
        }
        return sort_order.value === 'asc' ? 'arrow_upward' : 'arrow_downward';
    };

    const canRemove = (file) => {
        if (auth.is_accreditor) return false;
        
        // Admin, Staff, Officer can remove anything
        const hasHigherRole = auth.roles.some(r => ['admin', 'ido_staff', 'college_officer', 'coordinator'].includes(r));
        if (hasHigherRole) return true;

        // Uploader can remove their own file
        return file.uploaded_by_id === auth.user.id;
    };

    const isUnshareModalOpen = ref(false);
    const fileToUnshare = ref(null);
    const isUnsharing = ref(false);

    const handleUnshare = (file) => {
        fileToUnshare.value = file;
        isUnshareModalOpen.value = true;
    };

    const confirmUnshare = async () => {
        if (!fileToUnshare.value) return;

        isUnsharing.value = true;
        try {
            await axios.post(route('events.unshare', { event: props.event.slug, area: props.area.slug }), {
                file_id: fileToUnshare.value.id,
                subfolder: currentSubfolder.value || null,
                parameter: currentParameter.value || null,
                parameter_folder: currentParameterFolder.value || null
            });
            
            if (window.toast) {
                window.toast('File removed from folder successfully!', 'success');
            }
            
            isUnshareModalOpen.value = false;
            fileToUnshare.value = null;
            
            router.reload({ preserveScroll: true });
        } catch (e) {
            console.error(e);
            if (window.toast) {
                window.toast(e.response?.data?.message || 'Failed to remove file from folder.', 'error');
            }
        } finally {
            isUnsharing.value = false;
        }
    };

    const isDeleteModalOpen = ref(false);
    const fileToDelete = ref(null);
    const isDeleting = ref(false);

    const handleDeleteFile = (file) => {
        fileToDelete.value = file;
        isDeleteModalOpen.value = true;
    };

    const confirmDelete = () => {
        if (!fileToDelete.value) return;

        isDeleting.value = true;
        router.delete(route('files.destroy', fileToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                fileToDelete.value = null;
                if (window.toast) window.toast('File deleted successfully.', 'success');
            },
            onFinish: () => {
                isDeleting.value = false;
            }
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

    const selectSubfolder = (sub) => {
        currentSubfolder.value = sub;
        currentParameter.value = '';
        currentParameterFolder.value = '';
        applyFilters();
    };

    const selectParameter = (param) => {
        currentParameter.value = param;
        currentParameterFolder.value = '';
        applyFilters();
    };

    const selectParameterFolder = (folder) => {
        currentParameterFolder.value = folder;
        applyFilters();
    };

    const navigateToRoot = () => {
        currentSubfolder.value = '';
        currentParameter.value = '';
        currentParameterFolder.value = '';
        applyFilters();
    };

    const navigateToSubfolder = () => {
        currentParameter.value = '';
        currentParameterFolder.value = '';
        applyFilters();
    };

    const navigateToParameter = () => {
        currentParameterFolder.value = '';
        applyFilters();
    };

    const areaParameters = computed(() => {
        return AREA_PARAMETERS[props.area.order_no] || [];
    });

    const canUploadInCurrentFolder = computed(() => {
        if (!currentSubfolder.value) return false;
        if (currentSubfolder.value === 'PARAMETER' && !currentParameter.value) return false;
        if (currentParameter.value && (currentParameter.value.startsWith('Parameter A') || currentParameter.value.startsWith('Parameter B')) && !currentParameterFolder.value) return false;
        return true;
    });

    const filteredSubfolders = computed(() => {
        return SUBFOLDERS.filter(sub => {
            if (sub === 'AVP - AUDIO-VISUAL PRESENTATION') {
                if (props.isAvpHidden && auth.roles?.includes('taskforce')) {
                    return false;
                }
            }
            return true;
        });
    });

    const canToggleAvp = computed(() => {
        return auth.roles?.some(r => ['admin', 'ido_staff', 'college_officer'].includes(r));
    });

    const isTogglingAvp = ref(false);

    const toggleAvpVisibility = async () => {
        isTogglingAvp.value = true;
        try {
            const response = await axios.post(route('events.areas.toggle-avp', { event: props.event.slug, area: props.area.slug }));
            if (window.toast) {
                window.toast(response.data.is_avp_hidden ? 'AVP folder is now hidden from task force' : 'AVP folder is now visible to task force', 'success');
            }
            router.reload({ preserveScroll: true });
        } catch (e) {
            console.error(e);
            if (window.toast) window.toast('Failed to toggle AVP folder visibility', 'error');
        } finally {
            isTogglingAvp.value = false;
        }
    };

    const getFolderCount = (sub, param = null, paramFolder = null) => {
        if (!props.fileCounts) return 0;
        return props.fileCounts.reduce((total, item) => {
            if (paramFolder) {
                if (item.subfolder === sub && item.parameter === param && item.parameter_folder === paramFolder) {
                    return total + item.count;
                }
            } else if (param) {
                if (item.subfolder === sub && item.parameter === param) {
                    return total + item.count;
                }
            } else {
                if (item.subfolder === sub) {
                    return total + item.count;
                }
            }
            return total;
        }, 0);
    };
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
                    <button @click="navigateToRoot" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium focus:outline-none">
                        {{ area.code }}
                    </button>
                </li>
                <template v-if="currentSubfolder">
                    <li>
                        <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                    </li>
                    <li v-if="currentParameter">
                        <button @click="navigateToSubfolder" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium focus:outline-none">
                            {{ currentSubfolder }}
                        </button>
                    </li>
                    <li v-else>
                        <span class="text-slate-900 dark:text-white text-sm font-semibold">{{ currentSubfolder }}</span>
                    </li>
                </template>
                <template v-if="currentParameter">
                    <li>
                        <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                    </li>
                    <li v-if="currentParameterFolder">
                        <button @click="navigateToParameter" class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium focus:outline-none truncate max-w-[200px] inline-block align-bottom" :title="currentParameter">
                            {{ currentParameter }}
                        </button>
                    </li>
                    <li v-else>
                        <span class="text-slate-900 dark:text-white text-sm font-semibold truncate max-w-[200px] inline-block align-bottom" :title="currentParameter">
                            {{ currentParameter }}
                        </span>
                    </li>
                </template>
                <template v-if="currentParameterFolder">
                    <li>
                        <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                    </li>
                    <li>
                        <span class="text-slate-900 dark:text-white text-sm font-semibold truncate max-w-[200px] inline-block align-bottom" :title="currentParameterFolder">
                            {{ currentParameterFolder }}
                        </span>
                    </li>
                </template>
            </ol>
        </nav>
        <!-- Page Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2 max-w-2xl">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ area.code }}
                    <span v-if="currentSubfolder" class="text-primary text-2xl font-bold block sm:inline sm:ml-2">
                        &rsaquo; {{ currentParameterFolder ? currentParameterFolder : (currentParameter ? currentParameter.split(' - ')[0] : currentSubfolder) }}
                    </span>
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    {{ currentParameterFolder ? `${currentParameter} - ${currentParameterFolder}` : (currentParameter ? currentParameter : (currentSubfolder ? `Files shared under the ${currentSubfolder} category of ${area.code}.` : area.description)) }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div v-if="currentSubfolder === 'AVP - AUDIO-VISUAL PRESENTATION' && canToggleAvp" class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-surface-dark border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Hidden from Task Force</span>
                    <button 
                        @click="toggleAvpVisibility"
                        :disabled="isTogglingAvp"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                        :class="isAvpHidden ? 'bg-primary' : 'bg-slate-200 dark:bg-slate-700'"
                    >
                        <span class="sr-only">Toggle AVP Visibility</span>
                        <span 
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="isAvpHidden ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>
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
                    v-if="canUploadInCurrentFolder"
                    @click="openUploadModal"
                    class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-md shadow-blue-500/20 animate-in fade-in zoom-in duration-200">
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
        <ConfirmationModal 
            :show="isDeleteModalOpen"
            title="Delete File"
            :message="`Are you sure you want to permanently delete '${fileToDelete?.title}'? This action cannot be undone.`"
            confirmText="Delete Permanently"
            confirmButtonClass="bg-red-600 hover:bg-red-700"
            :isProcessing="isDeleting"
            @close="isDeleteModalOpen = false"
            @confirm="confirmDelete"
        />

        <ConfirmationModal 
            :show="isUnshareModalOpen"
            title="Remove from Folder"
            :message="`Are you sure you want to remove '${fileToUnshare?.title}' from this folder? It will no longer be visible here but will remain in your files archive.`"
            confirmText="Remove from Folder"
            confirmButtonClass="bg-red-600 hover:bg-red-700"
            :isProcessing="isUnsharing"
            @close="isUnshareModalOpen = false"
            @confirm="confirmUnshare"
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
        <!-- Folders View Grid -->
        <div v-if="!search && (!currentSubfolder || (currentSubfolder === 'PARAMETER' && !currentParameter) || (currentParameter && (currentParameter.startsWith('Parameter A') || currentParameter.startsWith('Parameter B')) && !currentParameterFolder))" 
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 animate-in fade-in duration-200"
        >
            <!-- Case 1: Subfolders list at Area root -->
            <template v-if="!currentSubfolder">
                <div v-for="sub in filteredSubfolders" :key="sub" 
                    @click="selectSubfolder(sub)"
                    class="group cursor-pointer bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-5 hover:border-primary/50 hover:shadow-lg transition-all relative"
                >
                    <div v-if="sub === 'AVP - AUDIO-VISUAL PRESENTATION' && canToggleAvp && isAvpHidden" class="absolute top-3 right-3 flex items-center justify-center p-1 rounded-full bg-red-100 text-red-600" title="Hidden from Task Force">
                        <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                    </div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[32px] fill-[1]">folder</span>
                        </div>
                        <!-- <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                            {{ sub === 'PARAMETER' ? 'Parent Folder' : 'Deepest Folder' }}
                        </span> -->
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">{{ sub }}</h3>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">{{ getFolderCount(sub) }} Files</span>
                        <span class="material-symbols-outlined text-[18px] text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                </div>
            </template>

            <!-- Case 2: Parameters list inside PARAMETER folder -->
            <template v-else-if="currentSubfolder === 'PARAMETER' && !currentParameter">
                <div v-for="param in areaParameters" :key="param" 
                    @click="selectParameter(param)"
                    class="group cursor-pointer bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-5 hover:border-primary/50 hover:shadow-lg transition-all"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[32px] fill-[1]">folder</span>
                        </div>
                        <!-- <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                            {{ param.startsWith('Parameter A') || param.startsWith('Parameter B') ? 'Parent Folder' : 'Deepest Folder' }}
                        </span> -->
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white mb-1 leading-snug line-clamp-3 min-h-[3rem] group-hover:text-primary transition-colors" :title="param">
                        {{ param }}
                    </h3>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">{{ getFolderCount('PARAMETER', param) }} Files</span>
                        <span class="material-symbols-outlined text-[18px] text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                </div>
            </template>

            <!-- Case 3: Parameter Folders inside Parameter A or B -->
            <template v-else-if="currentParameter && (currentParameter.startsWith('Parameter A') || currentParameter.startsWith('Parameter B')) && !currentParameterFolder">
                <div v-for="folder in PARAMETER_FOLDERS" :key="folder" 
                    @click="selectParameterFolder(folder)"
                    class="group cursor-pointer bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-5 hover:border-primary/50 hover:shadow-lg transition-all"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[32px] fill-[1]">folder</span>
                        </div>
                        <!-- <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Deepest Folder</span> -->
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                        {{ folder }}
                    </h3>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">{{ getFolderCount('PARAMETER', currentParameter, folder) }} Files</span>
                        <span class="material-symbols-outlined text-[18px] text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Files Table View -->
        <div v-else class="space-y-6 animate-in fade-in duration-200">
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
        
        <!-- Name Column -->
        <th @click="toggleSort('title')" class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" scope="col">
            <div class="flex items-center gap-1 select-none">
                <span>File Name</span>
                <span class="material-symbols-outlined text-[16px] text-slate-400">
                    {{ getSortIcon('title') }}
                </span>
            </div>
        </th>

        <!-- Date Uploaded Column -->
        <th @click="toggleSort('created_at')" class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" scope="col">
            <div class="flex items-center gap-1 select-none">
                <span>Date Uploaded</span>
                <span class="material-symbols-outlined text-[16px] text-slate-400">
                    {{ getSortIcon('created_at') }}
                </span>
            </div>
        </th>

        <!-- College Column -->
        <th @click="toggleSort('college')" class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors hidden md:table-cell" scope="col">
            <div class="flex items-center gap-1 select-none">
                <span>College</span>
                <span class="material-symbols-outlined text-[16px] text-slate-400">
                    {{ getSortIcon('college') }}
                </span>
            </div>
        </th>

        <!-- Program Column -->
        <th @click="toggleSort('program')" class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors hidden md:table-cell" scope="col">
            <div class="flex items-center gap-1 select-none">
                <span>Program</span>
                <span class="material-symbols-outlined text-[16px] text-slate-400">
                    {{ getSortIcon('program') }}
                </span>
            </div>
        </th>

        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell" scope="col">Uploaded By</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right" scope="col">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <tr v-if="files.data.length === 0">
            <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400 italic">
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
                        <span class="text-xs text-slate-500">{{ file.size_human }} • {{ file.extension.toUpperCase() }}</span>
                    </div>
                </div>
            </td>
            <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                {{ file.created_at_clean }}
            </td>
            <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">
                {{ file.college?.code || 'N/A' }}
            </td>
            <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">
                {{ file.program?.code || 'N/A' }}
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

                    <!-- Delete Permanently -->
                    <button 
                        v-if="auth.roles.includes('admin') || auth.roles.includes('ido_staff') || auth.roles.includes('college_officer') || file.uploaded_by_id === auth.user.id"
                        @click.stop="handleDeleteFile(file)"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors tooltip"
                        title="Delete File Permanently"
                    >
                        <span class="material-symbols-outlined text-[20px]">delete_forever</span>
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
        </div>

        <UploadModal 
            v-if="showUploadModal"
            :colleges="colleges"
            :programs="programs"
            :areas="areas"
            :currentArea="area"
            :currentSubfolder="currentSubfolder"
            :currentParameter="currentParameter"
            :accreditationEventId="event.id"
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