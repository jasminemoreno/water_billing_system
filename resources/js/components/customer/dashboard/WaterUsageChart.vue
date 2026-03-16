Ah! Right — the Water Usage Chart in my current setup is directly rendered inside Dashboard.vue. It works, but for cleaner and reusable code, it’s better to move it into its own component — for example, WaterUsageChart.vue. That way, the dashboard stays clean and the chart logic is encapsulated.

Here’s how we can do it:

1️⃣ WaterUsageChart.vue
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
  labels: Array,
  data: Array,
  colors: Array
})

const chartCanvas = ref(null)
let chartInstance = null

const renderChart = () => {
  if (chartInstance) chartInstance.destroy() // destroy previous chart

  chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
    type: 'bar',
    data: {
      labels: props.labels,
      datasets: [{
        label: 'Water Consumption (m³)',
        data: props.data,
        backgroundColor: props.colors,
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, position: 'top' },
        tooltip: { enabled: true }
      },
      scales: {
        y: { beginAtZero: true, title: { display: true, text: 'Consumption (m³)' } },
        x: { title: { display: true, text: 'Month' } }
      }
    }
  })
}

onMounted(renderChart)
watch([() => props.labels, () => props.data, () => props.colors], renderChart)
</script>

<style scoped>
.chart-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.chart-card h3 {
  margin-bottom: 15px;
  font-size: 1.5rem;
  color: #2c3e50;
}
</style>