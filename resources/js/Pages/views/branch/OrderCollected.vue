<script setup>
import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { capitalize } from '@/utils/stringHelper.js'
import { useFlashToast } from '@/composables/useFlashToast.js'
import { toast } from 'vue-sonner'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import SearchInput from '@/components/SearchInput.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const orders = computed(() => page.props.orders ?? [])

const selectedOrderBoxes = ref([])

const form = useForm({
    orderBoxes: []
})

useFlashToast()

function toggleSelection(orderBoxId) {
    if (selectedOrderBoxes.value.includes(orderBoxId)) {
        selectedOrderBoxes.value = selectedOrderBoxes.value.filter(id => id !== orderBoxId)
    } else {
        selectedOrderBoxes.value.push(orderBoxId)
    }
}

function collectOrders() {
    if (selectedOrderBoxes.value.length === 0) {
        toast.error('Please select at least one box.')
        return
    }

    form.orderBoxes = selectedOrderBoxes.value

    form.post('/collected-orders/collect', {
        onSuccess: () => {
            selectedOrderBoxes.value = []
        },
    })
}

const isAllSelected = computed(() => {
    return orders.value.data?.length > 0 &&
        selectedOrderBoxes.value.length === orders.value.data.length
})

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedOrderBoxes.value = []
    } else {
        selectedOrderBoxes.value = orders.value.data.map(order => order.id)
    }
}


defineOptions({
    layout: Layout
})
</script>
<template>
    <ToastAlert />
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">COLLECTED ORDERS</h1>

            <div class="flex justify-between items-center">
                <SearchInput />
                <button class="btn btn-sm btn-primary" @click="collectOrders">Collect</button>
            </div>
        </template>

        <template #table-content>
            <DataTable
                :headers="['', 'Invoice #', 'Sender Name', 'Sender Address', 'Receiver Name', 'Receiver Address', 'Box Type', 'Quantity']"
                :rows="orders.data">
                <template #header="{ header, index }">
                    <input v-if="index === 0" type="checkbox" class="checkbox" @change="toggleSelectAll"
                        :checked="isAllSelected" />
                    <span v-else>{{ header }}</span>
                </template>

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