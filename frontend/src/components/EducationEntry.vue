<template>
  <li class="relative pl-8">
    <span class="absolute left-0 top-2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-linear-green" aria-hidden="true" />

    <article class="space-y-2 rounded-md border border-linear-border bg-linear-surface/80 p-5">
      <div class="flex items-start justify-between gap-4">
        <h3 class="text-sm font-bold uppercase tracking-wide text-linear-text">{{ title }}</h3>
        <p v-if="period" class="shrink-0 text-[11px] font-bold uppercase tracking-widest text-linear-green">{{ period }}</p>
      </div>

      <p v-if="subtitle" class="text-xs font-medium uppercase tracking-wide text-linear-blue">{{ subtitle }}</p>
      <p v-if="body" class="whitespace-pre-line text-sm leading-relaxed text-linear-secondary">{{ body }}</p>

      <a
          v-if="href"
          :href="href"
          target="_blank"
          rel="noreferrer"
          class="inline-block text-xs font-bold uppercase tracking-widest text-linear-blue hover:text-linear-text"
      >
        {{ hrefLabel }}
      </a>
    </article>
  </li>
</template>

<script setup lang="ts">
defineProps<{
    title: string
    subtitle: string | null
    period: string | null
    body: string | null
    href: string | null
    hrefLabel: string | null
}>()
</script>
