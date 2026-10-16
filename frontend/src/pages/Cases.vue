<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import CaseStudyAddCard from '@/components/CaseStudyAddCard.vue'
import CaseStudyCard from '@/components/CaseStudyCard.vue'
import EducationCreatePanel from '@/components/EducationCreatePanel.vue'
import { useCaseStudies } from '@/composables/useCaseStudies'
import { coverImage } from '@/types/caseStudy'
import type { EducationCreateBody, EducationFormField } from '@/types/education'

const caseStudyFields: EducationFormField[] = [
    { key: 'title', label: 'Title', required: true, placeholder: 'Project title', wide: true },
    { key: 'slug', label: 'Slug', placeholder: 'project-title' },
    { key: 'client_name', label: 'Client', placeholder: 'Client name' },
    { key: 'tagline', label: 'Tagline', placeholder: 'Short line' },
    { key: 'summary', label: 'Summary', multiline: true, wide: true },
    { key: 'introduction', label: 'Introduction', multiline: true, wide: true },
]

const { caseStudies, creating, load, createCaseStudy } = useCaseStudies()
const formOpen = ref(false)
const formKey = ref(0)
const fieldErrors = ref<Record<string, string[]>>({})
const formMessage = ref('')

function openCreate() {
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
    formOpen.value = true
}

function closeForm() {
    if (creating.value) {
        return
    }

    formOpen.value = false
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

onUnmounted(() => window.removeEventListener('keydown', onFormKey))

function slugFromTitle(title: string) {
    return title
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '')
}

async function onSubmit(body: EducationCreateBody) {
    fieldErrors.value = {}
    formMessage.value = ''

    const title = typeof body.title === 'string' ? body.title : ''
    const slug = typeof body.slug === 'string' && body.slug !== '' ? body.slug : slugFromTitle(title)

    if (slug === '') {
        fieldErrors.value = { slug: ['A slug is required.'] }
        return
    }

    try {
        await createCaseStudy({ ...body, slug })
        formOpen.value = false
    } catch (error) {
        if (error instanceof ApiError) {
            fieldErrors.value = error.errors
            formMessage.value = error.message
        }
    }
}

onMounted(load)
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-8 p-8">
    <header class="space-y-2 border-b border-linear-border pb-6">
      <h1 class="flex items-center gap-3 text-[22px] font-bold uppercase tracking-tight text-linear-text">
        <span class="text-linear-green">//</span>
        Cases &amp; Projects
      </h1>
      <p class="text-xs font-medium uppercase tracking-wide text-linear-blue">
        Client work and the systems built for it.
      </p>
    </header>

    <p v-if="caseStudies.loading" class="text-xs uppercase tracking-wide text-linear-secondary">
      Loading cases.
    </p>
    <p v-else-if="caseStudies.error" class="text-xs uppercase tracking-wide text-linear-red" role="alert">
      {{ caseStudies.error }}
    </p>
    <p v-else-if="caseStudies.items.length === 0 && !isAuthenticated" class="text-xs uppercase tracking-wide text-linear-secondary">
      No cases yet.
    </p>

    <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <CaseStudyCard
          v-for="item in caseStudies.items"
          :key="item.id"
          :id="item.id"
          :title="item.title"
          :client-name="item.client_name"
          :tagline="item.tagline"
          :image-url="coverImage(item.assets)"
          :draft="item.status === 'draft'"
      />
      <CaseStudyAddCard v-if="isAuthenticated" @add="openCreate" />
    </div>

    <div
        v-if="formOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6 py-8"
        @click.self="closeForm"
    >
      <div class="max-h-full w-full max-w-3xl overflow-y-auto">
        <EducationCreatePanel
            :key="formKey"
            title="Add case"
            submit-label="Add case"
            id-prefix="case-study"
            form-id="case-study-form"
            :fields="caseStudyFields"
            :cancellable="true"
            :pending="creating"
            :errors="fieldErrors"
            :message="formMessage"
            @submit="onSubmit"
            @cancel="closeForm"
        />
      </div>
    </div>
  </div>
</template>
