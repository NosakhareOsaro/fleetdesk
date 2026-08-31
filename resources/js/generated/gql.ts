/* eslint-disable */
import * as types from './graphql';
import { TypedDocumentNode as DocumentNode } from '@graphql-typed-document-node/core';

/**
 * Map of all GraphQL operations in the project.
 *
 * This map has several performance disadvantages:
 * 1. It is not tree-shakeable, so it will include all operations in the project.
 * 2. It is not minifiable, so the string of a GraphQL query will be multiple times inside the bundle.
 * 3. It does not support dead code elimination, so it will add unused operations.
 *
 * Therefore it is highly recommended to use the babel or swc plugin for production.
 * Learn more about it here: https://the-guild.dev/graphql/codegen/plugins/presets/preset-client#reducing-bundle-size
 */
type Documents = {
    "\n    query Jobs($status: JobStatus, $driverId: ID) {\n        jobs(status: $status, driverId: $driverId) {\n            id\n            status\n            notes\n            scheduledAt\n            completedAt\n            customer {\n                id\n                name\n            }\n            driver {\n                id\n                user {\n                    id\n                    name\n                }\n            }\n        }\n    }\n": typeof types.JobsDocument,
    "\n    query Drivers {\n        drivers {\n            id\n            status\n            user {\n                id\n                name\n            }\n        }\n    }\n": typeof types.DriversDocument,
    "\n    mutation AssignDriver($jobId: ID!, $driverId: ID!) {\n        assignDriver(jobId: $jobId, driverId: $driverId) {\n            id\n            status\n            driver {\n                id\n                user {\n                    name\n                }\n            }\n        }\n    }\n": typeof types.AssignDriverDocument,
    "\n    mutation UpdateJobStatus($jobId: ID!, $status: JobStatus!) {\n        updateJobStatus(jobId: $jobId, status: $status) {\n            id\n            status\n            completedAt\n        }\n    }\n": typeof types.UpdateJobStatusDocument,
};
const documents: Documents = {
    "\n    query Jobs($status: JobStatus, $driverId: ID) {\n        jobs(status: $status, driverId: $driverId) {\n            id\n            status\n            notes\n            scheduledAt\n            completedAt\n            customer {\n                id\n                name\n            }\n            driver {\n                id\n                user {\n                    id\n                    name\n                }\n            }\n        }\n    }\n": types.JobsDocument,
    "\n    query Drivers {\n        drivers {\n            id\n            status\n            user {\n                id\n                name\n            }\n        }\n    }\n": types.DriversDocument,
    "\n    mutation AssignDriver($jobId: ID!, $driverId: ID!) {\n        assignDriver(jobId: $jobId, driverId: $driverId) {\n            id\n            status\n            driver {\n                id\n                user {\n                    name\n                }\n            }\n        }\n    }\n": types.AssignDriverDocument,
    "\n    mutation UpdateJobStatus($jobId: ID!, $status: JobStatus!) {\n        updateJobStatus(jobId: $jobId, status: $status) {\n            id\n            status\n            completedAt\n        }\n    }\n": types.UpdateJobStatusDocument,
};

/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 *
 *
 * @example
 * ```ts
 * const query = graphql(`query GetUser($id: ID!) { user(id: $id) { name } }`);
 * ```
 *
 * The query argument is unknown!
 * Please regenerate the types.
 */
export function graphql(source: string): unknown;

/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(source: "\n    query Jobs($status: JobStatus, $driverId: ID) {\n        jobs(status: $status, driverId: $driverId) {\n            id\n            status\n            notes\n            scheduledAt\n            completedAt\n            customer {\n                id\n                name\n            }\n            driver {\n                id\n                user {\n                    id\n                    name\n                }\n            }\n        }\n    }\n"): (typeof documents)["\n    query Jobs($status: JobStatus, $driverId: ID) {\n        jobs(status: $status, driverId: $driverId) {\n            id\n            status\n            notes\n            scheduledAt\n            completedAt\n            customer {\n                id\n                name\n            }\n            driver {\n                id\n                user {\n                    id\n                    name\n                }\n            }\n        }\n    }\n"];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(source: "\n    query Drivers {\n        drivers {\n            id\n            status\n            user {\n                id\n                name\n            }\n        }\n    }\n"): (typeof documents)["\n    query Drivers {\n        drivers {\n            id\n            status\n            user {\n                id\n                name\n            }\n        }\n    }\n"];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(source: "\n    mutation AssignDriver($jobId: ID!, $driverId: ID!) {\n        assignDriver(jobId: $jobId, driverId: $driverId) {\n            id\n            status\n            driver {\n                id\n                user {\n                    name\n                }\n            }\n        }\n    }\n"): (typeof documents)["\n    mutation AssignDriver($jobId: ID!, $driverId: ID!) {\n        assignDriver(jobId: $jobId, driverId: $driverId) {\n            id\n            status\n            driver {\n                id\n                user {\n                    name\n                }\n            }\n        }\n    }\n"];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(source: "\n    mutation UpdateJobStatus($jobId: ID!, $status: JobStatus!) {\n        updateJobStatus(jobId: $jobId, status: $status) {\n            id\n            status\n            completedAt\n        }\n    }\n"): (typeof documents)["\n    mutation UpdateJobStatus($jobId: ID!, $status: JobStatus!) {\n        updateJobStatus(jobId: $jobId, status: $status) {\n            id\n            status\n            completedAt\n        }\n    }\n"];

export function graphql(source: string) {
  return (documents as any)[source] ?? {};
}

export type DocumentType<TDocumentNode extends DocumentNode<any, any>> = TDocumentNode extends DocumentNode<  infer TType,  any>  ? TType  : never;