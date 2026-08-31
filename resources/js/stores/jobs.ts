import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { ResultOf } from '@graphql-typed-document-node/core';
import { graphql } from '@/generated';
import { graphqlClient, authHeaders } from '@/lib/graphql-client';
import type { JobStatus } from '@/generated/graphql';

const jobsQuery = graphql(`
    query Jobs($status: JobStatus, $driverId: ID) {
        jobs(status: $status, driverId: $driverId) {
            id
            status
            notes
            scheduledAt
            completedAt
            customer {
                id
                name
            }
            driver {
                id
                user {
                    id
                    name
                }
            }
        }
    }
`);

const driversQuery = graphql(`
    query Drivers {
        drivers {
            id
            status
            user {
                id
                name
            }
        }
    }
`);

const assignDriverMutation = graphql(`
    mutation AssignDriver($jobId: ID!, $driverId: ID!) {
        assignDriver(jobId: $jobId, driverId: $driverId) {
            id
            status
            driver {
                id
                user {
                    name
                }
            }
        }
    }
`);

const updateJobStatusMutation = graphql(`
    mutation UpdateJobStatus($jobId: ID!, $status: JobStatus!) {
        updateJobStatus(jobId: $jobId, status: $status) {
            id
            status
            completedAt
        }
    }
`);

export type Job = ResultOf<typeof jobsQuery>['jobs'][number];
export type Driver = ResultOf<typeof driversQuery>['drivers'][number];

export const useJobsStore = defineStore('jobs', () => {
    const jobs = ref<Job[]>([]);
    const drivers = ref<Driver[]>([]);
    const statusFilter = ref<JobStatus | null>(null);
    const driverFilter = ref<string | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchJobs() {
        loading.value = true;
        error.value = null;

        try {
            // Lighthouse's @eq applies an explicit `null` as `WHERE col IS NULL`,
            // so omit these keys entirely rather than sending null to mean "no filter".
            const variables: { status?: JobStatus; driverId?: string } = {};
            if (statusFilter.value) {
                variables.status = statusFilter.value;
            }
            if (driverFilter.value) {
                variables.driverId = driverFilter.value;
            }

            const result = await graphqlClient.request(jobsQuery, variables, authHeaders());
            jobs.value = result.jobs;
        } catch (e) {
            error.value = e instanceof Error ? e.message : 'Failed to load jobs.';
        } finally {
            loading.value = false;
        }
    }

    async function fetchDrivers() {
        const result = await graphqlClient.request(driversQuery, {}, authHeaders());
        drivers.value = result.drivers;
    }

    async function assignDriver(jobId: string, driverId: string) {
        error.value = null;

        try {
            await graphqlClient.request(assignDriverMutation, { jobId, driverId }, authHeaders());
            await fetchJobs();
        } catch (e) {
            error.value = e instanceof Error ? e.message : 'Failed to assign driver.';
        }
    }

    async function updateStatus(jobId: string, status: JobStatus) {
        error.value = null;

        try {
            await graphqlClient.request(updateJobStatusMutation, { jobId, status }, authHeaders());
            await fetchJobs();
        } catch (e) {
            error.value = e instanceof Error ? e.message : 'Failed to update job status.';
        }
    }

    return {
        jobs,
        drivers,
        statusFilter,
        driverFilter,
        loading,
        error,
        fetchJobs,
        fetchDrivers,
        assignDriver,
        updateStatus,
    };
});
