<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { sidebarLinks } from '@/data/sidebarLinks.js'

const page = usePage()
const user = page.props.auth.user;
const currentLink = computed(() => page.url);
</script>

<template>
    <div v-if="user?.role && sidebarLinks[user.role]" class="min-h-full p-2">
        <ul v-for="group in sidebarLinks[user.role]" :key="group.title" class="flex flex-col gap-1 mb-1 text-gray-300">
            <span class="text-sm text-gray-500 font-semibold">{{ group.title }}</span>
            <li v-for="item in group.items" :key="item.label">
                <Link :href="item.link" :class="[
                    'flex items-center gap-2 p-2 rounded',
                    currentLink === item.link
                        ? 'bg-gray-200 border border-gray-300 text-black font-medium shadow-lg rounded'
                        : 'hover:bg-gray-200 hover:font-medium hover:text-black'
                ]">
                <i :class="item.icon"></i>
                <span>{{ item.label }}</span>
                </Link>
            </li>
        </ul>
    </div>
</template>
