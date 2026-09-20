<template>
  <!-- Global Core Shell: Implements the permanent top-centered ambient glowing gradient -->
  <div class="min-h-screen bg-linear-base bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-800/15 via-linear-base to-linear-base text-zinc-100 flex overflow-hidden">

    <!-- 1. Permanent Navigation Menu Panel Layout Component Slot -->
    <LeftMenu />

    <!-- 2. Primary Dynamic View Content Window Dashboard Structure -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

      <!-- Permanent Navbar Container Component -->
      <TopBar />

      <!-- Main Shell Canvas Space: Dynamically swaps pages based on active browser URL context -->
      <main class="flex-1 overflow-x-hidden">
        <RouterView />
      </main>

    </div>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import LeftMenu from './components/LeftMenu.vue'
import TopBar from '@/components/TopBar.vue'
</script>
