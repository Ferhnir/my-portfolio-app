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
    >
      <EducationCreatePanel
          v-if="isAuthenticated"
          :key="formKeys.history"
          title="Add education"
          submit-label="Add education"
          id-prefix="history"
          :fields="historyFields"
          :pending="creating.history"
          :errors="fieldErrors.history"
          :message="formMessages.history"
          @submit="onCreate('history', $event)"
      />
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
    >
      <EducationCreatePanel
          v-if="isAuthenticated"
          :key="formKeys.courses"
          title="Add course"
          submit-label="Add course"
          id-prefix="course"
          :fields="courseFields"
          :pending="creating.courses"
          :errors="fieldErrors.courses"
          :message="formMessages.courses"
          @submit="onCreate('courses', $event)"
      />
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
    >
      <EducationCreatePanel
          v-if="isAuthenticated"
          :key="formKeys.certificates"
          title="Add certificate"
          submit-label="Add certificate"
          id-prefix="certificate"
          :fields="certificateFields"
          :pending="creating.certificates"
          :errors="fieldErrors.certificates"
          :message="formMessages.certificates"
          @submit="onCreate('certificates', $event)"
      />
    </EducationSection>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import EducationCreatePanel from '@/components/EducationCreatePanel.vue'
import EducationSection from '@/components/EducationSection.vue'
import { useEducation } from '@/composables/useEducation'
import { certificateFields, courseFields, historyFields, toCertificateEntry, toCourseEntry, toHistoryEntry } from '@/types/education'
import type { EducationCreateBody, EducationTab } from '@/types/education'

const tabs: { id: EducationTab, label: string }[] = [
    { id: 'history', label: 'Education' },
    { id: 'courses', label: 'Courses' },
    { id: 'certificates', label: 'Certificates' },
]

const active = ref<EducationTab>('history')
const formKeys = ref<Record<EducationTab, number>>({ history: 0, courses: 0, certificates: 0 })
const fieldErrors = ref<Record<EducationTab, Record<string, string[]>>>({
    history: {},
    courses: {},
    certificates: {},
})
const formMessages = ref<Record<EducationTab, string>>({ history: '', courses: '', certificates: '' })
const { histories, courses, certificates, creating, createHistory, createCourse, createCertificate } = useEducation()

const creators = {
    history: createHistory,
    courses: createCourse,
    certificates: createCertificate,
}

async function onCreate(key: EducationTab, body: EducationCreateBody) {
    fieldErrors.value[key] = {}
    formMessages.value[key] = ''

    try {
        await creators[key](body)
        formKeys.value[key] += 1
    } catch (error) {
        fieldErrors.value[key] = error instanceof ApiError ? error.errors : {}
        formMessages.value[key] = error instanceof ApiError ? error.message : 'Could not add this record.'
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

onUnmounted(() => observer?.disconnect())
</script>
