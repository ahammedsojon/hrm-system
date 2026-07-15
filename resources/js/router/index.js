import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/LoginPage.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        name: 'dashboard',
        component: () => import('../pages/DashboardPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/employees',
        name: 'employees',
        component: () => import('../pages/employees/EmployeesPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/employees/create',
        name: 'employees.create',
        component: () => import('../pages/employees/EmployeeFormPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/employees/:id',
        name: 'employees.show',
        component: () => import('../pages/employees/EmployeeShowPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/employees/:id/edit',
        name: 'employees.edit',
        component: () => import('../pages/employees/EmployeeFormPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/documents',
        name: 'documents',
        component: () => import('../pages/employees/DocumentsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/emergency-contacts',
        name: 'emergency-contacts',
        component: () => import('../pages/employees/EmergencyContactsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/company',
        name: 'company',
        component: () => import('../pages/organization/CompanyPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/branches',
        name: 'branches',
        component: () => import('../pages/organization/BranchesPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/departments',
        name: 'departments',
        component: () => import('../pages/organization/DepartmentsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/designations',
        name: 'designations',
        component: () => import('../pages/organization/DesignationsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/employment-types',
        name: 'employment-types',
        component: () => import('../pages/organization/EmploymentTypesPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/shifts',
        name: 'shifts',
        component: () => import('../pages/organization/ShiftsPage.vue'),
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (!authStore.isInitialized) {
        await authStore.fetchUser();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'login' });
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return next({ name: 'dashboard' });
    }

    return next();
});

export default router;
