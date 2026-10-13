<template>
  <li v-if="periodAside" class="grid grid-cols-1 items-stretch gap-2 sm:col-span-3 sm:grid-cols-subgrid sm:gap-x-4 sm:py-2">
    <p v-if="period" class="self-center text-[11px] font-bold uppercase tracking-widest text-linear-green sm:whitespace-nowrap">
      {{ period }}
    </p>
    <span v-else class="hidden sm:block" />

    <div class="relative hidden sm:block" aria-hidden="true">
      <span v-if="connectAbove" class="absolute bottom-1/2 left-1/2 top-0 w-px -translate-x-1/2 bg-linear-border" />
      <span v-if="connectBelow" class="absolute bottom-0 left-1/2 top-1/2 w-px -translate-x-1/2 bg-linear-border" />
      <span class="absolute left-1/2 top-1/2 h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-linear-green" />
    </div>

    <article class="space-y-2 self-center rounded-md border bg-linear-surface/80 p-5" :class="selected ? 'border-linear-green' : 'border-linear-border'">
      <div class="flex items-start justify-between gap-4">
        <h3 class="text-sm font-bold uppercase tracking-wide text-linear-text">{{ title }}</h3>
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

  <li v-else class="relative pl-8">
    <span class="absolute left-0 top-2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-linear-green" aria-hidden="true" />

    <article class="space-y-2 rounded-md border bg-linear-surface/80 p-5" :class="selected ? 'border-linear-green' : 'border-linear-border'">
      <div class="flex items-start justify-between gap-4">
        <h3 class="text-sm font-bold uppercase tracking-wide text-linear-text">{{ title }}</h3>
        <p v-if="period" class="shrink-0 text-[11px] font-bold uppercase tracking-widest text-linear-green">{{ period }}</p>
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
withDefaults(defineProps<{
    title: string
    subtitle: string | null
    period: string | null
    body: string | null
    href: string | null
    hrefLabel: string | null
    periodAside?: boolean
    connectAbove?: boolean
    connectBelow?: boolean
    canEdit?: boolean
    selected?: boolean
    busy?: boolean
}>(), {
    periodAside: false,
    connectAbove: false,
    connectBelow: false,
    canEdit: false,
    selected: false,
    busy: false,
})

const emit = defineEmits<{
    edit: []
    remove: []
}>()
</script>
