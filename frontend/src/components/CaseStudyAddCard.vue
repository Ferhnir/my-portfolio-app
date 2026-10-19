<script setup lang="ts">
const emit = defineEmits<{
    add: []
}>()
</script>

<template>
  <button
      type="button"
      class="group relative h-full w-full rounded-md bg-linear-surface text-center shadow-[0_0_16px_rgba(140,255,0,0.35),0_0_48px_rgba(140,255,0,0.16)] transition-[box-shadow] duration-200 hover:shadow-[0_0_18px_rgba(140,255,0,0.6),0_0_56px_rgba(140,255,0,0.32)]"
      aria-label="Add new"
      @click="emit('add')"
  >
    <span class="pointer-events-none absolute left-3 top-3 z-10 h-4 w-4" aria-hidden="true">
      <span class="absolute left-0 top-0 h-px w-full bg-linear-green"></span>
      <span class="absolute left-0 top-0 h-full w-px bg-linear-green"></span>
    </span>
    <span class="pointer-events-none absolute right-3 top-3 z-10 h-4 w-4" aria-hidden="true">
      <span class="absolute right-0 top-0 h-px w-full bg-linear-green"></span>
      <span class="absolute right-0 top-0 h-full w-px bg-linear-green"></span>
    </span>
    <span class="pointer-events-none absolute bottom-3 left-3 z-10 h-4 w-4" aria-hidden="true">
      <span class="absolute bottom-0 left-0 h-px w-full bg-linear-green"></span>
      <span class="absolute bottom-0 left-0 h-full w-px bg-linear-green"></span>
    </span>
    <span class="pointer-events-none absolute bottom-3 right-3 z-10 h-4 w-4" aria-hidden="true">
      <span class="absolute bottom-0 right-0 h-px w-full bg-linear-green"></span>
      <span class="absolute bottom-0 right-0 h-full w-px bg-linear-green"></span>
    </span>

    <span class="relative flex h-full min-h-[22rem] flex-col items-center justify-center gap-5 overflow-hidden rounded-md px-8 py-10">
      <span
          class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(40,91,116,0.175)_1px,transparent_1px),linear-gradient(90deg,rgba(40,91,116,0.175)_1px,transparent_1px)] bg-[size:22px_22px]"
          aria-hidden="true"
      ></span>
      <span
          class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_25%,rgba(8,13,17,0.78)_100%)]"
          aria-hidden="true"
      ></span>

      <span class="relative flex flex-col items-center gap-4">
        <span class="relative flex h-16 w-16 items-center justify-center" aria-hidden="true">
          <span class="absolute left-1 top-1 h-2.5 w-2.5">
            <span class="absolute left-0 top-0 h-px w-full bg-linear-green"></span>
            <span class="absolute left-0 top-0 h-full w-px bg-linear-green"></span>
          </span>
          <span class="absolute right-1 top-1 h-2.5 w-2.5">
            <span class="absolute right-0 top-0 h-px w-full bg-linear-green"></span>
            <span class="absolute right-0 top-0 h-full w-px bg-linear-green"></span>
          </span>
          <span class="absolute bottom-1 left-1 h-2.5 w-2.5">
            <span class="absolute bottom-0 left-0 h-px w-full bg-linear-green"></span>
            <span class="absolute bottom-0 left-0 h-full w-px bg-linear-green"></span>
          </span>
          <span class="absolute bottom-1 right-1 h-2.5 w-2.5">
            <span class="absolute bottom-0 right-0 h-px w-full bg-linear-green"></span>
            <span class="absolute bottom-0 right-0 h-full w-px bg-linear-green"></span>
          </span>
          <span class="text-4xl font-bold leading-none text-linear-green drop-shadow-[0_0_12px_rgba(140,255,0,0.9)]">+</span>
        </span>

        <span class="text-[11px] font-bold uppercase tracking-widest text-linear-blue">
          Create a new case study
        </span>
        <span class="h-px w-10 bg-linear-green" aria-hidden="true"></span>
        <span class="max-w-xs text-xs leading-relaxed text-linear-secondary">
          Start documenting a project, system, or client.
        </span>
      </span>
    </span>
    <svg class="pointer-events-none absolute inset-0 z-20 h-full w-full text-linear-green" aria-hidden="true">
      <rect
          x="0.5"
          y="0.5"
          rx="5.5"
          fill="none"
          stroke="currentColor"
          stroke-width="1"
          class="h-[calc(100%-1px)] w-[calc(100%-1px)]"
      />
    </svg>
  </button>
</template>
