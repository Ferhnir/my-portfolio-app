<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import BaseBadge from '@/components/BaseBadge.vue'
import BaseButton from '@/components/BaseButton.vue'
import CaseStudyCardItemsField from '@/components/CaseStudyCardItemsField.vue'
import CaseStudyImageGrid from '@/components/CaseStudyImageGrid.vue'
import CaseStudyInfoCard from '@/components/CaseStudyInfoCard.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import EducationCreatePanel from '@/components/EducationCreatePanel.vue'
import { useCaseStudy } from '@/composables/useCaseStudies'
import { cardViews, wideImages } from '@/types/caseStudy'
import type { CaseStudyAsset, CaseStudyCardItemDraft, CaseStudySection } from '@/types/caseStudy'
import type { EducationCreateBody, EducationFormField } from '@/types/education'

const caseFields: EducationFormField[] = [
    { key: 'title', label: 'Title', required: true, wide: true },
    { key: 'slug', label: 'Slug', required: true },
    { key: 'client_name', label: 'Client' },
    { key: 'tagline', label: 'Tagline' },
    { key: 'summary', label: 'Summary', multiline: true, wide: true },
    { key: 'introduction', label: 'Introduction', multiline: true, wide: true },
    {
        key: 'status',
        label: 'Status',
        type: 'select',
        required: true,
        options: [
            { value: 'draft', label: 'Draft' },
            { value: 'published', label: 'Published' },
        ],
    },
]

const sectionFields: EducationFormField[] = [
    { key: 'title', label: 'Title', required: true, wide: true },
    { key: 'introduction', label: 'Introduction', multiline: true, wide: true },
]

const cardFields: EducationFormField[] = [
    { key: 'body', label: 'Card', required: true, multiline: true, wide: true },
]

type FormKind = 'case' | 'section' | 'card'
type PendingDelete =
    | { kind: 'case', label: string }
    | { kind: 'section', id: number, label: string }
    | { kind: 'card', sectionId: number, blockId: number, label: string }
    | { kind: 'image', sectionId: number | null, assetId: number, label: string }

const route = useRoute()
const router = useRouter()
const caseStudyId = computed(() => Number(route.params.id))
const {
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
} = useCaseStudy(() => caseStudyId.value)

const sections = computed(() => {
    const rows = caseStudy.value?.sections ?? []

    if (isAuthenticated.value) {
        return rows
    }

    return rows.filter((section) => cardViews(section).length > 0 || wideImages(section.assets).length > 0)
})

const formOpen = ref(false)
const formKey = ref(0)
const formKind = ref<FormKind>('case')
const editingSection = ref<CaseStudySection | null>(null)
const cardSectionId = ref<number | null>(null)
const cardPointId = ref<number | null>(null)
const cardBlockId = ref<number | null>(null)
const cardItems = ref<CaseStudyCardItemDraft[]>([])
const fieldErrors = ref<Record<string, string[]>>({})
const formMessage = ref('')

const imageOpen = ref(false)
const imageSectionId = ref<number | null>(null)
const imageCollection = ref<'cover' | 'gallery'>('gallery')
const imageFiles = ref<File[]>([])
const imageInputKey = ref(0)
const imageError = ref('')

const pendingDelete = ref<PendingDelete | null>(null)
const deleteError = ref('')

const formFields = computed(() => {
    if (formKind.value === 'section') {
        return sectionFields
    }

    if (formKind.value === 'card') {
        return cardFields
    }

    return caseFields
})

const formTitle = computed(() => {
    if (formKind.value === 'section') {
        return editingSection.value ? 'Edit section' : 'Add section'
    }

    if (formKind.value === 'card') {
        return cardPointId.value ? 'Edit card' : 'Add card'
    }

    return 'Edit case'
})

const submitLabel = computed(() => formKind.value === 'case' || editingSection.value || cardPointId.value ? 'Save' : 'Add')

const formValues = computed((): Record<string, string | boolean | null> => {
    if (formKind.value === 'section' && editingSection.value) {
        return {
            title: editingSection.value.title,
            introduction: editingSection.value.introduction ?? '',
        }
    }

    if (formKind.value === 'card' && cardPointId.value) {
        const section = sections.value.find((item) => item.id === cardSectionId.value)
        const card = section ? cardViews(section).find((item) => item.pointId === cardPointId.value) : undefined

        return { body: card?.body ?? '' }
    }

    if (formKind.value === 'case' && caseStudy.value) {
        return {
            title: caseStudy.value.title,
            slug: caseStudy.value.slug,
            client_name: caseStudy.value.client_name ?? '',
            tagline: caseStudy.value.tagline ?? '',
            summary: caseStudy.value.summary ?? '',
            introduction: caseStudy.value.introduction ?? '',
            status: caseStudy.value.status,
        }
    }

    return {}
})

const includeEmpty = computed(() => formKind.value === 'case' || editingSection.value !== null || cardPointId.value !== null)

function resetForm() {
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
}

function openCase() {
    formKind.value = 'case'
    editingSection.value = null
    cardPointId.value = null
    resetForm()
    formOpen.value = true
}

function openSection(section: CaseStudySection | null) {
    formKind.value = 'section'
    editingSection.value = section
    cardPointId.value = null
    resetForm()
    formOpen.value = true
}

function openCard(sectionId: number, card: { pointId: number, blockId: number, items: CaseStudyCardItemDraft[] } | null) {
    formKind.value = 'card'
    editingSection.value = null
    cardSectionId.value = sectionId
    cardPointId.value = card?.pointId ?? null
    cardBlockId.value = card?.blockId ?? null
    cardItems.value = card?.items.map((item) => ({ id: item.id, body: item.body })) ?? []
    resetForm()
    formOpen.value = true
}

function closeForm() {
    if (saving.value) {
        return
    }

    formOpen.value = false
}

function openImage(sectionId: number | null) {
    imageSectionId.value = sectionId
    imageCollection.value = 'gallery'
    imageFiles.value = []
    imageInputKey.value += 1
    imageError.value = ''
    imageOpen.value = true
}

watch(imageCollection, () => {
    imageFiles.value = []
    imageInputKey.value += 1
    imageError.value = ''
})

function closeImage() {
    if (saving.value) {
        return
    }

    imageOpen.value = false
}

function onFile(event: Event) {
    const input = event.target as HTMLInputElement
    const selected = [...(input.files ?? [])]
    imageFiles.value = imageCollection.value === 'cover' ? selected.slice(0, 1) : selected
}

function onFormKey(event: KeyboardEvent) {
    if (event.key !== 'Escape' || saving.value || pendingDelete.value) {
        return
    }

    imageOpen.value = false
    formOpen.value = false
}

watch([formOpen, imageOpen], ([form, image]) => {
    if (form || image) {
        window.addEventListener('keydown', onFormKey)
    } else {
        window.removeEventListener('keydown', onFormKey)
    }
})

onUnmounted(() => window.removeEventListener('keydown', onFormKey))

async function onSubmit(body: EducationCreateBody) {
    fieldErrors.value = {}
    formMessage.value = ''

    try {
        if (formKind.value === 'case') {
            await updateCase(body)
        } else if (formKind.value === 'section' && editingSection.value) {
            await updateSection(editingSection.value.id, body)
        } else if (formKind.value === 'section') {
            await createSection(body)
        } else if (formKind.value === 'card' && cardPointId.value && cardBlockId.value && cardSectionId.value) {
            const text = typeof body.body === 'string' ? body.body : ''
            await updateCard(cardSectionId.value, cardBlockId.value, cardPointId.value, text, cardItems.value)
        } else if (formKind.value === 'card' && cardSectionId.value) {
            const text = typeof body.body === 'string' ? body.body : ''
            await createCard(cardSectionId.value, text, cardItems.value)
        }

        formOpen.value = false
    } catch (reason) {
        if (reason instanceof ApiError) {
            fieldErrors.value = reason.errors
            formMessage.value = reason.message
        }
    }
}

async function onUpload() {
    imageError.value = ''
    const files = imageCollection.value === 'cover' ? imageFiles.value.slice(0, 1) : [...imageFiles.value]

    if (files.length === 0) {
        imageError.value = 'An image is required.'
        return
    }

    const result = await uploadImages(imageSectionId.value, files, imageCollection.value)

    if (result.error) {
        imageFiles.value = files.slice(result.stored)
        imageError.value = result.stored > 0
            ? `${result.error} ${result.stored} saved.`
            : result.error
        return
    }

    imageOpen.value = false
}

function askDelete(target: PendingDelete) {
    deleteError.value = ''
    pendingDelete.value = target
}

async function confirmDelete() {
    const target = pendingDelete.value

    if (!target) {
        return
    }

    deleteError.value = ''

    try {
        if (target.kind === 'case') {
            await removeCase()
            await router.push({ name: 'cases' })
            return
        }

        if (target.kind === 'section') {
            await removeSection(target.id)
        } else if (target.kind === 'card') {
            await removeCard(target.sectionId, target.blockId)
        } else {
            await removeImage(target.sectionId, target.assetId)
        }

        pendingDelete.value = null
    } catch (reason) {
        deleteError.value = reason instanceof ApiError ? reason.message : 'Could not delete.'
    }
}

function cancelDelete() {
    if (saving.value) {
        return
    }

    pendingDelete.value = null
}

function removeImageAsk(sectionId: number | null, image: CaseStudyAsset) {
    askDelete({
        kind: 'image',
        sectionId,
        assetId: image.id,
        label: image.original_name || 'this image',
    })
}

onMounted(() => {
    void load()
})

watch(caseStudyId, () => {
    void load()
})
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-10 p-8">
    <RouterLink
        :to="{ name: 'cases' }"
        class="text-[11px] font-bold uppercase tracking-widest text-linear-secondary transition-colors hover:text-linear-text"
    >
      // Cases
    </RouterLink>

    <p v-if="loading" class="text-xs uppercase tracking-wide text-linear-secondary">
      Loading case.
    </p>
    <p v-else-if="error" class="text-xs uppercase tracking-wide text-linear-red" role="alert">
      {{ error }}
    </p>

    <template v-else-if="caseStudy">
      <header class="space-y-3 border-b border-linear-border pb-6">
        <div class="flex items-center justify-between gap-3">
          <p v-if="caseStudy.client_name" class="text-[11px] font-bold uppercase tracking-widest text-linear-red">
            {{ caseStudy.client_name }}
          </p>
          <BaseBadge v-if="caseStudy.status === 'draft'" tone="red">Draft</BaseBadge>
        </div>
        <div class="flex items-start justify-between gap-4">
          <h1 class="flex items-center gap-3 text-[22px] font-bold uppercase tracking-tight text-linear-text">
            <span class="text-linear-green">//</span>
            {{ caseStudy.title }}
          </h1>
          <div v-if="isAuthenticated" class="flex shrink-0 items-center gap-2">
            <button
                type="button"
                class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green disabled:opacity-40"
                aria-label="Edit case"
                :disabled="saving"
                @click="openCase"
            >
              <FontAwesomeIcon :icon="['fas', 'tools']" class="text-sm" />
            </button>
            <button
                type="button"
                class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red disabled:opacity-40"
                aria-label="Delete case"
                :disabled="saving"
                @click="askDelete({ kind: 'case', label: caseStudy.title })"
            >
              <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
            </button>
          </div>
        </div>
        <p v-if="caseStudy.tagline" class="text-xs font-medium uppercase tracking-wide text-linear-blue">
          {{ caseStudy.tagline }}
        </p>
        <p v-if="caseStudy.summary" class="max-w-3xl text-sm leading-relaxed text-linear-secondary">
          {{ caseStudy.summary }}
        </p>
        <p v-if="caseStudy.introduction" class="max-w-3xl text-sm leading-relaxed text-linear-text">
          {{ caseStudy.introduction }}
        </p>
      </header>

      <CaseStudyImageGrid
          :images="wideImages(caseStudy.assets)"
          :can-remove="isAuthenticated"
          :can-add="isAuthenticated"
          :busy="saving"
          @remove="removeImageAsk(null, $event)"
      >
        <button
            v-if="isAuthenticated"
            type="button"
            class="flex aspect-[16/9] items-center justify-center gap-3 rounded-md border border-linear-border bg-linear-surface text-xs font-bold uppercase tracking-widest text-linear-text transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
            @click="openImage(null)"
        >
          <span class="text-linear-green">//</span>
          Add image
        </button>
      </CaseStudyImageGrid>

      <section v-for="section in sections" :key="section.id" class="space-y-6">
        <header class="space-y-2">
          <div class="flex items-start justify-between gap-4">
            <h2 class="text-xs font-bold uppercase tracking-widest text-linear-text">
              <span class="text-linear-green">//</span>
              {{ section.title }}
            </h2>
            <div v-if="isAuthenticated" class="flex shrink-0 items-center gap-2">
              <button
                  type="button"
                  class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green disabled:opacity-40"
                  aria-label="Edit section"
                  :disabled="saving"
                  @click="openSection(section)"
              >
                <FontAwesomeIcon :icon="['fas', 'tools']" class="text-sm" />
              </button>
              <button
                  type="button"
                  class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red disabled:opacity-40"
                  aria-label="Delete section"
                  :disabled="saving"
                  @click="askDelete({ kind: 'section', id: section.id, label: section.title })"
              >
                <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
              </button>
            </div>
          </div>
          <p v-if="section.introduction" class="max-w-3xl text-sm leading-relaxed text-linear-secondary">
            {{ section.introduction }}
          </p>
        </header>

        <CaseStudyImageGrid
            :images="wideImages(section.assets)"
            :can-remove="isAuthenticated"
            :can-add="isAuthenticated"
            :busy="saving"
            @remove="removeImageAsk(section.id, $event)"
        >
          <button
              v-if="isAuthenticated"
              type="button"
              class="flex aspect-[16/9] items-center justify-center gap-3 rounded-md border border-linear-border bg-linear-surface text-xs font-bold uppercase tracking-widest text-linear-text transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
              @click="openImage(section.id)"
          >
            <span class="text-linear-green">//</span>
            Add image
          </button>
        </CaseStudyImageGrid>

        <div v-if="cardViews(section).length > 0 || isAuthenticated" class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <CaseStudyInfoCard
              v-for="card in cardViews(section)"
              :key="card.pointId"
              :body="card.body"
              :items="card.items"
              :can-edit="isAuthenticated"
              :busy="saving"
              @edit="openCard(section.id, card)"
              @remove="askDelete({ kind: 'card', sectionId: section.id, blockId: card.blockId, label: 'this card' })"
          />
          <button
              v-if="isAuthenticated"
              type="button"
              class="flex min-h-32 items-center justify-center gap-3 rounded-md border border-linear-border bg-linear-surface text-xs font-bold uppercase tracking-widest text-linear-text transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
              @click="openCard(section.id, null)"
          >
            <span class="text-linear-green">//</span>
            Add card
          </button>
        </div>
      </section>

      <BaseButton v-if="isAuthenticated" type="button" :disabled="saving" @click="openSection(null)">
        Add section
      </BaseButton>
    </template>

    <div
        v-if="formOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6 py-8"
        @click.self="closeForm"
    >
      <div class="max-h-full w-full max-w-3xl overflow-y-auto">
        <EducationCreatePanel
            :key="formKey"
            :title="formTitle"
            :submit-label="submitLabel"
            id-prefix="case-editor"
            form-id="case-editor-form"
            :fields="formFields"
            :values="formValues"
            :include-empty="includeEmpty"
            :cancellable="true"
            :pending="saving"
            :errors="fieldErrors"
            :message="formMessage"
            @submit="onSubmit"
            @cancel="closeForm"
        >
          <CaseStudyCardItemsField v-if="formKind === 'card'" v-model="cardItems" />
        </EducationCreatePanel>
      </div>
    </div>

    <div
        v-if="imageOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6 py-8"
        @click.self="closeImage"
    >
      <form class="relative w-full max-w-lg space-y-4 rounded-md border border-linear-border bg-linear-surface p-6" @submit.prevent="onUpload">
        <button
            type="button"
            class="absolute right-4 top-4 flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-sm leading-none text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green disabled:opacity-40"
            aria-label="Close"
            :disabled="saving"
            @click="closeImage"
        >
          ×
        </button>
        <p class="pr-10 text-[11px] font-bold uppercase tracking-widest text-linear-red">Add image</p>
        <label class="block space-y-2" for="case-image-collection">
          <span class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">Place</span>
          <select
              id="case-image-collection"
              v-model="imageCollection"
              class="w-full rounded-md border border-linear-border bg-linear-field px-3 py-2 text-xs uppercase tracking-wide text-linear-text outline-none focus:border-linear-border-hover"
          >
            <option value="gallery">Gallery</option>
            <option value="cover">Cover</option>
          </select>
        </label>
        <label class="block space-y-2" for="case-image-file">
          <span class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">
            {{ imageCollection === 'gallery' ? 'Images' : 'Image' }}
          </span>
          <input
              id="case-image-file"
              :key="imageInputKey"
              type="file"
              accept="image/jpeg,image/png,image/webp,image/gif"
              :multiple="imageCollection === 'gallery'"
              class="w-full text-xs text-linear-secondary file:mr-3 file:rounded-md file:border file:border-linear-border file:bg-linear-field file:px-3 file:py-2 file:text-xs file:uppercase file:tracking-wide file:text-linear-text"
              @change="onFile"
          >
        </label>
        <ul v-if="imageFiles.length > 1" class="space-y-1">
          <li v-for="(file, index) in imageFiles" :key="`${file.name}-${index}`" class="text-xs text-linear-secondary">
            {{ file.name }}
          </li>
        </ul>
        <p v-if="imageError" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ imageError }}</p>
        <BaseButton type="submit" :disabled="saving">Upload</BaseButton>
      </form>
    </div>

    <ConfirmDialog
        :open="pendingDelete !== null"
        :title="pendingDelete?.kind === 'case' ? 'Delete case' : 'Delete'"
        :message="pendingDelete ? `Delete ${pendingDelete.label}?` : ''"
        confirm-label="Delete"
        :pending="saving"
        :error="deleteError"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
    />
  </div>
</template>
