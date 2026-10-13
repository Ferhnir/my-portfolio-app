<template>
  <form :id="formId" class="space-y-4 rounded-md border border-linear-border bg-linear-surface p-6" @submit.prevent="submit">
    <p class="text-[11px] font-bold uppercase tracking-widest text-linear-red">{{ title }}</p>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <label
          v-for="field in fields"
          :key="field.key"
          class="space-y-2"
          :class="field.wide ? 'md:col-span-2' : ''"
          :for="field.checkbox ? undefined : fieldId(field.key)"
      >
        <span v-if="!field.checkbox" class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">
          {{ field.label }}
        </span>

        <span v-else class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-linear-secondary">
          <input
              v-model="checks[field.key]"
              class="accent-linear-green"
              type="checkbox"
          >
          {{ field.label }}
        </span>

        <BaseDatePicker
            v-if="field.type === 'date'"
            :id="fieldId(field.key)"
            v-model="textValues[field.key]"
            :name="field.key"
            :placeholder="field.placeholder || 'Select a date'"
        />

        <BaseInput
            v-else-if="!field.checkbox"
            :id="fieldId(field.key)"
            v-model="textValues[field.key]"
            :name="field.key"
            :type="field.type === 'url' ? 'url' : 'text'"
            :multiline="field.multiline"
            :required="field.required"
            :placeholder="field.placeholder ?? ''"
        />

        <p v-if="errors[field.key]?.[0]" class="text-[11px] uppercase tracking-wide text-linear-red">
          {{ errors[field.key][0] }}
        </p>
      </label>
    </div>

    <p v-if="message && Object.keys(errors).length === 0" class="text-xs uppercase tracking-wide text-linear-red" role="alert">
      {{ message }}
    </p>

    <div class="flex items-center gap-4">
      <div class="w-full max-w-xs">
        <BaseButton type="submit" :disabled="pending">{{ submitLabel }}</BaseButton>
      </div>
      <button
          v-if="cancellable"
          type="button"
          class="text-[11px] font-bold uppercase tracking-widest text-linear-secondary hover:text-linear-text"
          @click="emit('cancel')"
      >
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseDatePicker from '@/components/BaseDatePicker.vue'
import BaseInput from '@/components/BaseInput.vue'
import type { EducationCreateBody, EducationFormField } from '@/types/education'

const props = withDefaults(defineProps<{
    title: string
    submitLabel: string
    idPrefix: string
    fields: EducationFormField[]
    values?: Record<string, string | boolean | null>
    pending?: boolean
    errors?: Record<string, string[]>
    message?: string
    includeEmpty?: boolean
    cancellable?: boolean
    formId?: string
}>(), {
    values: () => ({}),
    pending: false,
    errors: () => ({}),
    message: '',
    includeEmpty: false,
    cancellable: false,
    formId: undefined,
})

const emit = defineEmits<{
    submit: [body: EducationCreateBody]
    cancel: []
}>()

const textValues = reactive<Record<string, string>>({})
const checks = reactive<Record<string, boolean>>({})

for (const field of props.fields) {
    const initial = props.values[field.key]

    if (field.checkbox) {
        checks[field.key] = initial === true
    } else {
        textValues[field.key] = typeof initial === 'string' ? initial : ''
    }
}

function fieldId(key: string) {
    return `${props.idPrefix}-${key}`
}

function submit() {
    const body: EducationCreateBody = {}

    for (const field of props.fields) {
        if (field.checkbox) {
            if (props.includeEmpty || checks[field.key]) {
                body[field.key] = checks[field.key]
            }
            continue
        }

        const value = textValues[field.key].trim()

        if (props.includeEmpty) {
            body[field.key] = value === '' ? null : value
        } else if (value !== '') {
            body[field.key] = value
        }
    }

    emit('submit', body)
}
</script>
