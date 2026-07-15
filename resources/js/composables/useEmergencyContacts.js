import { reactive, ref } from 'vue';
import { emergencyContactService } from '../services/emergencyContactService';
import { employeeService } from '../services/employeeService';

const defaultForm = () => ({
    employee_id: '',
    name: '',
    phone: '',
    relationship: '',
    address: '',
    priority: 1,
});

export function useEmergencyContacts() {
    const contacts = ref([]);
    const employees = ref([]);
    const pagination = ref(null);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchContacts(page = 1) {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await emergencyContactService.list({
                search: search.value || undefined,
                page,
            });
            contacts.value = data.data ?? data;
            pagination.value = data.meta ? {
                currentPage: data.meta.current_page,
                lastPage: data.meta.last_page,
                total: data.meta.total,
            } : null;
        } catch {
            error.value = 'Failed to load emergency contacts.';
            contacts.value = [];
            pagination.value = null;
        } finally {
            isLoading.value = false;
        }
    }

    async function loadEmployees() {
        const { data } = await employeeService.managers();
        employees.value = data.data ?? data;
    }

    function openCreateModal() {
        editingId.value = null;
        Object.assign(form, defaultForm());
        errors.value = {};
        showModal.value = true;
    }

    function openEditModal(contact) {
        editingId.value = contact.id;
        Object.assign(form, {
            employee_id: contact.employee_id ?? '',
            name: contact.name ?? '',
            phone: contact.phone ?? '',
            relationship: contact.relationship ?? '',
            address: contact.address ?? '',
            priority: contact.priority ?? 1,
        });
        errors.value = {};
        showModal.value = true;
    }

    function closeModal() {
        showModal.value = false;
        editingId.value = null;
        errors.value = {};
    }

    async function save() {
        isSaving.value = true;
        errors.value = {};

        const payload = {
            employee_id: form.employee_id,
            name: form.name,
            phone: form.phone,
            relationship: form.relationship,
            address: form.address || null,
            priority: form.priority || 1,
        };

        try {
            if (isEditing()) {
                await emergencyContactService.update(editingId.value, payload);
            } else {
                await emergencyContactService.create(payload);
            }

            closeModal();
            await fetchContacts(pagination.value?.currentPage ?? 1);
            return true;
        } catch (err) {
            if (err.response?.status === 422) {
                const validationErrors = err.response.data.errors ?? {};
                errors.value = Object.fromEntries(
                    Object.entries(validationErrors).map(([key, messages]) => [key, messages[0]]),
                );
            }
            return false;
        } finally {
            isSaving.value = false;
        }
    }

    async function remove(id) {
        await emergencyContactService.delete(id);
        await fetchContacts(pagination.value?.currentPage ?? 1);
    }

    return {
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
    };
}
