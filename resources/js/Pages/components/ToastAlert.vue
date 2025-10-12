<script setup>
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css'
</script>
<template>
    <Toaster richColors position="top-right" :visibleToasts="10" :duration="5000" />
</template>