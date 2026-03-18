<template>
  <div class="revenue-chart-card">
    <h3>Monthly Revenue</h3>
    <div class="chart-container">
      <canvas ref="chartRef"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  monthlyRevenue: {
    type: Array,
    default: () => []
  }
})

const chartRef = ref(null)
let chartInstance = null

const renderChart = () => {
  if (!chartRef.value) return

  if (chartInstance) {
    chartInstance.data.datasets[0].data = props.monthlyRevenue
    chartInstance.update()
    return
  }

  chartInstance = new Chart(chartRef.value, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: 'Revenue (₱)',
        data: props.monthlyRevenue,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        fill: true,
        tension: 0.3,
        pointRadius: 5,
        pointBackgroundColor: 'rgba(54, 162, 235, 1)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true, position: 'top' }
      },
      scales: {
        y: { beginAtZero: true },
        x: { ticks: { color: '#555', font: { size: 14 } } }
      }
    }
  })
}

watch(() => props.monthlyRevenue, renderChart, { deep: true, immediate: true })

onMounted(() => {
  renderChart()
})
</script>

<style scoped>
.revenue-chart-card {
  width: 100%;
  background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
  border-radius: 18px;
  padding: 20px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.revenue-chart-card h3 {
  text-align: center;
  font-size: 18px;
  color: #1e3a8a;
  margin-bottom: 12px;
  font-weight: 600;
}

/* Only container sets height; canvas fills container */
.chart-container {
  height: 300px; /* fixed height so dashboard layout is stable */
}

canvas {
  width: 100%;
  height: 100%;
}
</style>