<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useEmployees } from '../../composables/useEmployees';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';
import EmployeeTable from '../../components/employees/EmployeeTable.vue';

const router = useRouter();
const {
    employees,
    pagination,
    isLoading,
    search,
    error,
    fetchEmployees,
    deleteEmployee,
} = useEmployees();

const showDeleteDialog = ref(false);
const employeeToDelete = ref(null);
const isDeleting = ref(false);
let searchTimeout = null;

onMounted(() => {
    fetchEmployees();
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchEmployees(), 300);
});

function goToCreate() {
    router.push({ name: 'employees.create' });
}

function goToView(id) {
    router.push({ name: 'employees.show', params: { id } });
}

function goToEdit(id) {
    router.push({ name: 'employees.edit', params: { id } });
}

function confirmDelete(employee) {
    employeeToDelete.value = employee;
    showDeleteDialog.value = true;
}

async function handleDelete() {
    if (!employeeToDelete.value) {
        return;
    }

    isDeleting.value = true;

    try {
        await deleteEmployee(employeeToDelete.value.id);
        showDeleteDialog.value = false;
        employeeToDelete.value = null;
    } finally {
        isDeleting.value = false;
    }
}

function changePage(page) {
    if (page < 1 || (pagination.value && page > pagination.value.last_page)) {
        return;
    }

    fetchEmployees(page);
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Employees"
                description="Manage employee records, profiles, and organizational assignments."
            />
            <BaseButton class="shrink-0" @click="goToCreate">
                Add Employee
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="search"
                v-model="search"
                label="Search employees"
                placeholder="Search by name, email, or employment ID"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && employees.length === 0">
            <EmployeeTable
                v-if="employees.length"
                :employees="employees"
                @view="goToView"
                @edit="goToEdit"
                @delete="confirmDelete"
            />
        </BaseTable>

        <div
            v-if="pagination && pagination.last_page > 1"
            class="mt-4 flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3"
        >
            <p class="text-sm text-slate-600">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
                <span class="text-slate-400">({{ pagination.total }} total)</span>
            </p>
            <div class="flex gap-2">
                <button
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-50"
                    :disabled="pagination.current_page <= 1"
                    @click="changePage(pagination.current_page - 1)"
                >
                    Previous
                </button>
                <button
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-50"
                    :disabled="pagination.current_page >= pagination.last_page"
                    @click="changePage(pagination.current_page + 1)"
                >
                    Next
                </button>
            </div>
        </div>

        <ConfirmDialog
            :open="showDeleteDialog"
            title="Delete Employee"
            :message="`Are you sure you want to delete ${employeeToDelete?.full_name ?? 'this employee'}? This will also remove their user account.`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
