<template>
  <div class="dashboard-page">

    <DashboardSummary
      :totalCustomers="stats.totalCustomers"
      :totalBills="stats.totalBills"
      :totalPayments="stats.totalPayments"
    />

    <div class="charts-section">
      <div class="chart-wrapper">
        <RevenueChart :monthlyRevenue="stats.monthlyRevenue" />
      </div>

      <div class="chart-wrapper">
        <PaymentStatusChart
          :paidCount="stats.paidCount"
          :unpaidCount="stats.unpaidCount"
        />
      </div>
    </div>

  </div>
</template>

<script setup>
import { reactive } from "vue"
import api from "@/api"
import { usePolling } from "@/polling"

import DashboardSummary from "@/components/admin/DashboardSummary.vue"
import RevenueChart from "@/components/admin/RevenueChart.vue"
import PaymentStatusChart from "@/components/admin/PaymentStatusChart.vue"

const stats = reactive({
  totalCustomers: 0,
  totalBills: 0,
  totalPayments: 0,
  monthlyRevenue: [],
  paidCount: 0,
  unpaidCount: 0,
})

const fetchDashboard = async () => {
  try {
    const { data } = await api.get("/admin/dashboard")

    stats.totalCustomers = Number(data.totalCustomers ?? 0)
    stats.totalBills = Number(data.totalBills ?? 0)
    stats.totalPayments = Number(data.totalPayments ?? 0)
    stats.monthlyRevenue = data.monthlyRevenue ?? []
    stats.paidCount = Number(data.paidCount ?? 0)
    stats.unpaidCount = Number(data.unpaidCount ?? 0)

  } catch (err) {
    console.error("Dashboard error:", err)
  }
}

// ✅ REAL TIME UPDATE
usePolling(fetchDashboard, 5000)
</script>

<style scoped>
.dashboard-page {
  margin-left: 250px;
  padding: 80px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}

.charts-section {
  display: flex;
  flex-wrap: wrap;
  gap: 25px;
}

.chart-wrapper {
  flex: 1 1 45%;
  min-width: 300px;
  background: white;
  padding: 20px;
  border-radius: 12px;
}
</style>