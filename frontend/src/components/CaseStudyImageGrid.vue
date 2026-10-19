<script setup lang="ts">
import { computed, ref } from 'vue'
import ImageGallery from '@/components/ImageGallery.vue'
import type { GalleryImage } from '@/components/ImageGallery.vue'
import type { CaseStudyAsset } from '@/types/caseStudy'

const props = withDefaults(defineProps<{
    images: CaseStudyAsset[]
    canRemove?: boolean
    canAdd?: boolean
    busy?: boolean
    notice?: { collection: 'cover' | 'gallery', message: string } | null
    withCover?: boolean
}>(), {
    canRemove: false,
    canAdd: false,
    busy: false,
    notice: null,
    withCover: false,
})

const cover = computed(() => props.images.find((image) => image.collection === 'cover') ?? null)
const screenshots = computed(() => props.images.filter((image) => image.collection === 'gallery'))
const showCover = computed(() => props.withCover && (props.canAdd || cover.value !== null))
const showScreenshots = computed(() => props.canAdd || screenshots.value.length > 0)

const emit = defineEmits<{
    remove: [image: CaseStudyAsset]
    add: [collection: 'cover' | 'gallery', files: File[]]
}>()

const dragging = ref<'cover' | 'gallery' | null>(null)

function zoneClass(collection: 'cover' | 'gallery') {
    return [
        dragging.value === collection ? 'border-linear-green shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]' : '',
        props.busy ? 'pointer-events-none opacity-40' : '',
    ]
}

function onPick(event: Event, collection: 'cover' | 'gallery') {
    const input = event.target as HTMLInputElement
    const files = [...(input.files ?? [])]
    input.value = ''
    emit('add', collection, files)
}

function onDragOver(event: DragEvent, collection: 'cover' | 'gallery') {
    event.preventDefault()

    if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'copy'
    }

    dragging.value = collection
}

function onDragLeave(event: DragEvent, collection: 'cover' | 'gallery') {
    const next = event.relatedTarget

    if (next instanceof Node && event.currentTarget instanceof Node && event.currentTarget.contains(next)) {
        return
    }

    if (dragging.value === collection) {
        dragging.value = null
    }
}

function onDrop(event: DragEvent, collection: 'cover' | 'gallery') {
    event.preventDefault()
    dragging.value = null
    emit('add', collection, [...(event.dataTransfer?.files ?? [])])
}

const active = ref<number | null>(null)

const galleryImages = computed((): GalleryImage[] => screenshots.value.map((image) => ({
    src: image.url,
    thumb: thumb(image),
    alt: image.original_name || 'Screenshot',
})))

function thumb(image: CaseStudyAsset) {
    return image.thumb_url || image.url
}
</script>

<template>
  <div v-if="showCover || showScreenshots" class="flex flex-col md:flex-row">
    <section v-if="showCover" class="min-w-0 md:w-1/3 md:pr-8">
      <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-linear-secondary">Cover</h3>
      <figure
          v-if="cover"
          class="relative overflow-hidden rounded-md border border-linear-border bg-linear-base"
      >
        <img
            :src="thumb(cover)"
            :alt="cover.original_name || 'Cover'"
            class="aspect-[16/9] w-full object-cover"
        >
        <button
            v-if="canRemove"
            type="button"
            class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-md border border-linear-border bg-linear-surface text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red disabled:opacity-40"
            aria-label="Delete cover"
            :disabled="busy"
            @click="emit('remove', cover)"
        >
          <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
        </button>
      </figure>
      <div v-else-if="canAdd" class="space-y-2">
        <label
            class="flex aspect-[16/9] cursor-pointer flex-col items-center justify-center gap-2 rounded-md border border-linear-border bg-linear-surface px-4 text-center text-xs font-bold uppercase tracking-widest text-linear-text transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
            :class="zoneClass('cover')"
            @dragover="onDragOver($event, 'cover')"
            @dragleave="onDragLeave($event, 'cover')"
            @drop="onDrop($event, 'cover')"
        >
          <input
              type="file"
              accept="image/jpeg,image/png,image/webp,image/gif"
              class="sr-only"
              :disabled="busy"
              @change="onPick($event, 'cover')"
          >
          <span class="flex items-center gap-3">
            <span class="text-linear-green">//</span>
            Add cover
          </span>
          <span class="text-[11px] font-medium tracking-wide text-linear-secondary">
            Click to upload or drag and drop
          </span>
        </label>
        <p
            v-if="notice?.collection === 'cover'"
            class="text-center text-[11px] font-bold uppercase tracking-wide text-linear-red"
            role="alert"
        >
          {{ notice.message }}
        </p>
      </div>
    </section>

    <div v-if="showCover && showScreenshots" class="hidden w-px shrink-0 bg-linear-border md:block" aria-hidden="true"></div>

    <section
        v-if="showScreenshots"
        class="min-w-0"
        :class="showCover ? 'mt-8 border-t border-linear-border pt-8 md:mt-0 md:w-2/3 md:border-t-0 md:pl-8 md:pt-0' : ''"
    >
      <h3 v-if="showCover" class="mb-4 text-sm font-bold uppercase tracking-widest text-linear-secondary">Screenshots</h3>
      <div class="grid grid-cols-2 gap-6" :class="showCover ? '' : 'md:grid-cols-4'">
        <figure
            v-for="(image, index) in screenshots"
            :key="image.id"
            class="relative overflow-hidden rounded-md border border-linear-border bg-linear-base"
        >
          <button type="button" class="block w-full cursor-pointer" aria-label="View image" @click="active = index">
            <img
                :src="thumb(image)"
                :alt="image.original_name || 'Screenshot'"
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
        <div v-if="canAdd" class="space-y-2">
          <label
              class="flex aspect-[16/9] cursor-pointer flex-col items-center justify-center gap-2 rounded-md border border-linear-border bg-linear-surface px-4 text-center text-xs font-bold uppercase tracking-widest text-linear-text transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
              :class="zoneClass('gallery')"
              @dragover="onDragOver($event, 'gallery')"
              @dragleave="onDragLeave($event, 'gallery')"
              @drop="onDrop($event, 'gallery')"
          >
            <input
                type="file"
                accept="image/jpeg,image/png,image/webp,image/gif"
                class="sr-only"
                multiple
                :disabled="busy"
                @change="onPick($event, 'gallery')"
            >
            <span class="flex items-center gap-3">
              <span class="text-linear-green">//</span>
              Add images
            </span>
            <span class="text-[11px] font-medium tracking-wide text-linear-secondary">
              Click to upload or drag and drop
            </span>
          </label>
          <p
              v-if="notice?.collection === 'gallery'"
              class="text-center text-[11px] font-bold uppercase tracking-wide text-linear-red"
              role="alert"
          >
            {{ notice.message }}
          </p>
        </div>
      </div>
    </section>
  </div>

  <ImageGallery v-model="active" :images="galleryImages" />
</template>
