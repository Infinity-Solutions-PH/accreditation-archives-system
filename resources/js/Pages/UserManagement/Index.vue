<script setup>
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });
</script>

<template>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
    <!-- Top Header -->
    <header class="h-16 flex items-center justify-between px-6 bg-white dark:bg-[#1a2234] border-b border-slate-200 dark:border-slate-800 shrink-0">
    <!-- Mobile Menu Trigger -->
    <button class="md:hidden p-2 -ml-2 text-slate-600 dark:text-slate-300">
    <span class="material-symbols-outlined">menu</span>
    </button>
    <div class="hidden md:flex text-sm text-slate-500 dark:text-slate-400 font-medium">
                    CvSU Accreditation Archives System
                </div>
    <div class="flex items-center gap-3 ml-auto">
    <button class="relative flex items-center justify-center size-9 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
    <span class="material-symbols-outlined text-[20px]">notifications</span>
    <span class="absolute top-1.5 right-1.5 size-2 bg-red-500 rounded-full border-2 border-white dark:border-[#1a2234]"></span>
    </button>
    <button class="flex items-center justify-center size-9 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
    <span class="material-symbols-outlined text-[20px]">help</span>
    </button>
    </div>
    </header>
    <!-- Scrollable Page Content -->
    <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <nav class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400">
    <a class="hover:text-primary transition-colors" href="#">Home</a>
    <span class="mx-2">/</span>
    <a class="hover:text-primary transition-colors" href="#">Administration</a>
    <span class="mx-2">/</span>
    <span class="text-slate-900 dark:text-white">User Management</span>
    </nav>
    <!-- Page Heading & Primary Action -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div class="flex flex-col gap-1">
    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white tracking-tight">User Management</h1>
    <p class="text-slate-500 dark:text-slate-400 text-base max-w-2xl">Manage user accounts, assign roles, and approve access requests for college accreditation officers.</p>
    </div>
    <button class="flex items-center gap-2 bg-primary hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all active:scale-95 whitespace-nowrap">
    <span class="material-symbols-outlined text-[20px]">add</span>
                            Create New User
                        </button>
    </div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
    <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Active Users</span>
    <div class="flex items-baseline gap-2">
    <span class="text-2xl font-bold text-slate-900 dark:text-white">1,284</span>
    <span class="text-green-600 text-sm font-medium bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded flex items-center">
    <span class="material-symbols-outlined text-[14px] mr-0.5">trending_up</span> 5%
                                </span>
    </div>
    </div>
    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1 relative overflow-hidden group cursor-pointer hover:border-primary/50 transition-colors">
    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
    <span class="material-symbols-outlined text-6xl text-primary">person_add</span>
    </div>
    <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Pending Requests</span>
    <div class="flex items-baseline gap-2">
    <span class="text-2xl font-bold text-slate-900 dark:text-white">12</span>
    <span class="text-primary text-sm font-medium">Needs Approval</span>
    </div>
    </div>
    <div class="bg-white dark:bg-[#1a2234] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-1">
    <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">Faculty Admins</span>
    <div class="flex items-baseline gap-2">
    <span class="text-2xl font-bold text-slate-900 dark:text-white">45</span>
    <span class="text-slate-400 text-sm font-medium">Across 12 Colleges</span>
    </div>
    </div>
    </div>
    <!-- Main Table Section -->
    <div class="bg-white dark:bg-[#1a2234] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
    <!-- Filters Toolbar -->
    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center bg-slate-50/50 dark:bg-slate-800/20">
    <!-- Search -->
    <div class="relative w-full lg:w-96">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
    <input class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500" placeholder="Search by name, email, or college..." type="text"/>
    </div>
    <!-- Filter Actions -->
    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
    <div class="flex items-center gap-2">
    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Filter:</span>
    <select class="bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 cursor-pointer">
    <option>All Roles</option>
    <option>IDO Staff</option>
    <option>College Admin</option>
    <option>Faculty</option>
    </select>
    <select class="bg-white dark:bg-[#151b2b] border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 cursor-pointer">
    <option>All Status</option>
    <option>Active</option>
    <option>Pending</option>
    <option>Inactive</option>
    </select>
    </div>
    <div class="h-6 w-px bg-slate-300 dark:bg-slate-600 hidden sm:block"></div>
    <button class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-blue-400 text-sm font-medium transition-colors">
    <span class="material-symbols-outlined text-[20px]">filter_list</span>
    <span>Advanced</span>
    </button>
    </div>
    </div>
    <!-- Table -->
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
    <thead>
    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
    <th class="p-4 w-12">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </th>
    <th class="p-4">User Details</th>
    <th class="p-4">Role</th>
    <th class="p-4">Department / College</th>
    <th class="p-4">Status</th>
    <th class="p-4">Last Active</th>
    <th class="p-4 text-right">Actions</th>
    </tr>
    </thead>
    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm text-slate-700 dark:text-slate-300">
    <!-- Row 1 -->
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
    <td class="p-4">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </td>
    <td class="p-4">
    <div class="flex items-center gap-3">
    <div class="size-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center text-sm font-bold">
                                                    JD
                                                </div>
    <div>
    <div class="font-medium text-slate-900 dark:text-white">Juan Dela Cruz</div>
    <div class="text-slate-500 dark:text-slate-400 text-xs">juan.delacruz@cvsu.edu.ph</div>
    </div>
    </div>
    </td>
    <td class="p-4">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300 border border-purple-100 dark:border-purple-800">
    <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                                IDO Staff
                                            </div>
    </td>
    <td class="p-4">Quality Assurance Office</td>
    <td class="p-4">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300 border border-green-100 dark:border-green-800">
    <span class="size-1.5 rounded-full bg-green-500"></span>
                                                Active
                                            </span>
    </td>
    <td class="p-4 text-slate-500 dark:text-slate-400">Just now</td>
    <td class="p-4 text-right">
    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
    <button class="p-1.5 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit User">
    <span class="material-symbols-outlined text-[20px]">edit</span>
    </button>
    <button class="p-1.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete User">
    <span class="material-symbols-outlined text-[20px]">delete</span>
    </button>
    </div>
    </td>
    </tr>
    <!-- Row 2 -->
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
    <td class="p-4">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </td>
    <td class="p-4">
    <div class="flex items-center gap-3">
    <div class="size-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-sm font-bold bg-cover bg-center" data-alt="Profile picture of Maria Santos" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBpMkA7u44hQfo1iG7yfK6gbVwHnKcmdx0f2YVXEq4OZx9YA_203r5rLncM38dc00UIy2AD0UfSPjVFci3wHodODP5FS0nzSLnrYkagFjst3_nLePAupYSeq9W0zjJMPTAhqrTdPIxOfKBAqfVhbVR_FyF7B1gCVmNz1L_mPrDn6A5BhIcg8E3E9alTr7s7csoho2L5Bqsb5rpHxLcHchxdB3MZ1i1LpuLvi2-irBgNHIAAU79eDPogPFT1qNBELJrrKOeQlFTss0g8");'>
    </div>
    <div>
    <div class="font-medium text-slate-900 dark:text-white">Maria Santos</div>
    <div class="text-slate-500 dark:text-slate-400 text-xs">maria.santos@cvsu.edu.ph</div>
    </div>
    </div>
    </td>
    <td class="p-4">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
    <span class="material-symbols-outlined text-[14px]">school</span>
                                                College Admin
                                            </div>
    </td>
    <td class="p-4">College of Engineering</td>
    <td class="p-4">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300 border border-green-100 dark:border-green-800">
    <span class="size-1.5 rounded-full bg-green-500"></span>
                                                Active
                                            </span>
    </td>
    <td class="p-4 text-slate-500 dark:text-slate-400">2 hours ago</td>
    <td class="p-4 text-right">
    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
    <button class="p-1.5 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit User">
    <span class="material-symbols-outlined text-[20px]">edit</span>
    </button>
    <button class="p-1.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete User">
    <span class="material-symbols-outlined text-[20px]">delete</span>
    </button>
    </div>
    </td>
    </tr>
    <!-- Row 3 - Pending Request -->
    <tr class="bg-amber-50/50 dark:bg-amber-900/10 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group border-l-4 border-l-amber-400 dark:border-l-amber-600">
    <td class="p-4">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </td>
    <td class="p-4">
    <div class="flex items-center gap-3">
    <div class="size-9 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-sm font-bold">
                                                    RR
                                                </div>
    <div>
    <div class="font-medium text-slate-900 dark:text-white">Roberto Reyes</div>
    <div class="text-slate-500 dark:text-slate-400 text-xs">roberto.reyes@cvsu.edu.ph</div>
    </div>
    </div>
    </td>
    <td class="p-4">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
    <span class="material-symbols-outlined text-[14px]">person</span>
                                                Faculty
                                            </div>
    </td>
    <td class="p-4">College of Arts and Sciences</td>
    <td class="p-4">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
    <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                                                Pending
                                            </span>
    </td>
    <td class="p-4 text-slate-500 dark:text-slate-400">Never</td>
    <td class="p-4 text-right">
    <div class="flex items-center justify-end gap-2">
    <button class="flex items-center gap-1 px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition-colors shadow-sm">
    <span class="material-symbols-outlined text-[14px]">check</span> Approve
                                                </button>
    <button class="p-1 rounded text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-colors" title="Reject">
    <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
    </div>
    </td>
    </tr>
    <!-- Row 4 -->
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
    <td class="p-4">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </td>
    <td class="p-4">
    <div class="flex items-center gap-3">
    <div class="size-9 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 flex items-center justify-center text-sm font-bold bg-cover bg-center" data-alt="Profile picture of Ana Gomez" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCaY04hVWyIBeu6aIy-vdkSHMI2a8LQPwTQo9jB-CzZi_auaSZiYMI8mGC2dzKeli8Rjh9nEWA0p9N6prOg3K0Ar3xKq3otFj7AlWxf7aRDHl7dA4T0-nMTOtPzlGocM77wRb7BhM3djE6_mLHDDsGaeR0kZTnvJE9lNNRI7ZFeszexVbXGgh_rph1LeAYBeArBR-FnmUjr3iAUuYD5nt6MKlLi8lK4Sm7dXB2HJS_P7XlEMHuisDT1Dfthv32IvTdqXJTYuQiAmJYl");'>
    </div>
    <div>
    <div class="font-medium text-slate-900 dark:text-white">Ana Gomez</div>
    <div class="text-slate-500 dark:text-slate-400 text-xs">ana.gomez@cvsu.edu.ph</div>
    </div>
    </div>
    </td>
    <td class="p-4">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
    <span class="material-symbols-outlined text-[14px]">school</span>
                                                College Admin
                                            </div>
    </td>
    <td class="p-4">College of Education</td>
    <td class="p-4">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
    <span class="size-1.5 rounded-full bg-slate-400"></span>
                                                Inactive
                                            </span>
    </td>
    <td class="p-4 text-slate-500 dark:text-slate-400">30+ days ago</td>
    <td class="p-4 text-right">
    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
    <button class="p-1.5 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit User">
    <span class="material-symbols-outlined text-[20px]">edit</span>
    </button>
    <button class="p-1.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete User">
    <span class="material-symbols-outlined text-[20px]">delete</span>
    </button>
    </div>
    </td>
    </tr>
    <!-- Row 5 -->
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
    <td class="p-4">
    <input class="rounded border-slate-300 text-primary focus:ring-primary bg-white dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
    </td>
    <td class="p-4">
    <div class="flex items-center gap-3">
    <div class="size-9 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 flex items-center justify-center text-sm font-bold">
                                                    LP
                                                </div>
    <div>
    <div class="font-medium text-slate-900 dark:text-white">Luis Pantoja</div>
    <div class="text-slate-500 dark:text-slate-400 text-xs">luis.pantoja@cvsu.edu.ph</div>
    </div>
    </div>
    </td>
    <td class="p-4">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
    <span class="material-symbols-outlined text-[14px]">person</span>
                                                Faculty
                                            </div>
    </td>
    <td class="p-4">CCAT Campus</td>
    <td class="p-4">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300 border border-green-100 dark:border-green-800">
    <span class="size-1.5 rounded-full bg-green-500"></span>
                                                Active
                                            </span>
    </td>
    <td class="p-4 text-slate-500 dark:text-slate-400">1 day ago</td>
    <td class="p-4 text-right">
    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
    <button class="p-1.5 rounded text-slate-400 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Edit User">
    <span class="material-symbols-outlined text-[20px]">edit</span>
    </button>
    <button class="p-1.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete User">
    <span class="material-symbols-outlined text-[20px]">delete</span>
    </button>
    </div>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    <!-- Pagination -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
    <span class="text-sm text-slate-500 dark:text-slate-400">Showing <span class="font-medium text-slate-900 dark:text-white">1</span> to <span class="font-medium text-slate-900 dark:text-white">5</span> of <span class="font-medium text-slate-900 dark:text-white">1,284</span> results</span>
    <div class="flex items-center gap-1">
    <button class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 disabled:opacity-50 transition-colors">
    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
    </button>
    <button class="px-3 py-1 rounded bg-primary text-white text-sm font-medium">1</button>
    <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">2</button>
    <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">3</button>
    <span class="px-2 text-slate-400">...</span>
    <button class="px-3 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">24</button>
    <button class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
    </button>
    </div>
    </div>
    </div>
    </div>
    </div>
    </main>
</template>