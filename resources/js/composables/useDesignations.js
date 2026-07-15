import { reactive, ref } from 'vue';
import { designationService } from '../services/designationService';

const defaultForm = () => ({
    name: '',
    description: '',
});

export function useDesignations() {
    const designations = ref([]);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchDesignations() {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await designationService.list({
                search: search.value || undefined,
            });
            designations.value = data.data ?? data;
        } catch {
            error.value = 'Failed to load designations.';
            designations.value = [];
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

    function openEditModal(designation) {
        editingId.value = designation.id;
        Object.assign(form, {
            name: designation.name ?? '',
            description: designation.description ?? '',
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
        };

        try {
            if (isEditing()) {
                await designationService.update(editingId.value, payload);
            } else {
                await designationService.create(payload);
            }

            closeModal();
            await fetchDesignations();
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
        await designationService.delete(id);
        await fetchDesignations();
    }

    return {
        designations,
        isLoading,
        isSaving,
        search,
        error,
        showModal,
        form,
        errors,
        isEditing,
        fetchDesignations,
        openCreateModal,
        openEditModal,
        closeModal,
        save,
        remove,
    };
}
