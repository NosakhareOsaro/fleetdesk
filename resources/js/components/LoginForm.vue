<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

const email = ref('');
const password = ref('');
const error = ref<string | null>(null);
const submitting = ref(false);

async function onSubmit() {
    submitting.value = true;
    error.value = null;

    try {
        await auth.login(email.value, password.value);
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Login failed.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <form class="flex items-end gap-2" @submit.prevent="onSubmit">
        <div>
            <label class="block text-xs text-gray-500" for="email">Email</label>
            <input
                id="email"
                v-model="email"
                type="email"
                required
                class="rounded border border-gray-300 px-2 py-1 text-sm"
            />
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="password">Password</label>
            <input
                id="password"
                v-model="password"
                type="password"
                required
                class="rounded border border-gray-300 px-2 py-1 text-sm"
            />
        </div>
        <button
            type="submit"
            :disabled="submitting"
            class="rounded bg-gray-900 px-3 py-1 text-sm text-white disabled:opacity-50"
        >
            {{ submitting ? 'Signing in…' : 'Sign in' }}
        </button>
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </form>
</template>
