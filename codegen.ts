import type { CodegenConfig } from '@graphql-codegen/cli';

const config: CodegenConfig = {
    schema: process.env.GRAPHQL_SCHEMA_URL ?? 'http://nginx/graphql',
    documents: 'resources/js/**/*.{ts,vue}',
    generates: {
        'resources/js/generated/': {
            preset: 'client',
        },
    },
};

export default config;
