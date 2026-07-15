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
        type: [String, Number],
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label :for="id" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
        </label>
        <select
            :id="id"
            :value="modelValue"
            class="block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0"
            :class="error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-slate-900 focus:ring-slate-900'"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.value ?? option.id"
                :value="option.value ?? option.id"
            >
                {{ option.label ?? option.name }}
            </option>
        </select>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
