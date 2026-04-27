<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    event: {
        type: Object,
        default: null
    },
    colleges: Array,
    programs: Array
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    title: '',
    description: '',
    college_id: '',
    program_id: '',
    level: '',
    expires_at: '',
    status: 'active'
});

watch(() => props.show, (isVisible) => {
    if (isVisible) {
        if (props.event) {
            form.title = props.event.title;
            form.description = props.event.description;
            form.college_id = props.event.college_id;
            form.program_id = props.event.program_id;
            form.level = props.event.level;
            form.expires_at = props.event.expires_at ? props.event.expires_at.split('T')[0] : '';
            form.status = props.event.status;
        } else {
            form.reset();
        }
    }
});

const submit = () => {
    if (props.event) {
        form.put(route('events.update', props.event.slug), {
            onSuccess: () => {
                emit('success');
                emit('close');
            }
        });
    } else {
        form.post(route('events.store'), {
            onSuccess: () => {
                emit('success');
                emit('close');
            }
        });
    }
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-surface-dark w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ event ? 'Edit Event' : 'Create New Event' }}
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 mt-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Title</label>
                    <input v-model="form.title" type="text" required placeholder="AACCUP 3rd Survey Visit"
                        class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Description</label>
                    <textarea v-model="form.description" rows="3" placeholder="Additional details about this visit..."
                        class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">College</label>
                        <select v-model="form.college_id" required
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                            <option value="" disabled>Select College</option>
                            <option v-for="college in colleges" :key="college.id" :value="college.id">{{ college.code }}</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Program</label>
                        <select v-model="form.program_id" required :disabled="!form.college_id"
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                            <option value="" disabled>Select Program</option>
                            <option v-for="program in programs.filter(p => p.college_id == form.college_id)" :key="program.id" :value="program.id">
                                {{ program.code }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Level</label>
                        <select v-model="form.level" required
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                            <option value="" disabled>Select Level</option>
                            <option value="Candidate Status">Candidate Status</option>
                            <option value="Level I">Level I</option>
                            <option value="Level II">Level II</option>
                            <option value="Level III">Level III</option>
                            <option value="Level IV">Level IV</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Expiry Date</label>
                        <input v-model="form.expires_at" type="date" required
                            class="w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <div v-if="event" class="space-y-1.5 pt-2">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <div class="flex gap-4">
                        <label v-for="s in ['active', 'completed', 'cancelled']" :key="s" class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.status" :value="s" class="text-primary focus:ring-primary">
                            <span class="text-sm capitalize">{{ s }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="$emit('close')"
                        class="flex-1 px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="flex-1 px-4 py-3 rounded-xl bg-primary text-white font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20 disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : (event ? 'Update Event' : 'Create Event') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
