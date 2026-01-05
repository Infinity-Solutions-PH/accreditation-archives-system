<script setup>
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });
</script>

<template>
    <main class="layout-container flex h-full grow flex-col">
        <div class="px-4 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col max-w-[1200px] flex-1 w-full">
        <!-- Breadcrumbs -->
        <div class="flex flex-wrap gap-2 px-4 pt-4 pb-2">
        <a class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Home</a>
        <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
        <a class="text-[#4c669a] hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Admin</a>
        <span class="text-[#4c669a] text-sm font-medium leading-normal">/</span>
        <span class="text-[#0d121b] dark:text-white text-sm font-medium leading-normal">Activity Logs</span>
        </div>
        <!-- Page Heading -->
        <div class="flex flex-col md:flex-row justify-between gap-6 px-4 py-6">
        <div class="flex flex-col gap-2">
        <h1 class="text-[#0d121b] dark:text-white text-3xl md:text-4xl font-bold leading-tight tracking-[-0.033em]">System Activity Logs</h1>
        <p class="text-[#4c669a] dark:text-gray-400 text-base font-normal leading-normal max-w-2xl">
                                        Monitor system integrity. View a detailed history of all document uploads, role modifications, and authentication events.
                                    </p>
        </div>
        <div class="flex items-start md:items-center">
        <button class="flex items-center justify-center gap-2 overflow-hidden rounded-lg h-10 px-6 bg-primary hover:bg-blue-700 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors shadow-md shadow-blue-500/20">
        <span class="material-symbols-outlined text-[20px]">download</span>
        <span class="truncate">Export Logs</span>
        </button>
        </div>
        </div>
        <!-- Filters and Search Toolbar -->
        <div class="flex flex-col gap-4 px-4 pb-6">
        <div class="bg-white dark:bg-[#151c2b] rounded-xl p-4 shadow-sm border border-[#e7ebf3] dark:border-gray-800 flex flex-col lg:flex-row gap-4 justify-between items-center">
        <!-- Search Bar -->
        <div class="w-full lg:w-1/3">
        <label class="flex flex-col w-full h-10">
        <div class="flex w-full flex-1 items-stretch rounded-lg h-full border border-[#e7ebf3] dark:border-gray-700 bg-[#f8f9fc] dark:bg-gray-800/50 overflow-hidden focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all">
        <div class="text-[#4c669a] flex items-center justify-center pl-3 pr-2">
        <span class="material-symbols-outlined text-[20px]">search</span>
        </div>
        <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden bg-transparent text-[#0d121b] dark:text-white focus:outline-0 h-full placeholder:text-[#9ca3af] text-sm font-normal leading-normal" placeholder="Search user, action ID..."/>
        </div>
        </label>
        </div>
        <!-- Filter Chips -->
        <div class="flex flex-wrap gap-3 w-full lg:w-auto justify-start lg:justify-end">
        <div class="flex items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-500 hidden sm:block">Filters:</span>
        </div>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">calendar_today</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Last 30 Days</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">category</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Action: All</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="group flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e7ebf3] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 transition-all">
        <span class="material-symbols-outlined text-[#4c669a] group-hover:text-primary text-[18px]">admin_panel_settings</span>
        <p class="text-[#0d121b] dark:text-gray-200 text-sm font-medium leading-normal">Role: All</p>
        <span class="material-symbols-outlined text-[#4c669a] text-[18px]">expand_more</span>
        </button>
        <button class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-dashed border-[#4c669a]/40 text-[#4c669a] hover:text-primary hover:border-primary hover:bg-primary/5 transition-all" title="Reset Filters">
        <span class="material-symbols-outlined text-[20px]">restart_alt</span>
        </button>
        </div>
        </div>
        </div>
        <!-- Data Table -->
        <div class="px-4 pb-4">
        <div class="w-full overflow-hidden rounded-xl border border-[#e7ebf3] dark:border-gray-800 bg-white dark:bg-[#151c2b] shadow-sm">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
        <thead>
        <tr class="border-b border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/50">
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 cursor-pointer hover:text-primary group select-none">
        <div class="flex items-center gap-1">
                                                            Timestamp
                                                            <span class="material-symbols-outlined text-[16px] opacity-0 group-hover:opacity-100 transition-opacity">arrow_downward</span>
        </div>
        </th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">User</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">Role</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400">Action Type</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 w-1/3">Description</th>
        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#4c669a] dark:text-gray-400 text-right">Details</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-[#e7ebf3] dark:divide-gray-800 text-sm">
        <!-- Row 1 -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">Oct 24, 2023</span>
        <span class="text-xs">10:45 AM</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center" data-alt="Portrait of Juan Dela Cruz" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA3rZC5PqZvA9f-M9Fw3jrjS96raKn6v4bewreECTGelfaAKyACKYBo9ie2REInYwbfLu3Vy8bpJJr8GDukNIbqs9ChZywHRDoO0pCxhpsEM0FHNm26A_n8V-6fqlo0nxNzmzkWmwSPQlQFYnZCzDVIDDzWl6CZNn4VZktgAu1wvN9bdf836z_eDkEzxZSvvg6-VkcdXG_tMeO0f_-m67UN8BitGM6YXj4skiKpWLhdiK3DMeCzpShPyj8Z5qKjZ6EfeHuGdtMMYUCN");'></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">Juan Dela Cruz</p>
        <p class="text-xs text-[#4c669a]">juan.delacruz@cvsu.edu.ph</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10">Admin</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20">
        <span class="size-1.5 rounded-full bg-amber-500"></span>
                                                            User Role Change
                                                        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
                                                        Changed role of <span class="font-medium">Maria Clara</span> from 'Viewer' to 'Editor'.
                                                    </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        <!-- Row 2 -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">Oct 24, 2023</span>
        <span class="text-xs">10:30 AM</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center" data-alt="Portrait of Maria Clara" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBfic3e3QtgAMbdEoZUDanEakKEXYeKbvZbt2Aa6U9d6CM8AH8Q5fAJot654WqYhWOPQiHnoHV0SfZx2bQ4HLAL8tlQdO-wge4WoMj_hnXBkJGJycPk_YY55vRP2WfxMipaP7B7n2vJTFfHpuJ37yvGRcBSU9hwScTzCiZvCPQeUqGv4XNOOH_f9zT1cr74UL7JpiLayHh-nKmBYWK5PXBozUDgHFHvb4K3HW1N4-Q3vT5L_IOLJTUaxBvHaln5Cpdt-JmBhDp7g_f7");'></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">Maria Clara</p>
        <p class="text-xs text-[#4c669a]">maria.clara@cvsu.edu.ph</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10">Faculty</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-600/20">
        <span class="size-1.5 rounded-full bg-green-500"></span>
                                                            File Upload
                                                        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
                                                        Uploaded document <a class="text-primary hover:underline font-medium" href="#">BSCS Level 3 Accreditation.pdf</a> to Archives.
                                                    </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        <!-- Row 3 -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">Oct 24, 2023</span>
        <span class="text-xs">09:15 AM</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center" data-alt="Portrait of Jose Rizal" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCsU2x7maCN6U2MnS6IKgs0cgx4Xcym_dHrnVeXAOSoKp1ZFmdP2BjSY8vuTbAL6KN6qB4V-tlE2SHTY8N5GXMKiaEL0TOkl50VbBwHhsX_y_RBmaqxPDCLaKh0AuKY16oWujlGC5Rhc05_vWzmAxAM29tQixtBhTVqEvQBGIbst3icvvYWGHvnK4FMHdHYyeJidS29TdQvae6OP2plXS1t_OpmgN6bW-KyIU7pFw7VD24vCUNeTpRtl563N9IIqPuE6867BVe7X3vD");'></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">Jose Rizal</p>
        <p class="text-xs text-[#4c669a]">jose.rizal@cvsu.edu.ph</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10">Staff</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-600/20">
        <span class="size-1.5 rounded-full bg-blue-500"></span>
                                                            System Login
                                                        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
                                                        Successful login via Web Portal from IP 192.168.1.15.
                                                    </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        <!-- Row 4 -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">Oct 23, 2023</span>
        <span class="text-xs">04:55 PM</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center" data-alt="Portrait of Andres Bonifacio" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAvXHrT-7yt35t7zEhPTOK241mxjYHaKyymaginc0XQJrfqJly3uFB7H37pxwYUIpogUCkO5h-TKxmXFTL3i-ujDmxtJTV-3_mTDvIbvWE_gwRMAyEQDZhlUztXi3r4Ca9_oiF59q0dua0wxjZwiwKk5v6mQGUDfjcf5tzq0QVOt90rHlgKPBaRshi6TtMsv5spWDJTdHbwHLvvm7z2X2HssHXBA2YWXFKw_NQAtmMdO9snmjz3ZVSKeEBQAD-nwy3qRUSfv-S-gl1W");'></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">Andres Bonifacio</p>
        <p class="text-xs text-[#4c669a]">andres.b@cvsu.edu.ph</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10">Admin</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/20">
        <span class="size-1.5 rounded-full bg-red-500"></span>
                                                            File Delete
                                                        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
                                                        Permanently deleted "Obsolete Syllabus 2020.pdf".
                                                    </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        <!-- Row 5 -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap text-[#4c669a] dark:text-gray-400">
        <div class="flex flex-col">
        <span class="font-medium text-[#0d121b] dark:text-gray-200">Oct 23, 2023</span>
        <span class="text-xs">02:10 PM</span>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
        <div class="size-8 rounded-full bg-cover bg-center" data-alt="Portrait of Apolinario Mabini" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAI-hhK7ayBwT5P6EewE5LFV7kIwiN7toGHHhPnr95loeH8-7bdy64yKvZeNn4QI2dK3XF0aPrcjxL89kEFweJhhfZDcRSIHK8HEiKK7gpo7lXeMdY52mWmKCR_vjmAZYiHSo-xXibWwxYrEBl1oOW7bXiu1lBqpyySd-zg23oNwpsK5GaaScNoB9c33iVWOFZRO_P4YHaMuDjWsVjRD8aix8RuCZ1xrClT5G7W-iuaeVh6lV6P-d6HAtsBfP1K3pwDxzfT2qMOX3xO");'></div>
        <div>
        <p class="font-medium text-[#0d121b] dark:text-white">Apolinario Mabini</p>
        <p class="text-xs text-[#4c669a]">apolinario.m@cvsu.edu.ph</p>
        </div>
        </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10">Faculty</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10">
        <span class="size-1.5 rounded-full bg-gray-500"></span>
                                                            File Download
                                                        </span>
        </td>
        <td class="px-6 py-4 text-[#0d121b] dark:text-gray-300">
                                                        Downloaded "Faculty Roster 2023.xlsx".
                                                    </td>
        <td class="px-6 py-4 whitespace-nowrap text-right">
        <button class="text-[#4c669a] hover:text-primary transition-colors">
        <span class="material-symbols-outlined">chevron_right</span>
        </button>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="flex items-center justify-between border-t border-[#e7ebf3] dark:border-gray-800 bg-[#f8f9fc] dark:bg-gray-800/30 px-6 py-3">
        <div class="hidden sm:flex flex-1 items-center justify-between">
        <div>
        <p class="text-sm text-[#4c669a] dark:text-gray-400">
                                                    Showing <span class="font-medium text-[#0d121b] dark:text-white">1</span> to <span class="font-medium text-[#0d121b] dark:text-white">5</span> of <span class="font-medium text-[#0d121b] dark:text-white">128</span> results
                                                </p>
        </div>
        <div>
        <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
        <a class="relative inline-flex items-center rounded-l-md px-2 py-2 text-[#4c669a] ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0" href="#">
        <span class="sr-only">Previous</span>
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>
        <a aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" href="#">1</a>
        <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#0d121b] dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0" href="#">2</a>
        <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#0d121b] dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0" href="#">3</a>
        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#4c669a] ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-offset-0">...</span>
        <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#0d121b] dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0" href="#">8</a>
        <a class="relative inline-flex items-center rounded-r-md px-2 py-2 text-[#4c669a] ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0" href="#">
        <span class="sr-only">Next</span>
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </a>
        </nav>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
    </main>
</template>