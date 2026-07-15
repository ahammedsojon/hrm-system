<script setup>
const props = defineProps({
    employee: {
        type: Object,
        required: true,
    },
});

const statusClasses = {
    active: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    probation: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    suspended: 'bg-orange-50 text-orange-700 ring-orange-600/20',
    terminated: 'bg-red-50 text-red-700 ring-red-600/20',
    resigned: 'bg-slate-100 text-slate-600 ring-slate-500/20',
    retired: 'bg-violet-50 text-violet-700 ring-violet-600/20',
};

function formatStatus(status) {
    return status?.replace('_', ' ') ?? '—';
}

function display(value) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    return value;
}

function formatDate(value) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatLabel(value) {
    if (!value) {
        return '—';
    }

    return String(value).replace(/_/g, ' ');
}

function initials() {
    const first = props.employee.first_name?.[0] ?? '';
    const last = props.employee.last_name?.[0] ?? '';

    return `${first}${last}`.toUpperCase() || '?';
}
</script>

<template>
    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-gradient-to-r from-slate-900 to-slate-700 px-6 py-8 sm:px-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                    <div class="shrink-0">
                        <img
                            v-if="employee.profile_photo"
                            :src="employee.profile_photo"
                            :alt="employee.full_name"
                            class="h-24 w-24 rounded-2xl border-4 border-white/20 object-cover shadow-lg"
                        />
                        <div
                            v-else
                            class="flex h-24 w-24 items-center justify-center rounded-2xl border-4 border-white/20 bg-white/10 text-2xl font-semibold text-white shadow-lg"
                        >
                            {{ initials() }}
                        </div>
                    </div>

                    <div class="min-w-0 flex-1 text-white">
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">
                                {{ employee.full_name }}
                            </h1>
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset"
                                :class="statusClasses[employee.status] ?? 'bg-white/10 text-white ring-white/20'"
                            >
                                {{ formatStatus(employee.status) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-slate-200">{{ employee.email }}</p>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-200">
                            <span v-if="employee.phone">{{ employee.phone }}</span>
                            <span v-if="employee.department?.name">{{ employee.department.name }}</span>
                            <span v-if="employee.designation?.name">{{ employee.designation.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 border-t border-slate-100 bg-slate-50/60 px-6 py-4 sm:grid-cols-3 sm:px-8">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Manager</p>
                    <p class="mt-1 text-sm font-medium text-slate-900">{{ employee.manager?.full_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Joining Date</p>
                    <p class="mt-1 text-sm font-medium text-slate-900">{{ formatDate(employee.joining_date) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Employee ID</p>
                    <p class="mt-1 text-sm font-medium text-slate-900">#{{ employee.id }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Personal Information</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">First Name</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.first_name) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Last Name</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.last_name) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Gender</dt>
                        <dd class="mt-1 text-sm capitalize text-slate-900">{{ formatLabel(employee.gender) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ formatDate(employee.date_of_birth) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Blood Group</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.blood_group) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Marital Status</dt>
                        <dd class="mt-1 text-sm capitalize text-slate-900">{{ formatLabel(employee.marital_status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nationality</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.nationality) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Religion</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.religion) }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Employment Details</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Department</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ employee.department?.name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Designation</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ employee.designation?.name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Employment Type</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ employee.employment_type?.name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Branch ID</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.branch_id) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Shift</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ employee.shift?.name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Probation Start</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ formatDate(employee.probation_start_date) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Probation End</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ formatDate(employee.probation_end_date) }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Address &amp; Identification</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Address</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.address) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">National ID</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.national_id) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Passport No</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.passport_no) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tax Number</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.tax_number) }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Bank Details</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Bank Name</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.bank_name) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Bank Account</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ display(employee.bank_account) }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-base font-semibold text-slate-900">Emergency Contacts</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                    {{ employee.emergency_contacts?.length ?? 0 }}
                </span>
            </div>

            <div v-if="employee.emergency_contacts?.length" class="mt-5 grid gap-4 md:grid-cols-2">
                <article
                    v-for="contact in employee.emergency_contacts"
                    :key="contact.id"
                    class="rounded-xl border border-slate-200 bg-slate-50/50 p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-medium text-slate-900">{{ contact.name }}</h3>
                            <p class="mt-1 text-sm capitalize text-slate-500">{{ formatLabel(contact.relationship) }}</p>
                        </div>
                        <span class="rounded-full bg-white px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                            Priority {{ contact.priority }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-slate-700">{{ contact.phone }}</p>
                    <p v-if="contact.address" class="mt-2 text-sm text-slate-500">{{ contact.address }}</p>
                </article>
            </div>
            <p v-else class="mt-5 text-sm text-slate-500">No emergency contacts recorded.</p>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-base font-semibold text-slate-900">Documents</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                    {{ employee.documents?.length ?? 0 }}
                </span>
            </div>

            <div v-if="employee.documents?.length" class="mt-5 divide-y divide-slate-200 rounded-xl border border-slate-200">
                <div
                    v-for="document in employee.documents"
                    :key="document.id"
                    class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <p class="font-medium text-slate-900">{{ document.name }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500">{{ document.media?.original_name ?? '—' }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="text-xs text-slate-400">{{ formatDate(document.created_at) }}</span>
                        <a
                            v-if="document.media?.url"
                            :href="document.media.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            Download
                        </a>
                    </div>
                </div>
            </div>
            <p v-else class="mt-5 text-sm text-slate-500">No documents uploaded.</p>
        </section>
    </div>
</template>
