<script setup lang="ts">
import { computed, nextTick, onUnmounted, useTemplateRef, watch } from 'vue'

export interface GalleryImage {
    src: string
    thumb?: string | null
    alt?: string
}

const active = defineModel<number | null>({ default: null })

const props = defineProps<{
    images: GalleryImage[]
}>()

const panel = useTemplateRef<HTMLElement>('panel')

const index = computed(() => {
    if (active.value === null || props.images.length === 0) {
        return 0
    }

    return Math.min(Math.max(active.value, 0), props.images.length - 1)
})

const current = computed(() => (active.value === null ? null : props.images[index.value] ?? null))

function close() {
    active.value = null
}

function show(next: number) {
    const count = props.images.length

    if (count === 0) {
        return
    }

    active.value = (next + count) % count
}

function onKey(event: KeyboardEvent) {
    if (active.value === null) {
        return
    }

    if (event.key === 'Escape') {
        close()
    }

    if (event.key === 'ArrowLeft') {
        event.preventDefault()
        show(index.value - 1)
    }

    if (event.key === 'ArrowRight') {
        event.preventDefault()
        show(index.value + 1)
    }
}

watch(active, async (value) => {
    if (value === null) {
        document.body.style.overflow = ''
        window.removeEventListener('keydown', onKey)
        return
    }

    document.body.style.overflow = 'hidden'
    window.addEventListener('keydown', onKey)
    await nextTick()
    panel.value?.focus()
    panel.value?.querySelector<HTMLElement>('[data-active-thumb="true"]')?.scrollIntoView({
        inline: 'center',
        block: 'nearest',
    })
})

onUnmounted(() => {
    document.body.style.overflow = ''
    window.removeEventListener('keydown', onKey)
})
</script>

<template>
  <Teleport to="body">
    <div
        v-if="current"
        class="fixed inset-0 z-50 flex flex-col bg-black px-4 py-6 sm:px-8"
        @click="close"
    >
      <div
          ref="panel"
          role="dialog"
          aria-modal="true"
          aria-label="Image gallery"
          tabindex="-1"
          class="flex min-h-0 flex-1 flex-col outline-none"
      >
        <button
            type="button"
            class="absolute right-4 top-4 z-10 flex h-7 w-7 cursor-pointer items-center justify-center rounded-md border border-linear-border bg-linear-surface text-sm leading-none text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green"
            aria-label="Close"
            @click.stop="close"
        >
          ×
        </button>

        <div class="relative min-h-0 flex-1">
          <div class="absolute inset-0 flex items-center justify-center px-14">
            <img
                :src="current.src"
                :alt="current.alt || 'Image'"
                class="h-full w-auto max-w-full object-contain"
                @click.stop
            >
          </div>

          <button
              v-if="images.length > 1"
              type="button"
              class="absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md border border-linear-border bg-linear-surface text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green"
              aria-label="Previous image"
              @click.stop="show(index - 1)"
          >
            <FontAwesomeIcon :icon="['fas', 'chevron-left']" />
          </button>

          <button
              v-if="images.length > 1"
              type="button"
              class="absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md border border-linear-border bg-linear-surface text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green"
              aria-label="Next image"
              @click.stop="show(index + 1)"
          >
            <FontAwesomeIcon :icon="['fas', 'chevron-right']" />
          </button>
        </div>

        <div class="mx-auto mt-4 flex max-w-full shrink-0 justify-center gap-3 overflow-x-auto px-12 pb-1" @click.stop>
          <button
              v-for="(image, imageIndex) in images"
              :key="imageIndex"
              type="button"
              class="h-16 w-24 shrink-0 cursor-pointer overflow-hidden rounded-md border bg-linear-base"
              :class="imageIndex === index ? 'border-linear-green' : 'border-linear-border'"
              :data-active-thumb="imageIndex === index ? 'true' : undefined"
              :aria-label="`Show image ${imageIndex + 1}`"
              :aria-current="imageIndex === index ? 'true' : undefined"
              @click="show(imageIndex)"
          >
            <img
                :src="image.thumb || image.src"
                :alt="image.alt || 'Thumbnail'"
                class="h-full w-full object-cover"
            >
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
