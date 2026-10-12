import { toastApiError } from '@/toast'

const baseUrl = import.meta.env.VITE_API_URL || 'https://api.portfolio.test'

export interface ApiResponse<T> {
    data: T
    message?: string
}

interface ApiFailure {
    message?: string
    errors?: Record<string, string[]>
}

export interface ApiRequest {
    method?: string
    body?: unknown
    auth?: boolean
    toast?: boolean
}

export class ApiError extends Error {
    status: number
    errors: Record<string, string[]>

    constructor(message: string, status: number, errors: Record<string, string[]> = {}) {
        super(message)
        this.name = 'ApiError'
        this.status = status
        this.errors = errors
    }
}

export async function api<T>(path: string, { method = 'GET', body, auth = true, toast = true }: ApiRequest = {}): Promise<T> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
    }

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json'
    }

    if (auth) {
        const token = localStorage.getItem('sanctum_token')

        if (token) {
            headers.Authorization = `Bearer ${token}`
        }
    }

    const response = await fetch(`${baseUrl}/api${path}`, {
        method,
        headers,
        body: body === undefined ? undefined : JSON.stringify(body),
    })

    const payload = await response.json().catch(() => ({})) as ApiFailure & T

    if (!response.ok) {
        const error = new ApiError(
            payload.message || 'Request failed.',
            response.status,
            payload.errors || {},
        )

        if (toast) {
            toastApiError(error)
        }

        throw error
    }

    return payload
}
