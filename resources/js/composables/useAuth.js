import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../stores/auth';

export function useAuth() {
    const authStore = useAuthStore();
    const { user, isLoading, isAuthenticated, isInitialized } = storeToRefs(authStore);

    return {
        user,
        isLoading,
        isAuthenticated,
        isInitialized,
        login: authStore.login,
        logout: authStore.logout,
        fetchUser: authStore.fetchUser,
        clearAuth: authStore.clearAuth,
    };
}
