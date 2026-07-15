<script setup>
import { onMounted, ref, watch } from 'vue';
import { useEmergencyContacts } from '../../composables/useEmergencyContacts';
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
    contacts,
    employees,
    pagination,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchContacts,
    loadEmployees,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useEmergencyContacts();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
let searchTimeout = null;

const relationshipOptions = [
    { value: 'spouse', label: 'Spouse' },
    { value: 'parent', label: 'Parent' },
    { value: 'sibling', label: 'Sibling' },
    { value: 'child', label: 'Child' },
    { value: 'friend', label: 'Friend' },
    { value: 'other', label: 'Other' },
];

const priorityOptions = [
    { value: 1, label: '1 - Primary' },
    { value: 2, label: '2' },
    { value: 3, label: '3' },
    { value: 4, label: '4' },
    { value: 5, label: '5' },
];

onMounted(async () => {
    await Promise.all([fetchContacts(), loadEmployees()]);
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchContacts(), 300);
});

function confirmDelete(contact) {
    itemToDelete.value = contact;
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
        deleteError.value = 'Failed to delete emergency contact.';
    } finally {
        isDeleting.value = false;
    }
}

function goToPage(page) {
    if (page >= 1 && page <= (pagination.value?.lastPage ?? 1)) {
        fetchContacts(page);
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Emergency Contacts"
                description="Manage employee emergency contact information."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Contact
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="emergency_contact_search"
                v-model="search"
                label="Search contacts"
                placeholder="Search by name, phone, relationship, or employee"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && contacts.length === 0" empty-message="No emergency contacts found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Relationship</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Priority</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900">
                        {{ contact.employee?.full_name ?? '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ contact.name }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ contact.phone }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm capitalize text-slate-600">
                        {{ contact.relationship }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ contact.priority }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                                @click="openEditModal(contact)"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                @click="confirmDelete(contact)"
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
            <p>{{ pagination.total }} contact(s) total</p>
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
            :title="isEditing() ? 'Edit Emergency Contact' : 'Add Emergency Contact'"
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
                <BaseInput id="name" v-model="form.name" label="Name" :error="errors.name" />
                <BaseInput id="phone" v-model="form.phone" label="Phone" :error="errors.phone" />
                <BaseSelect
                    id="relationship"
                    v-model="form.relationship"
                    label="Relationship"
                    :options="relationshipOptions"
                    :error="errors.relationship"
                />
                <BaseTextarea id="address" v-model="form.address" label="Address" :error="errors.address" />
                <BaseSelect
                    id="priority"
                    v-model="form.priority"
                    label="Priority"
                    :options="priorityOptions"
                    :error="errors.priority"
                />
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
            title="Delete Emergency Contact"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this contact'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
