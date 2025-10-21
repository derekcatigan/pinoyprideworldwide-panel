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
const branches = computed(() => page.props.branches ?? [])
const allBoxTotals = computed(() => page.props.boxTotals ?? [])

const viewModal = ref(null)
const selectedBranch = ref(null)
const boxTotals = ref([])

function openModal(branch) {
    selectedBranch.value = branch
    boxTotals.value = allBoxTotals.value.filter(t => t.branch_id === branch.id)
    viewModal.value.showModal()
}

const totalBoxes = computed(() => {
    return boxTotals.value.reduce((sum, item) => sum + Number(item.total_boxes ?? 0), 0)
})

defineOptions({
    layout: Layout
})
</script>

<template>
    <ToastAlert />
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">RECEIVED ORDERS</h1>
            <SearchInput />
        </template>

        <template #table-content>
            <DataTable :headers="['Branch', 'Action']" :rows="branches.data">
                <template #row="{ row: branch }">

                    <td class="font-semibold">
                        Pinoy Pride Worldwide - {{ branch?.branch_name }}
                    </td>

                    <td>
                        <button class="btn btn-sm btn-primary" @click="openModal(branch)">Review</button>
                    </td>

                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="branches.links" :to="branches.to" :from="branches.from" :total="branches.total" />
        </template>
    </CardTable>

    <!-- Modal -->
    <dialog ref="viewModal" class="modal">
        <div class="modal-box max-w-[800px]">
            <h3 class="font-bold text-lg mb-4">
                Pinoy Pride Worldwide - {{ selectedBranch?.branch_name }} | Total Boxes
            </h3>

            <!-- Table Content -->
            <DataTable :headers="['Location', 'Box Type', 'Total Boxes']" :rows="boxTotals">
                <template #row="{ row }">
                    <td>{{ row.location }}</td>
                    <td>{{ capitalize(row.box_type) }}</td>
                    <td>{{ row.total_boxes }}</td>
                </template>

                <template #footer>
                    <td colspan="2" class="text-right font-semibold">Total:</td>
                    <td class="font-bold">{{ totalBoxes }}</td>
                </template>
            </DataTable>


            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>