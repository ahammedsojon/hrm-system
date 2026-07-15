<script setup>
import { onMounted, ref, watch } from 'vue';
import { useDepartments } from '../../composables/useDepartments';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseSelect from '../../components/ui/BaseSelect.vue';
import BaseTextarea from '../../components/ui/BaseTextarea.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import BaseModal from '../../components/ui/BaseModal.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';

const {
    departments,
    employees,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchDepartments,
    loadEmployees,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useDepartments();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
let searchTimeout = null;

onMounted(async () => {
    await Promise.all([fetchDepartments(), loadEmployees()]);
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchDepartments(), 300);
});

function confirmDelete(department) {
    itemToDelete.value = department;
    deleteError.value = '';
    showDeleteDialog.value = true;
}

async function handleDelete() {
    if (!itemToDelete.value) {
        return;
    }

    isDeleting.value = true;
    deleteError.value = '';

    try {
        await remove(itemToDelete.value.id);
        showDeleteDialog.value = false;
        itemToDelete.value = null;
    } catch (err) {
        deleteError.value = err.response?.data?.message
            ?? err.response?.data?.errors?.department?.[0]
            ?? 'Failed to delete department.';
    } finally {
        isDeleting.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Departments"
                description="Organize your workforce into departments and teams."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Department
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="department_search"
                v-model="search"
                label="Search departments"
                placeholder="Search by name or description"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && departments.length === 0" empty-message="No departments found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Manager</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employees</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="department in departments" :key="department.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ department.name }}
                    </td>
                    <td class="max-w-xs truncate px-6 py-4 text-sm text-slate-600">
                        {{ department.description || '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ department.manager_employee?.full_name ?? '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ department.employees_count ?? 0 }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100" @click="openEditModal(department)">
                                Edit
                            </button>
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50" @click="confirmDelete(department)">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <BaseModal :open="showModal" :title="isEditing() ? 'Edit Department' : 'Add Department'" @close="closeModal">
            <form class="space-y-4" @submit.prevent="save">
                <BaseInput id="department_name" v-model="form.name" label="Name" :error="errors.name" />
                <BaseTextarea id="department_description" v-model="form.description" label="Description" :error="errors.description" />
                <BaseSelect
                    id="manager_employee_id"
                    v-model="form.manager_employee_id"
                    label="Manager"
                    placeholder="No manager assigned"
                    :options="employees.map((e) => ({ value: e.id, label: e.full_name ?? `${e.first_name} ${e.last_name}` }))"
                    :error="errors.manager_employee_id"
                />
            </form>
            <template #footer>
                <BaseButton type="button" variant="secondary" @click="closeModal">Cancel</BaseButton>
                <BaseButton :loading="isSaving" @click="save">{{ isEditing() ? 'Update' : 'Create' }}</BaseButton>
            </template>
        </BaseModal>

        <ConfirmDialog
            :open="showDeleteDialog"
            title="Delete Department"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this department'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
