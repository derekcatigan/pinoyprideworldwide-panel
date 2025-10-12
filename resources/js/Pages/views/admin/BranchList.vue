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
const branches = computed(() => page.props.branches ?? { data: [] })

const deleteModal = ref(null)
const branchToDelete = ref(null)

function openDeleteModal(branch) {
    branchToDelete.value = branch
    deleteModal.value?.showModal()
}

useFlashToast()

function deleteConfirm() {
    router.delete(`/admin/branch/destroy/${branchToDelete.value?.id}`, {
        onSuccess: () => {
            deleteModal.value?.close()
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
            <h1 class="font-semibold text-2xl mb-5">Branch List</h1>

            <div class="flex justify-between items-center">
                <SearchInput />

                <Link :href="route('admin.branch.create')" class="btn btn-sm btn-primary">
                Create Branch
                </Link>
            </div>
        </template>

        <template #table-content>
            <DataTable :headers="['Branch Code', 'Branch', 'Branch Owner', 'Actions']" :rows="branches.data">
                <template #row="{ row: branch }">
                    <td class="font-medium">{{ branch.branch_code }}</td>
                    <td class="font-medium">{{ branch.branch_name }}</td>
                    <td class="font-medium">{{ branch.branch_owner }}</td>
                    <td>
                        <button class="btn btn-sm btn-error" @click="openDeleteModal(branch)">Remove</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="branches.links" :to="branches.to" :from="branches.from" :total="branches.total" />
        </template>
    </CardTable>

    <!-- Delete Confirmation Modal -->
    <dialog ref="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">
                Are you sure you want to delete branch
                <span class="font-semibold">
                    {{ branchToDelete?.branch_name }}
                </span>?
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
                <button class="btn btn-error" @click="deleteConfirm">Yes, Delete</button>
            </div>
        </div>
    </dialog>
</template>