<script setup lang="ts">
import { RouterLink } from 'vue-router'
import BaseBadge from '@/components/BaseBadge.vue'

defineProps<{
    id: number
    title: string
    clientName: string | null
    tagline: string | null
    imageUrl: string | null
    draft: boolean
}>()
</script>

<template>
  <RouterLink
      :to="{ name: 'case-study', params: { id } }"
      class="group flex flex-col rounded-md border border-linear-border bg-linear-surface shadow-[0_20px_70px_rgba(0,0,0,0.28)] transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
  >
    <div class="aspect-[16/9] overflow-hidden rounded-t-md border-b border-linear-border bg-linear-base">
      <img
          v-if="imageUrl"
          :src="imageUrl"
          :alt="title"
          class="h-full w-full object-cover"
      >
    </div>

    <div class="space-y-3 p-6">
      <div class="flex items-center justify-between gap-3">
        <p v-if="clientName" class="text-[11px] font-bold uppercase tracking-widest text-linear-red">
          {{ clientName }}
        </p>
        <BaseBadge v-if="draft" tone="red">Draft</BaseBadge>
      </div>

      <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-linear-text">
        <span class="text-linear-green">//</span>
        {{ title }}
      </h2>

      <p v-if="tagline" class="text-xs font-medium uppercase tracking-wide text-linear-blue">
        {{ tagline }}
      </p>
    </div>
  </RouterLink>
</template>
