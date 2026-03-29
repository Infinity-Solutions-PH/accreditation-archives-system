<script setup>
    import AuthLayout from '@shared/Layouts/Auth.vue';
    import { useForm, Head, Link } from '@inertiajs/vue3';

    defineOptions({
        layout: AuthLayout
    });

    const props = defineProps({
        status: String,
    });

    const form = useForm({
        email: '',
        password: '',
    });

    const submit = () => {
        form.post(route('auth.store'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<template>
    <Head>
        <title>Auth</title>
    </Head>
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Hero Image -->
        <div class="hidden lg:flex w-1/2 relative bg-primary/5 overflow-hidden">
            <div class="absolute inset-0 z-10 bg-primary/10 mix-blend-multiply"></div>
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <img alt="University Background" class="w-full h-full object-cover" data-alt="University campus building with students walking" src="https://cvsu.edu.ph/wp-content/uploads/2023/09/CvSU-Front-09-2023-scaled.jpg"/>
            <div class="absolute bottom-0 left-0 w-full p-12 z-30 text-white">
                <div class="mb-4 size-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-white text-3xl">verified_user</span>
                </div>
                <h2 class="text-4xl font-bold leading-tight mb-4">Secure Accreditation Management</h2>
                <p class="text-lg text-white/90 max-w-md leading-relaxed">Streamlining the archival and verification process for Cavite State University's academic excellence.</p>
            </div>
        </div>
        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col relative bg-background-light dark:bg-background-dark">
            <!-- Mobile/Top Minimal Header -->
            <div class="w-full p-6 flex justify-between items-center lg:absolute lg:top-0 lg:right-0 z-10">
                <div class="lg:hidden flex items-center gap-2">
                    <div class="size-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="w-5 h-5" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.8261 30.5736C16.7203 29.8826 20.2244 29.4783 24 29.4783C27.7756 29.4783 31.2797 29.8826 34.1739 30.5736C36.9144 31.2278 39.9967 32.7669 41.3563 33.8352L24.8486 7.36089C24.4571 6.73303 23.5429 6.73303 23.1514 7.36089L6.64374 33.8352C8.00331 32.7669 11.0856 31.2278 13.8261 30.5736Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-lg dark:text-white">CvSU Archives</span>
                </div>
                <div class="flex gap-4 ml-auto">
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors" href="#">Help</a>
                </div>
            </div>
            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-24 overflow-y-auto">
                <div class="w-full max-w-[440px] space-y-8">
                    <!-- Header Section -->
                    <div class="text-left space-y-2">
                        <div class="hidden lg:flex items-center gap-3 mb-6 text-[#0d121b] dark:text-white">
                            <div class="size-10 p-2 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <!-- <svg class="w-6 h-6" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.8261 30.5736C16.7203 29.8826 20.2244 29.4783 24 29.4783C27.7756 29.4783 31.2797 29.8826 34.1739 30.5736C36.9144 31.2278 39.9967 32.7669 41.3563 33.8352L24.8486 7.36089C24.4571 6.73303 23.5429 6.73303 23.1514 7.36089L6.64374 33.8352C8.00331 32.7669 11.0856 31.2278 13.8261 30.5736Z" fill="currentColor"></path>
                                    <path clip-rule="evenodd" d="M39.998 35.764C39.9944 35.7463 39.9875 35.7155 39.9748 35.6706C39.9436 35.5601 39.8949 35.4259 39.8346 35.2825C39.8168 35.2403 39.7989 35.1993 39.7813 35.1602C38.5103 34.2887 35.9788 33.0607 33.7095 32.5189C30.9875 31.8691 27.6413 31.4783 24 31.4783C20.3587 31.4783 17.0125 31.8691 14.2905 32.5189C12.0012 33.0654 9.44505 34.3104 8.18538 35.1832C8.17384 35.2075 8.16216 35.233 8.15052 35.2592C8.09919 35.3751 8.05721 35.4886 8.02977 35.589C8.00356 35.6848 8.00039 35.7333 8.00004 35.7388C8.00004 35.739 8 35.7393 8.00004 35.7388C8.00004 35.7641 8.0104 36.0767 8.68485 36.6314C9.34546 37.1746 10.4222 37.7531 11.9291 38.2772C14.9242 39.319 19.1919 40 24 40C28.8081 40 33.0758 39.319 36.0709 38.2772C37.5778 37.7531 38.6545 37.1746 39.3151 36.6314C39.9006 36.1499 39.9857 35.8511 39.998 35.764ZM4.95178 32.7688L21.4543 6.30267C22.6288 4.4191 25.3712 4.41909 26.5457 6.30267L43.0534 32.777C43.0709 32.8052 43.0878 32.8338 43.104 32.8629L41.3563 33.8352C43.104 32.8629 43.1038 32.8626 43.104 32.8629L43.1051 32.865L43.1065 32.8675L43.1101 32.8739L43.1199 32.8918C43.1276 32.906 43.1377 32.9246 43.1497 32.9473C43.1738 32.9925 43.2062 33.0545 43.244 33.1299C43.319 33.2792 43.4196 33.489 43.5217 33.7317C43.6901 34.1321 44 34.9311 44 35.7391C44 37.4427 43.003 38.7775 41.8558 39.7209C40.6947 40.6757 39.1354 41.4464 37.385 42.0552C33.8654 43.2794 29.133 44 24 44C18.867 44 14.1346 43.2794 10.615 42.0552C8.86463 41.4464 7.30529 40.6757 6.14419 39.7209C4.99695 38.7775 3.99999 37.4427 3.99999 35.7391C3.99999 34.8725 4.29264 34.0922 4.49321 33.6393C4.60375 33.3898 4.71348 33.1804 4.79687 33.0311C4.83898 32.9556 4.87547 32.8935 4.9035 32.8471C4.91754 32.8238 4.92954 32.8043 4.93916 32.7889L4.94662 32.777L4.95178 32.7688ZM35.9868 29.004L24 9.77997L12.0131 29.004C12.4661 28.8609 12.9179 28.7342 13.3617 28.6282C16.4281 27.8961 20.0901 27.4783 24 27.4783C27.9099 27.4783 31.5719 27.8961 34.6383 28.6282C35.082 28.7342 35.5339 28.8609 35.9868 29.004Z" fill="currentColor" fill-rule="evenodd"></path>
                                </svg> -->
                                <img src="https://library.cvsu.edu.ph/landing/storage/images/CvSU-logo-64x64.webp">
                            </div>
                            <h2 class="text-xl font-bold tracking-tight">CvSU Accreditation Archives</h2>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight text-[#0d121b] dark:text-white">Welcome back</h1>
                        <p class="text-slate-500 dark:text-slate-400">Please enter your details to access the archives.</p>
                    </div>
                    <!-- Main Form -->
                    <form class="space-y-5" @submit.prevent="submit">
                        <div v-if="status" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800/50 dark:text-green-400 border border-green-100 dark:border-green-900/30 flex items-center gap-2" role="alert">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            {{ status }}
                        </div>
                        <div v-if="form.errors.email" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800/50 dark:text-red-400 border border-red-100 dark:border-red-900/30 flex items-center gap-2" role="alert">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            {{ form.errors.email }}
                        </div>
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="email">University Email</label>
                            <div class="relative">
                                <input v-model="form.email" class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 placeholder:text-[#4c669a] dark:placeholder:text-slate-500 px-4 text-base font-normal leading-normal transition-all" id="email" placeholder="username@cvsu.edu.ph" required="" type="email"/>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">alternate_email</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-1.5 mt-1.5">
                                <span class="material-symbols-outlined text-xs text-primary mt-0.5">info</span>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Authorized personnel only. Must use official <span class="font-medium text-primary">@cvsu.edu.ph</span> account.</p>
                            </div>
                        </div>
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal" for="password">Password</label>
                            <div class="relative">
                                <input v-model="form.password" class="form-input flex w-full rounded-lg text-[#0d121b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-[#cfd7e7] dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary h-12 placeholder:text-[#4c669a] dark:placeholder:text-slate-500 px-4 text-base font-normal leading-normal transition-all" id="password" placeholder="Enter your password" required="" type="password"/>
                                <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition-colors focus:outline-none" type="button">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>
                        <!-- Forgot Password Link -->
                        <div class="flex justify-end">
                            <Link class="text-sm font-medium text-primary hover:text-primary-hover hover:underline decoration-2 underline-offset-2 transition-all" :href="route('password.request')">Forgot password?</Link>
                        </div>
                        <!-- Submit Button -->
                        <button :disabled="form.processing" class="w-full h-12 bg-primary hover:bg-primary-hover text-white font-semibold rounded-lg shadow-sm shadow-primary/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" type="submit">
                            <span>Sign In</span>
                            <span class="material-symbols-outlined text-sm">login</span>
                        </button>
                        <a :href="route('google.redirect')" class="w-full h-12 bg-white dark:bg-slate-800 border border-[#cfd7e7] dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-white font-medium rounded-lg shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-3" type="button">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                            </svg>
                            <span>Sign in with Google</span>
                        </a>
                    </form>
                    <!-- Divider -->
                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="flex-shrink-0 mx-4 text-slate-400 text-sm font-medium">External Access</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                    <!-- Accreditor Access Section -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-100 dark:border-slate-700 flex flex-col items-start gap-3">
                        <div class="flex items-center gap-2 text-[#0d121b] dark:text-white font-medium">
                            <span class="material-symbols-outlined text-amber-500">badge</span>
                            <span>External Accreditor?</span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                            If you are an external accreditor visiting for a scheduled evaluation, please use the temporary portal.
                        </p>
                        <Link class="mt-1 text-sm font-semibold text-primary hover:text-primary-hover flex items-center gap-1 group" :href="route('accreditor.auth')">
                            Access Temporary Portal 
                            <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </Link>
                    </div>
                    <!-- Footer -->
                    <div class="pt-6 text-center">
                        <p class="text-xs text-slate-400 dark:text-slate-600">
                            © 2024 Cavite State University. All rights reserved.
                            <br/>
                            <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a> · <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>