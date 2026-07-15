<script setup>
import { onMounted, ref, watch } from 'vue';
import { useDocuments } from '../../composables/useDocuments';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseSelect from '../../components/ui/BaseSelect.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import BaseModal from '../../components/ui/BaseModal.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';

const {
    documents,
    employees,
    pagination,
    file,
    existingFileName,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchDocuments,
    loadEmployees,
    setFile,
    clearFile,
    removeExistingFile,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useDocuments();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
const fileInput = ref(null);
let searchTimeout = null;

onMounted(async () => {
    await Promise.all([fetchDocuments(), loadEmployees()]);
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchDocuments(), 300);
});

function formatDate(value) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleDateString();
}

function formatFileSize(bytes) {
    if (!bytes) {
        return '';
    }

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function handleFileChange(event) {
    setFile(event.target.files[0] ?? null);
}

function handleRemoveExistingFile() {
    if (fileInput.value) {
        fileInput.value.value = '';
    }

    removeExistingFile();
}

function handleClearFile() {
    if (fileInput.value) {
        fileInput.value.value = '';
    }

    clearFile();
}

function confirmDelete(document) {
    itemToDelete.value = document;
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
    } catch {
        deleteError.value = 'Failed to delete document.';
    } finally {
        isDeleting.value = false;
    }
}

function goToPage(page) {
    if (page >= 1 && page <= (pagination.value?.lastPage ?? 1)) {
        fetchDocuments(page);
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Documents"
                description="Manage employee documents and files."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Document
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="document_search"
                v-model="search"
                label="Search documents"
                placeholder="Search by name, employee, or file name"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && documents.length === 0" empty-message="No documents found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Document</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">File</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Uploaded</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="document in documents" :key="document.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900">
                        {{ document.employee?.full_name ?? '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ document.name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <a
                            v-if="document.media?.url"
                            :href="document.media.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-slate-900 underline decoration-slate-300 underline-offset-2 hover:decoration-slate-900"
                        >
                            {{ document.media.original_name }}
                        </a>
                        <span v-else>—</span>
                        <span v-if="document.media?.size" class="mt-0.5 block text-xs text-slate-400">
                            {{ formatFileSize(document.media.size) }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ formatDate(document.created_at) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                                @click="openEditModal(document)"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                @click="confirmDelete(document)"
                            >
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <div
            v-if="pagination && pagination.lastPage > 1"
            class="mt-4 flex items-center justify-between text-sm text-slate-600"
        >
            <p>{{ pagination.total }} document(s) total</p>
            <div class="flex gap-2">
                <BaseButton
                    variant="secondary"
                    :disabled="pagination.currentPage <= 1"
                    @click="goToPage(pagination.currentPage - 1)"
                >
                    Previous
                </BaseButton>
                <BaseButton
                    variant="secondary"
                    :disabled="pagination.currentPage >= pagination.lastPage"
                    @click="goToPage(pagination.currentPage + 1)"
                >
                    Next
                </BaseButton>
            </div>
        </div>

        <BaseModal
            :open="showModal"
            :title="isEditing() ? 'Edit Document' : 'Add Document'"
            @close="closeModal"
        >
            <form class="space-y-4" @submit.prevent="save">
                <BaseSelect
                    id="employee_id"
                    v-model="form.employee_id"
                    label="Employee"
                    :options="employees.map((e) => ({ value: e.id, label: e.full_name ?? `${e.first_name} ${e.last_name}` }))"
                    :error="errors.employee_id"
                />
                <BaseInput id="name" v-model="form.name" label="Document Name" :error="errors.name" />
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">File</label>
                    <div v-if="isEditing() && existingFileName && !file" class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="min-w-0 flex-1 truncate text-sm text-slate-600">
                            Current file: {{ existingFileName }}
                        </p>
                        <button
                            type="button"
                            class="shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                            @click="handleRemoveExistingFile"
                        >
                            Delete
                        </button>
                    </div>
                    <input
                        ref="fileInput"
                        type="file"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800"
                        @change="handleFileChange"
                    />
                    <button
                        v-if="file"
                        type="button"
                        class="mt-2 rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                        @click="handleClearFile"
                    >
                        Remove selected file
                    </button>
                    <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file }}</p>
                </div>
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
            title="Delete Document"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this document'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
