<script setup>
defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
            @click.self="$emit('close')"
        >
            <div class="w-full max-w-lg rounded-xl bg-white shadow-xl">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
                </div>

                <div class="px-6 py-5">
                    <slot />
                </div>

                <div v-if="$slots.footer" class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
