<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { capitalize } from '@/utils/stringHelper.js'
import { useFlashToast } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'
import SearchInput from '@/components/SearchInput.vue'

const page = usePage();
const orders = computed(() => page.props.orders ?? [])

const viewOrdersModal = ref(null)
const selectedOrders = ref(null)

const totalAmount = computed(() => {
    if (!selectedOrders.value || !selectedOrders.value.order_boxes) return 0

    return selectedOrders.value.order_boxes.reduce((sum, box) => {
        const boxFee = box.fee || 0
        const qty = box.quantity || 1
        return sum + boxFee * qty
    }, 0)
})


function showViewOrdersModal(order) {
    selectedOrders.value = { ...order }
    viewOrdersModal.value.showModal()
}

defineOptions({
    layout: Layout
})
</script>

<template>
    <ToastAlert />
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">ORDER LIST</h1>

            <div class="flex justify-between items-center">
                <SearchInput />
                <Link :href="route('order.create')" class="btn btn-sm btn-primary">Add Order</Link>
            </div>
        </template>

        <template #table-content>
            <DataTable
                :headers="['User Code', 'Sender Name', 'Sender Address', 'Sender Contact', 'Sender Email', 'Action']"
                :rows="orders.data">
                <template #row="{ row: order }">

                    <td class="font-medium">{{ order.user?.user_code }}</td>
                    <td class="font-medium">{{ order.sender_name }}</td>
                    <td class="font-medium">{{ order.sender_address }}</td>
                    <td class="font-medium">{{ order.sender_contact }}</td>
                    <td class="font-medium">{{ order.sender_email }}</td>

                    <td class="px-4 py-2 space-x-2">
                        <button class="btn btn-primary btn-sm" @click="showViewOrdersModal(order)">View</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="orders.links" :to="orders.to" :from="orders.from" :total="orders.total" />
        </template>
    </CardTable>

    <!-- View Orders -->
    <dialog ref="viewOrdersModal" class="modal">
        <div class="modal-box max-w-[1000px]">
            <div class="mb-5">
                <h2 class="font-bold text-xl">ORDER DETAILS</h2>
            </div>
            <!-- content -->
            <div class="flex justify-center items-center gap-3 p-3 border border-gray-300 rounded shadow mb-3">
                <div class="flex items-center gap-1">
                    <p class="text-sm">User Code:</p>
                    <p class="font-semibold text-sm">
                        {{ selectedOrders?.user?.user_code }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">Invoice Number:</p>
                    <p class="font-semibold text-sm">
                        {{ selectedOrders?.invoice_number }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">Destination:</p>
                    <p class="font-semibold text-sm">{{ selectedOrders?.box_location?.location }}</p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">Status:</p>
                    <p class="font-semibold text-sm">{{ selectedOrders?.status }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1 mb-3">
                <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                    <div class="divider divider-start font-semibold">From:</div>

                    <!-- Name -->
                    <div class="flex items-center gap-1 mb-1">
                        <p class="text-sm">Name:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.sender_name }}</p>
                    </div>

                    <!-- Address -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Address:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.sender_address }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Contact:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.sender_contact }}</p>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Email:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.sender_email }}</p>
                    </div>
                </div>

                <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                    <div class="divider divider-start font-semibold">To:</div>

                    <!-- Name -->
                    <div class="flex items-center gap-1 mb-1">
                        <p class="text-sm">Name:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.receiver_name }}</p>
                    </div>

                    <!-- Address -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Address:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.receiver_address }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Contact:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.receiver_contact }}</p>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Email:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.receiver_email }}</p>
                    </div>
                </div>
            </div>


            <DataTable :headers="['Quantity', 'Box Type', 'Description', 'Dimension', 'Box Fee', 'Total']"
                :rows="selectedOrders?.order_boxes ?? []">
                <template #row="{ row: box }">
                    <td>{{ box.quantity }}</td>
                    <td>{{ capitalize(box.box_type) }}</td>
                    <td>{{ box.description }}</td>
                    <td>{{ Math.round(box.length) }} x {{ Math.round(box.height) }} x {{ Math.round(box.width) }}</td>
                    <td>{{ box.fee }}</td>
                    <td>{{ (box.fee * box.quantity).toFixed(2) }}</td>
                </template>

                <template #footer>
                    <tr>
                        <td colspan="4" class="text-right font-bold">
                            Total Amount:
                        </td>
                        <td class="font-bold">
                            {{ totalAmount.toFixed(2) }}
                        </td>
                    </tr>
                </template>
            </DataTable>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn border border-gray-300">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>