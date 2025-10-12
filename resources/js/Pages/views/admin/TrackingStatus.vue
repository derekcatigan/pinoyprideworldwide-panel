<script setup>
import { ref, computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import { capitalize } from '@/utils/stringHelper.js'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const containerOrders = computed(() => page.props.containerOrders ?? [])

const form = useForm({
    container: '',
    invoice: '',
    status: '',
    remarks: '',
})

const viewOrdersModal = ref(null)
const selectedOrders = ref(null)

function showViewOrdersModal(order) {
    selectedOrders.value = { ...order }
    viewOrdersModal.value.showModal()
}

useFlashToast()

function submit() {
    form.post('/tracking-status/update', {
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
    <Card class="mb-3">
        <template #title>
            <span>UPDATE TRACKING</span>
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="flex flex-wrap items-center gap-1">
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Container Number:</legend>
                        <input type="text" v-model="form.container" class="w-full input"
                            placeholder="Enter container number">
                    </fieldset>
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Invoice Number:</legend>
                        <input type="text" v-model="form.invoice" class="w-full input"
                            placeholder="Enter Invoice number">
                    </fieldset>
                </div>
                <div class="flex flex-wrap items-center gap-1">
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Status:</legend>
                        <select v-model="form.status" class="w-full select">
                            <option disabled value="">Select status</option>
                            <option value="In Transit">In Transit</option>
                            <option value="Departed Vessel">Departed Vessel</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Out for Delivery">Out for Delivery</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </fieldset>
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend text-sm">Remarks:</legend>
                        <input type="text" v-model="form.remarks" class="w-full input"
                            placeholder="Enter Invoice number">
                    </fieldset>
                </div>
                <button type="submit" class="btn btn-block btn-primary mt-5" :disabled="form.processing">Update
                    Status</button>
            </form>
        </template>
    </Card>
    <CardTable>
        <template #header-content>
            <div class="mb-5">
                <div class="flex items-center gap-1">
                    <i class='bx  bx-box-alt text-2xl text-orange-800'></i>
                    <h1 class="font-semibold text-2xl">TRACKING STATUS</h1>
                </div>
                <p>Manage and update the status of containers and their orders.</p>
            </div>

            <div class="flex items-center gap-2">
                <fieldset class="fieldset flex-1 min-w-p-[200px]">
                    <legend class="fieldset-legend">Search</legend>
                    <input type="search" class="w-full input" placeholder="Search container...">
                </fieldset>
                <fieldset class="fieldset flex-1 min-w-p-[200px]">
                    <legend class="fieldset-legend">Filter by Status</legend>
                    <select class="w-full select">
                        <option disabled selected>Select status</option>
                    </select>
                </fieldset>
            </div>
        </template>

        <template #table-content>
            <div class="mb-3">
                <h3 class="font-semibold text-lg">CONTAINERS:</h3>
            </div>

            <!-- Container Cards -->
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 m-5">
                <div v-for="containerOrder in containerOrders.data" :key="containerOrder.id"
                    class="p-5 rounded border border-gray-300 bg-white shadow hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-lg font-semibold text-gray-800">
                                <span class="text-orange-700">Container #:</span>
                                {{ containerOrder.container?.container_number }}
                            </h2>
                        </div>

                        <!-- Divider -->
                        <div class="h-px bg-gray-100 mb-3"></div>

                        <!-- Order Details -->
                        <div class="space-y-1.5 text-sm text-gray-700">
                            <p>
                                <span class="font-medium text-gray-600">Invoice #:</span>
                                {{ containerOrder.order?.invoice_number }}
                            </p>
                            <p>
                                <span class="font-medium text-gray-600">Sender:</span>
                                {{ containerOrder.order?.sender_name }}
                            </p>
                            <p>
                                <span class="font-medium text-gray-600">Receiver:</span>
                                {{ containerOrder.order?.receiver_name }}
                            </p>
                            <p>
                                <span class="font-medium text-gray-600">Status: </span>
                                <span :class="[
                                    'font-semibold',
                                    containerOrder.order?.status === 'Delivered'
                                        ? 'text-green-600'
                                        : 'text-blue-600',
                                ]">
                                    {{ capitalize(containerOrder.order?.status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex justify-end gap-2">
                        <button class="btn btn-sm btn-neutral" @click="showViewOrdersModal(containerOrder)">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template #pagination-content>
            <Pagination :links="containerOrders.links" :to="containerOrders.to" :from="containerOrders.from"
                :total="containerOrders.total" />
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
                    <p class="text-sm">Container:</p>
                    <p class="font-semibold text-sm">
                        {{ selectedOrders?.container?.container_number }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">User Code:</p>
                    <p class="font-semibold text-sm">
                        {{ selectedOrders?.order?.user?.user_code }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">Invoice Number:</p>
                    <p class="font-semibold text-sm">
                        {{ selectedOrders?.order?.invoice_number }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <p class="text-sm">Destination:</p>
                    <p class="font-semibold text-sm">{{ selectedOrders?.order?.box_location?.location }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1 mb-3">
                <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                    <div class="divider divider-start font-semibold">From:</div>

                    <!-- Name -->
                    <div class="flex items-center gap-1 mb-1">
                        <p class="text-sm">Name:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.sender_name }}</p>
                    </div>

                    <!-- Address -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Address:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.sender_address }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Contact:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.sender_contact }}</p>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Email:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.sender_email }}</p>
                    </div>
                </div>

                <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                    <div class="divider divider-start font-semibold">To:</div>

                    <!-- Name -->
                    <div class="flex items-center gap-1 mb-1">
                        <p class="text-sm">Name:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.receiver_name }}</p>
                    </div>

                    <!-- Address -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Address:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.receiver_address }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Contact:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.receiver_contact }}</p>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center gap-1">
                        <p class="text-sm">Email:</p>
                        <p class="font-semibold text-sm">{{ selectedOrders?.order?.receiver_email }}</p>
                    </div>
                </div>
            </div>
            <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg shadow-sm mb-4">
                <DataTable :headers="['Quantity', 'Box Type', 'Description', 'Dimension', 'Box Fee', 'Total']"
                    :rows="selectedOrders?.order?.order_boxes ?? []">
                    <template #row="{ row: box }">
                        <td>{{ box.quantity }}</td>
                        <td>{{ capitalize(box.box_type) }}</td>
                        <td>{{ box.description }}</td>
                        <td>{{ Math.round(box.length) }} x {{ Math.round(box.height) }} x {{ Math.round(box.width) }}
                        </td>
                        <td>{{ box.fee }}</td>
                        <td>{{ (box.fee * box.quantity).toFixed(2) }}</td>
                    </template>
                </DataTable>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn border border-gray-300">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>
