<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { employeeService } from '../../services/employeeService';
import AppLayout from '../../layouts/AppLayout.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmployeeDetailView from '../../components/employees/EmployeeDetailView.vue';

const route = useRoute();
const router = useRouter();

const employee = ref(null);
const isLoading = ref(true);
const error = ref('');

const employeeId = computed(() => Number(route.params.id));

onMounted(async () => {
    await loadEmployee();
});

async function loadEmployee() {
    isLoading.value = true;
    error.value = '';

    try {
        const { data } = await employeeService.get(employeeId.value);
        employee.value = data.data ?? data;
    } catch {
        error.value = 'Failed to load employee details.';
        employee.value = null;
    } finally {
        isLoading.value = false;
    }
}

function goBack() {
    router.push({ name: 'employees' });
}

function goToEdit() {
    router.push({ name: 'employees.edit', params: { id: employeeId.value } });
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <button
                type="button"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition-colors hover:text-slate-900"
                @click="goBack"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Employees
            </button>

            <BaseButton v-if="employee" class="shrink-0" @click="goToEdit">
                Edit Employee
            </BaseButton>
        </div>

        <div v-if="isLoading" class="py-16 text-center text-sm text-slate-500">
            Loading employee details...
        </div>

        <p v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {{ error }}
        </p>

        <EmployeeDetailView v-else-if="employee" :employee="employee" />
    </AppLayout>
</template>
