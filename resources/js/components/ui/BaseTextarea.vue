<script setup>
defineProps({
    id: {
        type: String,
        default: undefined,
    },
    label: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    rows: {
        type: Number,
        default: 3,
    },
    placeholder: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label :for="id" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
        </label>
        <textarea
            :id="id"
            :rows="rows"
            :value="modelValue"
            :placeholder="placeholder"
            class="block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0"
            :class="error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-slate-900 focus:ring-slate-900'"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
