<script setup lang="ts">
withDefaults(defineProps<{
    variant?: 'primary' | 'danger'
    type?: 'button' | 'submit'
    disabled?: boolean
}>(), {
    variant: 'primary',
    type: 'button',
    disabled: false,
})
</script>

<template>
  <button
      :type="type"
      :disabled="disabled"
      class="w-full cursor-pointer text-[11px] font-bold uppercase tracking-widest font-cyber-display px-3 py-2.5 transition duration-150 disabled:opacity-60 disabled:pointer-events-none"
      :class="variant === 'danger'
          ? 'flex items-center justify-center gap-2 border border-linear-red text-linear-red hover:bg-black/25'
          : 'bg-linear-green text-linear-base hover:brightness-90'"
  >
    <slot />
  </button>
</template>
