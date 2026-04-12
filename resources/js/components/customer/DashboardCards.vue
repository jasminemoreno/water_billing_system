1️⃣ DashboardCards.vue
<template>
  <div class="cards-row">
    <!-- Current Bill -->
    <div class="card currentbill">
      <h3>Current Bill</h3>
      <p v-if="currentBill" class="amount">₱{{ formatCurrency(currentBill.total) }}</p>
      <p v-if="currentBill">Consumption: {{ currentBill.consumption }} m³</p>
      <p v-if="currentBill">Due Date: {{ formatDate(currentBill.due_date) }}</p>
      <p v-if="currentBill">Disconnection Date: {{ formatDate(currentBill.disconnection_date) }}</p>
      <p v-else>No current bill</p>
    </div>

    <!-- Last Payment -->
    <div class="card last-payment">
      <h3>Last Payment</h3>
      <p v-if="lastPayment" class="amount">₱{{ formatCurrency(lastPayment.amount) }}</p>
      <p v-if="lastPayment">Date: {{ formatDate(lastPayment.created_at) }}</p>
      <p v-if="lastPayment">Status: {{ capitalize(lastPayment.status) }}</p>
      <p v-else>No payment history</p>
    </div>

    <!-- Water Usage -->
    <div class="card water-usage">
      <h3>Water Usage</h3>
      <p v-if="currentBill">{{ currentBill.consumption }} m³</p>
      <p v-else>No data</p>
    </div>
  </div>
</template>

<script setup>
import { toRefs } from 'vue'

const props = defineProps({
  currentBill: Object,
  lastPayment: Object
})

const { currentBill, lastPayment } = toRefs(props)

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric'
  })
}

const formatCurrency = (value) => {
  if (!value) return '0.00'
  return parseFloat(value).toFixed(2)
}

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : ''
</script>

<style scoped>
.cards-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 30px;
}

.card {
  background: #fff;
  flex: 1;
  min-width: 250px;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.08);
  border-top: 4px solid #3498db;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.currentbill { border-top-color: #081566; background-color: #9BBCD4; }
.last-payment { border-top-color: #075B93; background-color: #B4EEF2; }
.water-usage { border-top-color: #B2965F; background-color: #FFEDCD; }

.card h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 10px;
}

.card p.amount {
  font-weight: 700;
  font-size: 1.2rem;
  color: #2c3e50;
  margin-bottom: 6px;
}

.card p {
  color: #34495e;
  margin-bottom: 5px;
}
</style>