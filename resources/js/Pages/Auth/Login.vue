<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const BRAND = "Single Inventory";

const form = useForm({
  email: "",
  password: "",
  // remember: false, // removed
});

const showPassword = ref(false);

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
  });
};

function fillDemo() {
  form.email = "admin@example.com";
  form.password = "password";
}
</script>

<template>
  <Head title="Log in" />

  <!-- Dark, low-contrast gradient background -->
  <div
    class="min-h-screen w-full bg-gradient-to-b from-slate-950 via-slate-900 to-indigo-950 flex items-center justify-center p-4"
  >
    <!-- soft ambient glows -->
    <div
      class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"
    ></div>
    <div
      class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-fuchsia-500/10 blur-3xl"
    ></div>

    <!-- Auth card -->
    <div
      class="relative w-full max-w-md rounded-2xl bg-white/5 backdrop-blur-xl shadow-2xl border border-white/10 overflow-hidden"
    >
      <!-- Header / Branding -->
      <div class="px-8 pt-8 pb-6 border-b border-white/10 bg-white/5">
        <div class="flex items-center gap-3">
          <div
            class="h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center"
          >
            <span class="text-indigo-300 font-bold">SI</span>
          </div>
          <div>
            <h1 class="text-xl font-semibold text-slate-100">{{ BRAND }}</h1>
            <p class="text-xs text-slate-300/70">Sign in to continue</p>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="px-8 py-7">
        <div class="space-y-5">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-slate-200"
              >Email</label
            >
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              autocomplete="username"
              class="mt-2 block w-full rounded-lg border border-white/10 bg-white/10 text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400/60 px-4 py-2.5"
              placeholder="you@example.com"
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-300">
              {{ form.errors.email }}
            </p>
          </div>

          <!-- Password (with eye toggle) -->
          <div>
            <label for="password" class="block text-sm font-medium text-slate-200"
              >Password</label
            >
            <div class="relative mt-2">
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="form.password"
                required
                autocomplete="current-password"
                class="block w-full rounded-lg border border-white/10 bg-white/10 text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400/60 px-4 py-2.5 pr-11"
                placeholder="••••••••"
              />
              <button
                type="button"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-300 hover:text-slate-100"
                @click="showPassword = !showPassword"
                aria-label="Toggle password visibility"
              >
                <!-- eye / eye-off icons (inline SVG) -->
                <svg
                  v-if="!showPassword"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.058.186.058.386 0 .572C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.98 8.223C5.73 6.218 8.65 5 12 5c4.64 0 8.577 2.51 9.964 6.678.058.186.058.386 0 .572-.432 1.35-1.143 2.54-2.07 3.52M15 12a3 3 0 00-4.243-2.828M9 9l6 6M3 3l18 18"
                  />
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-1 text-xs text-rose-300">
              {{ form.errors.password }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between">
            <!-- Demo data button -->
            <button
              type="button"
              @click="fillDemo"
              class="px-3 py-2 rounded-lg border border-white/10 text-slate-200 hover:bg-white/10"
              title="Fill demo credentials"
            >
              Use Demo Data
            </button>

            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center justify-center rounded-lg bg-indigo-500 hover:bg-indigo-400 transition-colors text-white font-medium px-4 py-2.5 shadow disabled:opacity-60"
            >
              <svg
                v-if="form.processing"
                class="animate-spin h-5 w-5 mr-2"
                viewBox="0 0 24 24"
                fill="none"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
              </svg>
              Log in
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
          <p class="text-xs text-slate-400/80">
            © {{ new Date().getFullYear() }} {{ BRAND }} — Nayem Hossain
          </p>
        </div>
      </form>
    </div>
  </div>
</template>
