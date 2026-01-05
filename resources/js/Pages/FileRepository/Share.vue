<script setup>

</script>

<template>
    <main class="flex-1 w-full max-w-[1280px] mx-auto px-4 md:px-10 py-8">
<!-- Breadcrumbs -->
<div class="flex flex-wrap gap-2 mb-6 text-sm">
<a class="text-slate-500 dark:text-slate-400 hover:text-primary font-medium transition-colors" href="#">Home</a>
<span class="text-slate-400 dark:text-slate-600">/</span>
<a class="text-slate-500 dark:text-slate-400 hover:text-primary font-medium transition-colors" href="#">Accreditation</a>
<span class="text-slate-400 dark:text-slate-600">/</span>
<a class="text-slate-500 dark:text-slate-400 hover:text-primary font-medium transition-colors" href="#">Level 3</a>
<span class="text-slate-400 dark:text-slate-600">/</span>
<span class="text-slate-900 dark:text-white font-medium">Share</span>
</div>
<!-- Page Heading -->
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
<div class="flex flex-col gap-2">
<h1 class="text-slate-900 dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-tight">Share Documents</h1>
<p class="text-slate-500 dark:text-slate-400 text-base">Securely distribute accreditation files to faculty and accreditation bodies.</p>
</div>
<div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-full border border-blue-100 dark:border-blue-800">
<span class="material-symbols-outlined text-primary text-[18px]">lock</span>
<span>End-to-end encrypted</span>
</div>
</div>
<!-- Main Content Split -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<!-- Left Column: Selected Files (4 columns) -->
<div class="lg:col-span-4 flex flex-col gap-4">
<div class="flex justify-between items-center">
<h3 class="text-slate-900 dark:text-white text-lg font-bold">Selected Files (2)</h3>
<button class="text-primary text-sm font-semibold hover:underline">Add more</button>
</div>
<!-- File Item 1 -->
<div class="group flex items-center gap-3 bg-surface-light dark:bg-surface-dark p-3 rounded-lg border border-border-light dark:border-border-dark shadow-sm hover:border-primary/50 transition-all">
<div class="flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 shrink-0 size-10">
<span class="material-symbols-outlined">picture_as_pdf</span>
</div>
<div class="flex-1 min-w-0">
<p class="text-slate-900 dark:text-white text-sm font-medium truncate">Curriculum_Vitae_2023.pdf</p>
<p class="text-slate-500 dark:text-slate-400 text-xs">2.4 MB • Uploaded today</p>
</div>
<button class="text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- File Item 2 -->
<div class="group flex items-center gap-3 bg-surface-light dark:bg-surface-dark p-3 rounded-lg border border-border-light dark:border-border-dark shadow-sm hover:border-primary/50 transition-all">
<div class="flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 shrink-0 size-10">
<span class="material-symbols-outlined">description</span>
</div>
<div class="flex-1 min-w-0">
<p class="text-slate-900 dark:text-white text-sm font-medium truncate">Extension_Activities_Report.docx</p>
<p class="text-slate-500 dark:text-slate-400 text-xs">1.8 MB • Modified yesterday</p>
</div>
<button class="text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Dropzone Placeholder -->
<div class="border-2 border-dashed border-border-light dark:border-border-dark rounded-xl p-6 flex flex-col items-center justify-center text-center bg-background-light dark:bg-surface-dark/50 hover:bg-slate-50 dark:hover:bg-surface-dark transition-colors cursor-pointer">
<span class="material-symbols-outlined text-slate-400 text-4xl mb-2">cloud_upload</span>
<p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Drag additional files here</p>
<p class="text-xs text-slate-400">or click to browse</p>
</div>
</div>
<!-- Right Column: Sharing Configuration (8 columns) -->
<div class="lg:col-span-8">
<div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm p-6 lg:p-8">
<h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Configuration</h3>
<div class="space-y-6">
<!-- Recipients Input -->
<div class="space-y-2">
<label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Add People or Groups</label>
<div class="relative">
<span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[20px]">person_add</span>
<input class="w-full pl-10 pr-4 py-3 rounded-lg border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent placeholder-slate-400" placeholder="Enter email, name, or department..." type="text"/>
<div class="absolute right-2 top-2 p-1 bg-slate-200 dark:bg-slate-700 rounded text-xs text-slate-600 dark:text-slate-300 font-mono">CMD + K</div>
</div>
<!-- Selected Recipients Tags -->
<div class="flex flex-wrap gap-2 mt-3">
<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
<span class="material-symbols-outlined text-[16px]">group</span>
                                    Quality Assurance Dept.
                                    <button class="hover:text-red-500"><span class="material-symbols-outlined text-[16px]">close</span></button>
</span>
<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
<div class="size-5 rounded-full bg-indigo-500 text-white flex items-center justify-center text-[10px]">JD</div>
                                    Juan Dela Cruz
                                    <button class="hover:text-red-500"><span class="material-symbols-outlined text-[16px]">close</span></button>
</span>
</div>
</div>
<div class="h-px bg-border-light dark:bg-border-dark w-full"></div>
<!-- Permissions & Settings -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Permission Level -->
<div class="space-y-2">
<label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Permission Level</label>
<div class="relative">
<select class="w-full pl-10 pr-10 py-2.5 rounded-lg border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary appearance-none cursor-pointer">
<option>View Only (Restricted)</option>
<option>Can Comment</option>
<option>Can Edit</option>
<option>Co-Owner</option>
</select>
<span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">visibility</span>
<span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px] pointer-events-none">expand_more</span>
</div>
<p class="text-xs text-slate-500 dark:text-slate-400">Viewers cannot download or print files.</p>
</div>
<!-- Expiry Settings -->
<div class="space-y-2">
<label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Link Expiry (Optional)</label>
<div class="relative">
<input class="w-full pl-10 pr-4 py-2.5 rounded-lg border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary placeholder-slate-400" type="date"/>
<span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">calendar_clock</span>
</div>
</div>
</div>
<!-- Message -->
<div class="space-y-2">
<label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Message (Optional)</label>
<textarea class="w-full px-4 py-3 rounded-lg border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary placeholder-slate-400 min-h-[100px]" placeholder="Add a note to the recipients explaining the contents..."></textarea>
</div>
<!-- Options Checkboxes -->
<div class="flex flex-col sm:flex-row gap-4 pt-2">
<label class="inline-flex items-center cursor-pointer group">
<input checked="" class="rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
<span class="ml-2 text-sm text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">Notify people via email</span>
</label>
<label class="inline-flex items-center cursor-pointer group">
<input class="rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-700 dark:border-slate-600" type="checkbox"/>
<span class="ml-2 text-sm text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">Require log-in to access</span>
</label>
</div>
<!-- Footer Buttons -->
<div class="flex items-center justify-end gap-3 pt-4 border-t border-border-light dark:border-border-dark mt-6">
<button class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Cancel</button>
<button class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-dark shadow-lg shadow-blue-500/20 flex items-center gap-2 transition-all active:scale-95">
<span class="material-symbols-outlined text-[18px]">send</span>
                                Share Files
                            </button>
</div>
</div>
</div>
</div>
</div>
<!-- Recent Shares Section -->
<div class="mt-12">
<h3 class="text-slate-900 dark:text-white text-xl font-bold leading-tight tracking-[-0.015em] mb-4">Recent Sharing Activity</h3>
<div class="overflow-x-auto rounded-xl border border-border-light dark:border-border-dark shadow-sm bg-surface-light dark:bg-surface-dark">
<table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
<thead class="bg-background-light dark:bg-slate-800/50 text-slate-900 dark:text-white uppercase font-semibold text-xs border-b border-border-light dark:border-border-dark">
<tr>
<th class="px-6 py-4">File Name</th>
<th class="px-6 py-4">Shared With</th>
<th class="px-6 py-4">Date</th>
<th class="px-6 py-4">Permissions</th>
<th class="px-6 py-4 text-right">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-border-light dark:divide-border-dark">
<!-- Row 1 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white flex items-center gap-3">
<span class="material-symbols-outlined text-blue-500">description</span>
                                Accreditation_Area_IV_Draft.docx
                            </td>
<td class="px-6 py-4">
<div class="flex -space-x-2">
<img alt="User 1" class="w-8 h-8 rounded-full border-2 border-white dark:border-surface-dark object-cover" data-alt="Small avatar of a female user" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAHXLETR-OPHRuGbK_uZ3dwTu4Zp_ieJ4Haz91rHRzSRGgeHfivoHTvC_ncuXENMqs70ohQ4AgnbLovjjwd_g9L_0_NLDqx9LxqCEyZUhdPXoGJAB8nuWoHaqnj10yFv0wj9IJxjuyQH50_2RgKhCn8OaMfdx1753lvVRg9aYwXk0s-eluGMJKRUCbEW3bx4Ww2b5uKADCnIWGJRF9xNoqYjocmKszccq9QqYlQITimSbO9c6lBGXytPnGP_BYEsIAEbu090yWZjZbJ"/>
<img alt="User 2" class="w-8 h-8 rounded-full border-2 border-white dark:border-surface-dark object-cover" data-alt="Small avatar of a male user" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUCFg07WtdZeWqao32hKq8Xwcobf2o-cCZS3NRgxlXFH_9OloXa9raJ6XA0Jlhmcd8W_tiO2cxtW33evoR5Dbub7Tblv2-oiUaqvyutPeFdFXNBPyF7hhUz1QB6WXFcevRZavJLTxpcamJz4npHu95TOrWEvacjuyA1LY-3koklQyHSfI2Omdk5HFZWPksNKzwCIWG3kL2KTiZkGHTBDLLubMcfjeCICvyAF9XWjA6PHDa1JRXoTBbp5qt_sIgUmG_UdN9uyC83xkC"/>
<div class="w-8 h-8 rounded-full border-2 border-white dark:border-surface-dark bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs text-slate-600 dark:text-slate-300 font-bold">+3</div>
</div>
</td>
<td class="px-6 py-4">Oct 24, 2023</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    Editor
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-slate-400 hover:text-primary transition-colors">
<span class="material-symbols-outlined">more_vert</span>
</button>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-900 dark:text-white flex items-center gap-3">
<span class="material-symbols-outlined text-red-500">picture_as_pdf</span>
                                Faculty_Summary_Report.pdf
                            </td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="size-8 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 flex items-center justify-center text-xs font-bold border-2 border-white dark:border-surface-dark">AB</div>
<span class="text-slate-600 dark:text-slate-400">Dr. A. Bernando</span>
</div>
</td>
<td class="px-6 py-4">Oct 22, 2023</td>
<td class="px-6 py-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    Viewer
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-slate-400 hover:text-primary transition-colors">
<span class="material-symbols-outlined">more_vert</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</main>
</template>