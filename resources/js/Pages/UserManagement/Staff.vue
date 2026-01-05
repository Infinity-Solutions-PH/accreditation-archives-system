<script setup>
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });
</script>

<template>
    <main class="flex-1 px-4 md:px-10 py-5 w-full max-w-[1440px] mx-auto flex flex-col gap-6">
        <!-- Breadcrumbs -->
        <div class="flex flex-wrap gap-2 px-4">
        <a class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Home</a>
        <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
        <span class="text-[#0d121b] dark:text-white text-sm font-medium leading-normal">IDO Dashboard</span>
        </div>
        <!-- Page Heading -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4">
        <div class="flex flex-col gap-1">
        <h1 class="text-[#0d121b] dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">College Management</h1>
        <p class="text-[#4c669a] text-base font-normal leading-normal">Overview of assigned colleges and accreditation status</p>
        </div>
        <button class="flex items-center justify-center gap-2 rounded-lg h-10 px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-blue-700 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[20px]">add</span>
        <span class="truncate">Add New College</span>
        </button>
        </div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 px-4">
        <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a202c] border border-[#e7ebf3] dark:border-[#2a3447] shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
        <span class="material-symbols-outlined text-[64px] text-primary">school</span>
        </div>
        <p class="text-[#4c669a] text-sm font-bold uppercase tracking-wider">Total Colleges</p>
        <p class="text-[#0d121b] dark:text-white text-3xl font-black leading-tight">8</p>
        <div class="flex items-center gap-1 text-green-600 text-xs font-bold mt-1">
        <span class="material-symbols-outlined text-[16px]">trending_up</span>
        <span>+1 this month</span>
        </div>
        </div>
        <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a202c] border border-[#e7ebf3] dark:border-[#2a3447] shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
        <span class="material-symbols-outlined text-[64px] text-primary">person_check</span>
        </div>
        <p class="text-[#4c669a] text-sm font-bold uppercase tracking-wider">Active Officers</p>
        <p class="text-[#0d121b] dark:text-white text-3xl font-black leading-tight">6</p>
        <div class="flex items-center gap-1 text-[#4c669a] text-xs font-medium mt-1">
        <span>75% Coverage</span>
        </div>
        </div>
        <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#1a202c] border border-[#e7ebf3] dark:border-[#2a3447] shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
        <span class="material-symbols-outlined text-[64px] text-orange-500">warning</span>
        </div>
        <p class="text-[#4c669a] text-sm font-bold uppercase tracking-wider">Pending Assignments</p>
        <p class="text-[#0d121b] dark:text-white text-3xl font-black leading-tight">2</p>
        <div class="flex items-center gap-1 text-orange-500 text-xs font-bold mt-1">
        <span class="material-symbols-outlined text-[16px]">priority_high</span>
        <span>Action Required</span>
        </div>
        </div>
        </div>
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 px-4 pt-2">
        <div class="flex-1 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <span class="material-symbols-outlined text-[#4c669a]">search</span>
        </div>
        <input class="block w-full pl-10 pr-3 py-3 border border-[#cfd7e7] dark:border-[#4a5568] rounded-lg leading-5 bg-white dark:bg-[#1a202c] placeholder-[#4c669a] focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-[#0d121b] dark:text-white transition-colors" placeholder="Search by college name or code..." type="text"/>
        </div>
        <div class="w-full md:w-64">
        <div class="relative">
        <select class="block w-full pl-3 pr-10 py-3 text-base border border-[#cfd7e7] dark:border-[#4a5568] focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-white dark:bg-[#1a202c] text-[#0d121b] dark:text-white appearance-none cursor-pointer">
        <option>All Statuses</option>
        <option>Active Officer</option>
        <option>Vacant Officer</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#4c669a]">
        <span class="material-symbols-outlined">expand_more</span>
        </div>
        </div>
        </div>
        </div>
        <!-- Data Table -->
        <div class="px-4">
        <div class="overflow-hidden rounded-xl border border-[#cfd7e7] dark:border-[#2a3447] bg-white dark:bg-[#1a202c] shadow-sm">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[#e7ebf3] dark:divide-[#2a3447]">
        <thead class="bg-[#f8f9fc] dark:bg-[#2d3748]">
        <tr>
        <th class="px-6 py-4 text-left text-xs font-bold text-[#4c669a] uppercase tracking-wider" scope="col">College Name</th>
        <th class="px-6 py-4 text-left text-xs font-bold text-[#4c669a] uppercase tracking-wider" scope="col">Code</th>
        <th class="px-6 py-4 text-left text-xs font-bold text-[#4c669a] uppercase tracking-wider" scope="col">Accreditation Officer</th>
        <th class="px-6 py-4 text-left text-xs font-bold text-[#4c669a] uppercase tracking-wider" scope="col">Status</th>
        <th class="px-6 py-4 text-right text-xs font-bold text-[#4c669a] uppercase tracking-wider" scope="col">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-[#e7ebf3] dark:divide-[#2a3447]">
        <!-- Row 1 -->
        <tr class="hover:bg-[#f8f9fc] dark:hover:bg-[#252f3f] transition-colors">
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">CEAT</div>
        </div>
        <div class="ml-4">
        <div class="text-sm font-medium text-[#0d121b] dark:text-white">College of Engineering and Agro-Industrial Technology</div>
        <div class="text-xs text-[#4c669a]">Main Campus</div>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#0d121b] dark:text-white font-mono">CEAT-01</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-[#e7ebf3] dark:bg-[#4a5568] flex items-center justify-center overflow-hidden mr-3">
        <span class="material-symbols-outlined text-gray-500 dark:text-gray-300 text-lg">person</span>
        </div>
        <div class="text-sm text-[#0d121b] dark:text-white">Engr. Juan Dela Cruz</div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    Active
                                                </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end gap-2">
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="View Details">
        <span class="material-symbols-outlined">visibility</span>
        </button>
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="Edit Officer">
        <span class="material-symbols-outlined">edit</span>
        </button>
        </div>
        </td>
        </tr>
        <!-- Row 2 (Vacant) -->
        <tr class="hover:bg-[#f8f9fc] dark:hover:bg-[#252f3f] transition-colors bg-orange-50/50 dark:bg-orange-900/10">
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-300 font-bold text-sm">CON</div>
        </div>
        <div class="ml-4">
        <div class="text-sm font-medium text-[#0d121b] dark:text-white">College of Nursing</div>
        <div class="text-xs text-[#4c669a]">Main Campus</div>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#0d121b] dark:text-white font-mono">CON-02</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#4c669a] italic">-- Not Assigned --</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 animate-pulse">
                                                    Vacant
                                                </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <button class="inline-flex items-center gap-1 bg-primary/10 hover:bg-primary/20 text-primary dark:text-blue-300 px-3 py-1.5 rounded-md text-xs font-bold transition-colors">
        <span class="material-symbols-outlined text-[16px]">person_add</span>
                                                    Assign
                                                </button>
        </td>
        </tr>
        <!-- Row 3 -->
        <tr class="hover:bg-[#f8f9fc] dark:hover:bg-[#252f3f] transition-colors">
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">CAS</div>
        </div>
        <div class="ml-4">
        <div class="text-sm font-medium text-[#0d121b] dark:text-white">College of Arts and Sciences</div>
        <div class="text-xs text-[#4c669a]">Main Campus</div>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#0d121b] dark:text-white font-mono">CAS-03</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-[#e7ebf3] dark:bg-[#4a5568] flex items-center justify-center overflow-hidden mr-3">
        <span class="material-symbols-outlined text-gray-500 dark:text-gray-300 text-lg">person</span>
        </div>
        <div class="text-sm text-[#0d121b] dark:text-white">Maria Santos</div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    Active
                                                </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end gap-2">
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="View Details">
        <span class="material-symbols-outlined">visibility</span>
        </button>
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="Edit Officer">
        <span class="material-symbols-outlined">edit</span>
        </button>
        </div>
        </td>
        </tr>
        <!-- Row 4 -->
        <tr class="hover:bg-[#f8f9fc] dark:hover:bg-[#252f3f] transition-colors">
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">CEMDS</div>
        </div>
        <div class="ml-4">
        <div class="text-sm font-medium text-[#0d121b] dark:text-white">College of Economics, Management and Development Studies</div>
        <div class="text-xs text-[#4c669a]">Main Campus</div>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#0d121b] dark:text-white font-mono">CEMDS-04</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="h-8 w-8 rounded-full bg-[#e7ebf3] dark:bg-[#4a5568] flex items-center justify-center overflow-hidden mr-3">
        <span class="material-symbols-outlined text-gray-500 dark:text-gray-300 text-lg">person</span>
        </div>
        <div class="text-sm text-[#0d121b] dark:text-white">Dr. Rina Reyes</div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    Active
                                                </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end gap-2">
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="View Details">
        <span class="material-symbols-outlined">visibility</span>
        </button>
        <button class="text-[#4c669a] hover:text-primary transition-colors" title="Edit Officer">
        <span class="material-symbols-outlined">edit</span>
        </button>
        </div>
        </td>
        </tr>
        <!-- Row 5 (Vacant) -->
        <tr class="hover:bg-[#f8f9fc] dark:hover:bg-[#252f3f] transition-colors bg-orange-50/50 dark:bg-orange-900/10">
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
        <div class="h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-300 font-bold text-sm">CCJ</div>
        </div>
        <div class="ml-4">
        <div class="text-sm font-medium text-[#0d121b] dark:text-white">College of Criminal Justice</div>
        <div class="text-xs text-[#4c669a]">Main Campus</div>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#0d121b] dark:text-white font-mono">CCJ-05</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-[#4c669a] italic">-- Not Assigned --</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 animate-pulse">
                                                    Vacant
                                                </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <button class="inline-flex items-center gap-1 bg-primary/10 hover:bg-primary/20 text-primary dark:text-blue-300 px-3 py-1.5 rounded-md text-xs font-bold transition-colors">
        <span class="material-symbols-outlined text-[16px]">person_add</span>
                                                    Assign
                                                </button>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="bg-white dark:bg-[#1a202c] px-4 py-3 border-t border-[#e7ebf3] dark:border-[#2a3447] flex items-center justify-between sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
        <p class="text-sm text-[#4c669a]">
                                            Showing <span class="font-medium text-[#0d121b] dark:text-white">1</span> to <span class="font-medium text-[#0d121b] dark:text-white">5</span> of <span class="font-medium text-[#0d121b] dark:text-white">8</span> results
                                        </p>
        </div>
        <div>
        <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
        <a class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-[#cfd7e7] dark:border-[#4a5568] bg-white dark:bg-[#1a202c] text-sm font-medium text-[#4c669a] hover:bg-[#f8f9fc] dark:hover:bg-[#2d3748]" href="#">
        <span class="sr-only">Previous</span>
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>
        <a aria-current="page" class="z-10 bg-primary/10 border-primary text-primary relative inline-flex items-center px-4 py-2 border text-sm font-bold" href="#">1</a>
        <a class="bg-white dark:bg-[#1a202c] border-[#cfd7e7] dark:border-[#4a5568] text-[#4c669a] hover:bg-[#f8f9fc] dark:hover:bg-[#2d3748] relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">2</a>
        <a class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-[#cfd7e7] dark:border-[#4a5568] bg-white dark:bg-[#1a202c] text-sm font-medium text-[#4c669a] hover:bg-[#f8f9fc] dark:hover:bg-[#2d3748]" href="#">
        <span class="sr-only">Next</span>
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </a>
        </nav>
        </div>
        </div>
        </div>
        </div>
        </div>
        <!-- Footer -->
        <footer class="mt-8 border-t border-[#e7ebf3] dark:border-[#2a3447] py-6 px-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-[#4c669a] text-sm">
        <p>© 2024 CvSU Accreditation Archives System. All rights reserved.</p>
        <div class="flex gap-4">
        <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
        <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
        <a class="hover:text-primary transition-colors" href="#">Support</a>
        </div>
        </div>
        </footer>
    </main>
</template>