<script setup lang="ts">
withDefaults(defineProps<{
    id: string
    placeholder?: string
    name?: string
    required?: boolean
}>(), {
    placeholder: '',
    name: 'name',
    required: false,
})

const model = defineModel<string>({ required: true })
</script>

<template>
  <input
      :id="id"
      v-model="model"
      class="w-full bg-linear-field border border-linear-border text-linear-text text-xs uppercase tracking-wide font-cyber-display px-3 py-2 rounded-md outline-none focus:border-linear-border-hover"
      type="text"
      :name="name"
      :placeholder="placeholder"
      :required="required"
  >
</template>
