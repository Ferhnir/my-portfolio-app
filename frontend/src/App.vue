<template>
  <Toaster />
  <RouterView v-if="isAuthScreen" />

  <div v-else class="min-h-screen text-linear-text flex overflow-hidden">

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
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import LeftMenu from './components/LeftMenu.vue'
import Toaster from './components/Toaster.vue'
import TopBar from './components/TopBar.vue'

const route = useRoute()
const isAuthScreen = computed(() => route.name === 'login' || route.name === 'forgot-password')
</script>
