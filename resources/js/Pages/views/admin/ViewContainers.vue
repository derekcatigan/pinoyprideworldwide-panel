<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { capitalize } from '@/utils/stringHelper.js'

// Layout & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const containers = computed(() => page.props.containerOrders ?? [])

const viewContainerModal = ref(null)
const selectedContainer = ref(null)

function showViewContainersModal(container) {
    selectedContainer.value = { ...container }
    viewContainerModal.value.showModal()
}

function exportToExcel(container) {
    if (!container?.id) return
    window.open(`/containers/export/${container.id}`, '_blank')
}

defineOptions({
    layout: Layout
})
</script>

<template>
    <ToastAlert />
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">CONTAINER DETAILS</h1>
            <SearchInput />
        </template>

        <template #table-content>
            <DataTable :headers="['Container Number', 'Branch Assigned', 'Boxes', 'Action']" :rows="containers.data">
                <template #row="{ row: container }">
                    <td class="font-medium">
                        {{ container.container_number }}
                    </td>
                    <td class="font-medium">
                        Pinoy Pride Worldwide - {{ container.branch?.branch_name ?? 'N/A' }}
                    </td>
                    <td class="font-medium">
                        {{ container.total_boxes }}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" @click="showViewContainersModal(container)">View
                            Details</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="containers.links" :to="containers.to" :from="containers.from"
                :total="containers.total" />
        </template>
    </CardTable>

    <!-- View Containers -->
    <dialog ref="viewContainerModal" class="modal">
        <div class="modal-box max-w-[1000px] max-h-[85vh] flex flex-col">
            <!-- Header -->
            <div class="mb-4 shrink-0 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl mb-2">
                        Container #: {{ selectedContainer?.container_number }}
                    </h2>
                    <p>
                        <strong>Branch:</strong> Pinoy Pride Worldwide - {{ selectedContainer?.branch?.branch_name ??
                            'N/A'
                        }}
                    </p>
                    <p><strong>Total Boxes:</strong> {{ selectedContainer?.total_boxes }}</p>
                </div>

                <!-- Excel Export Button -->
                <div>
                    <button class="btn btn-sm btn-accent" @click="exportToExcel(selectedContainer)">
                        <i class='bx  bx-form'></i>
                        Export to Excel
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto grow pr-2">
                <div v-for="containerOrder in selectedContainer?.container_orders ?? []" :key="containerOrder.id"
                    class="border border-gray-200 rounded-lg shadow-sm mb-4 p-3">
                    <h3 class="font-semibold text-lg text-orange-700 mb-2">
                        Invoice #: {{ containerOrder.order?.invoice_number }}
                    </h3>
                    <p><strong>Sender:</strong> {{ containerOrder.order?.sender_name }}</p>
                    <p><strong>Receiver:</strong> {{ containerOrder.order?.receiver_name }}</p>
                    <p><strong>Status:</strong> {{ containerOrder.order?.status }}</p>

                    <!-- Boxes -->
                    <div class="max-h-[250px] overflow-y-auto border border-gray-200 rounded-lg shadow-sm mt-3">
                        <DataTable :headers="['Qty', 'Box Type', 'Description', 'Dimensions', 'Fee', 'Total']"
                            :rows="containerOrder.order?.order_boxes ?? []">
                            <template #row="{ row: box }">
                                <td>{{ box.quantity }}</td>
                                <td>{{ capitalize(box.box_type) }}</td>
                                <td>{{ box.description }}</td>
                                <td>{{ Math.round(box.length) }} x {{ Math.round(box.height) }} x {{
                                    Math.round(box.width) }}</td>
                                <td>{{ box.fee }}</td>
                                <td>{{ (box.fee * box.quantity).toFixed(2) }}</td>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-action mt-2 shrink-0">
                <form method="dialog">
                    <button class="btn border border-gray-300">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>