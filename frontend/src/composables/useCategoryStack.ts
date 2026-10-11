import { ref } from 'vue'
import type { Ref } from 'vue'
import { isAuthenticated } from '@/auth'
import { api } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import {
    isUnassignedCategory,
} from '@/types/techStack'
import type {
    CategoryListItem,
    NamedRecord,
    StackDragEvent,
    TechStackCategoryLink,
    TechStackGroup,
    UnassignedCategory,
} from '@/types/techStack'

export function useCategoryStack(techGroups: Ref<TechStackGroup[]>) {
    const unassignedCategories = ref<UnassignedCategory[]>([])
    const unassignedLoading = ref(false)
    const unassignDrop = ref<CategoryListItem[]>([])
    const deleteDrop = ref<CategoryListItem[]>([])

    function renumber(categories: CategoryListItem[]) {
        categories.forEach((item, index) => {
            item.sort_order = index + 1
        })
    }

    function asUnassigned(category: NamedRecord): UnassignedCategory {
        return {
            id: `unassigned-${category.id}`,
            category,
            technologies: [],
        }
    }

    async function loadUnassigned() {
        unassignedLoading.value = true

        try {
            const body = await api<ApiResponse<NamedRecord[]>>('/tech-stack-categories?unassigned=1')
            unassignedCategories.value = (body.data ?? []).map(asUnassigned)
        } catch {
            unassignedCategories.value = []
        } finally {
            unassignedLoading.value = false
        }
    }

    async function assignCategory(event: StackDragEvent) {
        const toId = Number(event.to?.dataset?.groupId)
        const { oldIndex, newIndex } = event
        const destination = techGroups.value.find((group) => group.id === toId)
        const moved = newIndex == null ? undefined : destination?.categories?.[newIndex]
        const categoryId = moved?.category?.id

        if (!destination || !categoryId || oldIndex == null || newIndex == null) {
            return
        }

        try {
            const body = await api<ApiResponse<TechStackCategoryLink>>(`/tech-stack-groups/${toId}/tech-stack-categories/${categoryId}`, {
                method: 'POST',
                body: { sort_order: newIndex + 1 },
            })
            const created = body.data

            destination.categories.splice(newIndex, 1, {
                id: created.id,
                sort_order: created.sort_order,
                category: created.category,
                technologies: created.technologies ?? [],
            })

            renumber(destination.categories)
        } catch {
            const [item] = destination.categories.splice(newIndex, 1)

            if (item && isUnassignedCategory(item)) {
                unassignedCategories.value.splice(oldIndex, 0, item)
            }
        }
    }

    function restoreItem(item: CategoryListItem, fromList: string | undefined, fromId: number, oldIndex: number) {
        if (fromList === 'unassigned') {
            if (isUnassignedCategory(item)) {
                unassignedCategories.value.splice(oldIndex, 0, item)
            }
            return
        }

        const source = techGroups.value.find((group) => group.id === fromId)
        source?.categories.splice(oldIndex, 0, item)
    }

    async function onActionDrop(action: 'unassign' | 'delete', event: StackDragEvent) {
        const bin = action === 'unassign' ? unassignDrop : deleteDrop
        const item = bin.value.splice(event.newIndex ?? 0, 1)[0] ?? bin.value.shift()
        bin.value = []

        if (!item) {
            return
        }

        const fromList = event.from?.dataset?.list
        const fromId = Number(event.from?.dataset?.groupId)
        const oldIndex = event.oldIndex ?? 0

        if (action === 'unassign' && fromList !== 'group') {
            restoreItem(item, fromList, fromId, oldIndex)
            return
        }

        try {
            if (action === 'unassign') {
                await api(`/tech-stack-group-category/${item.id}`, { method: 'DELETE' })
                unassignedCategories.value.push(asUnassigned(item.category))
            } else if (fromList === 'group') {
                await api(`/tech-stack-group-category/${item.id}`, { method: 'DELETE' })
                await api(`/tech-stack-categories/${item.category.id}`, { method: 'DELETE' })
            } else {
                await api(`/tech-stack-categories/${item.category.id}`, { method: 'DELETE' })
            }

            if (fromList === 'group') {
                const source = techGroups.value.find((group) => group.id === fromId)
                if (source) {
                    renumber(source.categories)
                }
            }
        } catch {
            restoreItem(item, fromList, fromId, oldIndex)
        }
    }

    async function onCategoryDrop(event: StackDragEvent) {
        if (!isAuthenticated.value) {
            return
        }

        const fromList = event.from?.dataset?.list
        const toList = event.to?.dataset?.list
        const fromId = Number(event.from?.dataset?.groupId)
        const toId = Number(event.to?.dataset?.groupId)
        const { oldIndex, newIndex } = event

        if (toList === 'unassign' || toList === 'delete') {
            return
        }

        if (oldIndex == null || newIndex == null || toList === 'unassigned') {
            return
        }

        if (fromList === 'unassigned') {
            await assignCategory(event)
            return
        }

        if (fromId === toId && oldIndex === newIndex) {
            return
        }

        const source = techGroups.value.find((group) => group.id === fromId)
        const destination = techGroups.value.find((group) => group.id === toId)
        const link = destination?.categories?.[newIndex]

        if (!source || !destination || !link) {
            return
        }

        const body: { sort_order: number, group_id?: number } = { sort_order: newIndex + 1 }

        if (fromId !== toId) {
            body.group_id = toId
        }

        try {
            await api(`/tech-stack-group-category/${link.id}`, {
                method: 'PATCH',
                body,
            })

            renumber(source.categories)
            if (source !== destination) {
                renumber(destination.categories)
            }
        } catch {
            const [moved] = destination.categories.splice(newIndex, 1)

            if (moved) {
                source.categories.splice(oldIndex, 0, moved)
            }
        }
    }

    return {
        unassignedCategories,
        unassignedLoading,
        unassignDrop,
        deleteDrop,
        asUnassigned,
        loadUnassigned,
        onActionDrop,
        onCategoryDrop,
    }
}
