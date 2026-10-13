import { onMounted, ref, type Ref } from 'vue'
import { api, ApiError } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { Certificate, Course, EducationCreateBody, EducationHistory } from '@/types/education'

interface EducationList<T> {
    items: T[]
    loading: boolean
    error: string
}

function emptyList<T>(): EducationList<T> {
    return { items: [], loading: true, error: '' }
}

async function loadList<T>(path: string): Promise<Pick<EducationList<T>, 'items' | 'error'>> {
    try {
        const response = await api<ApiResponse<T[]>>(path, { auth: false, toast: false })

        return { items: response.data, error: '' }
    } catch (error) {
        const message = error instanceof ApiError ? error.message : 'Could not load this section.'

        return { items: [], error: message }
    }
}

type EducationCreateKey = 'history' | 'courses' | 'certificates'

export function useEducation() {
    const histories = ref(emptyList<EducationHistory>())
    const courses = ref(emptyList<Course>())
    const certificates = ref(emptyList<Certificate>())
    const creating = ref<Record<EducationCreateKey, boolean>>({
        history: false,
        courses: false,
        certificates: false,
    })

    function sortItems<T extends { sort_order: number }>(items: T[]) {
        return [...items].sort((left, right) => left.sort_order - right.sort_order)
    }

    async function createRecord<T extends { sort_order: number }>(
        path: string,
        body: EducationCreateBody,
        list: Ref<EducationList<T>>,
        key: EducationCreateKey,
    ) {
        creating.value[key] = true

        try {
            const response = await api<ApiResponse<T>>(path, { method: 'POST', body, toast: false })
            list.value.items = sortItems([...list.value.items, response.data])
        } finally {
            creating.value[key] = false
        }
    }

    async function updateRecord<T extends { id: number, sort_order: number }>(
        path: string,
        id: number,
        body: EducationCreateBody,
        list: Ref<EducationList<T>>,
        key: EducationCreateKey,
    ) {
        creating.value[key] = true

        try {
            const response = await api<ApiResponse<T>>(`${path}/${id}`, { method: 'PATCH', body, toast: false })
            list.value.items = sortItems(list.value.items.map((item) => item.id === id ? response.data : item))
        } finally {
            creating.value[key] = false
        }
    }

    async function removeRecord<T extends { id: number }>(
        path: string,
        id: number,
        list: Ref<EducationList<T>>,
        key: EducationCreateKey,
    ) {
        creating.value[key] = true

        try {
            await api(`${path}/${id}`, { method: 'DELETE', toast: false })
            list.value.items = list.value.items.filter((item) => item.id !== id)
        } finally {
            creating.value[key] = false
        }
    }

    function createHistory(body: EducationCreateBody) {
        return createRecord('/education-histories', body, histories, 'history')
    }

    function updateHistory(id: number, body: EducationCreateBody) {
        return updateRecord('/education-histories', id, body, histories, 'history')
    }

    function removeHistory(id: number) {
        return removeRecord('/education-histories', id, histories, 'history')
    }

    function createCourse(body: EducationCreateBody) {
        return createRecord('/courses', body, courses, 'courses')
    }

    function updateCourse(id: number, body: EducationCreateBody) {
        return updateRecord('/courses', id, body, courses, 'courses')
    }

    function removeCourse(id: number) {
        return removeRecord('/courses', id, courses, 'courses')
    }

    function createCertificate(body: EducationCreateBody) {
        return createRecord('/certificates', body, certificates, 'certificates')
    }

    function updateCertificate(id: number, body: EducationCreateBody) {
        return updateRecord('/certificates', id, body, certificates, 'certificates')
    }

    function removeCertificate(id: number) {
        return removeRecord('/certificates', id, certificates, 'certificates')
    }

    async function load() {
        const [history, courseList, certificateList] = await Promise.all([
            loadList<EducationHistory>('/education-histories'),
            loadList<Course>('/courses'),
            loadList<Certificate>('/certificates'),
        ])

        histories.value = { ...history, loading: false }
        courses.value = { ...courseList, loading: false }
        certificates.value = { ...certificateList, loading: false }
    }

    onMounted(load)

    return {
        histories,
        courses,
        certificates,
        creating,
        createHistory,
        createCourse,
        createCertificate,
    }
}
