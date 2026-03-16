<template>
    <div class="chart-card">
      <h3>Monthly Revenue</h3>
      <canvas ref="chartRef"></canvas>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref } from 'vue'
  import Chart from 'chart.js/auto'
  
  const props = defineProps({
    monthlyRevenue: Array
  })
  
  const chartRef = ref(null)
  
  onMounted(() => {
    new Chart(chartRef.value, {
      type: 'line',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
          label: 'Revenue (₱)',
          data: props.monthlyRevenue,
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
      }
    })
  })
  </script>

<style scoped>
.chart-card {
  flex: 1;
  min-width: 300px;
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.chart-card h3 {
  font-size: 16px;
  color: #007bff;
  margin-bottom: 15px;
  text-align: center;
}

canvas {
  width: 100% !important;
  height: 300px !important;
}
</style>