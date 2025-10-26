<script setup>
import { Link } from "@inertiajs/vue3";

// With Ziggy (included in Breeze), we can check active route:
const is = (name) => route().current(name);
</script>

<template>
  <aside class="w-64 shrink-0 border-r bg-white">
    <div class="p-4 border-b">
      <h2 class="text-lg font-semibold">Inventory</h2>
      <p class="text-xs text-gray-500">Single-Store Billing</p>
    </div>

    <nav class="p-2 space-y-1 text-sm">
      <Link
        :href="route('dashboard')"
        class="block px-3 py-2 rounded hover:bg-gray-100"
        :class="is('dashboard') ? 'bg-gray-100 font-medium' : ''"
      >
        🏠 Dashboard
      </Link>

      <div class="mt-3 px-3 text-xs uppercase tracking-wide text-gray-500">Manage</div>

      <Link
        :href="route('products.index')"
        class="block px-3 py-2 rounded hover:bg-gray-100"
        :class="is('products.index') ? 'bg-gray-100 font-medium' : ''"
      >
        📦 Products
      </Link>

      <Link
        :href="route('customers.index')"
        class="block px-3 py-2 rounded hover:bg-gray-100"
        :class="is('customers.index') ? 'bg-gray-100 font-medium' : ''"
      >
        👤 Customers
      </Link>

      <div class="mt-3 px-3 text-xs uppercase tracking-wide text-gray-500">Invoices</div>

      <Link
        :href="route('invoices.index')"
        class="block px-3 py-2 rounded hover:bg-gray-100"
        :class="is('invoices.index') ? 'bg-gray-100 font-medium' : ''"
      >
        🧾 All Invoices
      </Link>

      <Link
        :href="route('invoices.create')"
        class="block px-3 py-2 rounded hover:bg-gray-100"
        :class="is('invoices.create') ? 'bg-gray-100 font-medium' : ''"
      >
        ➕ Create Invoice
      </Link>
    </nav>
  </aside>
</template>
