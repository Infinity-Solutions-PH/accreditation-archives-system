<script setup>
    import { ref, computed, onMounted } from 'vue';
    import VideoPlayer from './VideoPlayer.vue';

    const props = defineProps({
        file: Object,
        currentArea: Object
    });

    const emit = defineEmits(['close']);

    const isVideo = computed(() => {
        return ['mp4', 'webm', 'ogg', 'mov'].includes(props.file?.extension?.toLowerCase());
    });

    const isImage = computed(() => {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(props.file?.extension?.toLowerCase());
    });

    const isPDF = computed(() => {
        return props.file?.extension?.toLowerCase() === 'pdf';
    });

    const isText = computed(() => {
        return ['txt', 'md', 'csv', 'log'].includes(props.file?.extension?.toLowerCase());
    });

    const textContent = ref('');
    const isLoadingText = ref(false);

    onMounted(async () => {
        if (isText.value) {
            isLoadingText.value = true;
            try {
                const response = await fetch(route('files.view', { file: props.file.id }));
                textContent.value = await response.text();
            } catch (e) {
                textContent.value = 'Failed to load text content.';
            } finally {
                isLoadingText.value = false;
            }
        }
    });

    const closeModal = () => {
        emit('close');
    };

    const getIcon = (ext) => {
        const e = ext?.toLowerCase();
        if (isImage.value) return 'image';
        if (isPDF.value) return 'picture_as_pdf';
        if (isVideo.value) return 'movie';
        if (isText.value) return 'description';
        return 'insert_drive_file';
    };
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm p-4 md:p-10" @click.self="closeModal">
        <div class="relative w-full max-w-6xl bg-[#111722] rounded-xl shadow-2xl border border-white/10 flex flex-col overflow-hidden max-h-full transition-all duration-300">
            
            <!-- Header -->
            <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#232f48] px-6 py-4 shrink-0">
                <div class="flex items-center gap-4 text-white overflow-hidden">
                    <div class="size-10 flex items-center justify-center bg-primary rounded-xl shrink-0">
                        <span class="material-symbols-outlined text-white text-2xl">{{ getIcon(file?.extension) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-white text-lg font-bold leading-tight tracking-[-0.015em] truncate">{{ file?.title }}</h2>
                        <p class="text-slate-400 text-xs font-normal">{{ file?.original_filename }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a :href="route('files.download', { file: file.id })" 
                       class="flex min-w-[120px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors hover:bg-primary/90">
                        <span class="material-symbols-outlined text-lg">download</span>
                        <span class="truncate">Download</span>
                    </a>
                    <button @click="closeModal" class="flex size-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#232f48] text-white hover:bg-[#2d3b5a] transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">
                
                <!-- Viewer Region -->
                <div class="flex-1 bg-black/40 flex flex-col justify-center items-center relative overflow-hidden">
                    
                    <!-- Video -->
                    <VideoPlayer v-if="isVideo" :id="file?.id" />

                    <!-- Image -->
                    <div v-else-if="isImage" class="w-full h-full flex items-center justify-center p-4">
                        <img :src="route('files.view', { file: file.id })" 
                             class="max-w-full max-h-full object-contain shadow-2xl rounded-lg" 
                             :alt="file.title" />
                    </div>

                    <!-- PDF -->
                    <div v-else-if="isPDF" class="w-full h-full bg-white transition-opacity duration-300">
                        <iframe :src="route('files.view', { file: file.id })" 
                                class="w-full h-full border-none"></iframe>
                    </div>

                    <!-- Text -->
                    <div v-else-if="isText" class="w-full h-full p-8 overflow-y-auto text-slate-300 font-mono text-sm leading-relaxed whitespace-pre-wrap bg-[#0c111b]">
                        <div v-if="isLoadingText" class="flex items-center justify-center h-full">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        </div>
                        <template v-else>
                            {{ textContent }}
                        </template>
                    </div>

                    <!-- Fallback / Unsupported -->
                    <div v-else class="flex flex-col items-center justify-center p-12 text-center">
                        <div class="size-24 bg-white/5 rounded-full flex items-center justify-center mb-6 border border-white/10">
                            <span class="material-symbols-outlined text-white/20 text-[64px]">{{ getIcon(file?.extension) }}</span>
                        </div>
                        <h3 class="text-white text-xl font-bold mb-2">Preview not available</h3>
                        <p class="text-slate-400 text-sm max-w-md mb-8">This file type ({{ file?.extension?.toUpperCase() }}) cannot be previewed in the browser. Please download it to view the content.</p>
                        <a :href="route('files.download', { file: file.id })" 
                           class="flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl font-bold transition-all border border-white/10">
                            <span class="material-symbols-outlined">download</span>
                            Download Extension: .{{ file?.extension }}
                        </a>
                    </div>

                </div>

                <!-- Sidebar Metadata -->
                <aside class="w-full lg:w-[360px] bg-[#111722] border-l border-[#232f48] flex flex-col overflow-y-auto shrink-0 transition-all duration-500">
                    <div class="p-6">
                        <h3 class="text-white text-lg font-bold mb-6">File Details</h3>
                        <div class="space-y-6">
                            <div class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Title</p>
                                <p class="text-white text-sm font-medium break-all">{{ file?.title }}</p>
                            </div>
                            <div class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Original Filename</p>
                                <p class="text-white text-sm font-medium break-all">{{ file?.original_filename }}</p>
                            </div>
                            <div class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Uploaded By</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="size-7 rounded-full bg-primary/20 flex items-center justify-center border border-primary/30">
                                        <span class="material-symbols-outlined text-primary text-[14px]">person</span>
                                    </div>
                                    <p class="text-white text-sm font-medium">{{ file?.uploaded_by?.name || 'Unknown' }}</p>
                                </div>
                            </div>
                            <div v-if="currentArea" class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">AACCUP Location</p>
                                <p class="text-white text-sm font-medium">{{ `${currentArea?.code}: ${currentArea?.name}` }}</p>
                            </div>
                            <div class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">Created At</p>
                                <p class="text-white text-sm font-medium">{{ file?.created_at_clean }}</p>
                            </div>
                            <div class="border-b border-[#232f48] pb-4">
                                <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-1">File Size</p>
                                <p class="text-white text-sm font-medium">{{ file?.size_human }}</p>
                            </div>
                        </div>

                        <!-- Remarks if any -->
                        <div v-if="file?.description" class="mt-8 p-4 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-[#92a4c9] text-xs font-semibold uppercase tracking-wider mb-2">Remarks</p>
                            <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ file?.description }}</p>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Footer -->
            <footer class="bg-[#0c111b] px-6 py-3 border-t border-[#232f48] flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <div class="size-4 text-primary">
                        <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.8261 30.5736C16.7203 29.8826 20.2244 29.4783 24 29.4783C27.7756 29.4783 31.2797 29.8826 34.1739 30.5736C36.9144 31.2278 39.9967 32.7669 41.3563 33.8352L24.8486 7.36089C24.4571 6.73303 23.5429 6.73303 23.1514 7.36089L6.64374 33.8352C8.00331 32.7669 11.0856 31.2278 13.8261 30.5736Z" fill="currentColor"></path>
                            <path clip-rule="evenodd" d="M39.998 35.764C39.9944 35.7463 39.9875 35.7155 39.9748 35.6706C39.9436 35.5601 39.8949 35.4259 39.8346 35.2825C39.8168 35.2403 39.7989 35.1993 39.7813 35.1602C38.5103 34.2887 35.9788 33.0607 33.7095 32.5189C30.9875 31.8691 27.6413 31.4783 24 31.4783C20.3587 31.4783 17.0125 31.8691 14.2905 32.5189C12.0012 33.0654 9.44505 34.3104 8.18538 35.1832C8.17384 35.2075 8.16216 35.233 8.15052 35.2592C8.09919 35.3751 8.05721 35.4886 8.02977 35.589C8.00356 35.6848 8.00039 35.7333 8.00004 35.7388C8.00004 35.739 8 35.7393 8.00004 35.7388C8.00004 35.7641 8.0104 36.0767 8.68485 36.6314C9.34546 37.1746 10.4222 37.7531 11.9291 38.2772C14.9242 39.319 19.1919 40 24 40C28.8081 40 33.0758 39.319 36.0709 38.2772C37.5778 37.7531 38.6545 37.1746 39.3151 36.6314C39.9006 36.1499 39.9857 35.8511 39.998 35.764ZM4.95178 32.7688L21.4543 6.30267C22.6288 4.4191 25.3712 4.41909 26.5457 6.30267L43.0534 32.777C43.0709 32.8052 43.0878 32.8338 43.104 32.8629L41.3563 33.8352C43.104 32.8629 43.1038 32.8626 43.104 32.8629L43.1051 32.865L43.1065 32.8675L43.1101 32.8739L43.1199 32.8918C43.1276 32.906 43.1377 32.9246 43.1497 32.9473C43.1738 32.9925 43.2062 33.0545 43.244 33.1299C43.319 33.2792 43.4196 33.489 43.5217 33.7317C43.6901 34.1321 44 34.9311 44 35.7391C44 37.4427 43.003 38.7775 41.8558 39.7209C40.6947 40.6757 39.1354 41.4464 37.385 42.0552C33.8654 43.2794 29.133 44 24 44C18.867 44 14.1346 43.2794 10.615 42.0552C8.86463 41.4464 7.30529 40.6757 6.14419 39.7209C4.99695 38.7775 3.99999 37.4427 3.99999 35.7391C3.99999 34.8725 4.29264 34.0922 4.49321 33.6393C4.60375 33.3898 4.71348 33.1804 4.79687 33.0311C4.83898 32.9556 4.87547 32.8935 4.9035 32.8471C4.91754 32.8238 4.92954 32.8043 4.93916 32.7889L4.94662 32.777L4.95178 32.7688ZM35.9868 29.004L24 9.77997L12.0131 29.004C12.4661 28.8609 12.9179 28.7342 13.3617 28.6282C16.4281 27.8961 20.0901 27.4783 24 27.4783C27.9099 27.4783 31.5719 27.8961 34.6383 28.6282C35.082 28.7342 35.5339 28.8609 35.9868 29.004Z" fill="currentColor" fill-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">CvSU Accreditation Archives</span>
                </div>
                <div class="text-xs text-slate-500">
                    System Version 2.4.0
                </div>
            </footer>
        </div>
    </div>
</template>
