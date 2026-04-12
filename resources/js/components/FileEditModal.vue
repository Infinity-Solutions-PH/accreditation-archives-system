<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    isOpen: Boolean,
    file: Object
});

const emit = defineEmits(['close', 'updated']);

const form = useForm({
    title: props.file?.title || '',
    description: props.file?.description || '',
    tmp_id: props.file?.tmp_id || ''
});

watch(() => props.file, (newFile) => {
    if (newFile) {
        form.title = newFile.title;
        form.description = newFile.description;
        form.tmp_id = newFile.tmp_id;
    }
}, { immediate: true });

const isReplacing = ref(false);
const replacementFile = ref(null);
const uploadProgress = ref(0);
const isUploading = ref(false);

const handleFileSelect = (e) => {
    replacementFile.value = e.target.files[0];
};

const startReplacement = async () => {
    if (!replacementFile.value) return;
    
    isUploading.value = true;
    try {
        // 1. Create Temp Metadata
        const tempRes = await axios.post(route('files.temp'), {
            filename: replacementFile.value.name,
            metadata: { title: form.title, description: form.description }
        });
        const tmp_id = tempRes.data.tmp_id;

        // 2. Upload (Simplified for demo, usually handle chunks here)
        const formData = new FormData();
        formData.append('tmp_id', tmp_id);
        formData.append('index', 0);
        formData.append('chunk', replacementFile.value);
        
        await axios.post(route('files.upload-chunk'), formData);
        
        // 3. Complete
        await axios.post(route('files.complete'), { tmp_id });
        
        form.tmp_id = tmp_id;
        isReplacing.value = false;
        alert('File uploaded successfully. Save changes to finalize.');
    } catch (e) {
        alert('File replacement failed.');
    } finally {
        isUploading.value = false;
    }
};

const submit = () => {
    axios.post(route('files.update', props.file.id), {
        tmp_id: form.tmp_id,
        metadata: {
            title: form.title,
            description: form.description
        }
    }).then(res => {
        emit('updated', res.data.file);
        emit('close');
    });
};

</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @click.self="emit('close')">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Edit File Details</h2>
                        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">File Title</label>
                            <input 
                                v-model="form.title"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Description</label>
                            <textarea 
                                v-model="form.description"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition-all h-24"
                            ></textarea>
                        </div>

                        <!-- Replace File Section -->
                        <div class="p-4 bg-primary/5 rounded-xl border border-primary/20">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                                    File Replacement
                                </h3>
                                <button 
                                    v-if="!isReplacing"
                                    type="button" 
                                    @click="isReplacing = true"
                                    class="text-xs font-bold text-primary hover:underline"
                                >
                                    Replace current file?
                                </button>
                            </div>
                            
                            <div v-if="isReplacing" class="space-y-4 animate-in fade-in slide-in-from-top-2">
                                <input type="file" @change="handleFileSelect" class="text-sm text-slate-500 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                                <div class="flex gap-2">
                                    <button 
                                        type="button" 
                                        @click="startReplacement" 
                                        :disabled="isUploading || !replacementFile"
                                        class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg disabled:opacity-50"
                                    >
                                        {{ isUploading ? 'Uploading...' : 'Start Upload' }}
                                    </button>
                                    <button @click="isReplacing = false" type="button" class="text-xs text-slate-500 border border-slate-300 rounded-lg px-4 py-2">Cancel</button>
                                </div>
                            </div>
                            <div v-else class="text-xs text-slate-500">
                                Current file: <strong>{{ file.original_filename }}</strong>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <button type="button" @click="emit('close')" class="px-6 py-2 rounded-xl text-slate-600 dark:text-slate-400 font-semibold">Cancel</button>
                            <button 
                                type="submit" 
                                class="px-6 py-2 bg-primary text-white rounded-xl font-bold shadow-lg hover:shadow-primary/30 transition-shadow disabled:opacity-50"
                                :disabled="isUploading"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
