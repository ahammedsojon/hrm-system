import { reactive, ref } from 'vue';
import { shiftService } from '../services/shiftService';

const defaultForm = () => ({
    name: '',
    start_time: '',
    end_time: '',
    working_hours: '',
});

export function useShifts() {
    const shifts = ref([]);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const search = ref('');
    const error = ref('');
    const showModal = ref(false);
    const editingId = ref(null);
    const form = reactive(defaultForm());
    const errors = ref({});

    const isEditing = () => editingId.value !== null;

    async function fetchShifts() {
        isLoading.value = true;
        error.value = '';

        try {
            const { data } = await shiftService.list({
                search: search.value || undefined,
            });
            shifts.value = data.data ?? data;
        } catch {
            error.value = 'Failed to load shifts.';
            shifts.value = [];
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

    function openEditModal(shift) {
        editingId.value = shift.id;
        Object.assign(form, {
            name: shift.name ?? '',
            start_time: shift.start_time ?? '',
            end_time: shift.end_time ?? '',
            working_hours: shift.working_hours ?? '',
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
            start_time: form.start_time,
            end_time: form.end_time,
            working_hours: form.working_hours,
        };

        try {
            if (isEditing()) {
                await shiftService.update(editingId.value, payload);
            } else {
                await shiftService.create(payload);
            }

            closeModal();
            await fetchShifts();
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
        await shiftService.delete(id);
        await fetchShifts();
    }

    return {
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
    };
}
