import { ref } from 'vue';
import { employeeService } from '../services/employeeService';

export function useEmployees() {
    const employees = ref([]);
    const pagination = ref(null);
    const isLoading = ref(false);
    const search = ref('');
    const error = ref('');

    async function fetchEmployees(page = 1) {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await employeeService.list({
                search: search.value || undefined,
                page,
            });

            employees.value = data.data ?? [];
            pagination.value = data.meta ?? null;
        } catch (err) {
            error.value = 'Failed to load employees.';
            employees.value = [];
            pagination.value = null;
        } finally {
            isLoading.value = false;
        }
    }

    async function deleteEmployee(id) {
        await employeeService.delete(id);
        await fetchEmployees(pagination.value?.current_page ?? 1);
    }

    return {
        employees,
        pagination,
        isLoading,
        search,
        error,
        fetchEmployees,
        deleteEmployee,
    };
}
