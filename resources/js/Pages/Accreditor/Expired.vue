<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Clock, Send, AlertTriangle, LogOut } from 'lucide-vue-next';
import { ref } from 'vue';

const page = usePage();
const accreditor = page.props.accreditor;

const form = useForm({});
const submitted = ref(false);

const requestExtension = () => {
    form.post(route('accreditor.request-extension'), {
        onSuccess: () => {
            submitted.value = true;
        }
    });
};
</script>

<template>
    <Head title="Access Expired" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-8 text-center">
                <div class="size-20 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <Clock class="size-10" />
                </div>
                
                <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Access Expired</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                    Hello <span class="font-bold text-slate-700 dark:text-slate-200">{{ accreditor?.name }}</span>, your accreditation access window has closed. 
                    To continue viewing files and leaving comments, you must request an extension from the IDO admin.
                </p>

                <div v-if="!submitted" class="space-y-4">
                    <button 
                        @click="requestExtension"
                        :disabled="form.processing"
                        class="w-full py-4 bg-primary hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-primary/25 transition-all active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-50"
                    >
                        <span v-if="form.processing" class="material-symbols-outlined animate-spin">progress_activity</span>
                        <Send v-else class="size-5" />
                        {{ form.processing ? 'Sending Request...' : 'Request Access Extension' }}
                    </button>

                    <button 
                        @click="$inertia.post(route('logout'))"
                        class="w-full py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all flex items-center justify-center gap-3"
                    >
                        <LogOut class="size-5" />
                        Sign Out
                    </button>
                </div>

                <div v-else class="p-6 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-2xl">
                    <div class="size-10 bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <p class="text-green-800 dark:text-green-300 font-bold">Request Sent Successfully</p>
                    <p class="text-green-600 dark:text-green-400 text-sm mt-1">An administrator has been notified. You will receive an email once your access is restored.</p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 p-6 px-8 flex items-start gap-4 border-t border-slate-100 dark:border-slate-800">
                <AlertTriangle class="size-6 text-amber-500 shrink-0 mt-0.5" />
                <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-tight">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Why did this happen?</span> 
                    Accreditors are granted temporary access tied to specific events. Your current assignments or account window has reached its end date.
                </p>
            </div>
        </div>
    </div>
</template>
