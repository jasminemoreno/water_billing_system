<template>
  <div class="report-page">
    <div class="reports-dashboard">
      <ReportCard :icon="billingIcon" title="Billing Reports" color="#4f46e5" @click="openPopup('billing')" />
      <ReportCard :icon="paymentIcon" title="Payment Reports" color="#16a34a" @click="openPopup('payment')" />
      <ReportCard :icon="paymentHistoryIcon" title="Payment History" color="#0ea5e9" @click="openPopup('history')" />
      <ReportCard :icon="billHistoryIcon" title="Bill History" color="#f97316" @click="openPopup('bill-history')" />
      <ReportCard :icon="unpaidIcon" title="Unpaid Bills" color="#dc2626" @click="openPopup('unpaid')" />
      <ReportCard :icon="revenueIcon" title="Monthly Revenue" color="#f59e0b" @click="openPopup('revenue')" />

      <!-- NEW CARD: Rejected Payments -->
      <ReportCard :icon="rejectPayment" title="Rejected Payments" color="#ef4444" @click="openPopup('rejected')" />
    </div>

    <ReportPopup
      v-if="popup.show"
      :show="popup.show"
      :title="popup.title"
      :headers="popup.headers"
      :keys="popup.keys"
      :rows="popup.rows"
      :customContent="popup.customContent"
      :type="popup.type"
      @close="popup.show = false"
      @year-month-selected="handleDateFilter"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api.js'

import ReportCard from '@/components/admin/ReportCard.vue'
import ReportPopup from '@/components/admin/ReportPopup.vue'

import billingIcon from '@/assets/icons/billing_reports.png'
import paymentIcon from '@/assets/icons/payment_reports.png'
import paymentHistoryIcon from '@/assets/icons/history.png'
import unpaidIcon from '@/assets/icons/unpaid_bills.png'
import revenueIcon from '@/assets/icons/revenue.png'
import billHistoryIcon from '@/assets/icons/bill_history.png'
import rejectPayment from '@/assets/icons/reject.png'

const bills = ref([])
const payments = ref([])
const unpaidBills = ref([])
const monthlyRevenue = ref({})

const popup = ref({
  show: false,
  type: null,
  title: '',
  headers: [],
  keys: [],
  rows: [],
  customContent: null
})

// Initial fetch for reports
onMounted(async () => {
  const res = await api.get('/reports')
  bills.value = res.data.bills
  payments.value = res.data.payments
  unpaidBills.value = res.data.unpaidBills
  monthlyRevenue.value = res.data.monthlyRevenue
})

// Open popup handler
const openPopup = (type) => {
  popup.value.type = type
  popup.value.show = true
  popup.value.rows = []

  // -------------------------
  // BILL HISTORY
  // -------------------------
  if (type === 'bill-history') {
    popup.value.title = 'Bill History'
    popup.value.headers = ['ID','Customer','Meter','Consumption','Total','Billing Date','Due Date','Disconnection Date','Status']
    popup.value.keys = ['id','customer','meter','consumption','total','billing_date','due_date','disconnection_date','status']
    popup.value.customContent = {
      type: 'year-month-select',
      selectedYear: new Date().getFullYear(),
      selectedMonth: new Date().getMonth() + 1
    }
    fetchBillHistory(popup.value.customContent.selectedYear, popup.value.customContent.selectedMonth)
  }

  // -------------------------
  // PAYMENT HISTORY (APPROVED ONLY)
  // -------------------------
  if (type === 'history') {
    popup.value.title = 'Payment History'
    popup.value.headers = ['ID','Customer','Bill','Amount','Method','Date','Status']
    popup.value.keys = ['id','customer','bill','amount','method','date','status']
    popup.value.customContent = {
      type: 'year-month-select',
      selectedYear: new Date().getFullYear(),
      selectedMonth: new Date().getMonth() + 1
    }
    fetchHistory(popup.value.customContent.selectedYear, popup.value.customContent.selectedMonth)
  }

  // -------------------------
  // REJECTED PAYMENTS
  // -------------------------
  if (type === 'rejected') {
  popup.value.title = 'Rejected Payment History'
  popup.value.headers = ['ID','Customer','Bill','Amount','Method','Date','Screenshot']
  popup.value.keys = ['id','customer','bill','amount','method','date','screenshot']

  // ✅ ADD THIS
  popup.value.customContent = {
    type: 'year-month-select',
    selectedYear: new Date().getFullYear(),
    selectedMonth: new Date().getMonth() + 1
  }

  fetchRejectedPayments(
    popup.value.customContent.selectedYear,
    popup.value.customContent.selectedMonth
  )
}

  // -------------------------
  // BILLING REPORTS
  // -------------------------
  if (type === 'billing') {
    popup.value.title = 'Billing Reports'
    popup.value.headers = ['ID','Customer','Meter','Consumption','Total','Billing Date','Status']
    popup.value.keys = ['id','customer','meter','consumption','total','billing_date','status']
    popup.value.rows = bills.value.map(b => ({
      id: b.id,
      customer: b.customer?.customer_name,
      meter: b.meter_no,
      consumption: b.consumption,
      total: Number(b.total).toFixed(2),
      billing_date: new Date(b.billing_date).toLocaleDateString(),
      status: b.payments.some(p => ['Approved','Verified'].includes(p.status)) ? 'Paid' : 'Unpaid'
    }))
  }

  // -------------------------
  // PAYMENT REPORTS (ALL STATUS)
  // -------------------------
  if (type === 'payment') {
    popup.value.title = 'Payment Reports'
    popup.value.headers = ['ID','Customer','Bill','Amount','Method','Date','Status']
    popup.value.keys = ['id','customer','bill','amount','method','date','status']
    popup.value.rows = payments.value.map(p => ({
      id: p.id,
      customer: p.customer?.customer_name,
      bill: p.bill_id,
      amount: Number(p.amount).toFixed(2),
      method: p.payment_method,
      date: new Date(p.created_at).toLocaleDateString(),
      status: p.status
    }))
  }

  // -------------------------
  // UNPAID BILLS
  // -------------------------
  if (type === 'unpaid') {
    popup.value.title = 'Unpaid Bills'
    popup.value.headers = ['ID','Customer','Meter','Consumption','Total','Billing Date']
    popup.value.keys = ['id','customer','meter','consumption','total','billing_date']
    popup.value.rows = unpaidBills.value.map(b => ({
      id: b.id,
      customer: b.customer?.customer_name,
      meter: b.meter_no,
      consumption: b.consumption,
      total: Number(b.total).toFixed(2),
      billing_date: new Date(b.billing_date).toLocaleDateString()
    }))
  }

  // -------------------------
  // MONTHLY REVENUE
  // -------------------------
  if (type === 'revenue') {
    popup.value.title = 'Monthly Revenue'
    popup.value.headers = ['Month','Revenue']
    popup.value.keys = ['month','revenue']
    popup.value.rows = Object.entries(monthlyRevenue.value).map(([m, total]) => ({
      month: m,
      revenue: Number(total).toFixed(2)
    }))
  }
}

// -------------------------
// FETCH FUNCTIONS
// -------------------------
const fetchHistory = async (year, month) => {
  const res = await api.get(`/reports/payment-history/${year}/${month}`)
  popup.value.rows = res.data.payments
    .filter(p => ['Approved','Verified'].includes(p.status))
    .map(p => ({
      id: p.id,
      customer: p.customer?.customer_name,
      bill: p.bill_id,
      amount: Number(p.amount).toFixed(2),
      method: p.payment_method,
      date: new Date(p.created_at).toLocaleDateString(),
      status: 'Paid'
    }))
}

const fetchBillHistory = async (year, month) => {
  const res = await api.get(`/reports/bill-history/${year}/${month}`)
  popup.value.rows = res.data.bills.map(b => ({
    id: b.id,
    customer: b.customer?.customer_name,
    meter: b.meter_no,
    consumption: b.consumption,
    total: Number(b.total).toFixed(2),
    billing_date: new Date(b.billing_date).toLocaleDateString(),
    due_date: new Date(b.due_date).toLocaleDateString(),
    disconnection_date: b.disconnection_date ? new Date(b.disconnection_date).toLocaleDateString() : '',
    status: b.payments.some(p => ['Approved','Verified'].includes(p.status)) ? 'Paid' : 'Unpaid'
  }))
}

const fetchRejectedPayments = async (year, month) => {
  const res = await api.get(`/reports/rejected-payments/${year}/${month}`)

  popup.value.rows = res.data.payments.map(p => ({
    id: p.id,
    customer: p.customer?.customer_name,
    bill: p.bill_id,
    amount: Number(p.amount).toFixed(2),
    method: p.payment_method,
    date: new Date(p.created_at).toLocaleDateString(),
    screenshot: p.gcash_screenshot
  }))
}
// -------------------------
// DATE FILTER HANDLER
// -------------------------
const handleDateFilter = (year, month) => {
  if (popup.value.type === 'history') fetchHistory(year, month)
  if (popup.value.type === 'bill-history') fetchBillHistory(year, month)
  if (popup.value.type === 'rejected') fetchRejectedPayments(year, month) // ✅ ADD THIS
}
</script>

<style scoped>
.report-page {
  margin-left: 300px;
  padding: 80px 30px;
  background: #f4f6f8;
  min-height: 100vh;
}
.reports-dashboard {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}
</style>