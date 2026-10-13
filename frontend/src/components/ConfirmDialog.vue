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
        class="w-full max-w-sm space-y-4 rounded-md border border-linear-border bg-linear-surface p-6 outline-none"
    >
      <p id="confirm-title" class="text-[11px] font-bold uppercase tracking-widest text-linear-red">{{ title }}</p>
      <p class="text-sm uppercase tracking-wide text-linear-text">{{ message }}</p>
      <p v-if="error" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ error }}</p>
      <div class="flex items-center gap-4">
        <div class="w-full max-w-xs">
          <BaseButton variant="danger" :disabled="pending" @click="emit('confirm')">{{ confirmLabel }}</BaseButton>
        </div>
        <button
            type="button"
            class="text-[11px] font-bold uppercase tracking-widest text-linear-secondary hover:text-linear-text"
            :disabled="pending"
            @click="emit('cancel')"
        >
          Cancel
        </button>
      </div>
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
