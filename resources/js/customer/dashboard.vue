<template>
  <div class="home-page">
    
    <!-- Dashboard Cards -->
    <DashboardCards 
      :currentBill="currentBill" 
      :lastPayment="lastPayment" 
    />

    <!-- Grid row: Chart + Notifications -->
    <div class="grid-row">

      <!-- Water Usage Chart -->
      <WaterUsageChart
        :chartData="chartData"
        :chartLabels="chartLabels"
        :chartColors="chartColors"
      />

      <!-- Notifications -->
      <Notifications 
        :currentBill="currentBill" 
        :lastPayment="lastPayment" 
      />

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// ✅ use customer API
import customerApi from '@/customerApi'

// Components
import DashboardCards from '@/components/customer/dashboard/DashboardCards.vue'
import Notifications from '@/components/customer/dashboard/Notifications.vue'
import WaterUsageChart from '@/components/customer/dashboard/WaterUsageChart.vue'

// State
const currentBill = ref(null)
const lastPayment = ref(null)

const chartData = ref([])
const chartLabels = ref([])
const chartColors = ref([])

// Fetch dashboard data
onMounted(async () => {
  try {

    const res = await customerApi.get('/customer/dashboard')

    currentBill.value = res.data.currentBill
    lastPayment.value = res.data.lastPayment

    chartData.value = res.data.chartData || []
    chartLabels.value = res.data.chartLabels || []
    chartColors.value = res.data.chartColors || []

  } catch (error) {
    console.error('Error fetching dashboard:', error)

    // optional: redirect if token expired
    if (error.response && error.response.status === 401) {
      sessionStorage.removeItem("customerToken")
      window.location.href = "/customer/login"
    }
  }
})
</script>

<style scoped>
.home-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}

/* Grid row for chart and notifications */
.grid-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-top: 20px;
}

@media screen and (max-width: 1200px) {
  .grid-row {
    grid-template-columns: 1fr;
  }
}
</style>