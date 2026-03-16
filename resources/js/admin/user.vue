<template>
  <div class="user-page">

    <!-- USER TABLE -->
    <UserTable
      :customers="customers"
      @delete-user="openDeletePopup"
      @search="handleSearch"
    />

    <!-- DELETE POPUP -->
    <DeletePopup
      v-if="showDeletePopup"
      @confirm="confirmDelete"
      @cancel="closeDeletePopup"
    />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api.js'
import UserTable from '@/components/admin/user/UserTable.vue'
import DeletePopup from '@/components/admin/user/DeletePopup.vue'

// ----------------------------
// State
// ----------------------------
const customers = ref([])
const deleteId = ref(null)
const showDeletePopup = ref(false)
const searchQuery = ref('')

// ----------------------------
// Fetch customers from backend
// ----------------------------
const fetchCustomers = async () => {
  try {
    const response = await api.get('/admin/user-management', {
      params: { search: searchQuery.value } // backend search query
    })
    customers.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('Error fetching customers:', error)
  }
}

// ----------------------------
// Handle live search
// ----------------------------
const handleSearch = (value) => {
  searchQuery.value = value
  fetchCustomers()
}

// ----------------------------
// Delete customer
// ----------------------------
const openDeletePopup = (id) => {
  deleteId.value = id
  showDeletePopup.value = true
}

const closeDeletePopup = () => {
  deleteId.value = null
  showDeletePopup.value = false
}

const confirmDelete = async () => {
  if (!deleteId.value) return
  try {
    await api.delete(`/admin/user-management/${deleteId.value}`) // fixed path
    await fetchCustomers() // refresh table after delete
    closeDeletePopup()
  } catch (error) {
    console.error('Error deleting user:', error)
  }
}

// ----------------------------
// Initial fetch on mount
// ----------------------------
onMounted(() => {
  fetchCustomers()
})
</script>

<style scoped>
.user-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background: #f4f6f8;
}
</style>