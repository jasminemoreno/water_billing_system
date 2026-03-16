<template>
    <div class="my-bills-page">
      <h2>My Bills</h2>
      <BillsTable :bills="bills" />
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import api from '@/customerApi.js'
  import BillsTable from '@/components/customer/mybill/BillsTable.vue'
  
  const bills = ref([])
  
  const fetchBills = async () => {
    try {
      // Use token from api (set in customerApi.js)
      const response = await api.get('/customer/bills')
  
      // Filter: ensure we have an array
      bills.value = Array.isArray(response.data) ? response.data : []
  
    } catch (error) {
      console.error('Failed to fetch bills:', error)
      bills.value = []
  
      // Optional: redirect if unauthorized
      if (error.response?.status === 401) {
        alert('Please login to view your bills.')
      }
    }
  }
  
  onMounted(() => {
    fetchBills()
  })
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
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    text-align: center;
    border-color: black ;
   
    font-weight: 600;
  }
  
  /* Responsive adjustments */
  @media screen and (max-width: 1200px) {
    .my-bills-page {
      margin-left: 200px;
    }
  }
  
  @media screen and (max-width: 992px) {
    .my-bills-page {
      margin-left: 0;
      padding: 15px;
    }
  }
  </style>