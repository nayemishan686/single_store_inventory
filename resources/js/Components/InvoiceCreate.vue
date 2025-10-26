<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/lib/api";
import { useToast } from "@/composables/useToast";
const { success, error } = useToast();

const customers = ref([]);
const products = ref([]);
const customer_id = ref("");
const searchCustomer = ref("");
const searchProduct = ref("");
const items = ref([]); // { product_id, name, price, quantity, subtotal }
const loading = ref(false);
const formErrors = ref({});

const total = computed(() =>
  items.value.reduce((s, i) => s + (Number(i.subtotal) || 0), 0).toFixed(2)
);

async function loadCustomers(q = "") {
  const { data } = await api.get("/api/customers", {
    params: { search: q, per_page: 10 },
  });
  customers.value = data.data;
}
async function loadProducts(q = "") {
  const { data } = await api.get("/api/products", {
    params: { search: q, per_page: 10 },
  });
  products.value = data.data;
}
function addProduct(p) {
  const ex = items.value.find((i) => i.product_id === p.id);
  if (ex) {
    ex.quantity += 1;
    ex.subtotal = (ex.quantity * Number(ex.price)).toFixed(2);
  } else {
    items.value.push({
      product_id: p.id,
      name: p.name,
      price: p.price,
      quantity: 1,
      subtotal: Number(p.price).toFixed(2),
    });
  }
}
function updateQty(i) {
  i.quantity = Math.max(1, Number(i.quantity || 1));
  i.subtotal = (i.quantity * Number(i.price)).toFixed(2);
}
function removeItem(idx) {
  items.value.splice(idx, 1);
}

async function submit() {
  try {
    loading.value = true;
    formErrors.value = {};
    const payload = {
      customer_id: Number(customer_id.value),
      items: items.value.map((i) => ({ product_id: i.product_id, quantity: i.quantity })),
    };
    await api.post("/api/invoices", payload);
    success("Invoice created");
    // reset
    customer_id.value = "";
    items.value = [];
    searchCustomer.value = "";
    searchProduct.value = "";
  } catch (e) {
    if (e.response?.status === 422) formErrors.value = e.response.data.errors;
    else error(e.response?.data?.message || "Failed to create invoice");
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadCustomers();
  loadProducts();
});
</script>

<template>
  <div class="space-y-4">
    <div class="text-lg text-white font-semibold">Create Invoice</div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="space-y-2">
        <label class="text-sm text-white">Select Customer</label>
        <div class="flex gap-2">
          <input
            v-model="searchCustomer"
            @keyup.enter="loadCustomers(searchCustomer)"
            placeholder="Search..."
            class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2 w-full"
          />
          <button class="px-3 py-2 border rounded" @click="loadCustomers(searchCustomer)">
            Find
          </button>
        </div>
        <div class="max-h-48 overflow-auto border rounded">
          <div
            v-for="c in customers"
            :key="c.id"
            class="px-3 py-2 border-b  flex items-center gap-2"
          >
            <input type="radio" :value="c.id" v-model="customer_id" />
            <div>
              <div class="font-medium text-white">{{ c.name }}</div>
              <div class="text-xs text-gray-300">{{ c.email }}</div>
            </div>
          </div>
          <div v-if="customers.length === 0" class="p-3 text-sm">No customers</div>
        </div>
        <p class="text-xs text-rose-600" v-if="formErrors.customer_id">
          {{ formErrors.customer_id[0] }}
        </p>
      </div>

      <div class="space-y-2">
        <label class="text-sm">Add Products</label>
        <div class="flex gap-2">
          <input
            v-model="searchProduct"
            @keyup.enter="loadProducts(searchProduct)"
            placeholder="Search..."
            class="border border-white/10 bg-white/10 text-slate-100 rounded px-2 py-2 w-full"
          />
          <button class="px-3 py-2 border rounded" @click="loadProducts(searchProduct)">
            Find
          </button>
        </div>
        <div class="max-h-48 overflow-auto border rounded">
          <div
            v-for="p in products"
            :key="p.id"
            class="px-3 py-2 border-b flex items-center justify-between"
          >
            <div>
              <div class="font-medium">
                {{ p.name }} <span class="text-xs text-gray-300">({{ p.sku }})</span>
              </div>
              <div class="text-xs text-gray-300">
                BDT {{ Number(p.price).toFixed(2) }} • Qty: {{ p.quantity }}
              </div>
            </div>
            <button class="px-2 py-1 border rounded" @click="addProduct(p)">Add</button>
          </div>
          <div v-if="products.length === 0" class="p-3 text-sm">No products</div>
        </div>
      </div>
    </div>

    <div class="mt-4 border rounded">
      <table class="min-w-full text-sm text-white">
        <thead class="bg-gray-50 text-black">
          <tr>
            <th class="p-2 text-left">Product</th>
            <th class="p-2 text-right">Price</th>
            <th class="p-2 text-right">Qty</th>
            <th class="p-2 text-right">Subtotal</th>
            <th class="p-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(i, idx) in items" :key="i.product_id" class="border-t">
            <td class="p-2">{{ i.name }}</td>
            <td class="p-2 text-right ">{{ Number(i.price).toFixed(2) }}</td>
            <td class="p-2 text-right">
              <input
                type="number"
                min="1"
                v-model.number="i.quantity"
                @change="updateQty(i)"
                class="border rounded px-2 py-1 w-24 text-right text-black"
              />
            </td>
            <td class="p-2 text-right">{{ i.subtotal }}</td>
            <td class="p-2 text-right">
              <button class="px-2 py-1 border rounded" @click="removeItem(idx)">
                Remove
              </button>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td class="p-3" colspan="5">No items selected.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end items-center gap-4">
      <div class="text-lg font-semibold">Total: BDT {{ total }}</div>
      <button
        class="px-4 py-2 bg-indigo-600 text-white rounded"
        :disabled="loading || !customer_id || items.length === 0"
        @click="submit"
      >
        {{ loading ? "Saving..." : "Create Invoice" }}
      </button>
    </div>
  </div>
</template>
