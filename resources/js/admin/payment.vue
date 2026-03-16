<template>
  <div class="payment-page">
    <PaymentTable
      :payments="payments"
      :searchQuery="searchQuery"
      @add="openAdd"
      @edit="openEdit"
      @view="openView"
      @delete="openDelete"
    />
    <PaymentForm
      v-if="showForm"
      :mode="mode"
      :payment="selectedPayment"
      @close="closeForm"
      @saved="handleSaved"
    />
    <SuccessPopup
      v-if="showSuccess"
      :message="successMessage"
      @close="showSuccess = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/api.js";

import PaymentTable from "@/components/admin/payment/PaymentTable.vue";
import PaymentForm from "@/components/admin/payment/PaymentForm.vue";
import SuccessPopup from "@/components/admin/payment/SuccessPopup.vue";

const payments = ref([]);
const searchQuery = ref("");
const showForm = ref(false);
const showSuccess = ref(false);
const successMessage = ref("");
const mode = ref("add");
const selectedPayment = ref(null);

// Fetch payments
const fetchPayments = async () => {
  try {
    const res = await api.get("/payments"); // token automatically attached
    payments.value = res.data;
  } catch (error) {
    console.error("Error fetching payments:", error);
  }
};

onMounted(fetchPayments);

// Popup handlers
const openAdd = () => { mode.value = "add"; selectedPayment.value = {}; showForm.value = true; };
const openEdit = (payment) => { mode.value = "edit"; selectedPayment.value = { ...payment }; showForm.value = true; };
const openView = (payment) => { mode.value = "view"; selectedPayment.value = { ...payment }; showForm.value = true; };
const openDelete = (payment) => { mode.value = "delete"; selectedPayment.value = { ...payment }; showForm.value = true; };
const closeForm = () => { showForm.value = false; };
const handleSaved = (msg) => { successMessage.value = msg; showSuccess.value = true; fetchPayments(); };
</script>

<style scoped>
.payment-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}
</style>