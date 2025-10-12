<script setup>
import { useForm } from '@inertiajs/vue3'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import ToastAlert from '@/components/ToastAlert.vue'
import InfoAlert from '@/components/InfoAlert.vue'


const form = useForm({
    branch: '',
    owner: '',
})

useFlashToast()

function submit() {
    form.post('/admin/branch/store', {
        onSuccess: () => form.reset(),
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
            <span>CREATE BRANCH</span>
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <InfoAlert class="mb-5" />
                <div class="flex flex-wrap items-center gap-1 mb-5">
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Branch <span
                                class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.branch" class="w-full input" placeholder="Enter branch name">
                    </fieldset>
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Owner <span
                                class="label text-xs text-red-500">Required</span>
                        </legend>
                        <input type="text" v-model="form.owner" class="w-full input" placeholder="Enter branch owner">
                    </fieldset>
                </div>

                <button type="submit" class="btn btn-block btn-primary" :disabled="form.processing">Create
                    Branch</button>
            </form>
        </template>
    </Card>
</template>