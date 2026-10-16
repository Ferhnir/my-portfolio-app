<script setup lang="ts">
import type { CaseStudyCardItem } from '@/types/caseStudy'

withDefaults(defineProps<{
    body: string
    items?: CaseStudyCardItem[]
    canEdit?: boolean
    busy?: boolean
}>(), {
    items: () => [],
    canEdit: false,
    busy: false,
})

const emit = defineEmits<{
    edit: []
    remove: []
}>()
</script>

<template>
  <article class="rounded-md border border-linear-border bg-linear-field p-6 text-sm leading-relaxed text-linear-text">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0 flex-1 space-y-3">
        <p>{{ body }}</p>
        <ul v-if="items.length > 0" class="list-disc space-y-1 pl-5">
          <li v-for="item in items" :key="item.id">{{ item.body }}</li>
        </ul>
      </div>
      <div v-if="canEdit" class="flex shrink-0 items-center gap-2">
        <button
            type="button"
            class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-green hover:text-linear-green disabled:opacity-40"
            aria-label="Edit"
            :disabled="busy"
            @click="emit('edit')"
        >
          <FontAwesomeIcon :icon="['fas', 'tools']" class="text-sm" />
        </button>
        <button
            type="button"
            class="flex h-7 w-7 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red disabled:opacity-40"
            aria-label="Delete"
            :disabled="busy"
            @click="emit('remove')"
        >
          <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
        </button>
      </div>
    </div>
  </article>
</template>
