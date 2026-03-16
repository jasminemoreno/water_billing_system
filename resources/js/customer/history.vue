<template>
    <div class="payment-history-page">
      <h2>Payment History</h2>
  
      <PaymentHistoryTable
        :payments="payments"
      />
  
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from "vue"
  import customerApi from "@/customerApi.js"
  
  import PaymentHistoryTable from "@/components/customer/payment/PaymentHistoryTable.vue"
  
  const payments = ref([])
  
  const fetchPayments = async () => {
    try {
      const res = await customerApi.get("/customer/history")
      // Ensure only 'Verified' payments are shown
      payments.value = (res.data.payments || []).filter(p => p.status === "Verified")
    } catch (error) {
      console.error("Error fetching payment history:", error)
    }
  }
  
  onMounted(fetchPayments)
  </script>
  
  <style scoped>
  .payment-history-page{
    margin-left:250px;
    padding:80px 30px 30px 30px;
    min-height:100vh;
    background:#f4f6f8;
    font-family:'Roboto', sans-serif;
  }
  h2 {
    margin-bottom: 20px;
    color: #007bff;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    text-align: center;
    border-color: black ;
   
    font-weight: 600;
  }
  </style>