<template>
  <div class="chart-card">
    <h3>Water Usage Chart</h3>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  chartLabels: Array,
  chartData: Array,
  chartColors: Array
})

const chartCanvas = ref(null)
let chartInstance = null

const renderChart = () => {
  if (!chartCanvas.value) return

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
    type: 'bar',
    data: {
      labels: props.chartLabels,
      datasets: [{
        label: 'Water Consumption (m³)',
        data: props.chartData,
        backgroundColor: props.chartColors,
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        borderRadius: 8,
        hoverBackgroundColor: 'rgba(75,192,192,0.7)',
        maxBarThickness: 50
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { 
          display: true, 
          position: 'top',
          labels: { font: { size: 14 }, color: '#333' }
        },
        tooltip: { 
          enabled: true,
          backgroundColor: '#1f2937',
          titleColor: '#fff',
          bodyColor: '#fff',
          padding: 10
        }
      },
      scales: {
        y: { 
          beginAtZero: true,
          title: { display: true, text: 'Consumption (m³)', color: '#555', font: { size: 14 } },
          ticks: { stepSize: 10, color: '#555', font: { size: 12 } },
          grid: { color: '#e0e0e0', borderDash: [4, 4] }
        },
        x: { 
          title: { display: true, text: 'Month', color: '#555', font: { size: 14 } },
          ticks: { color: '#555', font: { size: 12 } },
          grid: { color: '#f0f0f0' }
        }
      }
    }
  })
}

onMounted(renderChart)

watch(
  [() => props.chartLabels, () => props.chartData, () => props.chartColors],
  renderChart,
  { deep: true }
)
</script>

<style scoped>
.chart-card {
  background: linear-gradient(135deg, #ffffff, #f0f4f8);
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  height: 400px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chart-card h3 {
  margin-bottom: 20px;
  font-size: 1.7rem;
  color: #0f172a;
  font-weight: 600;
  text-align: center;
}

canvas {
  flex: 1;
}
</style>