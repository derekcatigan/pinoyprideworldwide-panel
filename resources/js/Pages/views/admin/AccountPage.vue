<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'
import { capitalize } from '@/utils/stringHelper.js'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import SearchInput from '@/components/SearchInput.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const users = computed(() => page.props?.users)

const userToDelete = ref(null)
const confirmModalRef = ref(null)

function openConfirmModal(user) {
    userToDelete.value = user
    confirmModalRef.value.showModal()
}

useFlashToast()

function confirmDelete() {
    if (!userToDelete.value) return

    router.delete(route('admin.account.destroy', userToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (confirmModalRef.value) {
                confirmModalRef.value.close()
            }
            userToDelete.value = null
        },
        onError: (error) => {
            useFlashValidation(error)
            confirmModalRef.value.close()
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
            <h1 class="font-semibold text-2xl mb-5">ACCOUNT LIST</h1>
            <!-- Search + Add Account -->
            <div class="flex justify-between items-center">
                <SearchInput />

                <Link :href="route('admin.account.create')" class="btn btn-sm btn-primary">
                Add Account
                </Link>
            </div>
        </template>

        <template #table-content>
            <DataTable :headers="['Name', 'User Code', 'Email', 'Role', 'Branch', 'Status', 'Actions']"
                :rows="users.data">
                <template #row="{ row: user }">
                    <!-- Name -->
                    <td class="font-semibold">
                        {{ user.profile?.first_name }} {{ user.profile?.last_name }}
                    </td>

                    <!-- User Code -->
                    <td>{{ user.user_code }}</td>

                    <!-- Email -->
                    <td>{{ user.email }}</td>

                    <!-- Role -->
                    <td>{{ user.role_name }}</td>

                    <!-- Branch -->
                    <td>
                        {{ user.branch?.branch_name ? `Pinoy Pride Worldwide - ${user.branch.branch_name}` :
                            '-' }}
                    </td>

                    <!-- Status -->
                    <td>
                        <span :class="user.status === 'active' ? 'badge badge-success' : 'badge badge-error'">
                            {{ capitalize(user.status) }}
                        </span>
                    </td>

                    <td class="px-4 py-2">
                        <Link :href="route('admin.account.edit', user.id)" class="btn btn-primary btn-sm">Edit</Link>
                        <button class="btn btn-error btn-sm ml-2" @click="openConfirmModal(user)">Remove</button>
                    </td>
                </template>
            </DataTable>
        </template>

        <template #pagination-content>
            <Pagination :links="users.links" :to="users.to" :from="users.from" :total="users.total" />
        </template>
    </CardTable>

    <!-- Delete Confirmation Modal -->
    <dialog ref="confirmModalRef" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">Are you sure you want to delete
                <span class="font-semibold">{{ userToDelete?.profile?.first_name }} {{
                    userToDelete?.profile?.last_name }}</span>?
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