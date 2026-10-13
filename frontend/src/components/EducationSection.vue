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

      <ol v-else class="relative ml-3 space-y-6 border-l border-linear-border">
        <EducationEntry
            v-for="item in items"
            :key="item.id"
            :title="item.title"
            :subtitle="item.subtitle"
            :period="item.period"
            :body="item.body"
            :href="item.href"
            :href-label="item.hrefLabel"
        />
      </ol>

      <slot />
    </div>
  </section>
</template>

<script setup lang="ts">
import EducationEntry from '@/components/EducationEntry.vue'
import type { EducationEntryView } from '@/types/education'

defineProps<{
    id: string
    kicker: string
    title: string
    summary: string
    loading: boolean
    error: string
    empty: string
    items: EducationEntryView[]
}>()
</script>
