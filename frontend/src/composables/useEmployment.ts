import { onMounted, ref } from 'vue'
import { api, ApiError } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { EducationCreateBody } from '@/types/education'
import type { Employment } from '@/types/employment'

interface EmploymentList {
    items: Employment[]
    loading: boolean
    error: string
}

export function useEmployment() {
    const employments = ref<EmploymentList>({ items: [], loading: true, error: '' })
    const creating = ref(false)

    async function load() {
        try {
            const response = await api<ApiResponse<Employment[]>>('/employments', { auth: false, toast: false })

            employments.value = { items: response.data, loading: false, error: '' }
        } catch (error) {
            const message = error instanceof ApiError ? error.message : 'Could not load employment.'

            employments.value = { items: [], loading: false, error: message }
        }
    }

    function sortItems(items: Employment[]) {
        return [...items].sort((left, right) => left.sort_order - right.sort_order)
    }

    async function createEmployment(body: EducationCreateBody) {
        creating.value = true

        try {
            const response = await api<ApiResponse<Employment>>('/employments', { method: 'POST', body, toast: false })

            employments.value.items = sortItems([...employments.value.items, response.data])
        } finally {
            creating.value = false
        }
    }

    async function updateEmployment(id: number, body: EducationCreateBody) {
        creating.value = true

        try {
            const response = await api<ApiResponse<Employment>>(`/employments/${id}`, { method: 'PATCH', body, toast: false })

            employments.value.items = sortItems(
                employments.value.items.map((item) => item.id === id ? response.data : item),
            )
        } finally {
            creating.value = false
        }
    }

    async function removeEmployment(id: number) {
        creating.value = true

        try {
            await api(`/employments/${id}`, { method: 'DELETE', toast: false })

            employments.value.items = employments.value.items.filter((item) => item.id !== id)
        } finally {
            creating.value = false
        }
    }

    onMounted(load)

    return { employments, creating, createEmployment, updateEmployment, removeEmployment }
}
