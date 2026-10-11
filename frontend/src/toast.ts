import { reactive } from 'vue'

interface ApiFailureError extends Error {
    status: number
    errors?: Record<string, string[]>
}

function isApiFailure(error: unknown): error is ApiFailureError {
    return error instanceof Error && 'status' in error && typeof error.status === 'number'
}

export interface Toast {
    id: number
    title: string
    message: string
}

export const toasts = reactive<Toast[]>([])

let nextId = 0

export function dismissToast(id: number) {
    const index = toasts.findIndex((toast) => toast.id === id)

    if (index !== -1) {
        toasts.splice(index, 1)
    }
}

export function pushToast({ title, message, timeout = 6000 }: { title: string, message: string, timeout?: number }) {
    const id = ++nextId

    toasts.unshift({ id, title, message })

    if (timeout) {
        window.setTimeout(() => dismissToast(id), timeout)
    }

    return id
}

function validationLines(error: ApiFailureError) {
    const lines = Object.values(error.errors ?? {}).flat().filter(Boolean)

    return [...new Set(lines)]
}

export function toastApiError(error: unknown) {
    if (isApiFailure(error) && error.status === 401) {
        pushToast({
            title: 'UNAUTHORIZED',
            message: error.message || 'You are not signed in.',
        })
        return
    }

    if (isApiFailure(error) && error.status === 422) {
        const lines = validationLines(error)

        pushToast({
            title: 'VALIDATION FAILED',
            message: lines.length
                ? lines.join('\n')
                : (error.message || 'The submitted data is invalid.'),
        })
        return
    }

    pushToast({
        title: 'REQUEST FAILED',
        message: error instanceof Error ? error.message : 'Request failed.',
    })
}
