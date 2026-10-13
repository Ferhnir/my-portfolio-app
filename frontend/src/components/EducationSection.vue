<template>
  <section :id="id" class="min-h-screen scroll-mt-14 px-8 py-16">
    <div class="mx-auto max-w-3xl space-y-8">
      <header class="space-y-2 border-b border-linear-border pb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-linear-red">{{ kicker }}</p>
        <h2 class="flex items-center gap-3 text-[22px] font-bold uppercase tracking-tight text-linear-text">
          <span class="text-linear-green">//</span>
          {{ title }}
        </h2>
        <p class="text-xs font-medium uppercase tracking-wide text-linear-blue">{{ summary }}</p>
      </header>

      <p v-if="loading" class="text-xs uppercase tracking-wide text-linear-secondary">Loading.</p>
      <p v-else-if="error" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ error }}</p>
      <p v-else-if="items.length === 0" class="text-xs uppercase tracking-wide text-linear-secondary">{{ empty }}</p>

      <ol
          v-else
          :class="periodAside
            ? 'grid grid-cols-1 gap-y-2 sm:grid-cols-[max-content_0.75rem_minmax(0,1fr)] sm:gap-x-4 sm:gap-y-0'
            : 'relative ml-3 space-y-6 border-l border-linear-border'"
      >
        <EducationEntry
            v-for="(item, index) in items"
            :key="item.id"
            :title="item.title"
            :subtitle="item.subtitle"
            :period="item.period"
            :body="item.body"
            :href="item.href"
            :href-label="item.hrefLabel"
            :period-aside="periodAside"
            :connect-above="index > 0"
            :connect-below="index < items.length - 1"
            :can-edit="canEdit"
            :busy="busy"
            :selected="editingId === item.id"
            @edit="emit('edit', item.id)"
            @remove="emit('remove', item.id)"
        />
      </ol>

      <slot />
    </div>
  </section>
</template>

<script setup lang="ts">
import EducationEntry from '@/components/EducationEntry.vue'
import type { EducationEntryView } from '@/types/education'

withDefaults(defineProps<{
    id: string
    kicker: string
    title: string
    summary: string
    loading: boolean
    error: string
    empty: string
    items: EducationEntryView[]
    periodAside?: boolean
    canEdit?: boolean
    editingId?: number | null
    busy?: boolean
}>(), {
    periodAside: false,
    canEdit: false,
    editingId: null,
    busy: false,
})

const emit = defineEmits<{
    edit: [id: number]
    remove: [id: number]
}>()
</script>
