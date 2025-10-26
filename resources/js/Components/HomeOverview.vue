<script setup>
import { onMounted, ref } from "vue";
import api from "@/lib/api";
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

const loading = ref(true);
const totals = ref({ products: 0, customers: 0, invoices: 0 });
const sales = ref({ today: 0, this_month: 0, lifetime: 0 });
const alerts = ref({ low_stock_products: 0 });
const topProducts = ref([]);
const chartCanvas = ref(null);
let chartInstance = null;

async function fetchOverview() {
  loading.value = true;
  try {
    const { data } = await api.get("/api/dashboard/overview");
    totals.value = data.totals;
    sales.value = data.sales;
    alerts.value = data.alerts;
    topProducts.value = data.top_products || [];
    drawChart(data.chart);
  } finally {
    loading.value = false;
  }
}

function drawChart(chartData) {
  if (!chartCanvas.value) return;
  const labels = chartData.map((d) => d.date);
  const totals = chartData.map((d) => d.total);
  if (chartInstance) chartInstance.destroy();
  chartInstance = new Chart(chartCanvas.value, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Sales (Last 7 Days)",
          data: totals,
          tension: 0.3,
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true },
      },
    },
  });
}

onMounted(fetchOverview);
</script>

<template>
  <div class="space-y-8">
    <div class="text-lg font-semibold">Overview</div>

    <!-- Totals -->
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">Total Products</div>
        <div class="text-2xl text-black font-bold mt-1">
          <span v-if="!loading">{{ totals.products }}</span
          ><span v-else>…</span>
        </div>
      </div>
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">Total Customers</div>
        <div class="text-2xl text-black font-bold mt-1">
          <span v-if="!loading">{{ totals.customers }}</span
          ><span v-else>…</span>
        </div>
      </div>
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">Total Invoices</div>
        <div class="text-2xl text-black font-bold mt-1">
          <span v-if="!loading">{{ totals.invoices }}</span
          ><span v-else>…</span>
        </div>
      </div>
    </div>

    <!-- Sales summary -->
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">Today’s Sales</div>
        <div class="text-xl text-black font-semibold mt-1">৳{{ sales.today.toFixed(2) }}</div>
      </div>
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">This Month</div>
        <div class="text-xl text-black font-semibold mt-1">৳{{ sales.this_month.toFixed(2) }}</div>
      </div>
      <div class="p-5 rounded-xl border bg-white">
        <div class="text-sm text-black">Low Stock (&lt;5)</div>
        <div class="text-xl text-black font-semibold mt-1">{{ alerts.low_stock_products }}</div>
      </div>
    </div>

    <!-- 🔹 Top 5 Products -->
    <div class="p-5 rounded-xl border bg-white">
      <div class="text-sm text-black font-semibold mb-2">Top 5 Products (Sold Qty)</div>
      <table class="w-full text-sm">
        <thead class="text-left text-black border-b">
          <tr>
            <th class="py-1">Product</th>
            <th class="py-1">Sold</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in topProducts" :key="p.name" class="border-b last:border-0 text-black">
            <td class="py-1">{{ p.name }}</td>
            <td class="py-1">{{ p.sold_qty }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 🔹 Last 7 Days Sales Chart -->
    <div class="p-5 rounded-xl border bg-white">
      <div class="text-sm text-blackfont-semibold mb-2">Last 7 Days Sales</div>
      <canvas ref="chartCanvas" height="120"></canvas>
    </div>
  </div>
</template>
