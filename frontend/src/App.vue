<template>
  <!-- Main layout container with the top-centered ambient radial glow -->
  <div class="min-h-screen bg-linear-base bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-800/15 via-linear-base to-linear-base text-zinc-100 flex">

    <!-- Left Sidebar Panel -->
    <aside class="w-64 border-r border-linear-border bg-linear-surface/40 backdrop-blur-sm p-4 flex flex-col justify-between">
      <div>
        <div class="flex items-center space-x-2 px-2 py-1.5 font-semibold text-sm tracking-tight text-white hover:bg-white/5 rounded-md cursor-pointer transition-colors duration-150">
          <span class="w-4 h-4 bg-linear-border border border-white/20 rounded-md"></span>
          <span>Linear Workspace</span>
        </div>

        <nav class="mt-6 space-y-1 text-zinc-400 text-sm font-medium">
          <div class="px-3 py-1.5 text-white bg-white/5 rounded-md cursor-pointer">Inbox</div>
          <div class="px-3 py-1.5 hover:text-zinc-200 hover:bg-white/5 rounded-md cursor-pointer transition-colors">My Issues</div>
          <div class="px-3 py-1.5 hover:text-zinc-200 hover:bg-white/5 rounded-md cursor-pointer transition-colors">Views</div>
        </nav>
      </div>
    </aside>

    <!-- Right Workspace Dashboard Area -->
    <main class="flex-1 flex flex-col">
      <!-- Top Navbar Bar -->
      <header class="h-12 border-b border-linear-border flex items-center justify-between px-6 bg-linear-surface/20">
        <div class="text-xs font-semibold text-zinc-400 tracking-wider uppercase">DRV-8852 Faster app launch</div>
        <div class="flex items-center space-x-3 text-zinc-400 text-xs">
          <span class="px-2 py-0.5 bg-linear-border rounded text-zinc-300 border border-white/5">1/84</span>
        </div>
      </header>

      <!-- Central Issue Stream Panel Grid -->
      <section class="p-8 max-w-4xl w-full mx-auto space-y-6">
        <h1 class="text-2xl font-semibold text-white tracking-tight">Faster app launch</h1>
        <p class="text-sm text-text-muted leading-relaxed max-w-2xl">
          Render UI before <code class="px-1.5 py-0.5 bg-linear-surface border border-linear-border text-zinc-200 rounded font-mono text-xs">vehicle_state</code> sync when minimum required state is present, instead of blocking on full refresh during iOS startup.
        </p>

        <!-- Dynamic Content Card Section Example -->
        <div class="border border-linear-border bg-linear-surface rounded-xl p-5 shadow-2xl">
          <div class="flex items-center space-x-2 text-xs text-zinc-500 mb-3">
            <div class="w-5 h-5 rounded-full bg-zinc-700"></div>
            <span class="font-medium text-zinc-300">karri</span>
            <span>commented 4 minutes ago</span>
          </div>
          <p class="text-sm text-zinc-200 pl-7">
            Right now we show a spinner forever, which makes it look like the car disappeared...
          </p>
        </div>
      </section>
    </main>

  </div>
</template>

<script setup>
// Component state initialization logic sits here cleanly
</script>
