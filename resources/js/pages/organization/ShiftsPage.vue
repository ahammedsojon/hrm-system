<script setup>
import { onMounted, ref, watch } from 'vue';
import { useShifts } from '../../composables/useShifts';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import BaseInput from '../../components/ui/BaseInput.vue';
import BaseTable from '../../components/ui/BaseTable.vue';
import BaseModal from '../../components/ui/BaseModal.vue';
import ConfirmDialog from '../../components/ui/ConfirmDialog.vue';

const {
    shifts,
    isLoading,
    isSaving,
    search,
    error,
    showModal,
    form,
    errors,
    isEditing,
    fetchShifts,
    openCreateModal,
    openEditModal,
    closeModal,
    save,
    remove,
} = useShifts();

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);
const deleteError = ref('');
let searchTimeout = null;

function formatTime12Hour(time) {
    if (!time) {
        return '—';
    }

    const [hours, minutes] = time.split(':').map(Number);
    const period = hours >= 12 ? 'PM' : 'AM';
    const hour12 = hours % 12 || 12;

    return `${hour12}:${String(minutes).padStart(2, '0')} ${period}`;
}

onMounted(() => {
    fetchShifts();
});

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchShifts(), 300);
});

function confirmDelete(shift) {
    itemToDelete.value = shift;
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
            ?? err.response?.data?.errors?.shift?.[0]
            ?? 'Failed to delete shift.';
    } finally {
        isDeleting.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <PageHeader
                title="Shifts"
                description="Manage work shifts and scheduling patterns."
            />
            <BaseButton class="shrink-0" @click="openCreateModal">
                Add Shift
            </BaseButton>
        </div>

        <div class="mb-4">
            <BaseInput
                id="shift_search"
                v-model="search"
                label="Search shifts"
                placeholder="Search by name"
            />
        </div>

        <p v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</p>

        <BaseTable :loading="isLoading" :empty="!isLoading && shifts.length === 0" empty-message="No shifts found.">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Start Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">End Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Working Hours</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Employees</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="shift in shifts" :key="shift.id" class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                        {{ shift.name }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ formatTime12Hour(shift.start_time) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ formatTime12Hour(shift.end_time) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ shift.working_hours }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                        {{ shift.employees_count ?? 0 }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                        <div class="flex justify-end gap-2">
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100" @click="openEditModal(shift)">
                                Edit
                            </button>
                            <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50" @click="confirmDelete(shift)">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <BaseModal :open="showModal" :title="isEditing() ? 'Edit Shift' : 'Add Shift'" @close="closeModal">
            <form class="space-y-4" @submit.prevent="save">
                <BaseInput id="shift_name" v-model="form.name" label="Name" :error="errors.name" />
                <BaseInput id="shift_start_time" v-model="form.start_time" label="Start Time" type="time" :error="errors.start_time" />
                <BaseInput id="shift_end_time" v-model="form.end_time" label="End Time" type="time" :error="errors.end_time" />
                <BaseInput id="shift_working_hours" v-model="form.working_hours" label="Working Hours" type="number" step="0.5" min="0.5" max="24" :error="errors.working_hours" />
            </form>
            <template #footer>
                <BaseButton type="button" variant="secondary" @click="closeModal">Cancel</BaseButton>
                <BaseButton :loading="isSaving" @click="save">{{ isEditing() ? 'Update' : 'Create' }}</BaseButton>
            </template>
        </BaseModal>

        <ConfirmDialog
            :open="showDeleteDialog"
            title="Delete Shift"
            :message="deleteError || `Are you sure you want to delete ${itemToDelete?.name ?? 'this shift'}?`"
            confirm-label="Delete"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
