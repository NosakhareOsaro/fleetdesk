export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface AuthResponse {
    token: string;
    user: AuthUser;
}

async function post<T>(path: string, body: unknown, token?: string): Promise<T> {
    const response = await fetch(`/api${path}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: JSON.stringify(body),
    });

    if (!response.ok) {
        const payload = await response.json().catch(() => null);
        throw new Error(payload?.message ?? `Request to ${path} failed with ${response.status}`);
    }

    if (response.status === 204) {
        return undefined as T;
    }

    return response.json() as Promise<T>;
}

export function login(email: string, password: string): Promise<AuthResponse> {
    return post<AuthResponse>('/login', { email, password });
}

export function register(name: string, email: string, password: string, passwordConfirmation: string): Promise<AuthResponse> {
    return post<AuthResponse>('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
    });
}

export function logout(token: string): Promise<void> {
    return post<void>('/logout', {}, token);
}
