<template>
  <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6"
      @click.self="emit('cancel')"
  >
    <div
        ref="panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-title"
        tabindex="-1"
        class="relative w-full max-w-sm space-y-4 rounded-md border border-linear-border bg-linear-surface p-6 outline-none"
    >
      <button
          type="button"
          class="absolute right-4 top-4 flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-sm leading-none text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green disabled:opacity-40"
          aria-label="Close"
          :disabled="pending"
          @click="emit('cancel')"
      >
        ×
      </button>
      <p id="confirm-title" class="pr-10 text-[11px] font-bold uppercase tracking-widest text-linear-red">{{ title }}</p>
      <p class="text-sm uppercase tracking-wide text-linear-text">{{ message }}</p>
      <p v-if="error" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ error }}</p>
      <BaseButton variant="danger" :disabled="pending" @click="emit('confirm')">{{ confirmLabel }}</BaseButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { nextTick, onUnmounted, useTemplateRef, watch } from 'vue'
import BaseButton from '@/components/BaseButton.vue'

const props = defineProps<{
    open: boolean
    title: string
    message: string
    confirmLabel: string
    pending?: boolean
    error?: string
}>()

const emit = defineEmits<{
    confirm: []
    cancel: []
}>()

const panel = useTemplateRef<HTMLElement>('panel')

function onKey(event: KeyboardEvent) {
    if (event.key === 'Escape' && !props.pending) {
        emit('cancel')
    }
}

watch(() => props.open, (open) => {
    if (open) {
        window.addEventListener('keydown', onKey)
        nextTick(() => panel.value?.focus())
    } else {
        window.removeEventListener('keydown', onKey)
    }
})

onUnmounted(() => window.removeEventListener('keydown', onKey))
</script>
