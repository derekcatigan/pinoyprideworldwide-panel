<script setup>
import { watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import InfoAlert from '@/components/InfoAlert.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const branches = page.props.branches ?? []
const locations = page.props.locations ?? []
const boxes = page.props.boxes ?? []
const existingBoxTypes = page.props.existingBoxTypes ?? []

const form = useForm({
    boxType: '',
    length: '',
    height: '',
    width: '',
    price: '',
    location_id: '',
    branch_id: page.props.role !== 'admin' ? page.props.branch_id : ''
})

watch(() => form.boxType, (selected) => {
    if (!selected) return

    const match = existingBoxTypes.find(b => b.box_type === selected)
    if (match) {
        form.length = match.length
        form.height = match.height
        form.width = match.width
        form.price = match.price
    } else {
        form.length = ''
        form.height = ''
        form.width = ''
        form.price = ''
    }
})

useFlashToast()

function submit() {
    form.post('/box/store', {
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
            <span>CREATE BOX</span>
        </template>

        <template #content>
            <InfoAlert />
            <form @submit.prevent="submit">
                <div class="flex flex-wrap items-center gap-1">
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Box Type</legend>
                        <select v-model="form.boxType" class="w-full select">
                            <option disabled value="">Select box type</option>
                            <option v-for="box in boxes" :key="box.value" :value="box.value">
                                {{ box.name }}
                            </option>
                        </select>
                    </fieldset>

                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Branch</legend>
                        <select v-model="form.branch_id" class="w-full select">
                            <option disabled value="">Select branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_name }}
                            </option>
                        </select>
                    </fieldset>

                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Destination</legend>
                        <select v-model="form.location_id" class="w-full select">
                            <option disabled value="">Select location</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.location }}
                            </option>
                        </select>
                    </fieldset>

                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Dimension</legend>
                        <div class="join join-vertical lg:join-horizontal">
                            <input type="number" v-model="form.length" class="w-full input join-item" placeholder="0">
                            <input type="number" v-model="form.height" class="w-full input join-item" placeholder="0">
                            <input type="number" v-model="form.width" class="w-full input join-item" placeholder="0">
                        </div>
                    </fieldset>

                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Price</legend>
                        <input type="number" v-model="form.price" class="w-full input" placeholder="0" />
                    </fieldset>
                </div>

                <button type="submit" class="btn btn-block btn-primary mt-10" :disabled="form.processing">Create
                    Box</button>
            </form>
        </template>
    </Card>
</template>