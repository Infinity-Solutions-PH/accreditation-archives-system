<script setup>
import { useToast } from '@/Composables/useToast';

const { toasts, removeToast } = useToast();

const getIcon = (type) => {
    switch (type) {
        case 'success': return 'check_circle';
        case 'error': return 'error';
        case 'warning': return 'warning';
        case 'info': return 'info';
        default: return 'notifications';
    }
};

const getTypeClass = (type) => {
    switch (type) {
        case 'success': return 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/10 dark:text-emerald-400 dark:border-emerald-900/30';
        case 'error': return 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/10 dark:text-red-400 dark:border-red-900/30';
        default: return 'bg-white text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
    }
};
</script>

<template>
    <div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm w-full">
        <TransitionGroup 
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-4 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform translate-y-4 opacity-0 scale-95"
        >
            <div 
                v-for="toast in toasts" 
                :key="toast.id"
                class="flex items-center gap-3 p-4 rounded-2xl shadow-2xl border animate-in slide-in-from-right-4"
                :class="getTypeClass(toast.type)"
            >
                <span class="material-symbols-outlined shrink-0">{{ getIcon(toast.type) }}</span>
                <p class="text-sm font-semibold flex-1">{{ toast.message }}</p>
                <button @click="removeToast(toast.id)" class="shrink-0 opacity-50 hover:opacity-100">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
