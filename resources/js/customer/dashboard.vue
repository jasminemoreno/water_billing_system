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
        :chartLabels="chartLabels"
        :chartData="chartData"
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
import { ref } from 'vue'
import { usePolling } from '@/polling'

// API
import customerApi from '@/customerApi'

// Components
import DashboardCards from '@/components/customer/DashboardCards.vue'
import Notifications from '@/components/customer/Notifications.vue'
import WaterUsageChart from '@/components/customer/WaterUsageChart.vue'

// State
const currentBill = ref(null)
const lastPayment = ref(null)
const chartLabels = ref([])
const chartData = ref([])
const chartColors = ref([])

const fetchDashboard = async () => {
  try {
    const res = await customerApi.get('/customer/dashboardData')

    currentBill.value = res.data.currentBill
    lastPayment.value = res.data.lastPayment
    chartLabels.value = res.data.chartLabels
    chartData.value = res.data.chartData
    chartColors.value = res.data.chartColors

  } catch (error) {
    console.error('Dashboard fetch error:', error)
  }
}

usePolling(fetchDashboard, 8000)
    

</script>

<style scoped>
.home-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #CBDDE9;
  overflow-y: auto; /* make scrollable if content grows */
}

.grid-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-top: 20px;

  align-items: start; /* prevent items from stretching */
}

/* make it responsive */
@media screen and (max-width: 1200px) {
  .grid-row {
    grid-template-columns: 1fr;
  }
}
</style>