<template>
  <div class="customer-page">

    <CustomerTable
      :customers="filteredCustomers"
      :searchQuery="searchQuery"
      @update:searchQuery="searchQuery = $event"
      @edit="openEditPopup"
      @add="addPopup = true"
    />

    <!-- Add Customer Popup -->
    <CustomerForm
      v-if="addPopup"
      :form="newCustomer"
      :errors="errors"
      @save="storeCustomer"
      @close="addPopup = false"
    />

    <!-- Edit Customer Popup -->
    <CustomerEdit
      v-if="editPopup"
      :form="editCustomer"
      :errors="errors"
      @update="updateCustomer"
      @close="editPopup = false"
    />

    <!-- Success Popup -->
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

import CustomerTable from '@/components/admin/customer/CustomerTable.vue'
import CustomerForm from '@/components/admin/customer/CustomerForm.vue'
import CustomerEdit from '@/components/admin/customer/CustomerEdit.vue'
import SuccessPopup from '@/components/admin/customer/SuccessPopup.vue'

const customers = ref([])
const searchQuery = ref('')
const addPopup = ref(false)
const editPopup = ref(false)
const successMessage = ref('')
const generatedPassword = ref('')

const newCustomer = reactive({
  customer_name: '',
  email: '',
  meter_no: '',
  status: 'Active'
})

const editCustomer = reactive({
  id: null,
  customer_name: '',
  email: '',
  meter_no: '',
  status: 'Active'
})

const errors = reactive({})

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  return customers.value.filter(c =>
    c.customer_name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Load customers on mount
onMounted(async () => {
  try {
    const res = await api.get('/customers')
    customers.value = res.data
  } catch (err) {
    console.error('Failed to load customers:', err)
  }
})

// Add new customer
const storeCustomer = async (formData) => {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.post('/customers', { ...formData })
    customers.value.push(res.data.customer)
    successMessage.value = res.data.message
    generatedPassword.value = res.data.password || ''
    addPopup.value = false

    // Reset new customer form
    Object.assign(newCustomer, { customer_name:'', email:'', meter_no:'', status:'Active' })
  } catch (err) {
    if (err.response?.status === 422) Object.assign(errors, err.response.data.errors)
    else console.error('Failed to save customer:', err)
  }
}

// Open edit popup
const openEditPopup = (customer) => {
  Object.assign(editCustomer, customer)
  editPopup.value = true
}

// Update customer
const updateCustomer = async (formData) => {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.put(`/customers/${formData.id}`, { ...formData })
    const idx = customers.value.findIndex(c => c.id === formData.id)
    if (idx !== -1) customers.value[idx] = res.data.customer
    successMessage.value = res.data.message
    editPopup.value = false
  } catch (err) {
    if (err.response?.status === 422) Object.assign(errors, err.response.data.errors)
    else console.error('Failed to update customer:', err)
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
</style>