<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { MessageSquare, Send, X, Reply, Trash2, User as UserIcon, Clock } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    event: Object,
    area: Object,
    isOpen: Boolean
});

const emit = defineEmits(['close']);

const comments = ref([]);
const loading = ref(true);
const page = usePage();
const authUser = computed(() => page.props.auth.user);

const fetchComments = async () => {
    if (!props.event || !props.area) return;
    loading.value = true;
    try {
        const response = await axios.get(route('comments.index', { event: props.event.id, area: props.area.id }));
        comments.value = response.data;
    } catch (error) {
        console.error("Failed to fetch comments", error);
    } finally {
        loading.value = false;
    }
};

const form = useForm({
    content: '',
    parent_id: null
});

const replyingTo = ref(null);

const submitComment = () => {
    if (!form.content.trim()) return;

    form.post(route('comments.store', { event: props.event.id, area: props.area.id }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            replyingTo.value = null;
            fetchComments();
        }
    });
};

const deleteComment = async (id) => {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    try {
        await axios.delete(route('comments.destroy', { comment: id }));
        fetchComments();
    } catch (error) {
        alert('Failed to delete comment.');
    }
};

const setReply = (comment) => {
    replyingTo.value = comment;
    form.parent_id = comment.id;
    // Focus input? 
};

onMounted(() => {
    if (props.isOpen) fetchComments();
});

const isEventExpired = computed(() => {
    return props.event?.expires_at && new Date(props.event.expires_at) < new Date();
});
</script>

<template>
    <div 
        v-if="isOpen"
        class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl z-[100] border-l border-slate-200 dark:border-slate-800 flex flex-col animate-in slide-in-from-right duration-300"
    >
        <!-- Header -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <MessageSquare class="size-5 text-primary" />
                <h2 class="font-bold text-slate-900 dark:text-white">Area Comments</h2>
            </div>
            <button @click="$emit('close')" class="p-2 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <X class="size-5" />
            </button>
        </div>

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <div v-if="loading" class="flex flex-col items-center justify-center py-12 space-y-4">
                <span class="material-symbols-outlined animate-spin text-primary text-3xl">progress_activity</span>
                <p class="text-sm text-slate-500">Loading discussion...</p>
            </div>

            <div v-else-if="!comments.length" class="flex flex-col items-center justify-center py-20 text-center space-y-4">
                <div class="size-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                    <MessageSquare class="size-8" />
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-white">No comments yet</p>
                    <p class="text-sm text-slate-500">Be the first to start the discussion for this area.</p>
                </div>
            </div>

            <div v-else class="space-y-6">
                <div v-for="comment in comments" :key="comment.id" class="space-y-4">
                    <!-- Main Comment -->
                    <div class="group relative bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                        <div class="flex items-start gap-3">
                            <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <UserIcon class="size-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                        {{ comment.commentable?.name }}
                                        <span v-if="comment.commentable_type.includes('Accreditor')" class="ml-1 text-[10px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-md font-black uppercase">Accreditor</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                        <Clock class="size-3" />
                                        {{ new Date(comment.created_at).toLocaleDateString() }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ comment.content }}
                                </p>
                                
                                <div class="mt-3 flex items-center gap-4">
                                    <button 
                                        v-if="!isEventExpired"
                                        @click="setReply(comment)"
                                        class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1"
                                    >
                                        <Reply class="size-3" />
                                        Reply
                                    </button>
                                    <button 
                                        v-if="(comment.commentable_id === authUser.id) || $page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))"
                                        @click="deleteComment(comment.id)"
                                        class="text-[11px] font-bold text-red-500 hover:underline flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                    >
                                        <Trash2 class="size-3" />
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Replies -->
                    <div v-if="comment.replies && comment.replies.length" class="ml-8 space-y-4 border-l-2 border-slate-100 dark:border-slate-800 pl-4">
                        <div v-for="reply in comment.replies" :key="reply.id" class="group relative bg-[#f8fafc] dark:bg-slate-800/30 p-3 rounded-xl">
                            <div class="flex items-start gap-2">
                                <div class="size-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                    <UserIcon class="size-3" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-0.5">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                            {{ reply.commentable?.name }}
                                        </span>
                                        <span class="text-[9px] text-slate-400">
                                            {{ new Date(reply.created_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                        {{ reply.content }}
                                    </p>
                                    <button 
                                        v-if="(reply.commentable_id === authUser.id) || $page.props.auth.roles.some(r => ['admin', 'ido_staff'].includes(r))"
                                        @click="deleteComment(reply.id)"
                                        class="mt-2 text-[10px] font-bold text-red-500 hover:underline opacity-0 group-hover:opacity-100 transition-opacity"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Input -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
            <div v-if="isEventExpired" class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-start gap-3 mb-2">
                <Clock class="size-5 text-amber-600 shrink-0" />
                <p class="text-xs text-amber-700 dark:text-amber-400 leading-tight">
                    This event is archived or expired. New comments and replies are disabled.
                </p>
            </div>
            
            <div v-else class="space-y-3">
                <div v-if="replyingTo" class="flex items-center justify-between bg-primary/5 p-2 px-3 rounded-lg border border-primary/20">
                    <span class="text-[11px] text-primary font-medium">Replying to {{ replyingTo.commentable?.name }}</span>
                    <button @click="replyingTo = null; form.parent_id = null" class="text-slate-400 hover:text-red-500">
                        <X class="size-3" />
                    </button>
                </div>
                
                <div class="relative group">
                    <textarea 
                        v-model="form.content"
                        placeholder="Type your comment here..."
                        rows="3"
                        class="w-full p-4 pr-12 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none dark:text-white"
                    ></textarea>
                    <button 
                        @click="submitComment"
                        :disabled="form.processing || !form.content.trim()"
                        class="absolute bottom-3 right-3 p-2 bg-primary text-white rounded-xl shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:grayscale"
                    >
                        <Send class="size-5" />
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 px-1">Press Shift+Enter for new line</p>
            </div>
        </div>
    </div>
    
    <!-- Backdrop for mobile -->
    <div 
        v-if="isOpen" 
        @click="$emit('close')"
        class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[90] lg:hidden"
    ></div>
</template>
