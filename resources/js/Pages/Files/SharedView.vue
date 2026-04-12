<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    file: Object,
    isAccessible: Boolean,
    hasPendingRequest: Boolean,
    viewUrl: String
});

const form = useForm({
    reason: ''
});

const showRequestForm = ref(false);
const showDetails = ref(false);

const submitRequest = () => {
    form.post(route('files.request-access', props.file.id), {
        onSuccess: () => {
            showRequestForm.value = false;
        }
    });
};

const isVideo = computed(() => {
    return ['mp4', 'webm', 'ogg', 'mov'].includes(props.file?.extension?.toLowerCase());
});

const isImage = computed(() => {
    return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(props.file?.extension?.toLowerCase());
});

const isPDF = computed(() => {
    return props.file?.extension?.toLowerCase() === 'pdf';
});

const getIcon = (ext) => {
    const e = ext?.toLowerCase();
    if (isImage.value) return 'image';
    if (isPDF.value) return 'picture_as_pdf';
    if (isVideo.value) return 'movie';
    return 'insert_drive_file';
};

</script>

<template>
    <Head title="View Shared File" />
    <AppLayout>
        <div class="flex-1 flex flex-col h-full bg-black/95">
            <div class="flex-1 flex flex-col overflow-hidden m-4 md:m-8 bg-[#111722] rounded-2xl border border-white/10 shadow-2xl transition-all duration-300">
                
                <!-- Header (Modal Style) -->
                <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#232f48] px-6 py-4 shrink-0">
                    <div class="flex items-center gap-4 text-white overflow-hidden">
                        <div class="size-10 flex items-center justify-center bg-primary rounded-xl shrink-0">
                            <span class="material-symbols-outlined text-white text-2xl">{{ getIcon(file?.extension) }}</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h2 class="text-white text-lg font-bold leading-tight truncate">{{ file?.title }}</h2>
                            <p class="text-slate-400 text-xs font-normal truncate">{{ file?.original_filename }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button 
                            @click="showDetails = !showDetails"
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg h-10 px-4 transition-colors"
                            :class="showDetails ? 'bg-primary text-white' : 'bg-[#232f48] text-slate-300 hover:bg-[#2d3b5a]'"
                        >
                            <span class="material-symbols-outlined text-lg">{{ showDetails ? 'info_i' : 'info' }}</span>
                            <span class="hidden sm:inline text-sm font-bold">{{ showDetails ? 'Hide Details' : 'Show Details' }}</span>
                        </button>
                        <a v-if="isAccessible" :href="route('files.download', { file: file.id })" 
                           class="flex cursor-pointer items-center justify-center gap-2 rounded-lg h-10 px-4 bg-emerald-600 text-white text-sm font-bold transition-colors hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-lg">download</span>
                            <span class="hidden sm:inline">Download</span>
                        </a>
                    </div>
                </header>

                <!-- Content Area -->
                <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">
                    
                    <!-- Viewer Region -->
                    <div class="flex-1 bg-black/40 flex flex-col justify-center items-center relative overflow-hidden">
                        
                        <!-- Restricted State -->
                        <div v-if="!isAccessible" class="max-w-md w-full p-8 text-center animate-in zoom-in-95 duration-300">
                            <!-- Organization Restriction -->
                            <div v-if="props.isOrgRestricted" class="space-y-6">
                                <div class="size-20 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-amber-500/20">
                                    <span class="material-symbols-outlined text-amber-500 text-[40px]">domain_disabled</span>
                                </div>
                                <h3 class="text-white text-xl font-bold mb-2">Institutional Restriction</h3>
                                <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                                    This document is restricted to members of the <span class="text-primary font-bold">{{ file.college?.code || 'same college' }}</span> and the <span class="text-primary font-bold">{{ file.program?.code || 'specific program' }}</span>. 
                                    Internal policy prohibits access requests from outside your current organizational unit.
                                </p>
                                <div class="p-4 bg-slate-800/50 rounded-xl border border-white/5">
                                    <p class="text-xs text-slate-500 italic">Access request button is disabled for this file.</p>
                                </div>
                            </div>

                            <!-- Regular Permission Restriction -->
                            <div v-else>
                                <div class="size-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-red-500/20">
                                    <span class="material-symbols-outlined text-red-500 text-[40px]">lock_person</span>
                                </div>
                                <h3 class="text-white text-xl font-bold mb-2">Access Restricted</h3>
                                <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                                    You don't have permission to view this file. You must request access from the uploader or a college coordinator.
                                </p>

                                <div v-if="hasPendingRequest" class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center gap-3 text-amber-500 text-sm justify-center">
                                    <span class="material-symbols-outlined animate-pulse">history</span>
                                    <span>Your access request is currently pending approval.</span>
                                </div>

                                <div v-else-if="!showRequestForm">
                                    <button 
                                        @click="showRequestForm = true"
                                        class="w-full py-3 px-6 bg-primary text-white rounded-xl font-bold hover:bg-primary-hover shadow-xl shadow-primary/20 transition-all active:scale-95"
                                    >
                                        Request Permission
                                    </button>
                                </div>

                                <div v-else class="space-y-4 text-left animate-in slide-in-from-top-4">
                                    <textarea 
                                        v-model="form.reason"
                                        class="w-full bg-[#0c111b] border border-[#232f48] rounded-xl p-4 text-white text-sm focus:ring-2 focus:ring-primary outline-none transition-all h-28 placeholder:text-slate-600"
                                        placeholder="Explain why you need access to this file..."
                                    ></textarea>
                                    <div class="flex gap-3">
                                        <button @click="showRequestForm = false" class="flex-1 py-3 text-slate-400 font-bold hover:text-white transition-colors">Cancel</button>
                                        <button 
                                            @click="submitRequest" 
                                            :disabled="form.processing"
                                            class="flex-1 py-3 bg-primary text-white rounded-xl font-bold disabled:opacity-50"
                                        >
                                            {{ form.processing ? 'Sending...' : 'Submit Request' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Viewer State -->
                        <div v-else class="w-full h-full">
                            <iframe v-if="isPDF" :src="viewUrl" class="w-full h-full border-none"></iframe>
                            <div v-else-if="isImage" class="w-full h-full flex items-center justify-center p-8">
                                <img :src="viewUrl" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" />
                            </div>
                            <div v-else-if="isVideo" class="w-full h-full flex items-center justify-center">
                                <!-- Video Player Placeholder -->
                                <p class="text-white">Video Player goes here</p>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center text-center p-12">
                                <span class="material-symbols-outlined text-white/20 text-[64px] mb-4">draft</span>
                                <p class="text-slate-400 mb-6">Preview for this file type is better viewed via download.</p>
                                <a :href="route('files.download', file.id)" class="px-6 py-3 bg-white/10 text-white rounded-xl font-bold">Download File</a>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Metadata (Modal Style) -->
                    <aside 
                        class="bg-[#111722] border-l border-[#232f48] flex flex-col overflow-y-auto shrink-0 transition-all duration-500 ease-in-out"
                        :class="showDetails ? 'w-full lg:w-[360px] opacity-100' : 'w-0 opacity-0 border-none overflow-hidden'"
                    >
                        <div class="p-6">
                            <h3 class="text-white text-lg font-bold mb-6">File Details</h3>
                            <div class="space-y-6">
                                <div class="border-b border-[#232f48] pb-4">
                                    <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Uploaded By</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <img v-if="file.uploaded_by?.google_info?.avatar" :src="file.uploaded_by.google_info.avatar" class="size-8 rounded-full border border-primary/30" />
                                        <div v-else class="size-8 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-bold">U</div>
                                        <p class="text-white text-sm font-medium">{{ file?.uploaded_by?.name }}</p>
                                    </div>
                                </div>
                                <div class="border-b border-[#232f48] pb-4">
                                    <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Identity</p>
                                    <p class="text-white text-sm font-medium">{{ file.original_filename }}</p>
                                    <p class="text-slate-500 text-xs mt-1">UUID: {{ file.uuid }}</p>
                                </div>
                                <div v-if="file.college" class="border-b border-[#232f48] pb-4">
                                    <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">College/Program</p>
                                    <p class="text-white text-sm font-medium">{{ file.college.code }} - {{ file.program?.code || 'General' }}</p>
                                </div>
                                <div class="border-b border-[#232f48] pb-4">
                                    <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Metadata</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-slate-400 text-xs">Size</span>
                                        <span class="text-white text-sm">{{ file.size_human }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-slate-400 text-xs">Uploaded</span>
                                        <span class="text-white text-sm">{{ file.created_at_clean }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="file.description" class="mt-8 p-4 rounded-xl bg-white/5 border border-white/10">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-2">Remarks</p>
                                <p class="text-slate-300 text-sm italic">"{{ file.description }}"</p>
                            </div>
                        </div>
                    </aside>
                </div>

                <!-- Footer (Modal Style) -->
                <footer class="bg-[#0c111b] px-6 py-3 border-t border-[#232f48] flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="size-4 text-primary">
                            <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Secure Shared Access • CvSU Archives</span>
                    </div>
                    <div class="text-[10px] text-slate-600 font-mono">
                        HASH: {{ file.uuid.substring(0,8) }}...
                    </div>
                </footer>
            </div>
        </div>
    </AppLayout>
</template>
