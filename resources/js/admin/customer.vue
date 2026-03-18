<template>
  <div class="customer-page">

    <!-- REUSABLE TABLE -->
    <Table
      :rows="filteredCustomers"
      :columns="customerColumns"
      :search-query="searchQuery"
      @update:searchQuery="searchQuery = $event"
      @edit="openForm('edit', $event)"
      :hasDelete="false"
    />

    <!-- FLOATING ADD BUTTON -->
    <button class="add-btn" @click="openForm('add')">+</button>

    <!-- CUSTOMER FORM POPUP (ADD/EDIT) -->
    <CustomerForm
      v-if="formPopup"
      :form="currentCustomer"
      :errors="errors"
      :mode="formMode"
      @save="storeCustomer"
      @update="updateCustomer"
      @close="closeForm"
    />

    <!-- SUCCESS POPUP -->
    <SuccessPopup
      v-if="successMessage"
      :message="successMessage"
      :password="generatedPassword"
      @close="successMessage = ''"
    />

  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import api from '@/api.js'

import Table from '@/components/admin/Table.vue'
import CustomerForm from '@/components/admin/CustomerForm.vue'
import SuccessPopup from '@/components/admin/ResultPopup.vue'

// ----------------------------
// State
// ----------------------------
const customers = ref([])
const searchQuery = ref('')
const formPopup = ref(false)
const formMode = ref('add') // 'add' or 'edit'
const successMessage = ref('')
const generatedPassword = ref('')
const errors = reactive({})

const newCustomerTemplate = {
  id: null,
  customer_name: '',
  email: '',
  meter_no: '',
  status: 'Active'
}

// currentCustomer will be passed to the form (add/edit)
const currentCustomer = reactive({ ...newCustomerTemplate })

// ----------------------------
// Table columns
// ----------------------------
const customerColumns = [
  { label: 'Name', field: 'customer_name' },
  { label: 'Email', field: 'email' },
  { label: 'Meter No.', field: 'meter_no' },
  { label: 'Status', field: 'status' }
]

// ----------------------------
// Filtered customers
// ----------------------------
const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  return customers.value.filter(c =>
    c.customer_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.meter_no.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// ----------------------------
// Load customers
// ----------------------------
onMounted(async () => {
  await loadCustomers()
})

async function loadCustomers() {
  try {
    const res = await api.get('/customers')
    customers.value = res.data
  } catch (err) {
    console.error('Failed to load customers:', err)
  }
}

// ----------------------------
// Open form (add/edit)
// ----------------------------
function openForm(mode, customer = null) {
  formMode.value = mode
  if (mode === 'add') {
    Object.assign(currentCustomer, newCustomerTemplate)
  } else if (mode === 'edit' && customer) {
    Object.assign(currentCustomer, customer)
  }
  Object.keys(errors).forEach(k => delete errors[k])
  formPopup.value = true
}

// Close form
function closeForm() {
  formPopup.value = false
}

// ----------------------------
// Add customer
// ----------------------------
async function storeCustomer(formData) {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.post('/customers', { ...formData })
    customers.value.push(res.data.customer)

    successMessage.value = res.data.message
    generatedPassword.value = res.data.password || ''
    formPopup.value = false

    Object.assign(currentCustomer, newCustomerTemplate)
  } catch (err) {
    if (err.response?.status === 422)
      Object.assign(errors, err.response.data.errors)
    else
      console.error('Failed to save customer:', err)
  }
}

// ----------------------------
// Update customer
// ----------------------------
async function updateCustomer(formData) {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.put(`/customers/${formData.id}`, { ...formData })

    const idx = customers.value.findIndex(c => c.id === formData.id)
    if (idx !== -1) customers.value[idx] = res.data.customer

    successMessage.value = res.data.message
    formPopup.value = false
  } catch (err) {
    if (err.response?.status === 422)
      Object.assign(errors, err.response.data.errors)
    else
      console.error('Failed to update customer:', err)
  }
}
</script>

<style scoped>
.customer-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
  box-sizing: border-box;
}

/* FLOATING ADD BUTTON */
.add-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 55px;
  height: 55px;
  font-size: 26px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  display: flex;
  justify-content: center;
  align-items: center;
}

.add-btn:hover {
  background-color: #00a0ff;
  transform: scale(1.05);
  transition: 0.2s;
}
</style>