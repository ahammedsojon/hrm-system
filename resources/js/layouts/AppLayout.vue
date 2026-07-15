<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import AppSidebar from '../components/layout/AppSidebar.vue';

const router = useRouter();
const route = useRoute();
const { user, logout, isLoading } = useAuth();
const isMobileNavOpen = ref(false);

watch(() => route.fullPath, () => {
    isMobileNavOpen.value = false;
});

const userInitials = computed(() => {
    const name = user.value?.name ?? '';
    return name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});

async function handleLogout() {
    await logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <div class="flex min-h-screen bg-slate-50">
        <div
            v-if="isMobileNavOpen"
            class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"
            @click="isMobileNavOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:static lg:translate-x-0"
            :class="isMobileNavOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center gap-2.5 border-b border-slate-200 px-6">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white">
                    HR
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">HRM System</p>
                    <p class="text-xs text-slate-500">Administration</p>
                </div>
            </div>

            <AppSidebar />

            <div class="mt-auto border-t border-slate-200 p-4">
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-3 py-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white">
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-900">{{ user?.name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ user?.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur-sm sm:px-6">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                        @click="isMobileNavOpen = !isMobileNavOpen"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <p class="text-sm text-slate-500 lg:hidden">HRM System</p>
                </div>

                <button
                    type="button"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 disabled:opacity-50"
                    :disabled="isLoading"
                    @click="handleLogout"
                >
                    {{ isLoading ? 'Signing out...' : 'Sign out' }}
                </button>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
