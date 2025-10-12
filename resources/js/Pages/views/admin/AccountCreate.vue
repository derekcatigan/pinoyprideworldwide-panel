<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { usePhoneFormatter } from '@/composables/usePhoneFormatter.js'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast'
import { countries } from '@/data/countries.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import ToastAlert from '@/components/ToastAlert.vue'
import InfoAlert from '@/components/InfoAlert.vue'

const page = usePage()
const branches = page.props.branches ?? []
const roles = page.props.roles ?? []

const form = useForm({
    email: '',
    contact: '',
    role: '',
    branch_id: '',
    password: '',
    password_confirmation: '',
    first_name: '',
    last_name: '',
    gender: '',
    birthdate: '',
    street: '',
    barangay: '',
    city: '',
    province: '',
    country: ''
})

const contact = ref('PH')

usePhoneFormatter(
    {
        get: () => form.contact,
        set: (val) => form.contact = val
    },
    contact
)

useFlashToast()

function submit() {
    form.post('/admin/account/store', {
        onSuccess: () => {
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
    <Card>
        <template #title>
            <span>CREATE ACCOUNT</span>
        </template>

        <template #content>
            <InfoAlert />
            <form @submit.prevent="submit">
                <!-- Section 1 -->
                <div class="divider"><span>Account Information</span></div>

                <div class="flex flex-wrap items-center gap-1">
                    <!-- Email -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Email
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="email" v-model="form.email" class="w-full input" placeholder="Enter email" />
                    </fieldset>

                    <!-- Contact -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Contact
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <div class="flex gap-2">
                            <select v-model="contact" class="select w-32">
                                <option value="PH">PH</option>
                                <option value="US">US</option>
                            </select>
                            <input type="text" v-model="form.contact" class="w-full input"
                                placeholder="Enter contact" />
                        </div>
                    </fieldset>

                    <!-- Role -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Role
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <select v-model="form.role" class="w-full select">
                            <option disabled value="">Select a role</option>
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.name }}
                            </option>
                        </select>
                    </fieldset>
                </div>

                <div class="flex flex-wrap items-center gap-1">
                    <!-- Assign Branch -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Assigned Branch
                            <span class="label text-xs">Optional</span>
                        </legend>
                        <select v-model="form.branch_id" class="w-full select">
                            <option value="">Select a branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                Pinoy Pride Worldwide - {{ branch.branch_name }}
                            </option>
                        </select>
                    </fieldset>

                    <!-- Password -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Password
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="password" v-model="form.password" class="w-full input"
                            placeholder="Enter password">
                    </fieldset>

                    <!-- Confirm Password -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Confirm Password
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="password" v-model="form.password_confirmation" class="w-full input"
                            placeholder="Enter confirm password">
                    </fieldset>
                </div>

                <div class="divider">User Information Details</div>

                <div class="flex flex-wrap items-center gap-1">
                    <!-- First Name -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            First Name
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.first_name" class="w-full input"
                            placeholder="Enter first name">
                    </fieldset>
                    <!-- Last Name -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Last Name
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.last_name" class="w-full input" placeholder="Enter last name">
                    </fieldset>
                </div>

                <div class="flex flex-wrap items-center gap-1">
                    <!-- Gender -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Gender
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <select v-model="form.gender" class="w-full select">
                            <option disabled value="">Select sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </fieldset>
                    <!-- Birthdate -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Birthdate
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="date" v-model="form.birthdate" class="w-full input">
                    </fieldset>
                </div>

                <div class="divider">User Address Details</div>

                <div class="flex flex-wrap items-center gap-1">
                    <!-- Street -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Street
                            <span class="label text-xs">Optional</span>
                        </legend>
                        <input type="text" v-model="form.street" class="w-full input" placeholder="Enter street">
                    </fieldset>
                    <!-- Barangay -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Barangay
                            <span class="label text-xs">Optional</span>
                        </legend>
                        <input type="text" v-model="form.barangay" class="w-full input" placeholder="Enter barangay">
                    </fieldset>
                    <!-- City -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            City
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.city" class="w-full input" placeholder="Enter city">
                    </fieldset>
                </div>
                <div class="flex flex-wrap items-center gap-1">
                    <!-- Province/State -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Province/State
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.province" class="w-full input"
                            placeholder="Enter province/street">
                    </fieldset>
                    <!-- Country -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">
                            Country
                            <span class="label text-xs text-red-500">Required</span>
                        </legend>
                        <select v-model="form.country" class="w-full select">
                            <option disabled value="">Select country</option>
                            <option v-for="c in countries" :key="c.code" :value="c.name">
                                {{ c.name }}
                            </option>
                        </select>
                    </fieldset>
                </div>

                <button type="submit" class="btn btn-block btn-primary mt-10" :disabled="form.processing">Create
                    Account</button>
            </form>
        </template>
    </Card>
</template>