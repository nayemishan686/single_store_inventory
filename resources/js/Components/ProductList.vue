<script setup>
import { ref, onMounted } from "vue";
import api from "@/lib/api";
import { useToast } from "@/composables/useToast";

const { success, error } = useToast();

const rows = ref([]);
const meta = ref(null);
const search = ref("");
const perPage = ref(10);
const loading = ref(false);

const showForm = ref(false);
const editing = ref(null);
const form = ref({ name: "", sku: "", price: 0, quantity: 0, description: "" });
const formErrors = ref({});

async function load(page = 1) {
  loading.value = true;
  formErrors.value = {};
  try {
    const { data } = await api.get("/api/products", {
      params: { search: search.value, page, per_page: perPage.value },
    });
    rows.value = data.data;
    meta.value = data.meta;
  } catch (e) {
    error(e?.response?.data?.message || "Failed to load products");
  } finally {
    loading.value = false;
  }
}

function openCreate() {
  editing.value = null;
  form.value = { name: "", sku: "", price: 0, quantity: 0, description: "" };
  formErrors.value = {};
  showForm.value = true;
}
function openEdit(r) {
  editing.value = r;
  form.value = {
    name: r.name,
    sku: r.sku,
    price: r.price,
    quantity: r.quantity,
    description: r.description ?? "",
  };
  formErrors.value = {};
  showForm.value = true;
}

async function save() {
  formErrors.value = {};
  try {
    if (editing.value) await api.put(`/api/products/${editing.value.id}`, form.value);
    else await api.post("/api/products", form.value);
    showForm.value = false;
    success("Product saved");
    await load(meta.value?.current_page || 1);
  } catch (e) {
    if (e.response?.status === 422) formErrors.value = e.response.data.errors;
    else error(e.response?.data?.message || "Save failed");
  }
}
async function removeRow(r) {
  if (!confirm(`Delete "${r.name}"?`)) return;
  await api.delete(`/api/products/${r.id}`);
  success("Product deleted");
  await load(meta.value?.current_page || 1);
}

onMounted(() => load());
</script>

<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div class="text-lg font-semibold">
        Products 
      </div>
      <div class="flex gap-2">
        <input
          v-model="search"
          @keyup.enter="load(1)"
          placeholder="Search name/sku"
          class="border rounded px-3 py-1"
        />
        <select v-model="perPage" @change="load(1)" class="border rounded px-2 py-1 text-black">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button class="px-3 py-1 border rounded" @click="load(1)">Search</button>
        <button class="px-3 py-1 bg-indigo-600 text-white rounded" @click="openCreate">
          Add
        </button>
      </div>
    </div>

    <div class="overflow-x-auto border rounded">
      <table class="min-w-full text-sm bg-white text-black">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">SKU</th>
            <th class="p-2 text-right">Price</th>
            <th class="p-2 text-right">Qty</th>
            <th class="p-2 text-left">Description</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td class="p-3" colspan="6">Loading...</td>
          </tr>
          <tr v-for="r in rows" :key="r.id" class="border-t">
            <td class="p-2">{{ r.name }}</td>
            <td class="p-2">{{ r.sku }}</td>
            <td class="p-2 text-right">{{ Number(r.price).toFixed(2) }}</td>
            <td class="p-2 text-right">
              <span :class="r.quantity < 5 ? 'text-orange-600 font-medium' : ''">{{
                r.quantity
              }}</span>
            </td>
            <td class="p-2 truncate max-w-[320px]">{{ r.description }}</td>
            <td class="p-2 flex gap-2">
              <button class="px-2 py-1 border rounded" @click="openEdit(r)">Edit</button>
              <button class="px-2 py-1 border rounded text-red-600" @click="removeRow(r)">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!loading && rows.length === 0">
            <td class="p-3" colspan="6">No products found.</td>
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

    <!-- modal -->
    <div
      v-if="showForm"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl p-6 w-full max-w-lg">
        <h3 class="text-lg text-black font-semibold mb-3">
          {{ editing ? "Edit Product" : "Add Product" }}
        </h3>
        <div class="grid gap-3">
          <div>
            <label class="block text-sm text-black mb-1">Name</label>
            <input v-model="form.name" class="border rounded w-full px-3 py-2 text-black" />
            <p class="text-xs text-rose-600" v-if="formErrors.name">
              {{ formErrors.name[0] }}
            </p>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-black mb-1">SKU</label>
              <input v-model="form.sku" class="border rounded w-full px-3 py-2 text-black" />
              <p class="text-xs text-rose-600" v-if="formErrors.sku">
                {{ formErrors.sku[0] }}
              </p>
            </div>
            <div>
              <label class="block text-sm text-black mb-1">Price</label>
              <input
                type="number"
                step="0.01"
                v-model.number="form.price"
                class="border rounded w-full px-3 py-2 text-black"
              />
              <p class="text-xs text-rose-600" v-if="formErrors.price">
                {{ formErrors.price[0] }}
              </p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-black mb-1 text-black">Quantity</label>
              <input
                type="number"
                v-model.number="form.quantity"
                class="border rounded w-full px-3 py-2 text-black"
              />
              <p class="text-xs text-rose-600" v-if="formErrors.quantity">
                {{ formErrors.quantity[0] }}
              </p>
            </div>
            <div>
              <label class="block text-sm text-black mb-1">Description</label>
              <input v-model="form.description" class="border rounded w-full px-3 py-2 text-black" />
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
          <button class="px-3 py-1 border rounded bg-black text-white" @click="showForm = false">
            Cancel
          </button>
          <button class="px-3 py-1 bg-indigo-600 text-white rounded" @click="save">
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
