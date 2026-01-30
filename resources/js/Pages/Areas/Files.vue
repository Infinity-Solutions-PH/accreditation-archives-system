<script setup>
    import AppLayout from '@shared/Layouts/App.vue';

    defineOptions({
        layout: AppLayout
    });
</script>

<template>
    <main class="flex-1 overflow-y-auto scroll-smooth w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex mb-6">
            <ol class="flex items-center space-x-2">
                <li>
                    <a class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium" href="#">Home</a>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <a class="text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium" href="#">Archives</a>
                </li>
                <li>
                    <span class="text-slate-400 dark:text-slate-600 text-sm">/</span>
                </li>
                <li>
                    <span class="text-slate-900 dark:text-white text-sm font-semibold">File Repository</span>
                </li>
            </ol>
        </nav>
        <!-- Page Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
        <div class="flex flex-col gap-2 max-w-2xl">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">File Repository</h1>
        <p class="text-slate-600 dark:text-slate-400 text-base">
            Manage and track accreditation documents. Monitor expiration dates to ensure compliance standards are met.
        </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
        <span class="material-symbols-outlined text-[20px]">table_view</span>
                            Export to Excel
                        </button>
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-md shadow-blue-500/20">
        <span class="material-symbols-outlined text-[20px]">upload_file</span>
                            Upload File
                        </button>
        </div>
        </div>
        <!-- Filter & Search Toolbar -->
        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
        <!-- Search -->
        <div class="w-full lg:w-96 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <span class="material-symbols-outlined text-slate-400">search</span>
        </div>
        <input class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg leading-5 bg-white dark:bg-slate-800 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm text-slate-900 dark:text-white" placeholder="Search by filename, uploader, or tag..." type="text"/>
        </div>
        <!-- Filters -->
        <div class="flex flex-wrap gap-2 w-full lg:w-auto overflow-x-auto pb-1 lg:pb-0">
        <button class="group flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-transparent dark:border-slate-700">
        <span class="text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap">Status: All</span>
        <span class="material-symbols-outlined text-slate-500 text-[18px]">expand_more</span>
        </button>
        <button class="group flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-transparent dark:border-slate-700">
        <span class="text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap">Department: All</span>
        <span class="material-symbols-outlined text-slate-500 text-[18px]">expand_more</span>
        </button>
        <button class="group flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-transparent dark:border-slate-700">
        <span class="text-slate-700 dark:text-slate-300 text-sm font-medium whitespace-nowrap">Type: All</span>
        <span class="material-symbols-outlined text-slate-500 text-[18px]">expand_more</span>
        </button>
        <div class="w-px h-8 bg-slate-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>
        <button class="p-2 text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary">
        <span class="material-symbols-outlined">refresh</span>
        </button>
        </div>
        </div>
        </div>
        <!-- Files Table -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
        <thead>
        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
        <th class="p-4 w-12 text-center" scope="col">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">File Name</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell" scope="col">Department</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell" scope="col">Uploaded By</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Expiry Date</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" scope="col">Status</th>
        <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right" scope="col">Actions</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <!-- Row 1: Active -->
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
        <span class="material-symbols-outlined">picture_as_pdf</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">ISO_9001_Certification.pdf</span>
        <span class="text-xs text-slate-500">2.4 MB • Updated 2 days ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Quality Assurance</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAyd5GytrCn8kduh_Iuz0ySh5VVrmNP9pRGZMCPzCw5qgasNtIJeBvV38fJsICfT0uXATEWKrP1qSMUXTaiHEQ8QlR55UnM8zPob4lCVCQMVGRZVHaAITVT4hDYMsn2SBAQG1hJU1-yzIM_hWYfqnjVd9KLcTp60WDFeiZjIEai35-EjfEXHTVciP8uvi348D8T_7Q-o3H1SQbjAtaRU8emjmcB_i11XzlzHfEy61ZQtfoVyE55JOhPta5juvgvhscAr4N_QxvipB-R");'>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Maria Santos</span>
        </div>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">Dec 31, 2024</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <!-- Row 2: Expired - VISUAL EMPHASIS -->
        <tr class="group bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-l-4 border-l-red-500">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
        <span class="material-symbols-outlined">description</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Faculty_Roster_2022.docx</span>
        <span class="text-xs text-slate-500">1.8 MB • Updated 1 year ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Human Resources</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                JD
                                            </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">John Doe</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-red-600 dark:text-red-400">Jan 15, 2023</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
        <span class="material-symbols-outlined text-[14px]">error</span>
                                            Expired
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <!-- Row 3: Active Excel -->
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400">
        <span class="material-symbols-outlined">table_chart</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Budget_Report_Q3.xlsx</span>
        <span class="text-xs text-slate-500">4.1 MB • Updated 5 hours ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Finance</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCCqPftJCGVtsv2aaqBryoqy7FYeoMpmXKtrAsqugr27LilYbkz4FgkilBkIa2qatEZDblkd8z3RMPkSAdFgLhf6HhHw0vQFYPo4ECYPgf7436FZkYJ5rI_IgHwdmVN-6izDRyrDjzeo_y8ctwDDTcyzphZRosqMJJd7Tk6zRM-1Ksu7AkKFTc0TtCYFAN7_fzRTI8SJk5NVtdDCRVYnhgjkrjk93UeIVy17lrNgmpGUMNdjB8qJwqWiGuCox42x3LbIh92Yaw35NF0");'>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Elena Cruz</span>
        </div>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">--</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <!-- Row 4: Expiring Soon -->
        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
        <span class="material-symbols-outlined">picture_as_pdf</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Lab_Safety_Protocols.pdf</span>
        <span class="text-xs text-slate-500">1.2 MB • Updated 1 month ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Laboratory Services</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                RR
                                            </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Rene Reyes</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-amber-600 dark:text-amber-500">Tomorrow</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
        <span class="material-symbols-outlined text-[14px]">warning</span>
                                            Expiring Soon
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        <!-- Row 5: Another Expired -->
        <tr class="group bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border-l-4 border-l-red-500">
        <td class="p-4 text-center">
        <input class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" type="checkbox"/>
        </td>
        <td class="p-4">
        <div class="flex items-center gap-3">
        <div class="size-10 shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
        <span class="material-symbols-outlined">description</span>
        </div>
        <div class="flex flex-col">
        <span class="font-medium text-slate-900 dark:text-slate-200 text-sm">Memo_Guidance_2020.docx</span>
        <span class="text-xs text-slate-500">850 KB • Updated 3 years ago</span>
        </div>
        </div>
        </td>
        <td class="p-4 hidden md:table-cell text-sm text-slate-600 dark:text-slate-400">Admin</td>
        <td class="p-4 hidden lg:table-cell">
        <div class="flex items-center gap-2">
        <div class="size-6 rounded-full bg-cover bg-center" data-alt="User avatar small" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAKp0aCH5xDVWNGnFKBQKa6a_t-kMQuwdSxwiGAsWlyViE9A6eFrhxc2CvNSms75DjPRQAmXFLxWkFAIi6KkRmUCpGMtBrnedoo5ws5WryUKOq-T5VNUbQyjyhcjveWeu12TYIUGIJADpFkwIM8GtPsbZdRQmAn2NnA1jVRKDZM4X2DOYTF6QA6s4E4Vl6eD994okqvnX4CA3xteiCb5pvP_bLZlqbzPv7T6Yys5i9xQlGMbPn2WNAu1-yGqL91rLpP9jcS4gVYFahj");'>
        </div>
        <span class="text-sm text-slate-600 dark:text-slate-400">Ana Lee</span>
        </div>
        </td>
        <td class="p-4 text-sm font-medium text-red-600 dark:text-red-400">Mar 10, 2021</td>
        <td class="p-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
        <span class="material-symbols-outlined text-[14px]">error</span>
                                            Expired
                                        </span>
        </td>
        <td class="p-4 text-right">
        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700">
        <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-surface-dark">
        <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <span>Rows per page:</span>
        <select class="form-select bg-slate-50 dark:bg-slate-800 border-none rounded text-sm py-1 pl-2 pr-6 focus:ring-1 focus:ring-primary">
        <option>10</option>
        <option>20</option>
        <option>50</option>
        </select>
        </div>
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
        <span>1-5 of 124</span>
        <div class="flex items-center gap-1">
        <button class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50" disabled="">
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>
        <button class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>
        </div>
        </div>
        </div>
        </div>
    </main>
</template>