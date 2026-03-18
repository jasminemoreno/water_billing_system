<template>
    <div class="dashboard-boxes">
      <StatCard
        title="Total Customers"
        :value="totalCustomers"
        route="/admin/user"
      />
  
      <StatCard
        title="Total Bills Generated"
        :value="totalBills"
        route="/admin/bill"
        isMoney
      />
  
      <StatCard
        title="Total Payments"
        :value="totalPayments"
        route="/admin/payment"
        isMoney
      />
    </div>
    <h1></h1>
  </template>
  
  <script setup>
  import StatCard from './StatCard.vue'
  
  defineProps({
    totalCustomers: Number,
    totalBills: Number,
    totalPayments: Number
  })
  </script>
  <style scoped>
  .dashboard-boxes {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }
  
  .dashboard-boxes > * {
    flex: 1;
  }
  </style>