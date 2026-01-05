<script setup>
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });
</script>

<template>
    <!-- Scrollable Content -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-8 scroll-smooth">
        <div class="max-w-[1200px] mx-auto flex flex-col gap-6">
            <!-- PageHeading -->
            <div class="flex flex-wrap justify-between items-end gap-3">
            <div class="flex flex-col gap-1">
            <h1 class="text-text-main-light dark:text-white tracking-tight text-[28px] md:text-[32px] font-bold leading-tight">Welcome back, Admin</h1>
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-normal leading-normal">Here is what needs your attention today regarding the accreditation archives.</p>
            </div>
            <div class="flex gap-3">
            <button class="flex items-center gap-2 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-text-main-light dark:text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]">download</span>
                                            Generate Report
                                        </button>
            <button class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/20">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                                            Upload Document
                                        </button>
            </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Total Documents</p>
            <span class="material-symbols-outlined text-primary bg-blue-50 dark:bg-blue-900/20 p-1.5 rounded-lg">description</span>
            </div>
            <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">1,240</p>
            <span class="text-xs text-green-600 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">trending_up</span> +12% from last month</span>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Active Users</p>
            <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 p-1.5 rounded-lg">group</span>
            </div>
            <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">85</p>
            <span class="text-xs text-text-muted-light dark:text-text-muted-dark">Across 8 departments</span>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border-l-4 border-l-orange-400 border-y border-r border-r-[#cfd7e7] border-y-[#cfd7e7] dark:border-y-gray-700 dark:border-r-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Pending Approvals</p>
            <span class="material-symbols-outlined text-orange-500 bg-orange-50 dark:bg-orange-900/20 p-1.5 rounded-lg">person_add</span>
            </div>
            <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">3</p>
            <span class="text-xs text-orange-600 font-medium">Requires immediate action</span>
            </div>
            <div class="flex flex-col gap-2 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm font-medium uppercase tracking-wider">Expiring Soon</p>
            <span class="material-symbols-outlined text-red-500 bg-red-50 dark:bg-red-900/20 p-1.5 rounded-lg">timer</span>
            </div>
            <p class="text-text-main-light dark:text-white tracking-tight text-3xl font-bold leading-tight">12</p>
            <span class="text-xs text-red-600 font-medium">Documents expiring &lt; 30 days</span>
            </div>
            </div>
            <!-- Dashboard Main Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Column (Main) -->
            <div class="xl:col-span-2 flex flex-col gap-6">
            <!-- Charts Section -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm p-6">
            <div class="flex justify-between items-start mb-6">
            <div class="flex flex-col gap-1">
            <h3 class="text-text-main-light dark:text-white text-lg font-bold">Upload Activity</h3>
            <p class="text-text-muted-light dark:text-text-muted-dark text-sm">Document submission trends over last 6 months</p>
            </div>
            <div class="flex items-center gap-2 px-2 py-1 bg-green-50 dark:bg-green-900/20 rounded text-green-700 dark:text-green-400 text-xs font-bold">
            <span class="material-symbols-outlined text-[16px]">arrow_upward</span> 12% vs last period
                                                </div>
            </div>
            <div class="grid min-h-[220px] grid-flow-col gap-4 sm:gap-8 grid-rows-[1fr_auto] items-end justify-items-center px-2">
            <!-- Bars -->
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[60%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[45%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[35%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[80%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[55%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
            <div class="relative w-full bg-primary/20 dark:bg-primary/10 rounded-t-sm h-[90%] group-hover:bg-primary/30 transition-all">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-sm" style="height: 100%;"></div>
            </div>
            </div>
            <!-- Labels -->
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Jan</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Feb</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Mar</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Apr</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">May</p>
            <p class="text-text-muted-light dark:text-text-muted-dark text-xs font-semibold">Jun</p>
            </div>
            </div>
            <!-- Pending Approvals Table -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#cfd7e7] dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-text-main-light dark:text-white text-lg font-bold">Pending User Approvals</h3>
            <a class="text-primary text-sm font-bold hover:underline" href="#">View All</a>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-background-light dark:bg-gray-800 text-text-muted-light dark:text-text-muted-dark font-medium uppercase text-xs">
            <tr>
            <th class="px-6 py-3">Name</th>
            <th class="px-6 py-3">Department</th>
            <th class="px-6 py-3">Role</th>
            <th class="px-6 py-3">Date</th>
            <th class="px-6 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-[#e7ebf3] dark:divide-gray-700">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 flex items-center gap-3">
            <div class="size-8 rounded-full bg-gray-200 bg-cover bg-center" data-alt="Portrait of Maria Santos" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD3QfC4I02aYgWVUOl2Vta6J5xZ5Y5nlq_c-vqn4ziAC7eIWWIpIPN9owO-_8PxTeU0DzOX0LBQ1Id9nVoG1nC42bSqgMzBTG6m1PnwvWmBZw2c0tHLcR7wxmimyToQCchw7a3xFW3XC5zDtBXee1Nqg1eBZw4LvG9UA9vv-I3cA_u-pgV3DI6B_OTXeZFynzTiihojXb_CBUjCdVauIr0jAD73NvFi2rl_G2npFbEgeDKRUnxAyaFH16vpBPlJKkCKTBRqxmkFW_zT");'></div>
            <div class="font-bold text-text-main-light dark:text-white">Maria Santos</div>
            </td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">College of Engineering</td>
            <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-bold">Editor</span></td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">Oct 24, 2023</td>
            <td class="px-6 py-4 text-right flex justify-end gap-2">
            <button class="size-8 flex items-center justify-center rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors" title="Approve">
            <span class="material-symbols-outlined text-[18px]">check</span>
            </button>
            <button class="size-8 flex items-center justify-center rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors" title="Deny">
            <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
            </td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 flex items-center gap-3">
            <div class="size-8 rounded-full bg-gray-200 bg-cover bg-center" data-alt="Portrait of Juan Dela Cruz" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB4pe1w5NVQAX39cKnXB2yu470rywcV-Kl59Qx9ea2QLHKiMIC8exkGcYrn2WzIyzEHNuGekUD8OYIoVPT4DsIsfTDGkuHIwSdR-R6u1FOcL0TM-mVhKaEM4-S0Og_STJzq4WwVhlZzbOiEHWHz9awIMYldLV06TjWGdVqpQ0eQYahuKA0lPieMvqnjhtg72tZJotGr2oFdYuedWaVI16jRBvmNcAucktEd-fT3lB9TafhHbpWsU6clQobneWwfWzSmtdR-lum_XnpG");'></div>
            <div class="font-bold text-text-main-light dark:text-white">Juan Dela Cruz</div>
            </td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">College of Nursing</td>
            <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-xs font-bold">Viewer</span></td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">Oct 23, 2023</td>
            <td class="px-6 py-4 text-right flex justify-end gap-2">
            <button class="size-8 flex items-center justify-center rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors" title="Approve">
            <span class="material-symbols-outlined text-[18px]">check</span>
            </button>
            <button class="size-8 flex items-center justify-center rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors" title="Deny">
            <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
            </td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 flex items-center gap-3">
            <div class="size-8 rounded-full bg-gray-200 bg-cover bg-center" data-alt="Portrait of Ana Reyes" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD-aaklSkTT-FdNfPitaBm10hP2rGc5TJ7Gp2FUVa27bSfpIavCz39fmbUs6U-qV_dSv1G0HrU02vR-oQWUPUixWfdIOI4LDW0oSwUKikhL0XpV0_oFOXj9xER1HOukjvIfjtZxgLe8H4CQLJ96PkI4GCmaW1N69jnccCmvFP2_YW3yvj3n6lD6murTe4CU80mP33_XLnbL0tYOLmjcv0criNAi5W5vk3GnLsWUnXaZ9zzfWubXjA2YltkWO6KOpnw3_eVUA9bdBVUP");'></div>
            <div class="font-bold text-text-main-light dark:text-white">Ana Reyes</div>
            </td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">HR Department</td>
            <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-bold">Editor</span></td>
            <td class="px-6 py-4 text-text-muted-light dark:text-text-muted-dark">Oct 22, 2023</td>
            <td class="px-6 py-4 text-right flex justify-end gap-2">
            <button class="size-8 flex items-center justify-center rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors" title="Approve">
            <span class="material-symbols-outlined text-[18px]">check</span>
            </button>
            <button class="size-8 flex items-center justify-center rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors" title="Deny">
            <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            </div>
            </div>
            <!-- Right Column (Side) -->
            <div class="flex flex-col gap-6">
            <!-- Expiring Documents Widget -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-[#cfd7e7] dark:border-gray-700">
            <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-red-500 text-[20px]">warning</span>
            <h3 class="text-text-main-light dark:text-white text-base font-bold">Expiring Documents</h3>
            </div>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">Files needing renewal within 30 days</p>
            </div>
            <div class="flex flex-col divide-y divide-[#e7ebf3] dark:divide-gray-700 max-h-[300px] overflow-y-auto">
            <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-2 rounded shrink-0">
            <span class="material-symbols-outlined text-[20px]">description</span>
            </div>
            <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-text-main-light dark:text-white truncate">Curriculum Audit 2020.pdf</p>
            <p class="text-xs text-red-600 font-medium">Expires in 2 days</p>
            </div>
            <button class="text-text-muted-light hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">more_vert</span>
            </button>
            </div>
            <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 p-2 rounded shrink-0">
            <span class="material-symbols-outlined text-[20px]">description</span>
            </div>
            <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-text-main-light dark:text-white truncate">Safety Certifications.docx</p>
            <p class="text-xs text-orange-600 font-medium">Expires in 15 days</p>
            </div>
            <button class="text-text-muted-light hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">more_vert</span>
            </button>
            </div>
            <div class="p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 p-2 rounded shrink-0">
            <span class="material-symbols-outlined text-[20px]">description</span>
            </div>
            <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-text-main-light dark:text-white truncate">Faculty Roster Q3.xlsx</p>
            <p class="text-xs text-orange-600 font-medium">Expires in 21 days</p>
            </div>
            <button class="text-text-muted-light hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">more_vert</span>
            </button>
            </div>
            </div>
            <div class="p-3 border-t border-[#cfd7e7] dark:border-gray-700 text-center">
            <a class="text-xs font-bold text-primary hover:underline" href="#">View All 12 Files</a>
            </div>
            </div>
            <!-- Activity Log Feed -->
            <div class="flex flex-col rounded-xl bg-surface-light dark:bg-surface-dark border border-[#cfd7e7] dark:border-gray-700 shadow-sm overflow-hidden flex-1">
            <div class="p-5 border-b border-[#cfd7e7] dark:border-gray-700">
            <h3 class="text-text-main-light dark:text-white text-base font-bold">Recent Activity</h3>
            </div>
            <div class="p-5 overflow-y-auto">
            <div class="relative pl-4 border-l-2 border-[#e7ebf3] dark:border-gray-700 flex flex-col gap-6">
            <!-- Item 1 -->
            <div class="relative">
            <div class="absolute -left-[21px] top-1 bg-primary rounded-full size-2.5 outline outline-4 outline-white dark:outline-surface-dark"></div>
            <div class="flex flex-col gap-1">
            <p class="text-sm text-text-main-light dark:text-white"><span class="font-bold">Admin</span> approved <span class="font-bold">Maria Santos</span> as Editor.</p>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">10 mins ago</p>
            </div>
            </div>
            <!-- Item 2 -->
            <div class="relative">
            <div class="absolute -left-[21px] top-1 bg-gray-300 dark:bg-gray-600 rounded-full size-2.5 outline outline-4 outline-white dark:outline-surface-dark"></div>
            <div class="flex flex-col gap-1">
            <p class="text-sm text-text-main-light dark:text-white"><span class="font-bold">John Doe</span> uploaded 3 new files to <span class="text-primary cursor-pointer hover:underline">Engineering Dept</span>.</p>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">1 hour ago</p>
            </div>
            </div>
            <!-- Item 3 -->
            <div class="relative">
            <div class="absolute -left-[21px] top-1 bg-gray-300 dark:bg-gray-600 rounded-full size-2.5 outline outline-4 outline-white dark:outline-surface-dark"></div>
            <div class="flex flex-col gap-1">
            <p class="text-sm text-text-main-light dark:text-white"><span class="font-bold">System</span> archived 5 expired documents.</p>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">Yesterday at 5:00 PM</p>
            </div>
            </div>
            <!-- Item 4 -->
            <div class="relative">
            <div class="absolute -left-[21px] top-1 bg-gray-300 dark:bg-gray-600 rounded-full size-2.5 outline outline-4 outline-white dark:outline-surface-dark"></div>
            <div class="flex flex-col gap-1">
            <p class="text-sm text-text-main-light dark:text-white"><span class="font-bold">Sarah Lee</span> updated her profile information.</p>
            <p class="text-xs text-text-muted-light dark:text-text-muted-dark">Yesterday at 2:30 PM</p>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
        </div>
    </main>
</template>