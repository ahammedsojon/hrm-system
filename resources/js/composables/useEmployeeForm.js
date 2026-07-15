import { reactive, ref } from 'vue';
import { departmentService } from '../services/departmentService';
import { designationService } from '../services/designationService';
import { employeeService } from '../services/employeeService';

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
    const departments = ref([]);
    const designations = ref([]);
    const managers = ref([]);
    const errors = ref({});
    const isLoading = ref(false);
    const isSaving = ref(false);

    async function loadOptions(excludeId = null) {
        isLoading.value = true;

        try {
            const [departmentsRes, designationsRes, managersRes] = await Promise.all([
                departmentService.list(),
                designationService.list(),
                employeeService.managers(excludeId),
            ]);

            departments.value = departmentsRes.data.data ?? departmentsRes.data;
            designations.value = designationsRes.data.data ?? designationsRes.data;
            managers.value = managersRes.data.data ?? managersRes.data;
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
        } finally {
            isLoading.value = false;
        }
    }

    function resetForm() {
        Object.assign(form, defaultForm());
        photo.value = null;
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

    return {
        form,
        photo,
        departments,
        designations,
        managers,
        errors,
        isLoading,
        isSaving,
        loadOptions,
        loadEmployee,
        resetForm,
        save,
    };
}
