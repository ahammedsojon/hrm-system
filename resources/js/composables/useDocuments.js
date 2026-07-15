import { reactive, ref } from 'vue';
import { documentService } from '../services/documentService';
import { employeeService } from '../services/employeeService';

const defaultForm = () => ({
    employee_id: '',
    name: '',
});

export function useDocuments() {
    const documents = ref([]);
    const employees = ref([]);
    const pagination = ref(null);
    const file = ref(null);
    const existingFileName = ref(null);
    const removeFile = ref(false);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchDocuments(page = 1) {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await documentService.list({
                search: search.value || undefined,
                page,
            });
            documents.value = data.data ?? data;
            pagination.value = data.meta ? {
                currentPage: data.meta.current_page,
                lastPage: data.meta.last_page,
                total: data.meta.total,
            } : null;
        } catch {
            error.value = 'Failed to load documents.';
            documents.value = [];
            pagination.value = null;
        } finally {
            isLoading.value = false;
        }
    }

    async function loadEmployees() {
        const { data } = await employeeService.managers();
        employees.value = data.data ?? data;
    }

    function setFile(selectedFile) {
        file.value = selectedFile;

        if (selectedFile) {
            removeFile.value = false;
        }
    }

    function clearFile() {
        file.value = null;
    }

    function removeExistingFile() {
        existingFileName.value = null;
        removeFile.value = true;
        file.value = null;
    }

    function openCreateModal() {
        editingId.value = null;
        Object.assign(form, defaultForm());
        file.value = null;
        existingFileName.value = null;
        removeFile.value = false;
        errors.value = {};
        showModal.value = true;
    }

    function openEditModal(document) {
        editingId.value = document.id;
        Object.assign(form, {
            employee_id: document.employee_id ?? '',
            name: document.name ?? '',
        });
        file.value = null;
        existingFileName.value = document.media?.original_name ?? null;
        removeFile.value = false;
        errors.value = {};
        showModal.value = true;
    }

    function closeModal() {
        showModal.value = false;
        editingId.value = null;
        file.value = null;
        existingFileName.value = null;
        removeFile.value = false;
        errors.value = {};
    }

    function buildFormData() {
        const formData = new FormData();
        formData.append('employee_id', form.employee_id);
        formData.append('name', form.name);

        if (file.value) {
            formData.append('file', file.value);
        }

        if (removeFile.value) {
            formData.append('remove_file', '1');
        }

        return formData;
    }

    async function save() {
        isSaving.value = true;
        errors.value = {};

        try {
            const formData = buildFormData();

            if (isEditing()) {
                formData.append('_method', 'PUT');
                await documentService.update(editingId.value, formData);
            } else {
                await documentService.create(formData);
            }

            closeModal();
            await fetchDocuments(pagination.value?.currentPage ?? 1);
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
        await documentService.delete(id);
        await fetchDocuments(pagination.value?.currentPage ?? 1);
    }

    return {
        documents,
        employees,
        pagination,
        file,
        existingFileName,
        isLoading,
        isSaving,
        search,
        error,
        showModal,
        form,
        errors,
        isEditing,
        fetchDocuments,
        loadEmployees,
        setFile,
        clearFile,
        removeExistingFile,
        openCreateModal,
        openEditModal,
        closeModal,
        save,
        remove,
    };
}
