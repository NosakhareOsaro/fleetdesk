<script setup lang="ts">
import { onBeforeUnmount, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useJobsStore } from '@/stores/jobs';
import { connectEcho, disconnectEcho } from '@/lib/echo';
import LoginForm from '@/components/LoginForm.vue';
import JobBoard from '@/components/JobBoard.vue';

const auth = useAuthStore();
const jobs = useJobsStore();

watch(
    () => auth.token,
    (token) => {
        disconnectEcho();

        if (!token) {
            return;
        }

        connectEcho(token)
            .private('jobs')
            .listen('.job.status-updated', () => jobs.fetchJobs());
    },
    { immediate: true },
);

onBeforeUnmount(() => disconnectEcho());
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <header class="flex items-center justify-between border-b border-gray-200 px-6 py-3">
            <span class="font-semibold">FleetDesk</span>

            <LoginForm v-if="!auth.isAuthenticated" />
            <div v-else class="flex items-center gap-3 text-sm">
                <span>{{ auth.user?.name }}</span>
                <button class="text-gray-500 hover:text-gray-900" @click="auth.logout()">Sign out</button>
            </div>
        </header>

        <JobBoard />
    </div>
</template>
