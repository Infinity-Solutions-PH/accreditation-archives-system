<script setup>
    import { ref } from 'vue';

    const props = defineProps({
        show: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Confirm Action'
        },
        message: {
            type: String,
            default: 'Are you sure you want to proceed with this action?'
        },
        confirmText: {
            type: String,
            default: 'Confirm'
        },
        cancelText: {
            type: String,
            default: 'Cancel'
        },
        confirmButtonClass: {
            type: String,
            default: 'bg-primary hover:bg-blue-700'
        },
        isProcessing: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['close', 'confirm']);

    const closeHandler = () => {
        if (props.isProcessing) return;
        emit('close');
    };

    const confirmHandler = () => {
        emit('confirm');
    };
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-md rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-5 flex flex-col items-center text-center gap-4">
                <div :class="[
                    'size-14 rounded-full flex items-center justify-center',
                    confirmButtonClass.includes('red') ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'bg-blue-50 text-primary dark:bg-blue-900/20'
                ]">
                    <span class="material-symbols-outlined text-3xl">
                        {{ confirmButtonClass.includes('red') ? 'warning' : 'help' }}
                    </span>
                </div>
                
                <div class="space-y-1">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ message }}</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-end gap-3 mt-2">
                <button 
                    type="button" 
                    @click="closeHandler"
                    :disabled="isProcessing"
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors disabled:opacity-50"
                >
                    {{ cancelText }}
                </button>
                <button 
                    @click="confirmHandler" 
                    :disabled="isProcessing"
                    :class="[
                        'w-full sm:w-auto px-6 py-2.5 text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50',
                        confirmButtonClass
                    ]"
                >
                    <span v-if="isProcessing" class="size-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ isProcessing ? 'Processing...' : confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>
