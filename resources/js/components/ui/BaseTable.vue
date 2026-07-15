<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    empty: {
        type: Boolean,
        default: false,
    },
    emptyMessage: {
        type: String,
        default: 'No records found.',
    },
});
</script>

<template>
    <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div v-if="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/70">
            <svg class="h-6 w-6 animate-spin text-slate-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <slot />
            </table>
        </div>

        <div v-if="empty && !loading" class="px-6 py-12 text-center text-sm text-slate-500">
            {{ emptyMessage }}
        </div>
    </div>
</template>
