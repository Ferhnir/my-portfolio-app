export interface NamedRecord {
    id: number
    name: string
    slug: string
}

export interface TechStackPlacement {
    id: number
    sort_order: number
    technology: NamedRecord
}

export interface UnassignedTechnology {
    id: string
    technology: NamedRecord
}

export type TechnologyListItem = TechStackPlacement | UnassignedTechnology

export function isUnassignedTechnology(item: TechnologyListItem): item is UnassignedTechnology {
    return typeof item.id === 'string'
}

export interface TechStackCategoryLink {
    id: number
    sort_order: number
    category: NamedRecord
    technologies: TechnologyListItem[]
}

export interface UnassignedCategory {
    id: string
    sort_order?: number
    category: NamedRecord
    technologies: TechnologyListItem[]
}

export type CategoryListItem = TechStackCategoryLink | UnassignedCategory

export function isUnassignedCategory(item: CategoryListItem): item is UnassignedCategory {
    return typeof item.id === 'string'
}

export interface TechStackGroup {
    id: number
    name: string
    slug: string
    sort_order: number
    categories: CategoryListItem[]
}

export interface StackDragEvent {
    from?: HTMLElement
    to?: HTMLElement
    oldIndex?: number | null
    newIndex?: number | null
}
