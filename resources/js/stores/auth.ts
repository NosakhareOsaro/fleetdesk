import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import * as api from '@/lib/api-client';
import type { AuthUser } from '@/lib/api-client';

const TOKEN_KEY = 'fleetdesk_token';

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem(TOKEN_KEY));
    const user = ref<AuthUser | null>(null);

    const isAuthenticated = computed(() => token.value !== null);

    async function login(email: string, password: string) {
        const response = await api.login(email, password);
        token.value = response.token;
        user.value = response.user;
        localStorage.setItem(TOKEN_KEY, response.token);
    }

    async function logout() {
        if (token.value) {
            await api.logout(token.value).catch(() => undefined);
        }

        token.value = null;
        user.value = null;
        localStorage.removeItem(TOKEN_KEY);
    }

    return { token, user, isAuthenticated, login, logout };
});
