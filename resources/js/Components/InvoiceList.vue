<script setup>
import { ref, onMounted } from "vue";
import api from "@/lib/api";

const rows = ref([]);
const meta = ref(null);
const customers = ref([]);
const filters = ref({
  customer_id: "",
  date_from: "",
  date_to: "",
  search: "",
  per_page: 10,
});
const loading = ref(false);

async function load(page = 1) {
  loading.value = true;
  const { data } = await api.get("/api/invoices", { params: { ...filters.value, page } });
  rows.value = data.data;
  meta.value = data.meta;
  loading.value = false;
}
async function loadCustomers() {
  const { data } = await api.get("/api/customers", { params: { per_page: 100 } });
  customers.value = data.data;
}
onMounted(() => {
  load();
  loadCustomers();
});
</script>

<template>
  <div class="space-y-3">
    <div class="text-lg font-semibold">Invoices</div>

    <div class="grid md:grid-cols-4 gap-2">
      <select v-model="filters.customer_id" class="border rounded px-2 py-1">
        <option value="">All Customers</option>
        <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <input type="date" v-model="filters.date_from" class="border rounded px-2 py-1" />
      <input type="date" v-model="filters.date_to" class="border rounded px-2 py-1" />
      <input
        v-model="filters.search"
        placeholder="Search by customer"
        class="border rounded px-2 py-1"
      />
    </div>
    <div><button class="px-3 py-1 border rounded" @click="load(1)">Apply</button></div>

    <div class="overflow-x-auto border rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">#</th>
            <th class="p-2 text-left">Customer</th>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td class="p-3" colspan="4">Loading...</td>
          </tr>
          <tr v-for="r in rows" :key="r.id" class="border-t">
            <td class="p-2">{{ r.id }}</td>
            <td class="p-2">{{ r.customer?.name }}</td>
            <td class="p-2">{{ new Date(r.created_at).toLocaleString() }}</td>
            <td class="p-2 text-right">{{ Number(r.total).toFixed(2) }}</td>
          </tr>
          <tr v-if="!loading && rows.length === 0">
            <td class="p-3" colspan="4">No invoices found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="meta && meta.last_page > 1" class="flex gap-2">
      <button
        class="px-3 py-1 border rounded"
        :disabled="meta.current_page === 1"
        @click="load(meta.current_page - 1)"
      >
        Prev
      </button>
      <span class="text-sm">Page {{ meta.current_page }} / {{ meta.last_page }}</span>
      <button
        class="px-3 py-1 border rounded"
        :disabled="meta.current_page === meta.last_page"
        @click="load(meta.current_page + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>
