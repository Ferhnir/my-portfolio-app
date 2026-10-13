export interface EducationHistory {
    id: number
    school_name: string
    degree: string | null
    field_of_study: string | null
    start_date: string | null
    end_date: string | null
    is_current: boolean
    description: string | null
    sort_order: number
}

export interface Course {
    id: number
    name: string
    provider: string | null
    start_date: string | null
    end_date: string | null
    description: string | null
    url: string | null
    sort_order: number
}

export interface Certificate {
    id: number
    name: string
    issuer: string | null
    issued_at: string | null
    expires_at: string | null
    credential_id: string | null
    credential_url: string | null
    description: string | null
    sort_order: number
}

export interface EducationEntryView {
    id: number
    title: string
    subtitle: string | null
    period: string | null
    body: string | null
    href: string | null
    hrefLabel: string | null
}

export type EducationTab = 'history' | 'courses' | 'certificates'

export type EducationCreateBody = Record<string, string | boolean | null>

export interface EducationFormField {
    key: string
    label: string
    type?: 'text' | 'date' | 'url'
    multiline?: boolean
    checkbox?: boolean
    required?: boolean
    placeholder?: string
    wide?: boolean
}

export const historyFields: EducationFormField[] = [
    { key: 'school_name', label: 'School', required: true, placeholder: 'School name', wide: true },
    { key: 'degree', label: 'Degree', placeholder: 'Bachelor' },
    { key: 'field_of_study', label: 'Field of study', placeholder: 'Computer Science' },
    { key: 'start_date', label: 'Start', type: 'date' },
    { key: 'end_date', label: 'End', type: 'date' },
    { key: 'is_current', label: 'Current', checkbox: true },
    { key: 'description', label: 'Description', multiline: true, wide: true },
]

export const courseFields: EducationFormField[] = [
    { key: 'name', label: 'Name', required: true, placeholder: 'Course name', wide: true },
    { key: 'provider', label: 'Provider', placeholder: 'Provider' },
    { key: 'url', label: 'URL', type: 'url', placeholder: 'https://' },
    { key: 'start_date', label: 'Start', type: 'date' },
    { key: 'end_date', label: 'End', type: 'date' },
    { key: 'description', label: 'Description', multiline: true, wide: true },
]

export const certificateFields: EducationFormField[] = [
    { key: 'name', label: 'Name', required: true, placeholder: 'Certificate name', wide: true },
    { key: 'issuer', label: 'Issuer', placeholder: 'Issuer' },
    { key: 'credential_id', label: 'Credential id', placeholder: 'EX-001' },
    { key: 'credential_url', label: 'Credential URL', type: 'url', placeholder: 'https://' },
    { key: 'issued_at', label: 'Issued', type: 'date' },
    { key: 'expires_at', label: 'Expires', type: 'date' },
    { key: 'description', label: 'Description', multiline: true, wide: true },
]

export function formatEducationDate(value: string | null): string | null {
    if (!value) {
        return null
    }

    const date = new Date(`${value}T00:00:00`)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    return date.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' })
}

export function formatEducationRange(start: string | null, end: string | null, current = false): string | null {
    const from = formatEducationDate(start)
    const to = current ? 'Present' : formatEducationDate(end)

    if (from && to) {
        return `${from} — ${to}`
    }

    return from || to
}

export function toHistoryEntry(history: EducationHistory): EducationEntryView {
    const subtitle = [history.degree, history.field_of_study].filter(Boolean).join(' · ')

    return {
        id: history.id,
        title: history.school_name,
        subtitle: subtitle || null,
        period: formatEducationRange(history.start_date, history.end_date, history.is_current),
        body: history.description,
        href: null,
        hrefLabel: null,
    }
}

export function toCourseEntry(course: Course): EducationEntryView {
    return {
        id: course.id,
        title: course.name,
        subtitle: course.provider,
        period: formatEducationRange(course.start_date, course.end_date),
        body: course.description,
        href: course.url,
        hrefLabel: course.url ? 'Course page' : null,
    }
}

export function toCertificateEntry(certificate: Certificate): EducationEntryView {
    return {
        id: certificate.id,
        title: certificate.name,
        subtitle: certificate.issuer,
        period: formatEducationRange(certificate.issued_at, certificate.expires_at),
        body: certificate.description,
        href: certificate.credential_url,
        hrefLabel: certificate.credential_id || (certificate.credential_url ? 'View credential' : null),
    }
}
