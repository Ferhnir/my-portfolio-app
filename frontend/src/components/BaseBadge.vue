<script setup lang="ts">
withDefaults(defineProps<{
    tone?: 'red' | 'blue'
    kind?: 'static' | 'source' | 'handle'
    handleClass?: string
}>(), {
    tone: 'blue',
    kind: 'static',
    handleClass: '',
})
</script>

<template>
  <span
      class="px-2.5 py-1 text-[12px] font-medium uppercase tracking-wide font-cyber-display bg-linear-field border border-linear-border rounded-md"
      :class="[
          tone === 'red' ? 'text-linear-red' : 'text-linear-blue',
          kind === 'static' ? '' : 'cursor-grab active:cursor-grabbing',
          kind === 'handle' ? 'hover:text-linear-text hover:border-linear-border-hover transition-colors duration-150' : '',
          handleClass,
      ]"
  >
    <slot />
  </span>
</template>
