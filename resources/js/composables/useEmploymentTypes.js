import { reactive, ref } from 'vue';
import { employmentTypeService } from '../services/employmentTypeService';

const defaultForm = () => ({
    name: '',
});

export function useEmploymentTypes() {
    const employmentTypes = ref([]);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchEmploymentTypes() {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await employmentTypeService.list({
                search: search.value || undefined,
            });
            employmentTypes.value = data.data ?? data;
        } catch {
            error.value = 'Failed to load employment types.';
            employmentTypes.value = [];
        } finally {
            isLoading.value = false;
        }
    }

    function openCreateModal() {
        editingId.value = null;
        Object.assign(form, defaultForm());
        errors.value = {};
        showModal.value = true;
    }

    function openEditModal(employmentType) {
        editingId.value = employmentType.id;
        Object.assign(form, {
            name: employmentType.name ?? '',
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

        try {
            if (isEditing()) {
                await employmentTypeService.update(editingId.value, { name: form.name });
            } else {
                await employmentTypeService.create({ name: form.name });
            }

            closeModal();
            await fetchEmploymentTypes();
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
        await employmentTypeService.delete(id);
        await fetchEmploymentTypes();
    }

    return {
        employmentTypes,
        isLoading,
        isSaving,
        search,
        error,
        showModal,
        form,
        errors,
        isEditing,
        fetchEmploymentTypes,
        openCreateModal,
        openEditModal,
        closeModal,
        save,
        remove,
    };
}
