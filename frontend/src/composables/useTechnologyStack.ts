import { ref } from 'vue'
import type { Ref } from 'vue'
import { isAuthenticated } from '@/auth'
import { api } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import {
    isUnassignedTechnology,
} from '@/types/techStack'
import type {
    CategoryListItem,
    NamedRecord,
    StackDragEvent,
    TechStackGroup,
    TechStackPlacement,
    TechnologyListItem,
    UnassignedTechnology,
} from '@/types/techStack'

export function useTechnologyStack(techGroups: Ref<TechStackGroup[]>) {
    const unassignedTechnologies = ref<UnassignedTechnology[]>([])
    const unassignedTechnologiesLoading = ref(false)
    const technologyUnassignDrop = ref<TechnologyListItem[]>([])
    const technologyDeleteDrop = ref<TechnologyListItem[]>([])

    function asUnassignedTechnology(technology: NamedRecord): UnassignedTechnology {
        return {
            id: `unassigned-tech-${technology.id}`,
            technology,
        }
    }

    async function loadUnassignedTechnologies() {
        unassignedTechnologiesLoading.value = true

        try {
            const body = await api<ApiResponse<NamedRecord[]>>('/technologies?unassigned=1')
            unassignedTechnologies.value = (body.data ?? []).map(asUnassignedTechnology)
        } catch {
            unassignedTechnologies.value = []
        } finally {
            unassignedTechnologiesLoading.value = false
        }
    }

    function findLink(linkId: number): CategoryListItem | null {
        for (const group of techGroups.value) {
            const link = group.categories.find((item) => item.id === linkId)

            if (link) {
                return link
            }
        }

        return null
    }

    async function assignTechnology(event: StackDragEvent) {
        const toLinkId = Number(event.to?.dataset?.linkId)
        const { oldIndex, newIndex } = event
        const destination = findLink(toLinkId)
        const moved = newIndex == null ? undefined : destination?.technologies?.[newIndex]
        const technologyId = moved?.technology?.id

        if (!destination || !technologyId || oldIndex == null || newIndex == null) {
            return
        }

        try {
            const body = await api<ApiResponse<TechStackPlacement>>('/tech-stack-placements', {
                method: 'POST',
                body: {
                    group_category_id: toLinkId,
                    technology_id: technologyId,
                    sort_order: newIndex + 1,
                },
            })

            destination.technologies.splice(newIndex, 1, body.data)
        } catch {
            const [item] = destination.technologies.splice(newIndex, 1)

            if (item && isUnassignedTechnology(item)) {
                unassignedTechnologies.value.splice(oldIndex, 0, item)
            }
        }
    }

    async function moveTechnology(event: StackDragEvent) {
        const toLinkId = Number(event.to?.dataset?.linkId)
        const fromLinkId = Number(event.from?.dataset?.linkId)
        const { oldIndex, newIndex } = event
        const destination = findLink(toLinkId)
        const source = findLink(fromLinkId)
        const moved = newIndex == null ? undefined : destination?.technologies?.[newIndex]
        const placementId = moved?.id
        const technologyId = moved?.technology?.id

        if (!destination || !source || !moved || !placementId || !technologyId || oldIndex == null || newIndex == null) {
            return
        }

        try {
            const body = await api<ApiResponse<TechStackPlacement>>('/tech-stack-placements', {
                method: 'POST',
                body: {
                    group_category_id: toLinkId,
                    technology_id: technologyId,
                    sort_order: newIndex + 1,
                },
            })

            destination.technologies.splice(newIndex, 1, body.data)

            try {
                await api(`/tech-stack-placements/${placementId}`, { method: 'DELETE' })
            } catch {
                await api(`/tech-stack-placements/${body.data.id}`, { method: 'DELETE', toast: false }).catch(() => {})
                destination.technologies.splice(newIndex, 1)
                source.technologies.splice(oldIndex, 0, moved)
            }
        } catch {
            const [item] = destination.technologies.splice(newIndex, 1)

            if (item) {
                source.technologies.splice(oldIndex, 0, item)
            }
        }
    }

    function technologyIsPlaced(technologyId: number) {
        return techGroups.value.some((group) =>
            (group.categories ?? []).some((link) =>
                (link.technologies ?? []).some((placement) => placement.technology?.id === technologyId),
            ),
        )
    }

    function removeTechnologyEverywhere(technologyId: number) {
        for (const group of techGroups.value) {
            for (const link of group.categories ?? []) {
                link.technologies = (link.technologies ?? []).filter(
                    (placement) => placement.technology?.id !== technologyId,
                )
            }
        }

        unassignedTechnologies.value = unassignedTechnologies.value.filter(
            (item) => item.technology?.id !== technologyId,
        )
    }

    function restoreTechnology(item: TechnologyListItem, fromList: string | undefined, oldIndex: number, linkId: number) {
        if (fromList === 'technologies') {
            findLink(linkId)?.technologies.splice(oldIndex, 0, item)
            return
        }

        if (fromList === 'unassigned-technologies' && isUnassignedTechnology(item)) {
            unassignedTechnologies.value.splice(oldIndex, 0, item)
        }
    }

    async function onTechnologyDrop(event: StackDragEvent) {
        if (!isAuthenticated.value) {
            return
        }

        const fromList = event.from?.dataset?.list
        const toList = event.to?.dataset?.list
        const fromLinkId = Number(event.from?.dataset?.linkId)
        const toLinkId = Number(event.to?.dataset?.linkId)
        const { oldIndex, newIndex } = event

        if (toList === 'technology-unassign' || toList === 'technology-delete') {
            return
        }

        if (oldIndex == null || newIndex == null || toList === 'unassigned-technologies') {
            return
        }

        if (fromList === 'unassigned-technologies') {
            await assignTechnology(event)
            return
        }

        if (fromList === 'technologies' && toList === 'technologies' && fromLinkId !== toLinkId) {
            await moveTechnology(event)
        }
    }

    async function onTechnologyActionDrop(action: 'unassign' | 'delete', event: StackDragEvent) {
        const bin = action === 'unassign' ? technologyUnassignDrop : technologyDeleteDrop
        const item = bin.value.splice(event.newIndex ?? 0, 1)[0] ?? bin.value.shift()
        bin.value = []

        if (!item?.technology?.id) {
            return
        }

        const fromList = event.from?.dataset?.list
        const linkId = Number(event.from?.dataset?.linkId)
        const oldIndex = event.oldIndex ?? 0

        if (action === 'unassign') {
            if (fromList !== 'technologies') {
                restoreTechnology(item, fromList, oldIndex, linkId)
                return
            }

            try {
                await api(`/tech-stack-placements/${item.id}`, { method: 'DELETE' })

                if (!technologyIsPlaced(item.technology.id)) {
                    unassignedTechnologies.value.push(asUnassignedTechnology(item.technology))
                }
            } catch {
                restoreTechnology(item, fromList, oldIndex, linkId)
            }

            return
        }

        try {
            await api(`/technologies/${item.technology.id}`, { method: 'DELETE' })
            removeTechnologyEverywhere(item.technology.id)
        } catch {
            restoreTechnology(item, fromList, oldIndex, linkId)
        }
    }

    return {
        unassignedTechnologies,
        unassignedTechnologiesLoading,
        technologyUnassignDrop,
        technologyDeleteDrop,
        asUnassignedTechnology,
        loadUnassignedTechnologies,
        onTechnologyDrop,
        onTechnologyActionDrop,
    }
}
