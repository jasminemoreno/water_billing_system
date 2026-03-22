<template>
  <div class="pay-bills-page">
    <h2>Pay Bills</h2>

    <PayBillsTable
      :bills="bills"
      @pay="openModal"
    />

    <div class="payment-details-card">
      <h3>Payment Details</h3>
      <p><strong>GCash Number:</strong> 09XXXXXXXXX</p>
      <p><strong>Account Name:</strong> Telma</p>
      <p>Upload screenshot during payment.</p>
    </div>

    <PaymentModal
      :show="showModal"
      :bill="selectedBill"
      @close="closeModal"
      @submitted="onPaymentSuccess"
    />

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

const fetchBills = async () => {
  try {
    const res = await api.get("/customer/paybills")
    bills.value = (res.data || []).filter(
      b => b.status === 'Unpaid' || b.status === 'Pending'
    )
  } catch (err) {
    console.error("Failed to load bills", err)
  }
}

const openModal = (bill) => {
  selectedBill.value = bill
  showModal.value = true
}

const closeModal = () => showModal.value = false

const onPaymentSuccess = () => {
  closeModal()
  showSuccess.value = true
  fetchBills()
}

onMounted(() => fetchBills())
</script>

<style scoped>
.pay-bills-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background: #f4f6f8;
  font-family: 'Roboto', sans-serif;
}

h2 {
  margin-bottom: 20px;
  color: #007bff;
  text-align: center;
  font-weight: 600;
}

.payment-details-card {
  margin-top: 20px;
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
}

.payment-details-card h3 {
  margin-bottom: 10px;
  color: #0f4c75;
}
</style>