<template>
    <div class="table-container">
  
      <table class="payment-history-table">
  
        <thead>
          <tr>
            <th>Bill ID</th>
            <th>Billing Month</th>
            <th>Amount</th>
            <th>Date Paid</th>
            <th>Status</th>
          </tr>
        </thead>
  
        <tbody>
  
          <tr v-if="payments.length === 0">
            <td colspan="5">No payments found.</td>
          </tr>
  
          <tr v-for="payment in payments" :key="payment.id">
  
            <td>
              {{ payment.bill?.id ?? '-' }}
            </td>
  
            <td>
              {{ formatMonth(payment.bill?.billing_date) }}
            </td>
  
            <td>
              ₱{{ formatAmount(payment.amount) }}
            </td>
  
            <td>
              {{ formatDate(payment.created_at) }}
            </td>
  
            <td>
              <span
                class="status"
                :class="payment.status"
              >
                {{ payment.status }}
              </span>
            </td>
  
          </tr>
  
        </tbody>
  
      </table>
  
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    payments: Array
  })
  
  const formatAmount = (amount) => {
    return Number(amount).toLocaleString(undefined,{minimumFractionDigits:2})
  }
  
  const formatDate = (date) => {
    if(!date) return "-"
    return new Date(date).toLocaleDateString()
  }
  
  const formatMonth = (date) => {
    if(!date) return "-"
    const d = new Date(date)
    return d.toLocaleString('default',{month:'short',year:'numeric'})
  }
  </script>
  
  <style scoped>
  
  .table-container{
    overflow-x:auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
  }
  
  .payment-history-table{
    width:100%;
    border-collapse:collapse;
  }
  
  .payment-history-table thead th{
    background:#0f4c75;
    color:white;
    font-weight:600;
    text-transform:uppercase;
    padding:12px;
    text-align:center;
  }
  
  .payment-history-table tbody td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #e9ecef;
  }
  
  .payment-history-table tbody tr:hover{
    background:#f1f3f5;
  }
  
  .status{
    font-weight:600;
    padding:4px 10px;
    border-radius:6px;
  }
  
  .status.Pending{
    background:#fff3cd;
    color:#856404;
  }
  
  .status.Verified{
    background:#d4edda;
    color:#155724;
  }
  
  .status.Rejected{
    background:#f8d7da;
    color:#721c24;
  }
  
  </style>