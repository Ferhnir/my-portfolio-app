import { reactive } from 'vue'

export const toasts = reactive([])

let nextId = 0

export function dismissToast(id) {
    const index = toasts.findIndex((toast) => toast.id === id)

    if (index !== -1) {
        toasts.splice(index, 1)
    }
}

export function pushToast({ title, message, timeout = 6000 }) {
    const id = ++nextId

    toasts.unshift({ id, title, message })

    if (timeout) {
        window.setTimeout(() => dismissToast(id), timeout)
    }

    return id
}

function validationLines(error) {
    const lines = Object.values(error.errors ?? {}).flat().filter(Boolean)

    return [...new Set(lines)]
}

export function toastApiError(error) {
    if (error?.status === 401) {
        pushToast({
            title: 'UNAUTHORIZED',
            message: error.message || 'You are not signed in.',
        })
        return
    }

    if (error?.status === 422) {
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
        message: error?.message || 'Request failed.',
    })
}
