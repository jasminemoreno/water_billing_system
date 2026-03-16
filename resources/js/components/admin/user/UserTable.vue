<template>
  <div class="user-table-wrapper">

    <!-- SEARCH INPUT -->
    <div class="user-search-bar">
      <input
        type="text"
        v-model="search"
        placeholder="Search customer name..."
        class="search-input"
        @input="$emit('search', search)" 
      >
    </div>

    <!-- USER TABLE -->
    <table class="user-table">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Customer Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Date Joined</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="customer in customers" :key="customer.id">
          <td>{{ customer.id }}</td>
          <td>{{ customer.customer_name }}</td>
          <td>{{ customer.email }}</td>
          <td>{{ customer.status }}</td>
          <td>{{ formatDate(customer.created_at) }}</td>
          <td>
            <button 
              @click="$emit('delete-user', customer.id)" 
              class="delete-icon-btn"
            >
              <img :src="deleteIcon" alt="Delete" class="delete-icon">
            </button>
          </td>
        </tr>
        <tr v-if="customers.length === 0">
          <td colspan="6" class="user-empty-message">No customers found.</td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import dayjs from 'dayjs'
import deleteIcon from '@/assets/icons/delete.png'

// Props
const props = defineProps({
  customers: {
    type: Array,
    default: () => []
  }
})

// Local search input
const search = ref('')

// Format date
const formatDate = (date) => {
  return dayjs(date).format('MMMM DD, YYYY')
}
</script>

<style scoped>
.user-table-wrapper {
  margin: 0;
}

/* Search bar */
.user-search-bar {
  margin-bottom: 25px;
}
.user-search-bar input {
  width: 320px;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  outline: none;
  transition: 0.2s;
  font-size: 15px;
}
.user-search-bar input:focus {
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0, 123, 255, 0.4);
}

/* Table */
.user-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
.user-table th, .user-table td {
  padding: 12px 10px;
  text-align: center;
  font-size: 15px;
  border-bottom: 1px solid #eee;
}
.user-table th {
  background-color: #007bff;
  color: white;
  text-transform: uppercase;
  font-size: 14px;
  letter-spacing: 0.5px;
}
.user-table tr:hover {
  background-color: #f1faff;
  transition: 0.2s;
}
.delete-icon-btn {
  background: none;
  border: none;
}
.delete-icon {
  width: 20px;
  height: 20px;
  cursor: pointer;
  transition: transform 0.2s, opacity 0.2s;
}
.delete-icon:hover {
  transform: scale(1.2);
  opacity: 0.8;
}
.user-empty-message {
  text-align: center;
  color: #666;
  font-weight: 500;
}
</style>