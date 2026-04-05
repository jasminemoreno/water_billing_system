<template>
  <div class="my-bills-page">
    <h2>My Bills</h2>

    <CustomerTable
      :columns="columns"
      :rows="rows"
      :hasPay="false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/customerApi.js'
import CustomerTable from '@/components/customer/table.vue'
import dayjs from 'dayjs'

const bills = ref([])

const fetchBills = async () => {
  try {
    const response = await api.get('/customer/mybills')
    bills.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('Failed to fetch bills:', error)
    bills.value = []

    if (error.response?.status === 401) {
      alert('Please login to view your bills.')
    }
  }
}

onMounted(fetchBills)

// ✅ Columns defined in parent (like admin)
const columns = [
  { label: 'Bill ID', field: 'id' },
  { label: 'Meter No', field: 'meter_no' },
  { label: 'Consumption', field: 'consumption' },
  { label: 'Total', field: 'total' },
  { label: 'Billing Date', field: 'billing_date' },
  { label: 'Due Date', field: 'due_date' },
  { label: 'Status', field: 'status' }
]

// ✅ Rows formatted in parent (like admin)
const rows = computed(() =>
  bills.value.map(b => ({
    id: b.id,
    meter_no: b.meter_no,
    consumption: b.consumption + ' m³',
    total: '₱' + Number(b.total ?? 0).toFixed(2),
    billing_date: dayjs(b.billing_date).format('MMM DD, YYYY'),
    due_date: dayjs(b.due_date).format('MMM DD, YYYY'),
    status: b.status === 'Rejected' ? 'Unpaid' : b.status
  }))
)
</script>

<style scoped>
.my-bills-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}

h2 {
  margin-bottom: 20px;
  color: #007bff;
  text-align: center;
  font-weight: 600;
}
</style>