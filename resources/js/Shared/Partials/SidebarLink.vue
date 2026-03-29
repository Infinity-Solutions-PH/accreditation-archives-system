<script setup>
    import { computed } from 'vue'
    import { usePage } from '@inertiajs/vue3'

    const props = defineProps({
        href: {
            type: String,
            required: true,
        }
    })

    const page = usePage();

    const isActive = computed(() => {
        // If the href has query parameters, we want a more precise match
        if (props.href.includes('?')) {
            return page.url === new URL(props.href, window.location.origin).search.replace('?', '') 
                ? page.url.includes(new URL(props.href, window.location.origin).search)
                : page.url.startsWith(props.href.replace(window.location.origin, ''));
        }
        
        const url = new URL(props.href, window.location.origin);
        return page.url.startsWith(url.pathname) && !page.url.includes('?');
    })
</script>

<template>
    <Link
        :href="href"
        :class="[
            isActive
                ? 'bg-primary text-white shadow-md'
                : 'text-text-muted-light dark:text-text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary',
            'group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors'
        ]"
    >
        <!-- Icon -->
        <span class="material-symbols-outlined text-[24px]">
            <slot name="icon" />
        </span>

        <!-- Label -->
        <p class="text-sm font-medium leading-normal">
            <slot name="label" />
        </p>
    </Link>
</template>