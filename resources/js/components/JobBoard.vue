<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useJobsStore } from '@/stores/jobs';
import type { JobStatus } from '@/generated/graphql';

const store = useJobsStore();

const statusOptions: { label: string; value: JobStatus | null }[] = [
    { label: 'All statuses', value: null },
    { label: 'Pending', value: 'PENDING' },
    { label: 'Assigned', value: 'ASSIGNED' },
    { label: 'In transit', value: 'IN_TRANSIT' },
    { label: 'Completed', value: 'COMPLETED' },
    { label: 'Cancelled', value: 'CANCELLED' },
];

const driverSelections = ref<Record<string, string>>({});

onMounted(async () => {
    await Promise.all([store.fetchJobs(), store.fetchDrivers()]);
});

function onFilterChange() {
    void store.fetchJobs();
}

function assign(jobId: string) {
    const driverId = driverSelections.value[jobId];
    if (!driverId) {
        return;
    }

    void store.assignDriver(jobId, driverId);
}

function driverName(job: { driver: { user: { name: string } } | null }): string {
    return job.driver?.user.name ?? '—';
}
</script>

<template>
    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-4 flex items-center gap-4">
            <select v-model="store.statusFilter" class="rounded border border-gray-300 px-2 py-1 text-sm" @change="onFilterChange">
                <option v-for="option in statusOptions" :key="option.label" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <select v-model="store.driverFilter" class="rounded border border-gray-300 px-2 py-1 text-sm" @change="onFilterChange">
                <option :value="null">All drivers</option>
                <option v-for="driver in store.drivers" :key="driver.id" :value="driver.id">
                    {{ driver.user.name }}
                </option>
            </select>
        </div>

        <p v-if="store.error" class="mb-4 text-sm text-red-600">{{ store.error }}</p>
        <p v-if="store.loading" class="text-sm text-gray-500">Loading jobs…</p>

        <table v-else class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-300 text-left text-gray-500">
                    <th class="py-2 pr-4">Customer</th>
                    <th class="py-2 pr-4">Status</th>
                    <th class="py-2 pr-4">Driver</th>
                    <th class="py-2 pr-4">Scheduled</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="job in store.jobs" :key="job.id" class="border-b border-gray-100">
                    <td class="py-2 pr-4">{{ job.customer.name }}</td>
                    <td class="py-2 pr-4">{{ job.status }}</td>
                    <td class="py-2 pr-4">{{ driverName(job) }}</td>
                    <td class="py-2 pr-4">{{ job.scheduledAt ?? '—' }}</td>
                    <td class="py-2">
                        <div v-if="job.status === 'PENDING'" class="flex gap-2">
                            <select v-model="driverSelections[job.id]" class="rounded border border-gray-300 px-2 py-1 text-xs">
                                <option value="">Select driver</option>
                                <option v-for="driver in store.drivers" :key="driver.id" :value="driver.id">
                                    {{ driver.user.name }}
                                </option>
                            </select>
                            <button class="rounded bg-gray-900 px-2 py-1 text-xs text-white" @click="assign(job.id)">
                                Assign
                            </button>
                        </div>

                        <div v-else-if="job.status === 'ASSIGNED'" class="flex gap-2">
                            <button
                                class="rounded bg-gray-900 px-2 py-1 text-xs text-white"
                                @click="store.updateStatus(job.id, 'IN_TRANSIT')"
                            >
                                Start transit
                            </button>
                            <button
                                class="rounded border border-gray-300 px-2 py-1 text-xs"
                                @click="store.updateStatus(job.id, 'CANCELLED')"
                            >
                                Cancel
                            </button>
                        </div>

                        <div v-else-if="job.status === 'IN_TRANSIT'" class="flex gap-2">
                            <button
                                class="rounded bg-gray-900 px-2 py-1 text-xs text-white"
                                @click="store.updateStatus(job.id, 'COMPLETED')"
                            >
                                Complete
                            </button>
                            <button
                                class="rounded border border-gray-300 px-2 py-1 text-xs"
                                @click="store.updateStatus(job.id, 'CANCELLED')"
                            >
                                Cancel
                            </button>
                        </div>

                        <span v-else class="text-xs text-gray-400">No actions</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
