<script setup>
    import { computed, ref, reactive, watch } from 'vue';
    import api from '@/axios.js';

    const { currentArea, colleges, programs, areas } =  defineProps({
        currentArea: Object,
        colleges: Object,
        programs: Object,
        areas: Object
    });

    const emit = defineEmits(['close']);

    const filteredPrograms = computed(() => {
        if (!metadata.college_id) return programs;
        return programs.filter(p => p.college_id === metadata.college_id);
    });

    const file = ref(null);
    const fileError = ref('');
    const uploading = ref(false);
    const progress = ref(0);
    const isUpdatingMetadata = ref(false);

    const metadata = reactive({
        title: `Untitled-Document-${new Date().toISOString().split('T')[0]}`,
        description: '',
        college_id: null,
        program_id: null,
        level: null,
        area_id: null,
        tmp_id: null
    })

    const MAX_SIZE = 2 * 1024 * 1024
    const ALLOWED_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

        'image/png',
        'image/jpeg',
        'image/jpg',

        'video/mp4'
    ];

    // Select file & validate
    const onFileSelect = async (e) => {
        fileError.value = '';
        const selected = e.target.files[0];
        if (!selected) return;

        if (!ALLOWED_TYPES.includes(selected.type)) {
            fileError.value = 'File type is invalid.';
            return;
        }

        // if (selected.size > MAX_SIZE) {
        //     fileError.value = 'File exceeds 2MB size limit.'
        //     return;
        // }

        file.value = selected;
        await startUpload();
    }

    // Create temporary metadata and start chunk upload
    const startUpload = async () => {
        if (!file.value) return;

        uploading.value = true;
        progress.value = 0;

        // 1️⃣ Create temp metadata
        const { data } = await api.post(
            '/api/files/temp',
            {
                metadata: { title: metadata.title, description: metadata.description },
                filename: file.value.name
            },
            { 
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                } 
            }
        );

        metadata.tmp_id = data.tmp_id;

        // 2️⃣ Start chunk upload
        await uploadChunks();
    }

    // Chunked upload
    const uploadChunks = async () => {
        const CHUNK_SIZE = 512 * 1024; // 512KB
        const totalChunks = Math.ceil(file.value.size / CHUNK_SIZE);

        for (let i = 0; i < totalChunks; i++) {
            const start = i * CHUNK_SIZE;
            const end = start + CHUNK_SIZE;
            const chunk = file.value.slice(start, end);

            const formData = new FormData();
            formData.append('tmp_id', metadata.tmp_id);
            formData.append('chunk', chunk);
            formData.append('index', i);
            formData.append('total', totalChunks);

            await api.post('/api/files/upload-chunk', formData, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'multipart/form-data' }
            });

            progress.value = Math.round(((i + 1) / totalChunks) * 100);
        }

        // Complete
        await api.post('/api/files/complete', { tmp_id: metadata.tmp_id }, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // uploading.value = false
        // emit('close')
    }

    // Save metadata while uploading
    const saveMetadata = async () => {
        if (!metadata.tmp_id) return;
        isUpdatingMetadata.value = true;

        await api.post('/api/files/update-metadata', {
            tmp_id: metadata.tmp_id,
            metadata: {
                title: metadata.title,
                description: metadata.description,
                college_id: metadata.college_id,
                program_id: metadata.program_id,
                level: metadata.level,
                area_id: metadata.area_id
            }
        }, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        isUpdatingMetadata.value = false;
    }

    // Close / abort
    const onCloseAttempt = async () => {
        if (uploading.value && metadata.tmp_id) {
            const confirmAbort = confirm('File is uploading. Do you want to abort?');
            if (!confirmAbort) return;

            await api.post('/api/files/abort', { tmp_id: metadata.tmp_id }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
        }

        emit('close');
    }

    watch(
        () => currentArea,
            (newArea) => {
            if (newArea !== null && newArea.id) {
                metadata.area_id = newArea.id;
            }
        },
        { immediate: true }
    );  
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="onCloseAttempt">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-xl shadow-2xl border overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 text-primary p-2 rounded-lg">
                        <span class="material-symbols-outlined text-[24px]">cloud_upload</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Upload Document</h3>
                        <p class="text-xs text-gray-500">Add new files to the archive</p>
                    </div>
                </div>

                <button @click="onCloseAttempt" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- File Input -->
            <div class="p-6">
                <label class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center cursor-pointer hover:border-primary" v-show="!uploading">
                    <span class="material-symbols-outlined text-[32px] text-primary mb-2">upload</span>
                    <p class="text-sm font-medium">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">Documents, images, videos</p>
                    <input type="file" class="hidden" @change="onFileSelect" />
                </label>

                <!-- Validation Error -->
                <p v-if="fileError" class="mt-2 text-sm text-red-600">{{ fileError }}</p>

                <!-- Upload Progress -->
                <div v-if="uploading" class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                        <div class="h-2 rounded-full bg-primary transition-all" :style="{ width: progress + '%' }"></div>
                    </div>
                    <p class="text-sm text-gray-700">{{ progress }}% uploaded</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Document Title</label>
                            <input
                                class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" 
                                v-model="metadata.title"
                                type="text"
                                value="Untitled-Document"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">College</label>
                            <div class="relative">
                                <select class="w-full pl-3.5 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white appearance-none" v-model="metadata.college_id">
                                    <option :value="null" selected disabled>Select College</option>
                                    <option v-for="college in colleges" :key="college.id" :value="college.id">
                                        {{ `${college.code} - ${college.name}` }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Academic Program</label>
                            <div class="relative">
                                <select class="w-full pl-3.5 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white appearance-none" v-model="metadata.program_id" :disabled="metadata.college_id === null">
                                    <option :value="null" selected disabled>Select Program</option>
                                    <option v-for="program in filteredPrograms" :key="program.id" :value="program.id">
                                        {{ `${program.code} - ${program.name}` }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Area</label>
                            <div class="relative">
                                <select class="w-full pl-3.5 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white appearance-none" v-model="metadata.area_id" :disabled="currentArea">
                                    <option :value="null" selected disabled>Select area</option>
                                    <option v-for="area in areas" :key="area.id" :value="area.id">
                                        {{ area.code }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Accreditation Level</label>
                            <div class="relative">
                                <select class="w-full pl-3.5 pr-10 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white appearance-none" v-model="metadata.level">
                                    <option :value="null" selected disabled>Select Level</option>
                                    <option value="1">Level 1 - Candidate Status</option>
                                    <option value="2">Level 2 - Accredited</option>
                                    <option value="3">Level 3 - Re-accredited</option>
                                    <option value="4">Level 4 - Institutional</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Expiration Date</label>
                            <div class="relative">
                            <input class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white" type="date" value="2027-01-27"/>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-text-main-light dark:text-white mb-1.5">Description</label>
                            <textarea
                                class="w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all dark:text-white"
                                type="text" rows="3"
                                v-model="metadata.description"
                                placeholder="Description (optional)"
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button class="px-5 py-2.5 text-sm font-semibold text-text-muted-light dark:text-text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors" type="button" @click="onCloseAttempt">
                    Cancel
                </button>
                <button v-if="uploading" class="px-7 py-2.5 text-sm font-bold text-white bg-primary hover:bg-blue-700 rounded-lg shadow-md shadow-blue-500/20 transition-all cursor-not-allowed flex items-center gap-2" type="button" :disabled="isUpdatingMetadata" @click="saveMetadata" :class="{ 'opacity-70': isUpdatingMetadata }">
                    <span class="material-symbols-outlined text-[18px] animate-spin" v-show="isUpdatingMetadata">refresh</span>
                    <span class="material-symbols-outlined text-[18px]" v-show="!isUpdatingMetadata">refresh</span>
                    Save
                </button>
            </div>
        </div>
    </div>
</template>