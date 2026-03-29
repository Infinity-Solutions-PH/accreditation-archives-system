<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { useForm, Head } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });

    const props = defineProps({
        colleges: Array
    });

    const form = useForm({
        college_id: '',
    });

    const submit = () => {
        form.post(route('onboarding.college.store'));
    };
</script>

<template>
    <Head>
        <title>Select College - Onboarding</title>
    </Head>
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Hero Image -->
        <div class="hidden lg:flex w-1/2 relative bg-primary/5 overflow-hidden">
            <div class="absolute inset-0 z-10 bg-primary/10 mix-blend-multiply"></div>
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <img alt="University Background" class="w-full h-full object-cover" data-alt="University campus building with students walking" src="https://cvsu.edu.ph/wp-content/uploads/2023/09/CvSU-Front-09-2023-scaled.jpg"/>
            <div class="absolute bottom-0 left-0 w-full p-12 z-30 text-white">
                <div class="mb-4 size-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-white text-3xl">domain</span>
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-4">Select Your Academic College</h2>
                <p class="text-lg text-white/90 max-w-md leading-relaxed">Help us personalize your dashboard and correctly orient your accreditation taskforce assignments.</p>
            </div>
        </div>
        <!-- Right Side: College Selection Form -->
        <div class="w-full lg:w-1/2 flex flex-col relative bg-background-light dark:bg-background-dark">
            <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-24 overflow-y-auto">
                <div class="w-full max-w-[440px] space-y-8">
                    <!-- Header Section -->
                    <div class="text-left space-y-2">
                        <div class="hidden lg:flex items-center gap-3 mb-6 text-[#0d121b] dark:text-white">
                            <div class="size-10 p-2 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <img src="https://library.cvsu.edu.ph/landing/storage/images/CvSU-logo-64x64.webp">
                            </div>
                            <h2 class="text-xl font-bold tracking-tight">CvSU Accreditation Archives</h2>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#0d121b] dark:text-white">Almost there...</h1>
                        <p class="text-slate-500 dark:text-slate-400">Please choose your respective college to complete your account setup.</p>
                    </div>
                    <!-- Main Form -->
                    <form class="space-y-6" @submit.prevent="submit">
                        <div v-if="form.errors.college_id" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            {{ form.errors.college_id }}
                        </div>

                        <!-- College Dropdown -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="college_id">Academic College</label>
                            <div class="relative">
                                <select v-model="form.college_id" required class="flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 px-4 text-base font-normal leading-normal transition-all appearance-none cursor-pointer" id="college_id">
                                    <option value="" disabled selected>Select your college</option>
                                    <option v-for="college in colleges" :key="college.id" :value="college.id">
                                        {{ college.code }} - {{ college.name }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex flex-col gap-3">
                            <button :disabled="form.processing" class="w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-lg shadow-sm shadow-primary/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" type="submit">
                                <span>Submit Request</span>
                                <span class="material-symbols-outlined text-sm">send</span>
                            </button>
                            
                            <!-- Logout Button -->
                            <button 
                                type="button" 
                                @click="$inertia.post(route('logout'))"
                                class="w-full h-11 border border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-400 font-medium rounded-lg transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                <span>Cancel & Sign Out</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
