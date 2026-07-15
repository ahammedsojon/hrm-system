<script setup>
import { onMounted, ref, watch } from 'vue';
import { useEmploymentTypes } from '../../composables/useEmploymentTypes';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import BaseModal from '../../components/ui/BaseModal.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';

const {
    employmentTypes,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchEmploymentTypes,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useEmploymentTypes();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
let searchTimeout = null;

onMounted(() => {
    fetchEmploymentTypes();
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchEmploymentTypes(), 300);
});

function confirmDelete(employmentType) {
    itemToDelete.value = employmentType;
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
            ?? err.response?.data?.errors?.employment_type?.[0]
            ?? 'Failed to delete employment type.';
    } finally {
        isDeleting.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Employment Types"
                description="Manage employment types such as full-time, part-time, and contract."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Employment Type
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="employment_type_search"
                v-model="search"
                label="Search employment types"
                placeholder="Search by name"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && employmentTypes.length === 0" empty-message="No employment types found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employees</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="employmentType in employmentTypes" :key="employmentType.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ employmentType.name }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ employmentType.employees_count ?? 0 }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100" @click="openEditModal(employmentType)">
                                Edit
                            </button>
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50" @click="confirmDelete(employmentType)">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <BaseModal :open="showModal" :title="isEditing() ? 'Edit Employment Type' : 'Add Employment Type'" @close="closeModal">
            <form class="space-y-4" @submit.prevent="save">
                <BaseInput id="employment_type_name" v-model="form.name" label="Name" :error="errors.name" />
            </form>
            <template #footer>
                <BaseButton type="button" variant="secondary" @click="closeModal">Cancel</BaseButton>
                <BaseButton :loading="isSaving" @click="save">{{ isEditing() ? 'Update' : 'Create' }}</BaseButton>
            </template>
        </BaseModal>

        <ConfirmDialog
            :open="showDeleteDialog"
            title="Delete Employment Type"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this employment type'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
