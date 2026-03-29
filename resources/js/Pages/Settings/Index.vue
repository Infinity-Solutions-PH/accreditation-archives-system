<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@shared/Layouts/App.vue';

const props = defineProps({
    settings: Object
});

defineOptions({
    layout: AppLayout
});

const activeTab = ref('authentication');

const form = useForm({
    auth_strict_domains: props.settings.auth_strict_domains,
    auth_whitelisted_domains: props.settings.auth_whitelisted_domains,
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success feedback
        }
    });
};
</script>

<template>
    <Head title="System Settings" />

    <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gray-50 dark:bg-background-dark">
        <div class="max-w-4xl mx-auto flex flex-col gap-8">
            <!-- Header section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold text-text-main-light dark:text-white">System Configuration</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark">Manage global system behaviors and security policies.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-800">
                <button 
                    @click="activeTab = 'authentication'"
                    :class="{'border-primary text-primary': activeTab === 'authentication', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'authentication'}"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    Authentication & Security
                </button>
                <button 
                    @click="activeTab = 'general'"
                    :class="{'border-primary text-primary': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general'}"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-all duration-200 cursor-not-allowed opacity-50">
                    General Settings
                </button>
            </div>

            <!-- Configuration Content -->
            <div class="space-y-6">
                <div v-if="activeTab === 'authentication'" class="bg-white dark:bg-surface-dark rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                        <h2 class="text-lg font-bold">Email Domain Restrictions</h2>
                        <p class="text-sm text-gray-500">Control which email domains are allowed to register for internal staff accounts via Google.</p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-8">
                        <!-- Strict Mode Toggle -->
                        <div class="flex items-start justify-between gap-4 p-4 rounded-xl bg-gray-50 dark:bg-background-dark border border-gray-100 dark:border-gray-800">
                            <div class="flex flex-col gap-1">
                                <label class="font-bold text-sm">Strict Domain Enforcement</label>
                                <p class="text-xs text-gray-500 max-w-md">When enabled, only emails matching the whitelisted domains below will be allowed to register. Existing users and Accreditors are exempt.</p>
                            </div>
                            <button 
                                type="button"
                                @click="form.auth_strict_domains = !form.auth_strict_domains"
                                :class="form.auth_strict_domains ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-700'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out outline-none">
                                <span 
                                    :class="form.auth_strict_domains ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                </span>
                            </button>
                        </div>

                        <!-- Whitelist Domains -->
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Whitelisted Domains</label>
                            <p class="text-xs text-gray-500 mb-2">Comma-separated list of domains (e.g., cvsu.edu.ph, example.com).</p>
                            <textarea 
                                v-model="form.auth_whitelisted_domains"
                                rows="4"
                                placeholder="Enter domains separated by commas..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-background-dark focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono text-sm"
                                :disabled="!form.auth_strict_domains"
                                :class="{'opacity-50 grayscale cursor-not-allowed': !form.auth_strict_domains}"
                            ></textarea>
                            <p v-if="form.errors.auth_whitelisted_domains" class="text-xs text-red-500">{{ form.errors.auth_whitelisted_domains }}</p>
                        </div>

                        <!-- Save Actions -->
                        <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="bg-primary hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2">
                                <span v-if="form.processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                {{ form.processing ? 'Saving...' : 'Save Configuration' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="p-6 rounded-2xl bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800 flex items-start gap-4">
                    <span class="material-symbols-outlined text-indigo-600 text-[24px]">info</span>
                    <div class="flex flex-col gap-1">
                        <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-200">About Domain Restrictions</h4>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300 leading-relaxed">
                            These settings strictly apply to **new registrations** via Google. Accreditors are granted a special exemption to ensure they can access the system regardless of their institutional email provider. Existing users who have already been approved will not be affected even if their domain is not on the list.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
