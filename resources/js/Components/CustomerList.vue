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
const form = ref({ name: "", email: "", phone: "", address: "" });
const formErrors = ref({});

async function load(page = 1) {
  loading.value = true;
  formErrors.value = {};
  try {
    const { data } = await api.get("/api/customers", {
      params: { search: search.value, page, per_page: perPage.value },
    });
    rows.value = data.data;
    meta.value = data.meta;
  } catch (e) {
    error(e?.response?.data?.message || "Failed to load customers");
  } finally {
    loading.value = false;
  }
}
function openCreate() {
  editing.value = null;
  form.value = { name: "", email: "", phone: "", address: "" };
  formErrors.value = {};
  showForm.value = true;
}
function openEdit(r) {
  editing.value = r;
  form.value = {
    name: r.name,
    email: r.email ?? "",
    phone: r.phone ?? "",
    address: r.address ?? "",
  };
  formErrors.value = {};
  showForm.value = true;
}

async function save() {
  formErrors.value = {};
  try {
    if (editing.value) await api.put(`/api/customers/${editing.value.id}`, form.value);
    else await api.post("/api/customers", form.value);
    success("Customer saved");
    showForm.value = false;
    await load(meta.value?.current_page || 1);
  } catch (e) {
    if (e.response?.status === 422) formErrors.value = e.response.data.errors;
    else error(e.response?.data?.message || "Save failed");
  }
}
async function removeRow(r) {
  if (!confirm(`Delete "${r.name}"?`)) return;
  await api.delete(`/api/customers/${r.id}`);
  success("Customer deleted");
  await load(meta.value?.current_page || 1);
}

onMounted(() => load());
</script>

<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div class="text-lg font-semibold">
        Customers <span class="text-sm text-gray-500">({{ meta?.total || 0 }})</span>
      </div>
      <div class="flex gap-2">
        <input
          v-model="search"
          @keyup.enter="load(1)"
          placeholder="Search name/email"
          class="border rounded px-3 py-1"
        />
        <select v-model="perPage" @change="load(1)" class="border rounded px-2 py-1">
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
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left">Phone</th>
            <th class="p-2 text-left">Address</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td class="p-3" colspan="5">Loading...</td>
          </tr>
          <tr v-for="r in rows" :key="r.id" class="border-t">
            <td class="p-2">{{ r.name }}</td>
            <td class="p-2">{{ r.email }}</td>
            <td class="p-2">{{ r.phone }}</td>
            <td class="p-2">{{ r.address }}</td>
            <td class="p-2 flex gap-2">
              <button class="px-2 py-1 border rounded" @click="openEdit(r)">Edit</button>
              <button class="px-2 py-1 border rounded text-red-600" @click="removeRow(r)">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!loading && rows.length === 0">
            <td class="p-3" colspan="5">No customers found.</td>
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
        <h3 class="text-lg font-semibold mb-3">
          {{ editing ? "Edit Customer" : "Add Customer" }}
        </h3>
        <div class="grid gap-3">
          <div>
            <label class="block text-sm mb-1">Name</label>
            <input v-model="form.name" class="border rounded w-full px-3 py-2" />
            <p class="text-xs text-rose-600" v-if="formErrors.name">
              {{ formErrors.name[0] }}
            </p>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm mb-1">Email</label>
              <input v-model="form.email" class="border rounded w-full px-3 py-2" />
              <p class="text-xs text-rose-600" v-if="formErrors.email">
                {{ formErrors.email[0] }}
              </p>
            </div>
            <div>
              <label class="block text-sm mb-1">Phone</label>
              <input v-model="form.phone" class="border rounded w-full px-3 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-sm mb-1">Address</label>
            <input v-model="form.address" class="border rounded w-full px-3 py-2" />
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
          <button class="px-3 py-1 border rounded" @click="showForm = false">
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
