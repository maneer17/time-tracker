<script setup>
import { TailwindPagination } from 'laravel-vue-pagination'

defineProps({
    data: { type: Object, required: true },
})

const emit = defineEmits(['page-change'])
</script>

<template>
    <TailwindPagination
        :data="data"
        @pagination-change-page="emit('page-change', $event)"
        :item-classes="['bg-white','text-gray-500','border-gray-300', 'hover:bg-gray-50']"
        :active-classes="['bg-blue-50', 'border-blue-500', 'text-blue-600']"
    />
</template>