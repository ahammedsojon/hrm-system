<script setup>
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useEmployeeForm } from '../../composables/useEmployeeForm';
import AppLayout from '../../layouts/AppLayout.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import BaseButton from '../../components/ui/BaseButton.vue';
import EmployeeForm from '../../components/employees/EmployeeForm.vue';

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => route.name === 'employees.edit');
const employeeId = computed(() => (isEdit.value ? Number(route.params.id) : null));

const {
    form,
    displayPhoto,
    departments,
    designations,
    managers,
    employmentTypes,
    shifts,
    errors,
    isLoading,
    isSaving,
    loadOptions,
    loadEmployee,
    resetForm,
    setPhoto,
    removePhoto,
    save,
} = useEmployeeForm();

onMounted(async () => {
    await loadOptions(employeeId.value);

    if (isEdit.value && employeeId.value) {
        await loadEmployee(employeeId.value);
    } else {
        resetForm();
    }
});

async function handleSubmit() {
    const success = await save(employeeId.value);

    if (success) {
        router.push({ name: 'employees' });
    }
}

function handleCancel() {
    router.push({ name: 'employees' });
}
</script>

<template>
    <AppLayout>
        <PageHeader
            :title="isEdit ? 'Edit Employee' : 'Add Employee'"
            :description="isEdit ? 'Update employee information and employment details.' : 'Create a new employee record and user account.'"
        />

        <div v-if="isLoading" class="py-12 text-center text-sm text-slate-500">
            Loading...
        </div>

        <form v-else @submit.prevent="handleSubmit">
            <EmployeeForm
                :form="form"
                :errors="errors"
                :is-edit="isEdit"
                :display-photo="displayPhoto"
                :departments="departments"
                :designations="designations"
                :managers="managers"
                :employment-types="employmentTypes"
                :shifts="shifts"
                @update:photo="setPhoto"
                @remove-photo="removePhoto"
            />

            <div class="mt-6 flex justify-end gap-3">
                <BaseButton type="button" variant="secondary" @click="handleCancel">
                    Cancel
                </BaseButton>
                <BaseButton type="submit" :loading="isSaving">
                    {{ isEdit ? 'Update Employee' : 'Create Employee' }}
                </BaseButton>
            </div>
        </form>
    </AppLayout>
</template>
