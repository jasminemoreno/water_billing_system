<template>
  <div class="chart-card">
    <h3>Paid vs Unpaid</h3>
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import Chart from 'chart.js/auto'

// Props
const props = defineProps({
  paidCount: {
    type: [Number, String],
    default: 0
  },
  unpaidCount: {
    type: [Number, String],
    default: 0
  }
})

const chartRef = ref(null)
let chartInstance = null

// Function to create/update chart
const renderChart = () => {
  const paid = Number(props.paidCount) || 0
  const unpaid = Number(props.unpaidCount) || 0

  const data = {
    labels: ['Paid','Unpaid'],
    datasets: [{
      data: [paid, unpaid],
      backgroundColor: ['#4caf50','#f44336']
    }]
  }

  const options = { responsive: true }

  if (chartInstance) {
    chartInstance.data = data
    chartInstance.update()
  } else {
    chartInstance = new Chart(chartRef.value, { type: 'pie', data, options })
  }
}

// Render chart on mount
onMounted(() => {
  renderChart()
})

// Watch for prop changes and update chart
watch([() => props.paidCount, () => props.unpaidCount], () => {
  renderChart()
})
</script>

<style scoped>
.chart-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  text-align: center;
}
</style>