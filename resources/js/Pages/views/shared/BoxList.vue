<script setup>
import { computed, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useFlashToast } from '@/composables/useFlashToast.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'
import SearchInput from '@/components/SearchInput.vue'

const page = usePage()
const boxes = computed(() => page.props.boxes ?? [])

const deleteModal = ref(null)
const selectToDelete = ref(null)

function openDeleteModal(box) {
    selectToDelete.value = box
    deleteModal.value.showModal()
}

useFlashToast()

function confirmDelete() {
    if (!selectToDelete.value) return
    router.delete(`/box/delete/${selectToDelete.value.id}`, {
        onSuccess: () => {
            deleteModal.value.close()
        },
        onError: () => deleteModal.value.close()
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
            <h1 class="font-semibold text-2xl mb-5">BOX LIST</h1>

            <div class="flex justify-between items-center">
                <SearchInput />
                <Link :href="route('box.create')" class="btn btn-sm btn-primary">Add Box</Link>
            </div>
        </template>

        <template #table-content>
            <DataTable :headers="['Box Type', 'Dimension', 'Price', 'Location', 'Branch', 'Actions']"
                :rows="boxes.data">
                <template #row="{ row: box }">

                    <td class="font-semibold">
                        {{ box.box_type.display_name }}
                    </td>

                    <td class="font-semibold">
                        {{ Math.round(box.box_type?.length) }} x
                        {{ Math.round(box.box_type?.height) }} x
                        {{ Math.round(box.box_type?.width) }}
                    </td>

                    <td class="font-semibold">
                        {{ box.box_type?.price }}
                    </td>

                    <td class="font-semibold">
                        {{ box.destination?.location || '—' }}
                    </td>

                    <td class="font-semibold">
                        {{ box.branch?.branch_name || '—' }}
                    </td>

                    <td>
                        <button class="btn btn-sm btn-error" @click="openDeleteModal(box)">Remove</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="boxes.links" :to="boxes.to" :from="boxes.from" :total="boxes.total" />
        </template>
    </CardTable>

    <dialog ref="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">Are you sure you want to delete
                <span class="font-semibold">{{ selectToDelete?.box_type?.display_name }} box on {{
                    selectToDelete?.branch?.branch_name
                    }}</span>?
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
                <button class="btn btn-error" @click="confirmDelete">Yes, Delete</button>
            </div>
        </div>
    </dialog>
</template>