<script setup>
defineProps({
    width: { type: String, default: 'w-full' },
    bg: { type: String, default: 'bg-white' }
})
</script>
<template>
    <div :class="`card ${width} ${bg} card-lg border border-gray-300 shadow-sm`">
        <div class="card-body">
            <div class="card-title">
                <slot name="title"></slot>
            </div>

            <slot name="content"></slot>

            <div class="card-actions">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
</template>