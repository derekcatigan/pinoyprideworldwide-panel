<script setup>
import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'
// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import SearchInput from '@/components/SearchInput.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const locations = computed(() => page.props.locations ?? { data: [] })
const form = useForm({
    location: null,
})

const addModal = ref(null)
const deleteModal = ref(null)
const locationToDelete = ref(null)

const openAddModal = () => addModal.value?.showModal()
function openDeleteModal(location) {
    locationToDelete.value = location
    deleteModal.value?.showModal()
}

useFlashToast()

function deleteConfirm() {
    form.delete(`/admin/destination/destroy/${locationToDelete.value.id}`, {
        onSuccess: () => {
            deleteModal.value?.close()
        }
    })
}

function submit() {
    form.post('/admin/destination/store', {
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
    <CardTable>
        <template #header-content>
            <h1 class="font-semibold text-2xl mb-5">DESTINATION LIST</h1>

            <div class="flex justify-between items-center">
                <SearchInput />

                <button class="btn btn-sm btn-primary" @click="openAddModal">
                    Add Location
                </button>
            </div>
        </template>

        <template #table-content>
            <DataTable :headers="['Location', 'Action']" :rows="locations.data">
                <template #row="{ row: location }">
                    <td class="font-semibold">
                        {{ location.location }}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-error" @click="openDeleteModal(location)">
                            Remove
                        </button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="locations.links" :to="locations.to" :from="locations.from" :total="locations.total" />
        </template>
    </CardTable>

    <!-- Add Modal -->
    <dialog ref="addModal" class="modal">
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="text-lg font-bold mb-4">DESTINATION</h3>

            <form @submit.prevent="submit">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Location</legend>
                    <input type="text" v-model="form.location" class="w-full input" placeholder="Enter location" />
                </fieldset>
                <button type="submit" role="form" class="btn btn-block btn-primary mt-5" :disabled="form.processing">
                    Add Location
                </button>
            </form>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog ref="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">
                Are you sure you want to delete destination
                <span class="font-semibold">
                    {{ locationToDelete?.location }}
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