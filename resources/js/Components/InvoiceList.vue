<script setup>
import { ref, onMounted, computed } from "vue";
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

// Details modal state
const showView = ref(false);
const viewLoading = ref(false);
const invoice = ref(null); 

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

async function viewDetails(id) {
  showView.value = true;
  viewLoading.value = true;
  try {
    const { data } = await api.get(`/api/invoices/${id}`);
    invoice.value = data; 
  } catch (e) {
    
    invoice.value = null;
  } finally {
    viewLoading.value = false;
  }
}

function downloadPdf(id) {
  // new tab download — DomPDF endpoint
  window.open(`/api/invoices/${id}/pdf`, "_blank");
}

const invoiceTotal = computed(() => {
  if (!invoice.value?.items) return 0;
  return invoice.value.items
    .reduce((s, it) => s + Number(it.subtotal || it.price * it.quantity || 0), 0)
    .toFixed(2);
});

onMounted(() => {
  load();
  loadCustomers();
});
</script>

<template>
  <div class="space-y-4 text-slate-100">
    <div class="text-xl font-semibold">Invoices</div>

    <!-- filters -->
    <div class="grid md:grid-cols-4 gap-2">
      <select
        v-model="filters.customer_id"
        class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2"
      >
        <option value="">All Customers</option>
        <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <input
        type="date"
        v-model="filters.date_from"
        class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2"
      />
      <input
        type="date"
        v-model="filters.date_to"
        class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2"
      />
      <input
        v-model="filters.search"
        placeholder="Search by customer"
        class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2"
      />
    </div>
    <div>
      <button
        class="px-3 py-2 rounded-lg border border-white/10 hover:bg-white/10"
        @click="load(1)"
      >
        Apply
      </button>
    </div>

    <!-- table -->
    <div
      class="overflow-x-auto rounded-xl border border-white/10 bg-white/5 backdrop-blur"
    >
      <table class="min-w-full text-sm bg-white text-black">
        <thead class="bg-white/5 border-b border-white/10">
          <tr>
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Customer</th>
            <th class="p-3 text-left">Date</th>
            <th class="p-3 text-right">Total</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td class="p-4" colspan="5">Loading...</td>
          </tr>

          <tr
            v-for="r in rows"
            :key="r.id"
            class="border-b border-white/10 hover:bg-white/5"
          >
            <td class="p-3">{{ r.id }}</td>
            <td class="p-3 ">{{ r.customer?.name }}</td>
            <td class="p-3">
              {{ new Date(r.created_at).toLocaleString() }}
            </td>
            <td class="p-3 text-right">BDT {{ Number(r.total).toFixed(2) }}</td>
            <td class="p-3">
              <div class="flex justify-end gap-2">
                <!-- Eye: view details -->
                <button
                  class="px-2 py-1 rounded border border-white/10 hover:bg-white/10"
                  title="View details"
                  @click="viewDetails(r.id)"
                >
                  <!-- eye icon -->
                  <svg
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
                </button>

                <!-- Download: pdf -->
                <button
                  class="px-2 py-1 rounded border border-white/10 hover:bg-white/10"
                  title="Download PDF"
                  @click="downloadPdf(r.id)"
                >
                  <!-- download icon -->
                  <svg
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
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12 12 16.5m0 0L16.5 12M12 16.5V3"
                    />
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="!loading && rows.length === 0">
            <td class="p-4 text-slate-300" colspan="5">No invoices found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- pagination -->
    <div v-if="meta && meta.last_page > 1" class="flex items-center gap-2 mt-4">
      <button
        class="px-3 py-1 rounded border border-white/10 hover:bg-white/10 disabled:opacity-50"
        :disabled="meta.current_page === 1"
        @click="load(meta.current_page - 1)"
      >
        Prev
      </button>
      <span class="text-sm text-slate-300"
        >Page {{ meta.current_page }} / {{ meta.last_page }}</span
      >
      <button
        class="px-3 py-1 rounded border border-white/10 hover:bg-white/10 disabled:opacity-50"
        :disabled="meta.current_page === meta.last_page"
        @click="load(meta.current_page + 1)"
      >
        Next
      </button>
    </div>

    <!-- Details Modal -->
    <div
      v-if="showView"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
    >
      <div
        class="bg-slate-900/90 text-slate-100 rounded-2xl border border-white/10 w-full max-w-3xl p-6"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Invoice Details</h3>
          <div class="flex gap-2">
            <button
              v-if="invoice?.id"
              class="px-3 py-1.5 rounded border border-white/10 hover:bg-white/10"
              @click="downloadPdf(invoice.id)"
              title="Download PDF"
            >
              <!-- download icon -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 inline"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12 12 16.5m0 0L16.5 12M12 16.5V3"
                />
              </svg>
              <span class="ml-1">PDF</span>
            </button>
            <button
              class="px-3 py-1.5 rounded border border-white/10 hover:bg-white/10"
              @click="showView = false"
            >
              Close
            </button>
          </div>
        </div>

        <div v-if="viewLoading">Loading…</div>

        <div v-else-if="invoice">
          <!-- Header info -->
          <div class="grid sm:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-xs text-slate-400">Invoice #</div>
              <div class="font-medium">#{{ invoice.id }}</div>
            </div>
            <div>
              <div class="text-xs text-slate-400">Customer</div>
              <div class="font-medium">{{ invoice.customer?.name }}</div>
              <div class="text-xs text-slate-400">{{ invoice.customer?.email }}</div>
            </div>
            <div>
              <div class="text-xs text-slate-400">Date</div>
              <div class="font-medium">
                {{ new Date(invoice.created_at).toLocaleString() }}
              </div>
            </div>
          </div>

          <!-- Items -->
          <div class="rounded-xl border border-white/10 bg-white/5 overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-white/5 border-b border-white/10 text-slate-300">
                <tr>
                  <th class="p-3 text-left">Product</th>
                  <th class="p-3 text-left">SKU</th>
                  <th class="p-3 text-right">Price</th>
                  <th class="p-3 text-right">Qty</th>
                  <th class="p-3 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="it in invoice.items || []"
                  :key="it.id"
                  class="border-b border-white/10"
                >
                  <td class="p-3">{{ it.product?.name }}</td>
                  <td class="p-3 text-slate-300">{{ it.product?.sku }}</td>
                  <td class="p-3 text-right">BDT {{ Number(it.price).toFixed(2) }}</td>
                  <td class="p-3 text-right">{{ it.quantity }}</td>
                  <td class="p-3 text-right">
                    BDT {{ Number(it.subtotal ?? it.price * it.quantity).toFixed(2) }}
                  </td>
                </tr>
                <tr v-if="(invoice.items || []).length === 0">
                  <td colspan="5" class="p-4 text-slate-300">No items.</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="p-3 text-right font-semibold">Total</td>
                  <td class="p-3 text-right font-semibold">
                    BDT {{ (invoice.total ?? invoiceTotal).toString() }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div v-else class="text-slate-300">No data.</div>
      </div>
    </div>
  </div>
</template>
