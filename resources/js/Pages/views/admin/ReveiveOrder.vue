<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Layout & Components
import Layout from '@/layouts/Layout.vue'
import CardTable from '@/components/CardTable.vue'
import DataTable from '@/components/DataTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import Pagination from '@/components/Pagination.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()

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

        </template>

        <template #pagination-content>
            <Pagination :links="[]" />
        </template>
    </CardTable>
</template>