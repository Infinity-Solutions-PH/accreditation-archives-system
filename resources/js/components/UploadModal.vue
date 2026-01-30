<script setup>
    import { ref, reactive } from 'vue';
    import api from '@/axios.js';

    const emit = defineEmits(['close']);

    const file = ref(null);
    const fileError = ref('');
    const uploading = ref(false);
    const progress = ref(0);
    const savingMetadata = ref(false);

    const metadata = reactive({
        title: `Untitled-Document-${new Date().toISOString().split('T')[0]}`,
        description: '',
        tmp_id: null
    })

    const MAX_SIZE = 2 * 1024 * 1024
    const ALLOWED_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/png',
        'image/jpeg',
        'image/jpg'
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
            '/api/documents/temp',
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

            await api.post('/api/documents/upload-chunk', formData, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'multipart/form-data' }
            });

            progress.value = Math.round(((i + 1) / totalChunks) * 100);
        }

        // Complete
        await api.post('/api/documents/complete', { tmp_id: metadata.tmp_id }, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // uploading.value = false
        // emit('close')
    }

    // Save metadata while uploading
    const saveMetadata = async () => {
        if (!metadata.tmp_id) return;
        savingMetadata.value = true;

        await api.post('/api/documents/update-metadata', {
            tmp_id: metadata.tmp_id,
            metadata: { title: metadata.title, description: metadata.description }
        }, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        savingMetadata.value = false;
    }

    // Close / abort
    const onCloseAttempt = async () => {
        if (uploading.value && metadata.tmp_id) {
            const confirmAbort = confirm('File is uploading. Do you want to abort?');
            if (!confirmAbort) return;

            await api.post('/api/documents/abort', { tmp_id: metadata.tmp_id }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
        }

        emit('close');
    }
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
                    <p class="text-xs text-gray-500">Documents or images (max 2MB per file)</p>
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

                    <!-- Editable metadata -->
                    <div class="mt-2">
                        <input
                            type="text"
                            v-model="metadata.title"
                            class="w-full border rounded px-2 py-1 mb-1"
                            placeholder="Untitled Document"
                        />
                        <input
                            type="text"
                            v-model="metadata.description"
                            class="w-full border rounded px-2 py-1"
                            placeholder="Description (optional)"
                        />
                    </div>
                </div>

                <!-- Uploading metadata save -->
                <div v-if="uploading" class="mt-2 flex justify-end gap-2">
                    <button class="px-4 py-2 rounded bg-green-600 text-white"
                        @click="saveMetadata"
                        :disabled="savingMetadata">
                        {{ savingMetadata ? 'Saving...' : 'Save Metadata' }}
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button @click="onCloseAttempt" class="px-5 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>