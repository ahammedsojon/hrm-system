<script setup>
const props = defineProps({
    employees: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['view', 'edit', 'delete']);

const statusClasses = {
    active: 'bg-emerald-50 text-emerald-700',
    probation: 'bg-amber-50 text-amber-700',
    suspended: 'bg-orange-50 text-orange-700',
    terminated: 'bg-red-50 text-red-700',
    resigned: 'bg-slate-100 text-slate-600',
    retired: 'bg-violet-50 text-violet-700',
};

function formatStatus(status) {
    return status?.replace('_', ' ') ?? '';
}
</script>

<template>
    <thead class="bg-slate-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Department</th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Designation</th>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-200 bg-white">
        <tr v-for="employee in employees" :key="employee.id" class="hover:bg-slate-50">
            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                {{ employee.full_name }}
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                {{ employee.email }}
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                {{ employee.department?.name ?? '—' }}
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                {{ employee.designation?.name ?? '—' }}
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-sm">
                <span
                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                    :class="statusClasses[employee.status] ?? 'bg-slate-100 text-slate-600'"
                >
                    {{ formatStatus(employee.status) }}
                </span>
            </td>
            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                        @click="$emit('view', employee.id)"
                    >
                        View
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                        @click="$emit('edit', employee.id)"
                    >
                        Edit
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                        @click="$emit('delete', employee)"
                    >
                        Delete
                    </button>
                </div>
            </td>
        </tr>
    </tbody>
</template>
