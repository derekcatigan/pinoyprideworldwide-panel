<script setup>
import { computed, ref } from "vue"
import { usePage, useForm } from "@inertiajs/vue3"
import { capitalize } from "@/utils/stringHelper.js"
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const user = computed(() => page.props.user ?? {})

const changePassModal = ref(null)

function openModal() {
    changePassModal.value.showModal()
}

const showPassword = ref(false)

function togglePassword() {
    showPassword.value = !showPassword.value
}

const form = useForm({
    password: '',
    password_confirmation: ''
})

const initials = computed(() => {
    const profile = user.value.profile
    if (!profile) return '?'
    return (
        (profile.first_name?.charAt(0) || '') +
        (profile.last_name?.charAt(0) || '')
    ).toUpperCase()
})

const formattedBirthdate = computed(() => {
    const birthdate = user.value.profile?.birthdate
    if (!birthdate) return 'N/A'

    const date = new Date(birthdate)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
})

const age = computed(() => {
    const birthdate = user.value.profile?.birthdate
    if (!birthdate) return null

    const birth = new Date(birthdate)
    const today = new Date()

    let years = today.getFullYear() - birth.getFullYear()
    const hasHadBirthday =
        today.getMonth() > birth.getMonth() ||
        (today.getMonth() === birth.getMonth() && today.getDate() >= birth.getDate())

    if (!hasHadBirthday) years--

    return years
})

const formattedAddress = computed(() => {
    const addr = user.value.profile?.address
    if (!addr) return 'N/A'

    const parts = [
        addr.street,
        addr.barangay,
        addr.city,
        addr.province,
        addr.country
    ].filter(Boolean)

    return parts.join(', ')
})

useFlashToast()

function submitChangePassword() {
    form.post(route('profile.change-password'), {
        onSuccess: () => {
            changePassModal.value.close()
            form.reset()
        },
        onError: (error) => {
            useFlashValidation(error)
        }
    })
}


defineOptions({
    layout: Layout
})
</script>

<template>
    <ToastAlert />
    <section class="max-w-3xl mx-auto space-y-6">
        <!-- Profile Header Card -->
        <div class="bg-white shadow rounded-lg p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div
                class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-3xl font-bold">
                {{ initials }}
            </div>

            <div class="text-center sm:text-left">
                <h2 class="text-2xl font-semibold mb-1">
                    {{ user.profile?.first_name }} {{ user.profile?.last_name }}
                </h2>
                <p class="text-gray-600">
                    {{ user.role_name }} — {{ user.branch?.branch_name ?? 'No Branch' }}
                </p>
            </div>
        </div>

        <!-- Information Card -->
        <div class="bg-white shadow rounded-lg p-6 relative">
            <h3 class="font-semibold text-lg mb-4 border-b pb-2">Information</h3>
            <ul class="text-gray-700 space-y-2">
                <li><strong>Email:</strong> {{ user.email }}</li>
                <li><strong>Contact:</strong> {{ user.contact }}</li>
                <li><strong>Birthdate:</strong> {{ formattedBirthdate }}</li>
                <li><strong>Age:</strong> {{ age }}</li>
                <li><strong>Gender:</strong> {{ capitalize(user.profile?.gender) }}</li>
                <li><strong>Address:</strong> {{ formattedAddress }}</li>
            </ul>

            <!-- Button aligned at bottom right -->
            <div class="mt-6 flex justify-end">
                <button @click="openModal" class="btn btn-sm btn-primary">
                    Change Password
                </button>
            </div>
        </div>
    </section>

    <!-- Change Password -->
    <dialog ref="changePassModal" class="modal">
        <div class="modal-box max-w-[1000px]">
            <div class="mb-5">
                <h2 class="font-bold text-xl">CHANGE PASSWORD</h2>
            </div>

            <!-- Contents -->
            <form @submit.prevent="submitChangePassword">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">New Password</legend>
                    <input :type="showPassword ? 'text' : 'password'" v-model="form.password" class="w-full input"
                        placeholder="Enter new password">
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Confirm Password</legend>
                    <input :type="showPassword ? 'text' : 'password'" v-model="form.password_confirmation"
                        class="w-full input" placeholder="Enter confirm password">
                </fieldset>

                <div class="flex justify-end items-center text-sm mt-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="checkbox scale-90" @click="togglePassword" />
                        <span>Show password</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-block btn-primary mt-5" :disabled="form.processing">
                    Change Password
                </button>
            </form>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn border border-gray-300">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>
