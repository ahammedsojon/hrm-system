<script setup>
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseTable from '../../components/ui/BaseTable.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    emptyMessage: {
        type: String,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <PageHeader :title="title" :description="description" />

        <BaseTable :loading="false" :empty="true" :empty-message="emptyMessage">
            <thead class="bg-slate-50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column"
                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
                    >
                        {{ column }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white" />
        </BaseTable>
    </AppLayout>
</template>
