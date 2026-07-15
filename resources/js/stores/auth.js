import { defineStore } from 'pinia';
import { authService } from '../services/authService';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isLoading: false,
        isInitialized: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
    },

    actions: {
        async fetchUser() {
            this.isLoading = true;

            try {
                const { data } = await authService.fetchUser();
                this.user = data.data ?? data;
            } catch {
                this.user = null;
            } finally {
                this.isLoading = false;
                this.isInitialized = true;
            }
        },

        async login(credentials) {
            this.isLoading = true;

            try {
                const { data } = await authService.login(credentials);
                this.user = data.data ?? data;
                return this.user;
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            this.isLoading = true;

            try {
                await authService.logout();
            } finally {
                this.clearAuth();
                this.isLoading = false;
            }
        },

        clearAuth() {
            this.user = null;
        },
    },
});
