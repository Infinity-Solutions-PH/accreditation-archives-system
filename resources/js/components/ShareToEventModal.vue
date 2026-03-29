<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    file: Object,
    activeEvents: Array,
    areas: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    file_id: props.file?.id,
    accreditation_event_id: '',
    area_id: '',
});

watch(() => props.file, (newFile) => {
    if (newFile) {
        form.file_id = newFile.id;
    }
});

const submit = () => {
    form.post(route('events.share'), {
        onSuccess: () => {
            alert('File shared to virtual drive successfully!');
            emit('close');
        },
        onError: (errors) => {
            alert(errors.message || 'An error occurred while sharing the file.');
        }
    });
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">share_windows</span>
                    Share to Virtual Drive
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors mt-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="submit" class="p-6 space-y-5">
                <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                    <div class="size-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined">description</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-200 truncate">{{ file.title }}</span>
                        <span class="text-xs text-slate-500">{{ file.size_human }}</span>
                    </div>
                </div>

                <!-- Event Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Target Accreditation Event</label>
                    <select v-model="form.accreditation_event_id" required
                        class="w-full px-4 py-2.5 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                        <option value="" disabled>Select an active event</option>
                        <option v-for="event in activeEvents" :key="event.id" :value="event.id">
                            {{ event.title }} ({{ event.program.code }})
                        </option>
                    </select>
                </div>

                <!-- Area Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Target Area Folder</label>
                    <select v-model="form.area_id" required
                        class="w-full px-4 py-2.5 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                        <option value="" disabled>Select an area folder</option>
                        <option v-for="area in areas" :key="area.id" :value="area.id">
                            {{ area.code }} - {{ area.name }}
                        </option>
                    </select>
                </div>

                <!-- Footer Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="$emit('close')"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-white font-semibold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20 disabled:opacity-50">
                        {{ form.processing ? 'Sharing...' : 'Share Now' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
