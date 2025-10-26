<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const showingNavigationDropdown = ref(false);
</script>

<template>
  <!-- Fullscreen dark gradient (login page style) -->
  <div
    class="min-h-screen w-full bg-gradient-to-b from-slate-950 via-slate-900 to-indigo-950 text-slate-100"
  >
    <!-- Top Nav -->
    <nav class="sticky top-0 z-40 backdrop-blur bg-black/30 border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
          <!-- Left: Brand -->
          <div class="flex items-center gap-3">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg border border-white/10 hover:bg-white/10 mr-1"
              aria-label="Toggle menu"
            >
              <svg
                v-if="!showingNavigationDropdown"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>

            <Link :href="route('dashboard')" class="flex items-center gap-2">
              <div
                class="h-8 w-8 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center"
              >
                <span class="text-indigo-300 font-bold text-sm">SI</span>
              </div>
              <span class="font-semibold hidden sm:block">Single Inventory</span>
            </Link>
          </div>

          <!-- Right: User dropdown -->
          <div class="hidden md:flex items-center">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  type="button"
                  class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/10"
                >
                  <span class="text-sm">{{ $page.props.auth.user.name }}</span>
                  <svg
                    class="h-4 w-4 opacity-80"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </template>
              <template #content>
                <div class="px-3 py-2 text-xs text-slate-500">
                  {{ $page.props.auth.user.email }}
                </div>
                <DropdownLink :href="route('logout')" method="post" as="button"
                  >Log Out</DropdownLink
                >
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <!-- Mobile dropdown (right side options) -->
      <div
        class="md:hidden"
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
      >
        <div class="pt-2 pb-3 space-y-1 border-t border-white/10">
          <Link
            :href="route('dashboard')"
            class="block px-4 py-2 text-sm hover:bg-white/10"
            :class="route().current('dashboard') ? 'bg-white/10 font-medium' : ''"
          >
            Dashboard
          </Link>
        </div>
        <div class="pt-3 pb-3 border-t border-white/10">
          <div class="px-4">
            <div class="font-medium text-base text-slate-100">
              {{ $page.props.auth.user.name }}
            </div>
            <div class="font-medium text-sm text-slate-400">
              {{ $page.props.auth.user.email }}
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="block w-full text-left px-4 py-2 text-sm hover:bg-white/10"
            >
              Log Out
            </Link>
          </div>
        </div>
      </div>
    </nav>



    <!-- Main content -->
    <main>
      <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="">
          <slot />
        </div>
      </div>
    </main>
  </div>
</template>
