<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Search...'
    }
})

const emit = defineEmits(['update:modelValue'])

const query = ref(props.modelValue)

watch(query, (val) => {
    emit('update:modelValue', val)
})

watch(
    () => props.modelValue,
    (val) => {
        if (val !== query.value) query.value = val
    }
)
</script>

<template>
    <div class="w-[300px]">
        <label class="w-full input">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </g>
            </svg>
            <input type="search" v-model="query" :placeholder="placeholder" required />
        </label>
    </div>
</template>
