export interface CaseStudyAsset {
    id: number
    collection: string
    url: string
    thumb_url?: string | null
    original_name: string | null
    sort_order: number
}

export interface CaseStudyPoint {
    id: number
    body: string
    sort_order: number
}

export interface CaseStudyBlock {
    id: number
    layout: 'card' | 'group'
    sort_order: number
    points: CaseStudyPoint[]
}

export interface CaseStudySection {
    id: number
    title: string
    introduction: string | null
    sort_order: number
    assets: CaseStudyAsset[]
    blocks: CaseStudyBlock[]
}

export interface CaseStudy {
    id: number
    title: string
    slug: string
    client_name: string | null
    tagline: string | null
    summary: string | null
    introduction: string | null
    status: string
    sort_order: number
    assets?: CaseStudyAsset[]
    sections?: CaseStudySection[]
}

export function wideImages(assets: CaseStudyAsset[] | undefined): CaseStudyAsset[] {
    return (assets ?? []).filter((asset) => asset.collection === 'cover' || asset.collection === 'gallery')
}

export function coverImage(assets: CaseStudyAsset[] | undefined): string | null {
    const images = wideImages(assets)
    const cover = images.find((asset) => asset.collection === 'cover')

    const image = cover ?? images[0]

    return image?.thumb_url || image?.url || null
}

export interface CaseStudyCardItem {
    id: number
    body: string
}

export interface CaseStudyCardItemDraft {
    id: number | null
    body: string
}

export interface CaseStudyCardView {
    blockId: number
    pointId: number
    body: string
    items: CaseStudyCardItem[]
}

export function cardViews(section: CaseStudySection): CaseStudyCardView[] {
    return section.blocks
        .filter((block) => block.layout === 'card' && block.points.length > 0)
        .map((block) => {
            const [lead, ...items] = block.points

            return {
                blockId: block.id,
                pointId: lead.id,
                body: lead.body,
                items: items.map((point) => ({
                    id: point.id,
                    body: point.body,
                })),
            }
        })
}
