<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import Logo from '@/assets/logos/PinoyPrideLogo.png'

const page = usePage()
const user = page.props.auth.user


const roleLabels = {
    admin: 'Admin',
    branch_admin: 'Branch Admin',
    agent: 'Agent',
}

const initials = computed(() => {
    if (!user?.profile) return '?'
    return (
        (user.profile.first_name?.charAt(0) || '') +
        (user.profile.last_name?.charAt(0) || '')
    ).toUpperCase()
})

const emit = defineEmits(['toggle-sidebar'])

function logout() {
    router.post('/logout')
}
</script>

<template>
    <!-- Logo Section -->
    <div class="flex items-center gap-2">
        <button class="btn btn-square btn-ghost" @click="emit('toggle-sidebar')"><i
                class='bx  bx-sidebar text-2xl'></i></button>
        <img :src="Logo" alt="Logo" class="w-24 h-24 object-contain">
    </div>
    <div class="flex items-center gap-3">
        <!-- User Info -->
        <div class="flex flex-col text-right">
            <span class="font-semibold text-sm">
                {{ user.profile.first_name }} {{ user.profile.last_name }}
            </span>
            <span class="text-xs text-gray-500">
                {{ roleLabels[user.role] }}
            </span>
        </div>

        <!-- User Avatar -->
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-square bg-blue-700 text-white font-bold">
                {{ initials }}
            </div>
            <ul tabindex="0"
                class="dropdown-content menu bg-base-100 border border-gray-300 rounded-box z-1 w-52 p-2 shadow-sm">
                <li>
                    <Link>Profile</Link>
                </li>
                <li class="mb-1">
                    <Link>Settings</Link>
                </li>
                <li>
                    <button class="btn btn-block btn-sm btn-error" @click="logout">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</template>
