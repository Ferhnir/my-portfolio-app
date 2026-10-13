<template>
  <div>
    <EducationSection
        id="employment"
        kicker="01"
        title="Employment"
        summary="Roles, companies, and where you worked."
        empty="No employment history yet."
        :loading="employments.loading"
        :error="employments.error"
        :items="entries"
        :can-edit="isAuthenticated"
        :busy="creating"
        :editing-id="editing?.id ?? null"
        period-aside
        @edit="startEdit"
        @remove="onRemove"
    >
      <BaseButton v-if="isAuthenticated" type="button" @click="openCreate">Add new record</BaseButton>
    </EducationSection>

    <div
        v-if="formOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6 py-8"
        @click.self="closeForm"
    >
      <div class="max-h-full w-full max-w-3xl overflow-y-auto">
        <EducationCreatePanel
            :key="formKey"
            :title="editing ? 'Edit employment' : 'Add employment'"
            :submit-label="editing ? 'Save' : 'Add employment'"
            id-prefix="employment"
            form-id="employment-form"
            :fields="employmentFields"
            :values="formValues"
            :include-empty="editing !== null"
            :cancellable="true"
            :pending="creating"
            :errors="fieldErrors"
            :message="formMessage"
            @submit="onSubmit"
            @cancel="closeForm"
        />
      </div>
    </div>

    <ConfirmDialog
        :open="pendingDelete !== null"
        title="Delete employment"
        :message="pendingDelete ? `Delete ${pendingDelete.company_name}?` : ''"
        confirm-label="Delete"
        :pending="creating"
        :error="deleteError"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import BaseButton from '@/components/BaseButton.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import EducationCreatePanel from '@/components/EducationCreatePanel.vue'
import EducationSection from '@/components/EducationSection.vue'
import { useEmployment } from '@/composables/useEmployment'
import { employmentFields, toEmploymentEntry } from '@/types/employment'
import type { EducationCreateBody } from '@/types/education'
import type { Employment } from '@/types/employment'

const formKey = ref(0)
const formOpen = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})
const formMessage = ref('')
const editing = ref<Employment | null>(null)
const pendingDelete = ref<Employment | null>(null)
const deleteError = ref('')
const { employments, creating, createEmployment, updateEmployment, removeEmployment } = useEmployment()
const entries = computed(() => employments.value.items.map(toEmploymentEntry))

const formValues = computed((): Record<string, string | boolean | null> => {
    const row = editing.value

    if (!row) {
        return {}
    }

    return {
        company_name: row.company_name,
        job_title: row.job_title ?? '',
        location: row.location ?? '',
        start_date: row.start_date ?? '',
        end_date: row.end_date ?? '',
        is_current: row.is_current,
        description: row.description ?? '',
    }
})

function resetForm() {
    editing.value = null
    fieldErrors.value = {}
    formMessage.value = ''
    formOpen.value = false
    formKey.value += 1
}

function openCreate() {
    editing.value = null
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
    formOpen.value = true
}

function startEdit(id: number) {
    editing.value = employments.value.items.find((item) => item.id === id) ?? null
    fieldErrors.value = {}
    formMessage.value = ''
    formKey.value += 1
    formOpen.value = true
}

function closeForm() {
    if (creating.value) {
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

onUnmounted(() => window.removeEventListener('keydown', onFormKey))

function onRemove(id: number) {
    pendingDelete.value = employments.value.items.find((item) => item.id === id) ?? null
    deleteError.value = ''
}

function cancelDelete() {
    if (creating.value) {
        return
    }

    pendingDelete.value = null
    deleteError.value = ''
}

async function confirmDelete() {
    const row = pendingDelete.value

    if (!row) {
        return
    }

    deleteError.value = ''

    try {
        await removeEmployment(row.id)

        if (editing.value?.id === row.id) {
            resetForm()
        }

        pendingDelete.value = null
    } catch (error) {
        deleteError.value = error instanceof ApiError ? error.message : 'Could not delete this record.'
    }
}

async function onSubmit(body: EducationCreateBody) {
    fieldErrors.value = {}
    formMessage.value = ''

    try {
        if (editing.value) {
            await updateEmployment(editing.value.id, body)
        } else {
            await createEmployment(body)
        }

        resetForm()
    } catch (error) {
        fieldErrors.value = error instanceof ApiError ? error.errors : {}
        formMessage.value = error instanceof ApiError ? error.message : 'Could not save this record.'
    }
}
</script>
