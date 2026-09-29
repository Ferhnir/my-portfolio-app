const baseUrl = import.meta.env.VITE_API_URL || 'https://api.portfolio.test'

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message)
        this.name = 'ApiError'
        this.status = status
        this.errors = errors
    }
}

export async function api(path, { method = 'GET', body, auth = true } = {}) {
    const headers = {
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

    const payload = await response.json().catch(() => ({}))

    if (!response.ok) {
        throw new ApiError(
            payload.message || 'Request failed.',
            response.status,
            payload.errors || {},
        )
    }

    return payload
}
