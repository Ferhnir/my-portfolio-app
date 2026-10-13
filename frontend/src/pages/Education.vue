<template>
  <div>
    <nav aria-label="Education sections" class="sticky top-0 z-20 border-b border-linear-border bg-linear-surface/80 backdrop-blur">
      <div class="mx-auto flex max-w-3xl">
        <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            class="border-b-2 px-4 py-3 text-xs font-bold uppercase tracking-widest transition-colors"
            :class="active === tab.id
              ? 'border-linear-green text-linear-green'
              : 'border-transparent text-linear-secondary hover:text-linear-text'"
            :aria-current="active === tab.id ? 'true' : undefined"
            @click="show(tab.id)"
        >
          {{ tab.label }}
        </button>
      </div>
    </nav>

    <EducationSection
        id="history"
        kicker="01"
        title="Education"
        summary="Schools, degrees, and fields of study."
        empty="No education history yet."
        :loading="histories.loading"
        :error="histories.error"
        :items="historyEntries"
        :can-edit="isAuthenticated"
        :busy="creating.history"
        :editing-id="editingId('history')"
        @edit="startEdit('history', $event)"
        @remove="onRemove('history', $event)"
    >
      <BaseButton v-if="isAuthenticated" type="button" @click="openCreate('history')">Add new record</BaseButton>
    </EducationSection>

    <EducationSection
        id="courses"
        kicker="02"
        title="Courses"
        summary="Courses and workshops."
        empty="No courses yet."
        :loading="courses.loading"
        :error="courses.error"
        :items="courseEntries"
        :can-edit="isAuthenticated"
        :busy="creating.courses"
        :editing-id="editingId('courses')"
        @edit="startEdit('courses', $event)"
        @remove="onRemove('courses', $event)"
    >
      <BaseButton v-if="isAuthenticated" type="button" @click="openCreate('courses')">Add new record</BaseButton>
    </EducationSection>

    <EducationSection
        id="certificates"
        kicker="03"
        title="Certificates"
        summary="Issued credentials."
        empty="No certificates yet."
        :loading="certificates.loading"
        :error="certificates.error"
        :items="certificateEntries"
        :can-edit="isAuthenticated"
        :busy="creating.certificates"
        :editing-id="editingId('certificates')"
        @edit="startEdit('certificates', $event)"
        @remove="onRemove('certificates', $event)"
    >
      <BaseButton v-if="isAuthenticated" type="button" @click="openCreate('certificates')">Add new record</BaseButton>
    </EducationSection>

    <div
        v-if="formOpen && activeForm"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6 py-8"
        @click.self="closeForm"
    >
      <div class="max-h-full w-full max-w-3xl overflow-y-auto">
        <EducationCreatePanel
            :key="formKey"
            :title="editing ? activeForm.titleEdit : activeForm.titleAdd"
            :submit-label="editing ? 'Save' : activeForm.submitAdd"
            :id-prefix="activeForm.idPrefix"
            form-id="education-form"
            :fields="activeForm.fields"
            :values="formValues"
            :include-empty="editing !== null"
            :cancellable="true"
            :pending="formPending"
            :errors="fieldErrors"
            :message="formMessage"
            @submit="onSubmit"
            @cancel="closeForm"
        />
      </div>
    </div>

    <ConfirmDialog
        :open="pendingDelete !== null"
        :title="deleteTitle"
        :message="pendingDelete ? `Delete ${pendingDelete.label}?` : ''"
        confirm-label="Delete"
        :pending="deletePending"
        :error="deleteError"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import BaseButton from '@/components/BaseButton.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import EducationCreatePanel from '@/components/EducationCreatePanel.vue'
import EducationSection from '@/components/EducationSection.vue'
import { useEducation } from '@/composables/useEducation'
import { certificateFields, courseFields, historyFields, toCertificateEntry, toCourseEntry, toHistoryEntry } from '@/types/education'
import type { Certificate, Course, EducationCreateBody, EducationFormField, EducationHistory, EducationTab } from '@/types/education'

const tabs: { id: EducationTab, label: string }[] = [
    { id: 'history', label: 'Education' },
    { id: 'courses', label: 'Courses' },
    { id: 'certificates', label: 'Certificates' },
]

const forms: Record<EducationTab, {
    titleAdd: string
    titleEdit: string
    submitAdd: string
    idPrefix: string
    fields: EducationFormField[]
    deleteTitle: string
}> = {
    history: {
        titleAdd: 'Add education',
        titleEdit: 'Edit education',
        submitAdd: 'Add education',
        idPrefix: 'history',
        fields: historyFields,
        deleteTitle: 'Delete education',
    },
    courses: {
        titleAdd: 'Add course',
        titleEdit: 'Edit course',
        submitAdd: 'Add course',
        idPrefix: 'course',
        fields: courseFields,
        deleteTitle: 'Delete course',
    },
    certificates: {
        titleAdd: 'Add certificate',
        titleEdit: 'Edit certificate',
        submitAdd: 'Add certificate',
        idPrefix: 'certificate',
        fields: certificateFields,
        deleteTitle: 'Delete certificate',
    },
}

type EducationRow = EducationHistory | Course | Certificate

const active = ref<EducationTab>('history')
const formKey = ref(0)
const formOpen = ref(false)
const formSection = ref<EducationTab | null>(null)
const fieldErrors = ref<Record<string, string[]>>({})
const formMessage = ref('')
const editing = ref<EducationRow | null>(null)
const pendingDelete = ref<{ section: EducationTab, id: number, label: string } | null>(null)
const deleteError = ref('')
const {
    histories,
    courses,
    certificates,
    creating,
    createHistory,
    updateHistory,
    removeHistory,
    createCourse,
    updateCourse,
    removeCourse,
    createCertificate,
    updateCertificate,
    removeCertificate,
} = useEducation()

const creators = {
    history: createHistory,
    courses: createCourse,
    certificates: createCertificate,
}

const updaters = {
    history: updateHistory,
    courses: updateCourse,
    certificates: updateCertificate,
}

const removers = {
    history: removeHistory,
    courses: removeCourse,
    certificates: removeCertificate,
}

const activeForm = computed(() => formSection.value ? forms[formSection.value] : null)
const formPending = computed(() => formSection.value ? creating.value[formSection.value] : false)
const deletePending = computed(() => pendingDelete.value ? creating.value[pendingDelete.value.section] : false)
const deleteTitle = computed(() => pendingDelete.value ? forms[pendingDelete.value.section].deleteTitle : '')

const formValues = computed((): Record<string, string | boolean | null> => {
    const section = formSection.value
    const row = editing.value

    if (!section || !row) {
        return {}
    }

    if (section === 'history') {
        const history = row as EducationHistory

        return {
            school_name: history.school_name,
            degree: history.degree ?? '',
            field_of_study: history.field_of_study ?? '',
            start_date: history.start_date ?? '',
            end_date: history.end_date ?? '',
            is_current: history.is_current,
            description: history.description ?? '',
        }
    }

    if (section === 'courses') {
        const course = row as Course

        return {
            name: course.name,
            provider: course.provider ?? '',
            url: course.url ?? '',
            start_date: course.start_date ?? '',
            end_date: course.end_date ?? '',
            description: course.description ?? '',
        }
    }

    const certificate = row as Certificate

    return {
        name: certificate.name,
        issuer: certificate.issuer ?? '',
        credential_id: certificate.credential_id ?? '',
        credential_url: certificate.credential_url ?? '',
        issued_at: certificate.issued_at ?? '',
        expires_at: certificate.expires_at ?? '',
        description: certificate.description ?? '',
    }
})

function listFor(section: EducationTab) {
    if (section === 'history') {
        return histories.value.items
    }

    if (section === 'courses') {
        return courses.value.items
    }

    return certificates.value.items
}

function recordLabel(section: EducationTab, row: EducationRow) {
    if (section === 'history') {
        return (row as EducationHistory).school_name
    }

    return (row as Course | Certificate).name
}

function editingId(section: EducationTab) {
    return formSection.value === section ? editing.value?.id ?? null : null
}

function resetForm() {
    editing.value = null
    formSection.value = null
    fieldErrors.value = {}
    formMessage.value = ''
    formOpen.value = false
    formKey.value += 1
}

function openCreate(section: EducationTab) {
    editing.value = null
    formSection.value = section
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
    formOpen.value = true
}

function startEdit(section: EducationTab, id: number) {
    const row = listFor(section).find((item) => item.id === id)

    if (!row) {
        return
    }

    editing.value = row
    formSection.value = section
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
    formOpen.value = true
}

function closeForm() {
    if (formPending.value || pendingDelete.value) {
        return
    }

    resetForm()
}

function onFormKey(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        closeForm()
    }
}

watch(formOpen, (open) => {
    if (open) {
        window.addEventListener('keydown', onFormKey)
    } else {
        window.removeEventListener('keydown', onFormKey)
    }
})

function onRemove(section: EducationTab, id: number) {
    const row = listFor(section).find((item) => item.id === id)

    if (!row) {
        return
    }

    pendingDelete.value = { section, id, label: recordLabel(section, row) }
    deleteError.value = ''
}

function cancelDelete() {
    if (deletePending.value) {
        return
    }

    pendingDelete.value = null
    deleteError.value = ''
}

async function confirmDelete() {
    const pending = pendingDelete.value

    if (!pending) {
        return
    }

    deleteError.value = ''

    try {
        await removers[pending.section](pending.id)

        if (formSection.value === pending.section && editing.value?.id === pending.id) {
            resetForm()
        }

        pendingDelete.value = null
    } catch (error) {
        deleteError.value = error instanceof ApiError ? error.message : 'Could not delete this record.'
    }
}

async function onSubmit(body: EducationCreateBody) {
    const section = formSection.value

    if (!section) {
        return
    }

    fieldErrors.value = {}
    formMessage.value = ''

    try {
        if (editing.value) {
            await updaters[section](editing.value.id, body)
        } else {
            await creators[section](body)
        }

        resetForm()
    } catch (error) {
        fieldErrors.value = error instanceof ApiError ? error.errors : {}
        formMessage.value = error instanceof ApiError ? error.message : 'Could not save this record.'
    }
}

const historyEntries = computed(() => histories.value.items.map(toHistoryEntry))
const courseEntries = computed(() => courses.value.items.map(toCourseEntry))
const certificateEntries = computed(() => certificates.value.items.map(toCertificateEntry))

function show(id: EducationTab) {
    active.value = id
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

let observer: IntersectionObserver | undefined

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((left, right) => right.intersectionRatio - left.intersectionRatio)[0]

        const id = visible?.target.id

        if (id === 'history' || id === 'courses' || id === 'certificates') {
            active.value = id
        }
    }, {
        rootMargin: '-20% 0px -55% 0px',
        threshold: [0.15, 0.4, 0.7],
    })

    for (const tab of tabs) {
        const section = document.getElementById(tab.id)

        if (section) {
            observer.observe(section)
        }
    }
})

onUnmounted(() => {
    observer?.disconnect()
    window.removeEventListener('keydown', onFormKey)
})
</script>
