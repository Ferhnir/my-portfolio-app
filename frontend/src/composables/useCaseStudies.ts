import { ref } from 'vue'
import { api, ApiError } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { EducationCreateBody } from '@/types/education'
import type { CaseStudy, CaseStudyCardItemDraft } from '@/types/caseStudy'

interface CaseStudyList {
    items: CaseStudy[]
    loading: boolean
    error: string
}

export function useCaseStudies() {
    const caseStudies = ref<CaseStudyList>({ items: [], loading: true, error: '' })
    const creating = ref(false)

    async function load() {
        try {
            const response = await api<ApiResponse<CaseStudy[]>>('/case-studies', { toast: false })

            caseStudies.value = { items: response.data, loading: false, error: '' }
        } catch (error) {
            const message = error instanceof ApiError ? error.message : 'Could not load cases.'

            caseStudies.value = { items: [], loading: false, error: message }
        }
    }

    async function createCaseStudy(body: EducationCreateBody) {
        creating.value = true

        try {
            const response = await api<ApiResponse<CaseStudy>>('/case-studies', { method: 'POST', body, toast: false })

            caseStudies.value.items = [...caseStudies.value.items, response.data]
                .sort((left, right) => left.sort_order - right.sort_order)
        } finally {
            creating.value = false
        }
    }

    return { caseStudies, creating, load, createCaseStudy }
}

export function useCaseStudy(id: () => number) {
    const caseStudy = ref<CaseStudy | null>(null)
    const loading = ref(true)
    const error = ref('')
    const saving = ref(false)

    async function load(quiet = false) {
        if (!quiet) {
            loading.value = true
        }

        try {
            const response = await api<ApiResponse<CaseStudy>>(`/case-studies/${id()}`, { toast: false })

            caseStudy.value = response.data
            error.value = ''
        } catch (reason) {
            if (!quiet) {
                caseStudy.value = null
                error.value = reason instanceof ApiError ? reason.message : 'Could not load this case.'
            }

            if (quiet) {
                throw reason
            }
        } finally {
            loading.value = false
        }
    }

    async function change(path: string, method: string, body?: unknown) {
        saving.value = true

        try {
            await api(path, { method, body, toast: false })
            await load(true)
        } finally {
            saving.value = false
        }
    }

    function updateCase(body: EducationCreateBody) {
        return change(`/case-studies/${id()}`, 'PATCH', body)
    }

    async function removeCase() {
        saving.value = true

        try {
            await api(`/case-studies/${id()}`, { method: 'DELETE', toast: false })
        } finally {
            saving.value = false
        }
    }

    function createSection(body: EducationCreateBody) {
        return change(`/case-studies/${id()}/sections`, 'POST', body)
    }

    function updateSection(sectionId: number, body: EducationCreateBody) {
        return change(`/case-studies/${id()}/sections/${sectionId}`, 'PATCH', body)
    }

    function removeSection(sectionId: number) {
        return change(`/case-studies/${id()}/sections/${sectionId}`, 'DELETE')
    }

    function pointPath(sectionId: number, blockId: number, pointId?: number) {
        const path = `/case-studies/${id()}/sections/${sectionId}/blocks/${blockId}/points`

        return pointId === undefined ? path : `${path}/${pointId}`
    }

    async function createCard(sectionId: number, body: string, items: CaseStudyCardItemDraft[]) {
        saving.value = true

        try {
            const block = await api<ApiResponse<{ id: number }>>(
                `/case-studies/${id()}/sections/${sectionId}/blocks`,
                { method: 'POST', body: { layout: 'card' }, toast: false },
            )

            try {
                await api(pointPath(sectionId, block.data.id), {
                    method: 'POST',
                    body: { body },
                    toast: false,
                })
                await writeListItems(sectionId, block.data.id, items)
            } catch (reason) {
                await api(`/case-studies/${id()}/sections/${sectionId}/blocks/${block.data.id}`, {
                    method: 'DELETE',
                    toast: false,
                })
                throw reason
            }

            await load(true)
        } finally {
            saving.value = false
        }
    }

    async function updateCard(
        sectionId: number,
        blockId: number,
        pointId: number,
        body: string,
        items: CaseStudyCardItemDraft[],
    ) {
        saving.value = true

        try {
            await api(pointPath(sectionId, blockId, pointId), {
                method: 'PATCH',
                body: { body },
                toast: false,
            })
            await writeListItems(sectionId, blockId, items)
            await load(true)
        } finally {
            saving.value = false
        }
    }

    async function writeListItems(sectionId: number, blockId: number, items: CaseStudyCardItemDraft[]) {
        const kept = items.filter((item) => item.body.trim() !== '')
        const current = caseStudy.value?.sections
            ?.find((section) => section.id === sectionId)
            ?.blocks.find((block) => block.id === blockId)
            ?.points.slice(1) ?? []

        for (const point of current) {
            if (!kept.some((item) => item.id === point.id)) {
                await api(pointPath(sectionId, blockId, point.id), { method: 'DELETE', toast: false })
            }
        }

        for (const item of kept) {
            const text = item.body.trim()

            if (item.id === null) {
                await api(pointPath(sectionId, blockId), {
                    method: 'POST',
                    body: { body: text },
                    toast: false,
                })
                continue
            }

            await api(pointPath(sectionId, blockId, item.id), {
                method: 'PATCH',
                body: { body: text },
                toast: false,
            })
        }
    }

    function removeCard(sectionId: number, blockId: number) {
        return change(`/case-studies/${id()}/sections/${sectionId}/blocks/${blockId}`, 'DELETE')
    }

    async function uploadImages(sectionId: number | null, files: File[], collection: string) {
        saving.value = true
        const path = sectionId === null
            ? `/case-studies/${id()}/assets`
            : `/case-studies/${id()}/sections/${sectionId}/assets`
        let stored = 0
        let failure: unknown = null

        try {
            for (const file of files) {
                const form = new FormData()
                form.append('file', file)
                form.append('collection', collection)
                await api(path, { method: 'POST', body: form, toast: false })
                stored += 1
            }
        } catch (reason) {
            failure = reason
        }

        if (stored > 0) {
            try {
                await load(true)
            } catch (reason) {
                failure ??= reason
            }
        }

        saving.value = false

        return {
            stored,
            error: failure instanceof ApiError
                ? failure.message
                : failure ? 'The image could not be stored.' : '',
        }
    }

    function removeImage(sectionId: number | null, assetId: number) {
        const path = sectionId === null
            ? `/case-studies/${id()}/assets/${assetId}`
            : `/case-studies/${id()}/sections/${sectionId}/assets/${assetId}`

        return change(path, 'DELETE')
    }

    return {
        caseStudy,
        loading,
        error,
        saving,
        load,
        updateCase,
        removeCase,
        createSection,
        updateSection,
        removeSection,
        createCard,
        updateCard,
        removeCard,
        uploadImages,
        removeImage,
    }
}
