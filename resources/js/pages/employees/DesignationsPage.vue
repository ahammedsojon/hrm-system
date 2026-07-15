<script setup>
import { onMounted, ref, watch } from 'vue';
import { useDesignations } from '../../composables/useDesignations';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseTextarea from '../../components/ui/BaseTextarea.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import BaseModal from '../../components/ui/BaseModal.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';

const {
    designations,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchDesignations,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useDesignations();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
let searchTimeout = null;

onMounted(() => {
    fetchDesignations();
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchDesignations(), 300);
});

function confirmDelete(designation) {
    itemToDelete.value = designation;
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
            ?? err.response?.data?.errors?.designation?.[0]
            ?? 'Failed to delete designation.';
    } finally {
        isDeleting.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Designations"
                description="Define job titles and designations across your organization."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Designation
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="designation_search"
                v-model="search"
                label="Search designations"
                placeholder="Search by name or description"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && designations.length === 0" empty-message="No designations found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employees</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="designation in designations" :key="designation.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ designation.name }}
                    </td>
                    <td class="max-w-md truncate px-6 py-4 text-sm text-slate-600">
                        {{ designation.description || '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ designation.employees_count ?? 0 }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                                @click="openEditModal(designation)"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                @click="confirmDelete(designation)"
                            >
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <BaseModal
            :open="showModal"
            :title="isEditing() ? 'Edit Designation' : 'Add Designation'"
            @close="closeModal"
        >
            <form class="space-y-4" @submit.prevent="save">
                <BaseInput id="designation_name" v-model="form.name" label="Name" :error="errors.name" />
                <BaseTextarea id="designation_description" v-model="form.description" label="Description" :error="errors.description" />
            </form>

            <template #footer>
                <BaseButton type="button" variant="secondary" @click="closeModal">
                    Cancel
                </BaseButton>
                <BaseButton :loading="isSaving" @click="save">
                    {{ isEditing() ? 'Update' : 'Create' }}
                </BaseButton>
            </template>
        </BaseModal>

        <ConfirmDialog
            :open="showDeleteDialog"
            title="Delete Designation"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this designation'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
