import { formatEducationRange, type EducationEntryView, type EducationFormField } from '@/types/education'

export interface Employment {
    id: number
    company_name: string
    job_title: string | null
    location: string | null
    start_date: string | null
    end_date: string | null
    is_current: boolean
    description: string | null
    sort_order: number
}

export const employmentFields: EducationFormField[] = [
    { key: 'company_name', label: 'Company', required: true, placeholder: 'Company name', wide: true },
    { key: 'job_title', label: 'Job title', placeholder: 'Software Engineer' },
    { key: 'location', label: 'Location', placeholder: 'Remote' },
    { key: 'start_date', label: 'Start', type: 'date' },
    { key: 'end_date', label: 'End', type: 'date' },
    { key: 'is_current', label: 'Current', checkbox: true },
    { key: 'description', label: 'Description', multiline: true, wide: true },
]

export function toEmploymentEntry(employment: Employment): EducationEntryView {
    const subtitle = [employment.job_title, employment.location].filter(Boolean).join(' · ')

    return {
        id: employment.id,
        title: employment.company_name,
        subtitle: subtitle || null,
        period: formatEducationRange(employment.start_date, employment.end_date, employment.is_current),
        body: employment.description,
        href: null,
        hrefLabel: null,
    }
}
