<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    links: { type: Array, required: true },
    from: Number,
    to: Number,
    total: Number,
})

const currentPage = computed(() =>
    parseInt(props.links.find(l => l.active)?.label) || 1
)

const totalPages = computed(() => props.links.length - 2) // exclude prev & next

const paginationLinks = computed(() => {
    if (!props.links.length) return []

    const range = 2
    const pages = []

    for (let i = 1; i <= totalPages.value; i++) {
        if (i === 1 || i === totalPages.value || (i >= currentPage.value - range && i <= currentPage.value + range)) {
            pages.push(props.links[i])
        } else if (pages.at(-1)?.label !== '...') {
            pages.push({ label: '...', url: null })
        }
    }

    return pages
})
</script>

<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <!-- Mobile pagination -->
        <div class="flex flex-1 justify-between sm:hidden">
            <component :is="links[0]?.url ? Link : 'span'" :href="links[0]?.url"
                class="relative inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium" :class="links[0]?.url
                    ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    : 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed'">
                Previous
            </component>

            <component :is="links.at(-1)?.url ? Link : 'span'" :href="links.at(-1)?.url"
                class="relative ml-3 inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium" :class="links.at(-1)?.url
                    ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    : 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed'">
                Next
            </component>
        </div>

        <!-- Desktop pagination -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ from }}</span>
                to <span class="font-medium">{{ to }}</span>
                of <span class="font-medium">{{ total }}</span> results
            </p>

            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <!-- Previous -->
                <component :is="links[0]?.url ? Link : 'span'" :href="links[0]?.url"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300"
                    :class="links[0]?.url ? 'hover:bg-gray-50 focus:z-20' : 'bg-gray-100 cursor-not-allowed'">
                    <i class="bx bx-chevron-left"></i>
                </component>

                <!-- Page numbers -->
                <template v-for="(link, i) in paginationLinks" :key="i">
                    <span v-if="link.label === '...'" class="px-4 py-2 text-sm font-semibold text-gray-400">...</span>
                    <Link v-else :href="link.url" v-html="link.label"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20" :class="link.active
                            ? 'z-10 bg-indigo-600 text-white ring-1 ring-indigo-600 hover:bg-indigo-700'
                            : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'" />
                </template>

                <!-- Next -->
                <component :is="links.at(-1)?.url ? Link : 'span'" :href="links.at(-1)?.url"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300"
                    :class="links.at(-1)?.url ? 'hover:bg-gray-50 focus:z-20' : 'bg-gray-100 cursor-not-allowed'">
                    <i class="bx bx-chevron-right"></i>
                </component>
            </nav>
        </div>
    </div>
</template>
