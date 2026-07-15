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
        path: '/employees/:id/edit',
        name: 'employees.edit',
        component: () => import('../pages/employees/EmployeeFormPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/departments',
        name: 'departments',
        component: () => import('../pages/employees/DepartmentsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/designations',
        name: 'designations',
        component: () => import('../pages/employees/DesignationsPage.vue'),
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
