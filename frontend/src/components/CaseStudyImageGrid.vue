<script setup lang="ts">
import { onUnmounted, ref, watch } from 'vue'
import type { CaseStudyAsset } from '@/types/caseStudy'

withDefaults(defineProps<{
    images: CaseStudyAsset[]
    canRemove?: boolean
    canAdd?: boolean
    busy?: boolean
}>(), {
    canRemove: false,
    canAdd: false,
    busy: false,
})

const emit = defineEmits<{
    remove: [image: CaseStudyAsset]
}>()

const active = ref<CaseStudyAsset | null>(null)

function thumb(image: CaseStudyAsset) {
    return image.thumb_url || image.url
}

function close() {
    active.value = null
}

function onKey(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        close()
    }
}

watch(active, (image) => {
    if (image) {
        window.addEventListener('keydown', onKey)
    } else {
        window.removeEventListener('keydown', onKey)
    }
})

onUnmounted(() => window.removeEventListener('keydown', onKey))
</script>

<template>
  <div v-if="images.length > 0 || canAdd" class="grid grid-cols-2 gap-6 md:grid-cols-4">
    <figure
        v-for="image in images"
        :key="image.id"
        class="relative overflow-hidden rounded-md border border-linear-border bg-linear-base"
    >
      <button type="button" class="block w-full" aria-label="View image" @click="active = image">
        <img
            :src="thumb(image)"
            :alt="image.original_name || 'Case image'"
            class="aspect-[16/9] w-full object-cover"
        >
      </button>
      <button
          v-if="canRemove"
          type="button"
          class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-md border border-linear-border bg-linear-surface text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red disabled:opacity-40"
          aria-label="Delete image"
          :disabled="busy"
          @click="emit('remove', image)"
      >
        <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
      </button>
    </figure>
    <slot />
  </div>

  <div
      v-if="active"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 px-6 py-8"
      @click.self="close"
  >
    <button
        type="button"
        class="absolute right-4 top-4 flex h-7 w-7 items-center justify-center rounded-md border border-linear-border bg-linear-surface text-sm leading-none text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green"
        aria-label="Close"
        @click="close"
    >
      ×
    </button>
    <img
        :src="active.url"
        :alt="active.original_name || 'Case image'"
        class="max-h-[85vh] max-w-full object-contain"
    >
  </div>
</template>
