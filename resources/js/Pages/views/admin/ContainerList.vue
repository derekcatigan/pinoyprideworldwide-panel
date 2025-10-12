<script setup>
import { ref, computed } from 'vue'
import { usePage, useForm, router } from '@inertiajs/vue3'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'
import { capitalize } from '@/utils/stringHelper.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import SearchInput from '@/components/SearchInput.vue'
import InfoAlert from '@/components/InfoAlert.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage();
const branches = computed(() => page.props.branches ?? [])
const containers = computed(() => page.props.containers ?? [])

const form = useForm({
    containerNumber: '',
    containerBranch: '',
})

const formUpdate = useForm({
    containerBranchUpdate: '',
})

const assignModal = ref(null)
const selectToUpdate = ref(null)

const deleteModal = ref(null)
const selectToDelete = ref(null)

function openAssignModal(container) {
    selectToUpdate.value = container
    assignModal.value.showModal()
}

function openDeleteModal(container) {
    selectToDelete.value = container
    deleteModal.value.showModal();
}

useFlashToast()

function confirmDelete() {
    if (!selectToDelete.value) return

    router.delete(`/container/delete/${selectToDelete.value.id}`, {
        onSuccess: () => {
            deleteModal.value.close();
        }
    })
}

function submit() {
    form.post('/container/store', {
        onSuccess: () => {
            form.reset()
        },
        onError: (error) => {
            useFlashValidation(error)
        }
    })
}

function submitUpdate() {
    formUpdate.put(`/container/update/${selectToUpdate.value.id}`, {
        onSuccess: () => {
            formUpdate.reset()
            assignModal.value.close()
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
            <span>ADD CONTAINER</span>
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <InfoAlert />
                <div class="flex flex-wrap items-center gap-1">
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Container Number</legend>
                        <input type="text" class="w-full input" v-model="form.containerNumber"
                            placeholder="Enter container number" />
                    </fieldset>
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Branch <span class="label text-xs">Optional</span></legend>
                        <select v-model="form.containerBranch" class="w-full select">
                            <option disabled value="">Select a Branch</option>
                            <option selected v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_name }}
                            </option>
                        </select>
                    </fieldset>
                    <button type="submit" class="btn btn-block btn-primary text-xs" :disabled="form.processing">ADD
                        CONTAINER</button>
                </div>
            </form>
        </template>
    </Card>

    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">CONTAINER LIST</h1>
            <SearchInput />
        </template>

        <template #table-content>
            <DataTable :headers="['Container Number', 'Branch Assigned', 'Status', 'Action']" :rows="containers.data">
                <template #row="{ row: container }">
                    <td class="font-semibold">{{ container?.container_number }}</td>
                    <td class="font-semibold">
                        <span v-if="container?.branch?.branch_name">
                            Pinoy Pride Worldwide - {{ container.branch.branch_name }}
                        </span>

                        <span v-else class="badge badge-neutral">
                            Not Assigned
                        </span>
                    </td>
                    <td class="font-semibold">
                        <span v-if="container?.status === 'available'" class="badge badge-success">
                            {{ capitalize(container?.status) }}
                        </span>
                        <span v-else class="badge badge-warning">
                            {{ capitalize(container?.status) }}
                        </span>
                    </td>

                    <td class="px-4 py-2 space-x-2">
                        <button class="btn btn-primary btn-sm" :disabled="container?.status !== 'available'" @click="container?.status === 'available'
                            ? openAssignModal(container)
                            : toast.error('Container already assigned to branch.')">
                            Assign
                        </button>
                        <button class="btn btn-error btn-sm" @click="openDeleteModal(container)">Remove</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="containers.links" :to="containers.to" :from="containers.from"
                :total="containers.total" />
        </template>
    </CardTable>

    <!-- Assign Modal -->
    <dialog ref="assignModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Assign Container</h3>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Branch <span class="label text-xs">required</span></legend>
                <select v-model="formUpdate.containerBranchUpdate" class="w-full select">
                    <option disabled value="">Select a Branch</option>
                    <option selected v-for="branch in branches" :key="branch.id" :value="branch.id">
                        {{ branch.branch_name }}
                    </option>
                </select>
            </fieldset>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
                <button class="btn btn-primary" @click="submitUpdate">Yes, Assign</button>
            </div>
        </div>
    </dialog>

    <!-- Delete Modal -->
    <dialog ref="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">Are you sure you want to delete
                <span class="font-semibold">{{ selectToDelete?.container_number }}</span>?
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