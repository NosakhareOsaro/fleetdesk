import { GraphQLClient } from 'graphql-request';
import { useAuthStore } from '@/stores/auth';

export const graphqlClient = new GraphQLClient(`${window.location.origin}/graphql`);

export function authHeaders(): Record<string, string> {
    const { token } = useAuthStore();

    return token ? { Authorization: `Bearer ${token}` } : {};
}
