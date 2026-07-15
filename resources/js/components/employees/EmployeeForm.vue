<script setup>
import BaseInput from '../ui/BaseInput.vue';
import BaseSelect from '../ui/BaseSelect.vue';
import BaseTextarea from '../ui/BaseTextarea.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    departments: {
        type: Array,
        default: () => [],
    },
    designations: {
        type: Array,
        default: () => [],
    },
    managers: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:photo']);

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
];

const maritalOptions = [
    { value: 'single', label: 'Single' },
    { value: 'married', label: 'Married' },
    { value: 'divorced', label: 'Divorced' },
    { value: 'widowed', label: 'Widowed' },
];

const bloodGroupOptions = [
    { value: 'A+', label: 'A+' },
    { value: 'A-', label: 'A-' },
    { value: 'B+', label: 'B+' },
    { value: 'B-', label: 'B-' },
    { value: 'AB+', label: 'AB+' },
    { value: 'AB-', label: 'AB-' },
    { value: 'O+', label: 'O+' },
    { value: 'O-', label: 'O-' },
];

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'probation', label: 'Probation' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'terminated', label: 'Terminated' },
    { value: 'resigned', label: 'Resigned' },
    { value: 'retired', label: 'Retired' },
];

function handlePhotoChange(event) {
    emit('update:photo', event.target.files[0] ?? null);
}
</script>

<template>
    <div class="space-y-8">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Personal Information</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <BaseInput id="first_name" v-model="form.first_name" label="First Name" :error="errors.first_name" />
                <BaseInput id="last_name" v-model="form.last_name" label="Last Name" :error="errors.last_name" />
                <BaseInput id="email" v-model="form.email" label="Email" type="email" :error="errors.email" />
                <BaseInput id="phone" v-model="form.phone" label="Phone" :error="errors.phone" />
                <BaseSelect id="gender" v-model="form.gender" label="Gender" :options="genderOptions" :error="errors.gender" />
                <BaseInput id="date_of_birth" v-model="form.date_of_birth" label="Date of Birth" type="date" :error="errors.date_of_birth" />
                <BaseSelect id="blood_group" v-model="form.blood_group" label="Blood Group" :options="bloodGroupOptions" :error="errors.blood_group" />
                <BaseSelect id="marital_status" v-model="form.marital_status" label="Marital Status" :options="maritalOptions" :error="errors.marital_status" />
                <BaseInput id="nationality" v-model="form.nationality" label="Nationality" :error="errors.nationality" />
                <BaseInput id="religion" v-model="form.religion" label="Religion (Optional)" :error="errors.religion" />
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Profile Photo</label>
                    <input
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800"
                        @change="handlePhotoChange"
                    />
                    <p v-if="errors.profile_photo" class="mt-1 text-sm text-red-600">{{ errors.profile_photo }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Employment Details</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <BaseSelect id="status" v-model="form.status" label="Status" :options="statusOptions" :error="errors.status" />
                <BaseSelect id="department_id" v-model="form.department_id" label="Department" :options="departments" :error="errors.department_id" />
                <BaseSelect id="designation_id" v-model="form.designation_id" label="Designation" :options="designations" :error="errors.designation_id" />
                <BaseSelect
                    id="manager_id"
                    v-model="form.manager_id"
                    label="Manager"
                    :options="managers.map((m) => ({ value: m.id, label: m.full_name ?? `${m.first_name} ${m.last_name}` }))"
                    :error="errors.manager_id"
                />
                <BaseInput id="employment_type_id" v-model="form.employment_type_id" label="Employment Type ID" :error="errors.employment_type_id" />
                <BaseInput id="joining_date" v-model="form.joining_date" label="Joining Date" type="date" :error="errors.joining_date" />
                <BaseInput id="probation_start_date" v-model="form.probation_start_date" label="Probation Start Date" type="date" :error="errors.probation_start_date" />
                <BaseInput id="probation_end_date" v-model="form.probation_end_date" label="Probation End Date" type="date" :error="errors.probation_end_date" />
                <BaseInput id="branch_id" v-model="form.branch_id" label="Branch ID" :error="errors.branch_id" />
                <BaseInput id="shift_id" v-model="form.shift_id" label="Shift ID" :error="errors.shift_id" />
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Address &amp; Identification</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <BaseTextarea id="address" v-model="form.address" label="Address" :error="errors.address" />
                </div>
                <BaseInput id="national_id" v-model="form.national_id" label="National ID" :error="errors.national_id" />
                <BaseInput id="passport_no" v-model="form.passport_no" label="Passport No" :error="errors.passport_no" />
                <BaseInput id="tax_number" v-model="form.tax_number" label="Tax Number" :error="errors.tax_number" />
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Bank Details</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <BaseInput id="bank_name" v-model="form.bank_name" label="Bank Name" :error="errors.bank_name" />
                <BaseInput id="bank_account" v-model="form.bank_account" label="Bank Account" :error="errors.bank_account" />
            </div>
        </section>
    </div>
</template>
