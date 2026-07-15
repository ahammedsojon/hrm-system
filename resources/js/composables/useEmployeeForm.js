import { computed, onUnmounted, reactive, ref } from 'vue';
import { departmentService } from '../services/departmentService';
import { designationService } from '../services/designationService';
import { employeeService } from '../services/employeeService';
import { employmentTypeService } from '../services/employmentTypeService';
import { shiftService } from '../services/shiftService';

const defaultForm = () => ({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    date_of_birth: '',
    blood_group: '',
    gender: '',
    department_id: '',
    designation_id: '',
    status: 'active',
    joining_date: '',
    probation_start_date: '',
    probation_end_date: '',
    employment_type_id: '',
    manager_id: '',
    branch_id: '',
    shift_id: '',
    marital_status: '',
    national_id: '',
    passport_no: '',
    tax_number: '',
    bank_account: '',
    bank_name: '',
    religion: '',
    nationality: '',
});

export function useEmployeeForm() {
    const form = reactive(defaultForm());
    const photo = ref(null);
    const existingProfilePhoto = ref(null);
    const photoPreviewUrl = ref(null);
    const removeProfilePhoto = ref(false);
    const departments = ref([]);
    const designations = ref([]);
    const managers = ref([]);
    const employmentTypes = ref([]);
    const shifts = ref([]);
    const errors = ref({});
    const isLoading = ref(false);
    const isSaving = ref(false);

    const displayPhoto = computed(() => {
        if (photoPreviewUrl.value) {
            return photoPreviewUrl.value;
        }

        if (!removeProfilePhoto.value && existingProfilePhoto.value) {
            return existingProfilePhoto.value;
        }

        return null;
    });

    function clearPhotoPreview() {
        if (photoPreviewUrl.value) {
            URL.revokeObjectURL(photoPreviewUrl.value);
            photoPreviewUrl.value = null;
        }
    }

    function setPhoto(file) {
        clearPhotoPreview();
        photo.value = file;
        removeProfilePhoto.value = false;

        if (file) {
            photoPreviewUrl.value = URL.createObjectURL(file);
        }
    }

    function removePhoto() {
        clearPhotoPreview();
        photo.value = null;

        if (existingProfilePhoto.value) {
            removeProfilePhoto.value = true;
            existingProfilePhoto.value = null;
        }
    }

    function resetPhotoState() {
        clearPhotoPreview();
        photo.value = null;
        existingProfilePhoto.value = null;
        removeProfilePhoto.value = false;
    }

    async function loadOptions(excludeId = null) {
        isLoading.value = true;

        try {
            const [departmentsRes, designationsRes, managersRes, employmentTypesRes, shiftsRes] = await Promise.all([
                departmentService.list(),
                designationService.list(),
                employeeService.managers(excludeId),
                employmentTypeService.list(),
                shiftService.list(),
            ]);

            departments.value = departmentsRes.data.data ?? departmentsRes.data;
            designations.value = designationsRes.data.data ?? designationsRes.data;
            managers.value = managersRes.data.data ?? managersRes.data;
            employmentTypes.value = employmentTypesRes.data.data ?? employmentTypesRes.data;
            shifts.value = shiftsRes.data.data ?? shiftsRes.data;
        } finally {
            isLoading.value = false;
        }
    }

    async function loadEmployee(id) {
        isLoading.value = true;
        errors.value = {};

        try {
            const { data } = await employeeService.get(id);
            const employee = data.data ?? data;

            Object.assign(form, {
                first_name: employee.first_name ?? '',
                last_name: employee.last_name ?? '',
                email: employee.email ?? '',
                phone: employee.phone ?? '',
                address: employee.address ?? '',
                date_of_birth: employee.date_of_birth ?? '',
                blood_group: employee.blood_group ?? '',
                gender: employee.gender ?? '',
                department_id: employee.department_id ?? '',
                designation_id: employee.designation_id ?? '',
                status: employee.status ?? 'active',
                joining_date: employee.joining_date ?? '',
                probation_start_date: employee.probation_start_date ?? '',
                probation_end_date: employee.probation_end_date ?? '',
                employment_type_id: employee.employment_type_id ?? '',
                manager_id: employee.manager_id ?? '',
                branch_id: employee.branch_id ?? '',
                shift_id: employee.shift_id ?? '',
                marital_status: employee.marital_status ?? '',
                national_id: employee.national_id ?? '',
                passport_no: employee.passport_no ?? '',
                tax_number: employee.tax_number ?? '',
                bank_account: employee.bank_account ?? '',
                bank_name: employee.bank_name ?? '',
                religion: employee.religion ?? '',
                nationality: employee.nationality ?? '',
            });

            resetPhotoState();
            existingProfilePhoto.value = employee.profile_photo ?? null;
        } finally {
            isLoading.value = false;
        }
    }

    function resetForm() {
        Object.assign(form, defaultForm());
        resetPhotoState();
        errors.value = {};
    }

    function buildFormData() {
        const formData = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            if (value !== '' && value !== null && value !== undefined) {
                formData.append(key, value);
            }
        });

        if (photo.value) {
            formData.append('profile_photo', photo.value);
        }

        if (removeProfilePhoto.value) {
            formData.append('remove_profile_photo', '1');
        }

        return formData;
    }

    async function save(id = null) {
        isSaving.value = true;
        errors.value = {};

        try {
            const formData = buildFormData();

            if (id) {
                formData.append('_method', 'PUT');
                await employeeService.update(id, formData);
            } else {
                await employeeService.create(formData);
            }

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

    onUnmounted(() => {
        clearPhotoPreview();
    });

    return {
        form,
        photo,
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
    };
}
