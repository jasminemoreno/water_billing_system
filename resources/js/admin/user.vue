<template>
  <div class="user-page">

    <!-- USER TABLE -->
    <UserTable
      :rows="filteredCustomers"
      :columns="columns"
      v-model:searchQuery="searchQuery"
      @delete="openDeletePopup"
      :hasEdit="false"
      :hasDelete="true"
      :hasView="false"
    />

    <!-- DELETE CONFIRMATION POPUP -->
    <DeletePopup
      v-if="showDeletePopup"
      @confirm="confirmDelete"
      @cancel="closeDeletePopup"
    />

    <!-- SUCCESS POPUP -->
    <SuccessPopup
      v-if="showSuccess"
      :message="successMessage"
      @close="closeSuccess"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api.js'
import UserTable from '@/components/admin/Table.vue'
import DeletePopup from '@/components/admin/DeletePopup.vue'
import SuccessPopup from '@/components/admin/SuccessPopup.vue'

// ----------------------------
// STATE
// ----------------------------
const customers = ref([])
const searchQuery = ref('')

const deleteId = ref(null)
const showDeletePopup = ref(false)

const showSuccess = ref(false)
const successMessage = ref('')

// ----------------------------
// TABLE COLUMNS
// ----------------------------
const columns = [
  { label: 'Customer ID', field: 'id' },
  { label: 'Customer Name', field: 'customer_name' },
  { label: 'Email', field: 'email' },
  { label: 'Status', field: 'status' },
]

// ----------------------------
// FILTER (SEARCH)
// ----------------------------
const filteredCustomers = computed(() => {
  let list = customers.value

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()

    list = list.filter(c =>
      c.customer_name?.toLowerCase().includes(q) ||
      c.email?.toLowerCase().includes(q)
    )
  }

  return list
})

// ----------------------------
// FETCH CUSTOMERS
// ----------------------------
const fetchCustomers = async () => {
  try {
    const res = await api.get('/customers')
    customers.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Fetch customer error:', err)
  }
}

// ----------------------------
// DELETE POPUP OPEN
// IMPORTANT: table emits whole row
// ----------------------------
const openDeletePopup = (customer) => {
  deleteId.value = customer.id
  showDeletePopup.value = true
}

// CLOSE DELETE POPUP
const closeDeletePopup = () => {
  deleteId.value = null
  showDeletePopup.value = false
}

// ----------------------------
// CONFIRM DELETE
// ----------------------------
const confirmDelete = async () => {
  if (!deleteId.value) return

  try {
    await api.delete(`/admin/user-management/${deleteId.value}`)

    // ⭐ immediate table update
    customers.value = customers.value.filter(
      c => c.id !== deleteId.value
    )

    successMessage.value = 'Customer deleted successfully!'
    showSuccess.value = true

  } catch (err) {
    console.error('Delete error:', err)
  } finally {
    closeDeletePopup()
  }
}

// CLOSE SUCCESS POPUP
const closeSuccess = () => {
  showSuccess.value = false
  successMessage.value = ''
}

// ----------------------------
// INITIAL LOAD
// ----------------------------
onMounted(() => {
  fetchCustomers()
})
</script>

<style scoped>
.user-page {
  margin-left: 250px;
  padding: 80px 30px 30px;
  min-height: 100vh;
  background: #f4f6f8;
}
</style>