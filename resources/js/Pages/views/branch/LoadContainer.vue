<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { capitalize } from '@/utils/stringHelper.js'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'
import { toast } from 'vue-sonner'
// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import DataTable from '@/components/DataTable.vue'
import CardTable from '@/components/CardTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const orders = computed(() => page.props.orders ?? [])
const containers = computed(() => page.props.containers ?? [])

const selectedOrderBoxes = ref([])

const form = useForm({
    container: '',
    orderBoxes: [],
})

useFlashToast()

function toggleSelection(orderBoxId) {
    if (selectedOrderBoxes.value.includes(orderBoxId)) {
        selectedOrderBoxes.value = selectedOrderBoxes.value.filter(id => id !== orderBoxId)
    } else {
        selectedOrderBoxes.value.push(orderBoxId)
    }
}

function LoadOrders() {
    if (selectedOrderBoxes.value.length === 0) {
        toast.error('Please select at least one box.')
        return
    }

    form.orderBoxes = selectedOrderBoxes.value

    form.post('/loaded-orders/container', {
        onSuccess: () => {
            selectedOrderBoxes.value = []
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
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">LOAD TO CONTAINER</h1>

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-1">
                    <SearchInput />
                </div>

                <form class="flex items-center gap-1" @submit.prevent="LoadOrders">
                    <label class="w-[290px] select font-semibold gap-3">
                        Container:
                        <select v-model="form.container" class="font-normal">
                            <option disabled value="">Select Container</option>
                            <option v-for="container in containers" :key="container.id" :value="container.id">
                                {{ container.container_number }}
                            </option>
                        </select>
                    </label>
                    <button type="submit" class="btn btn-sm btn-primary" :disabled="form.processing">Load</button>
                </form>
            </div>
        </template>

        <template #table-content>
            <DataTable
                :headers="['', 'Invoice #', 'Sender Name', 'Sender Address', 'Receiver Name', 'Receiver Address', 'Box Type', 'Quantity']"
                :rows="orders.data">
                <template #row="{ row: order }">
                    <td class="font-semibold">
                        <input type="checkbox" class="checkbox" :value="order.id"
                            :checked="selectedOrderBoxes.includes(order.id)" @change="toggleSelection(order.id)" />
                    </td>

                    <td class="font-semibold">
                        {{ order?.order?.invoice_number }}
                    </td>

                    <td class="font-semibold">
                        {{ order?.order?.sender_name }}
                    </td>

                    <td class="font-semibold">
                        {{ order?.order?.sender_address }}
                    </td>

                    <td class="font-semibold">
                        {{ order?.order?.receiver_name }}
                    </td>

                    <td class="font-semibold">
                        {{ order?.order?.receiver_address }}
                    </td>

                    <td class="font-semibold">
                        {{ capitalize(order?.box_type) }}
                    </td>

                    <td class="font-semibold">
                        {{ order?.quantity }}
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="orders.links" :to="orders.to" :from="orders.from" :total="orders.total" />
        </template>
    </CardTable>

</template>