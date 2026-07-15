import { reactive, ref } from 'vue';
import { departmentService } from '../services/departmentService';
import { employeeService } from '../services/employeeService';

const defaultForm = () => ({
    name: '',
    description: '',
    manager_employee_id: '',
});

export function useDepartments() {
    const departments = ref([]);
    const employees = ref([]);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchDepartments() {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await departmentService.list({
                search: search.value || undefined,
            });
            departments.value = data.data ?? data;
        } catch {
            error.value = 'Failed to load departments.';
            departments.value = [];
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

    function openEditModal(department) {
        editingId.value = department.id;
        Object.assign(form, {
            name: department.name ?? '',
            description: department.description ?? '',
            manager_employee_id: department.manager_employee_id ?? '',
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
            name: form.name,
            description: form.description || null,
            manager_employee_id: form.manager_employee_id || null,
        };

        try {
            if (isEditing()) {
                await departmentService.update(editingId.value, payload);
            } else {
                await departmentService.create(payload);
            }

            closeModal();
            await fetchDepartments();
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
        await departmentService.delete(id);
        await fetchDepartments();
    }

    return {
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
    };
}
