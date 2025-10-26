<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

import HomeOverview from "@/Components/HomeOverview.vue";
import ProductList from "@/Components/ProductList.vue";
import CustomerList from "@/Components/CustomerList.vue";
import InvoiceList from "@/Components/InvoiceList.vue";
import InvoiceCreate from "@/Components/InvoiceCreate.vue";
import Toasts from "@/Components/Toasts.vue";

const page = usePage();
const section = page.props.section || "home";
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex">
          <!-- Sidebar -->
          <aside class="w-64 border-r p-2 space-y-1 text-sm">
            <Link
              :href="route('dashboard')"
              class="block px-3 py-2 rounded hover:bg-gray-100"
              :class="section === 'home' ? 'bg-gray-100 font-medium' : ''"
              >🏠 Overview</Link
            >
            <Link
              :href="route('products.index')"
              class="block px-3 py-2 rounded hover:bg-gray-100"
              :class="section === 'products' ? 'bg-gray-100 font-medium' : ''"
              >📦 Products</Link
            >
            <Link
              :href="route('customers.index')"
              class="block px-3 py-2 rounded hover:bg-gray-100"
              :class="section === 'customers' ? 'bg-gray-100 font-medium' : ''"
              >👤 Customers</Link
            >
            <Link
              :href="route('invoices.index')"
              class="block px-3 py-2 rounded hover:bg-gray-100"
              :class="section === 'invoices' ? 'bg-gray-100 font-medium' : ''"
              >🧾 Invoices</Link
            >
            <Link
              :href="route('invoices.create')"
              class="block px-3 py-2 rounded hover:bg-gray-100"
              :class="section === 'create' ? 'bg-gray-100 font-medium' : ''"
              >➕ Invoice Create</Link
            >
          </aside>

          <!-- Main -->
          <main class="flex-1 p-6 space-y-6">
            <div v-if="section === 'home'"><HomeOverview /></div>
            <div v-else-if="section === 'products'"><ProductList /></div>
            <div v-else-if="section === 'customers'"><CustomerList /></div>
            <div v-else-if="section === 'invoices'"><InvoiceList /></div>
            <div v-else-if="section === 'create'"><InvoiceCreate /></div>
          </main>
        </div>
      </div>
    </div>

    <Toasts />
  </AuthenticatedLayout>
</template>
