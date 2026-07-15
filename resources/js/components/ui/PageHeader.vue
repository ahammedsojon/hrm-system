<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
            {{ title }}
        </h1>
        <p v-if="description" class="mt-1 text-sm text-slate-500">
            {{ description }}
        </p>
    </div>
</template>
