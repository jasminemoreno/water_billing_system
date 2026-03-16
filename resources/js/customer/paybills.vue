<template>
  <div class="pay-bills-page">
    <h2>Pay Bills</h2>
    <PayBillsTable
      :bills="bills"
      @pay="openModal"
    />

    <!-- Payment Details -->
    <div class="payment-details-card">
      <h3>Payment Details</h3>
      <p><strong>GCash Number:</strong> 09XXXXXXXXX</p>
      <p><strong>Account Name:</strong> Telma</p>
      <p>Upload screenshot during payment.</p>
    </div>

    <!-- Payment Modal -->
    <PaymentModal
      :show="showModal"
      :bill="selectedBill"
      @close="closeModal"
      @submitted="onPaymentSuccess"
    />

    <!-- Success Popup -->
    <SuccessPopup
      :show="showSuccess"
      message="Payment submitted successfully! Awaiting admin approval."
      @close="showSuccess = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import api from "@/customerApi"

import PayBillsTable from "@/components/customer/paybill/PayBillsTable.vue"
import PaymentModal from "@/components/customer/paybill/PaymentPopup.vue"
import SuccessPopup from "@/components/customer/paybill/SuccessPopup.vue"

const bills = ref([])
const showModal = ref(false)
const selectedBill = ref(null)
const showSuccess = ref(false)

// Fetch unpaid bills from backend
const fetchBills = async () => {
  try {
    const res = await api.get("/customer/bills")
    // Only show unpaid or pending bills
    bills.value = (res.data || []).filter(
      (b) => b.status === "Unpaid" || b.status === "Pending"
    )
  } catch (error) {
    console.error("Failed loading bills", error)
  }
}

const openModal = (bill) => {
  selectedBill.value = bill
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const onPaymentSuccess = () => {
  closeModal()
  showSuccess.value = true
  fetchBills()
}

onMounted(() => {
  fetchBills()
})
</script>

<style scoped>
.pay-bills-page {
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
  

.payment-details-card {
  margin-top: 20px;
  background: #f0f4f8;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
}

.payment-details-card h3 {
  margin-bottom: 10px;
  color: #0f4c75;
}
</style>