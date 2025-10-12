<script setup>
defineProps({
    headers: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    },
    rowKey: {
        type: String,
        default: 'id',
    },
})
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table w-full border border-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th v-for="(header, index) in headers" :key="index"
                        class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b border-gray-300">
                        {{ header }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="rows.length > 0">
                    <tr v-for="row in rows" :key="row[rowKey]" class="hover:bg-gray-50 border-b border-gray-300">
                        <slot name="row" :row="row" />
                    </tr>
                </template>
                <tr v-else>
                    <td :colspan="headers.length" class="text-center py-4 text-gray-400">
                        No rows found
                    </td>
                </tr>
            </tbody>
            <tfoot v-if="$slots.footer">
                <slot name="footer" />
            </tfoot>
        </table>
    </div>
</template>