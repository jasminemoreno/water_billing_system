<template>
  <div class="report-page">

    <!-- Reports Dashboard -->
    <div class="reports-dashboard">

      <ReportCard
        :icon="billingIcon"
        title="Billing Reports"
        color="#4f46e5"
        @click="openPopup('billing')"
      />

      <ReportCard
        :icon="paymentIcon"
        title="Payment Reports"
        color="#16a34a"
        @click="openPopup('payment')"
      />

      <ReportCard
        :icon="unpaidIcon"
        title="Unpaid Bills"
        color="#dc2626"
        @click="openPopup('unpaid')"
      />

      <ReportCard
        :icon="revenueIcon"
        title="Monthly Revenue"
        color="#f59e0b"
        @click="openPopup('revenue')"
      />

    </div>

    <!-- Report Popup -->
    <ReportPopup
      v-if="popup.show"
      :show="popup.show"
      :title="popup.title"
      :headers="popup.headers"
      :keys="popup.keys"
      :rows="popup.rows"
      @close="popup.show = false"
    />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api.js'

import ReportCard from '@/components/admin/report/ReportCard.vue'
import ReportPopup from '@/components/admin/report/ReportPopup.vue'

import billingIcon from '@/assets/icons/billing_reports.png'
import paymentIcon from '@/assets/icons/payment_reports.png'
import unpaidIcon from '@/assets/icons/unpaid_bills.png'
import revenueIcon from '@/assets/icons/revenue.png'

// --- STATE ---
const bills = ref([])
const payments = ref([])
const unpaidBills = ref([])
const monthlyRevenue = ref({})

const popup = ref({
  show: false,
  title: '',
  headers: [],
  keys: [],
  rows: []
})

// --- LOAD REPORT DATA ---
onMounted(async () => {
  try {
    const res = await api.get('/reports')

    bills.value = res.data.bills || []
    payments.value = res.data.payments || []          // now includes both Cash & Online
    unpaidBills.value = res.data.unpaidBills || []
    monthlyRevenue.value = res.data.monthlyRevenue || {}

  } catch (err) {
    console.error('Failed to load reports:', err)
  }
})

// --- OPEN POPUP ---
const openPopup = (type) => {

  if (type === 'billing') {
    popup.value = {
      show: true,
      title: 'All Bills',
      headers: ['ID','Customer','Meter No','Amount','Status'],
      keys: ['id','customer_name','meter_no','total','status'],
      rows: bills.value.map(b => ({
        id: b.id,
        customer_name: b.customer?.customer_name || '-',
        meter_no: b.meter_no,
        total: Number(b.total).toFixed(2),
        status: Array.isArray(b.payments) && b.payments.some(p => ['Approved','Verified'].includes(p.status))
          ? 'Paid'
          : 'Unpaid'
      }))
    }
  }

  if (type === 'payment') {
    popup.value = {
      show: true,
      title: 'Payment Reports (Cash & Online)',
      headers: ['ID','Customer','Bill ID','Amount','Payment Method','Date'], // added Payment Method
      keys: ['id','customer_name','bill_id','amount','payment_method','created_at'],
      rows: payments.value.map(p => ({
        id: p.id,
        customer_name: p.customer?.customer_name || '-',
        bill_id: p.bill_id,
        amount: Number(p.amount).toFixed(2),
        payment_method: p.payment_method || '-',  // Cash or Online
        created_at: new Date(p.created_at).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})
      }))
    }
  }

  if (type === 'unpaid') {
    popup.value = {
      show: true,
      title: `Unpaid Bills (${unpaidBills.value.length})`,
      headers: ['ID','Customer','Meter No','Amount'],
      keys: ['id','customer_name','meter_no','total'],
      rows: unpaidBills.value.map(b => ({
        id: b.id,
        customer_name: b.customer?.customer_name || '-',
        meter_no: b.meter_no,
        total: Number(b.total).toFixed(2)
      }))
    }
  }

  if (type === 'revenue') {
    popup.value = {
      show: true,
      title: 'Monthly Revenue (Cash & Online)',
      headers: ['Month','Revenue (₱)'],
      keys: ['month','total'],
      rows: Object.entries(monthlyRevenue.value).map(([month,total]) => ({
        month: new Date(0, month-1).toLocaleString('default',{ month:'long' }),
        total: Number(total).toFixed(2)
      }))
    }
  }

}
</script>

<style scoped>
.report-page{
  margin-left:300px;
  padding:80px 30px 30px 30px;
  min-height:100vh;
  background:#f4f6f8;
}

.reports-dashboard{
  display:flex;
  gap:30px;
  flex-wrap:wrap;
  justify-content:flex-start;
}

/* Optional: Add scroll if many cards */
.reports-dashboard::-webkit-scrollbar{
  height:6px;
}
</style>