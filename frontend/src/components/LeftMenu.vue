<template>
  <aside class="w-64 border-r border-linear-border bg-linear-surface/80 flex flex-col justify-between select-none h-screen sticky top-0">
    <div class="space-y-6 p-4">

      <div class="flex items-center gap-2 px-2 py-1.5 font-cyber-display text-xs font-bold tracking-wider text-linear-text uppercase">
        <span class="text-linear-green font-bold">//</span>
        <span>M. ZDUNSKI // DEV</span>
      </div>

      <div v-for="group in menuGroups" :key="group.sectionTitle" class="space-y-1.5">

        <div class="text-[11px] font-bold tracking-widest text-linear-red font-cyber-display uppercase px-3 flex items-center justify-between cursor-default">
          <span>{{ group.sectionTitle }}</span>
          <span class="text-[8px] text-linear-muted">▼</span>
        </div>

        <nav class="space-y-0.5">
          <RouterLink
              v-for="item in group.items"
              :key="item.routeName"
              :to="{ name: item.routeName }"
              class="flex items-center space-x-3 px-3 py-1.5 rounded-md text-xs font-bold uppercase tracking-wider font-cyber-display border border-transparent transition-colors duration-150 group focus:outline-none focus:ring-0 select-none"
              active-class="transition-none text-linear-text bg-linear-field border-linear-border"
              inactive-class="text-linear-secondary hover:text-linear-text hover:bg-linear-field/60"
          >

            <FontAwesomeIcon
                :icon="['fas', item.icon]"
                class="w-4 h-4 text-linear-muted transition-colors duration-150 group-hover:text-linear-blue group-[.router-link-active]:text-linear-blue"
            />

            <span>{{ item.title }}</span>
          </RouterLink>
        </nav>
      </div>
    </div>

    <div class="space-y-0">
      <div class="text-[9px] text-linear-muted font-cyber-display px-3 tracking-widest uppercase">
        SYS // L13.V6.T4
      </div>

      <button
          type="button"
          class="group relative flex w-full cursor-pointer items-center justify-center gap-3 overflow-hidden px-3 py-2.5 text-[11px] font-bold uppercase tracking-widest font-cyber-display text-linear-base disabled:opacity-60 disabled:pointer-events-none"
          :class="isAuthenticated ? 'bg-linear-green' : 'bg-linear-red'"
          :disabled="pending"
          @click="onAuthButton"
      >
        <span class="pointer-events-none absolute inset-0 bg-black/0 transition-colors duration-150 group-hover:bg-black/25" aria-hidden="true"></span>
        <FontAwesomeIcon
            :icon="['fas', 'power-off']"
            class="relative text-black"
            style="height: 1.5em; width: 1.875em;"
        />
        <span class="relative">{{ isAuthenticated ? 'LOG OFF' : 'LOG IN' }}</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { isAuthenticated, setToken } from '@/auth'
import { api, ApiError } from '@/api/client'
import { toastApiError } from '@/toast'

const router = useRouter()
const pending = ref(false)

async function onAuthButton() {
    if (pending.value) {
        return
    }

    if (!isAuthenticated.value) {
        await router.push({ name: 'login' })
        return
    }

    pending.value = true

    try {
        await api('/logout', { method: 'POST', toast: false })
    } catch (error) {
        if (!(error instanceof ApiError && error.status === 401)) {
            toastApiError(error)
        }
    } finally {
        setToken('')
        pending.value = false
    }
}

const menuGroups = ref([
  {
    sectionTitle: 'Workspace',
    items: [
      {
        title: 'Cases & Projects',
        routeName: 'cases',
        icon: 'folder-open',
      },
      {
        title: 'My Tech Stack',
        routeName: 'tech-stack',
        icon: 'code',
      },
    ],
  },
  {
    sectionTitle: 'Personal Info',
    items: [
      {
        title: 'Employment',
        routeName: 'employment',
        icon: 'briefcase',
      },
      {
        title: 'Education',
        routeName: 'education',
        icon: 'graduation-cap',
      },
    ],
  },
  {
    sectionTitle: 'Systems',
    items: [
      {
        title: 'API Playground',
        routeName: 'api-docs',
        icon: 'terminal',
      },
      {
        title: 'Contact Inbox',
        routeName: 'contact',
        icon: 'envelope',
      },
    ],
  },
  {
    sectionTitle: 'Planner (Encrypted)',
    items: [
      {
        title: 'Completed Tasks',
        routeName: 'completed-tasks',
        icon: 'circle-check',
      },
      {
        title: 'Job Applications',
        routeName: 'job-applications',
        icon: 'file-lines',
      },
    ],
  },
])
</script>
