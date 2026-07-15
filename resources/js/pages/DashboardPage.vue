<script setup>
import { computed } from 'vue';
import { useAuth } from '../composables/useAuth';
import AppLayout from '../layouts/AppLayout.vue';

const { user } = useAuth();

const today = computed(() =>
    new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }),
);

const stats = [
    {
        label: 'Total Employees',
        value: '—',
        change: 'Awaiting data',
        icon: 'users',
        accent: 'bg-blue-50 text-blue-600',
    },
    {
        label: 'Departments',
        value: '—',
        change: 'Awaiting data',
        icon: 'building',
        accent: 'bg-violet-50 text-violet-600',
    },
    {
        label: 'Designations',
        value: '—',
        change: 'Awaiting data',
        icon: 'badge',
        accent: 'bg-amber-50 text-amber-600',
    },
    {
        label: 'Active Today',
        value: '—',
        change: 'Awaiting data',
        icon: 'activity',
        accent: 'bg-emerald-50 text-emerald-600',
    },
];

const quickLinks = [
    {
        title: 'Employees',
        description: 'View and manage employee records',
        to: { name: 'employees' },
    },
    {
        title: 'Departments',
        description: 'Organize teams and structure',
        to: { name: 'departments' },
    },
    {
        title: 'Designations',
        description: 'Manage job titles and roles',
        to: { name: 'designations' },
    },
];

const iconPaths = {
    users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    building: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    badge: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    activity: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
};
</script>

<template>
    <AppLayout>
        <div class="space-y-8">
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-sm sm:p-8">
                <p class="text-sm font-medium text-slate-300">{{ today }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">
                    Welcome back, {{ user?.name }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-300">
                    Monitor your organization at a glance. Manage employees, departments, and designations from one central dashboard.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ stat.label }}</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ stat.value }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="stat.accent">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPaths[stat.icon]" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">{{ stat.change }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Quick Access</h2>
                            <p class="mt-1 text-sm text-slate-500">Jump to frequently used modules</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <router-link
                            v-for="link in quickLinks"
                            :key="link.title"
                            :to="link.to"
                            class="group rounded-xl border border-slate-200 p-4 transition-all hover:border-slate-300 hover:shadow-sm"
                        >
                            <p class="text-sm font-medium text-slate-900 group-hover:text-slate-700">{{ link.title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ link.description }}</p>
                            <span class="mt-3 inline-flex items-center text-xs font-medium text-slate-400 group-hover:text-slate-600">
                                Open
                                <svg class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </router-link>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">System Status</h2>
                    <p class="mt-1 text-sm text-slate-500">Platform overview</p>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                            <span class="text-sm text-slate-600">Authentication</span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                            <span class="text-sm text-slate-600">Employee Module</span>
                            <span class="text-xs font-medium text-amber-700">In development</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                            <span class="text-sm text-slate-600">Your Role</span>
                            <span class="text-xs font-medium text-slate-700">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
