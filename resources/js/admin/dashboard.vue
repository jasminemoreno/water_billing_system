<template>
  <div class="dashboard-page">

    <!-- Summary Cards -->
    <DashboardSummary
      :totalCustomers="Number(stats.totalCustomers)"
      :totalBills="Number(stats.totalBills)"
      :totalPayments="Number(stats.totalPayments)"
    />

    <!-- Charts Section -->
    <div class="charts-section">
      <div class="chart-wrapper">
        <RevenueChart :monthlyRevenue="stats.monthlyRevenue" />
      </div>

      <div class="chart-wrapper">
        <PaymentStatusChart
          :paidCount="Number(stats.paidCount)"
          :unpaidCount="Number(stats.unpaidCount)"
        />
      </div>
    </div>

  </div>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import api from "@/api.js"; // Axios instance with auth headers

import DashboardSummary from "@/components/admin/DashboardSummary.vue";
import RevenueChart from "@/components/admin/RevenueChart.vue";
import PaymentStatusChart from "@/components/admin/PaymentStatusChart.vue";

// Reactive stats object
const stats = reactive({
  totalCustomers: 0,
  totalBills: 0,
  totalPayments: 0,
  monthlyRevenue: [],    // array of monthly revenue objects
  paidCount: 0,
  unpaidCount: 0,
});

// Fetch dashboard stats from backend
onMounted(async () => {
  try {
    const response = await api.get("/admin/dashboard");
    const data = response.data;

    // Ensure numeric values for charts and summary cards
    stats.totalCustomers = Number(data.totalCustomers ?? 0);
    stats.totalBills = Number(data.totalBills ?? 0);
    stats.totalPayments = Number(data.totalPayments ?? 0);
    stats.monthlyRevenue = data.monthlyRevenue ?? [];
    stats.paidCount = Number(data.paidCount ?? 0);
    stats.unpaidCount = Number(data.unpaidCount ?? 0);

  } catch (error) {
    console.error("Failed to fetch dashboard stats:", error);
  }
});
</script>

<style>
.dashboard-page {
  margin-left: 250px; /* space for sidebar */
  padding: 80px 30px 30px 30px; /* top for navbar */
  min-height: 100vh;
  background-color: #f4f6f8;
  box-sizing: border-box;
}

/* CHARTS */
.charts-section {
  display: flex;
  flex-wrap: wrap;
  gap: 25px;
}

/* Chart wrapper */
.chart-wrapper {
  flex: 1 1 45%;
  min-width: 300px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Chart titles */
.chart-wrapper h3 {
  text-align: center;
  font-size: 18px;
  color: #007bff;
  margin-bottom: 15px;
}
</style>