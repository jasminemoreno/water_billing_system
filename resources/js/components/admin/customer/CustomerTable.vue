<template>
  <div class="customer-table-container">

    <!-- Search Bar -->
    <div class="search-bar">
      <input
        type="text"
        v-model="searchQueryLocal"
        placeholder="Search customer name..."
      />
    </div>

    <table class="customer-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Meter No.</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="customer in customers" :key="customer.id">
          <td>{{ customer.customer_name }}</td>
          <td>{{ customer.email }}</td>
          <td>{{ customer.meter_no }}</td>
          <td>
            <span :class="['status', customer.status.toLowerCase()]">{{ customer.status }}</span>
          </td>
          <td class="customer-action-icons">
            <button @click="$emit('edit', customer)" title="Edit Customer">
              <img :src="editIcon" alt="Edit" />
            </button>
          </td>
        </tr>

        <tr v-if="customers.length === 0">
          <td colspan="5" class="empty-message">
            No customers found. Click + to add one.
          </td>
        </tr>
      </tbody>
    </table>

    <button class="add-btn" @click="$emit('add')" title="Add Customer">+</button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import editIconImg from '@/assets/icons/edit.png'

const props = defineProps({
  customers: Array,
  searchQuery: String
})

const emit = defineEmits(['edit', 'add', 'update:searchQuery'])
const searchQueryLocal = ref(props.searchQuery || '')

watch(searchQueryLocal, val => emit('update:searchQuery', val))
watch(() => props.searchQuery, val => searchQueryLocal.value = val)

const editIcon = editIconImg
</script>

<style scoped>
.customer-table-container { width: 100%; }
.search-bar { margin-bottom: 20px; display: flex; }
.search-bar input { width: 320px; padding: 10px 15px; border-radius: 8px; border: 1px solid #ccc; outline: none; font-size: 15px; }
.search-bar input:focus { border-color: #007bff; box-shadow: 0 0 4px rgba(0,123,255,0.4); }
.customer-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.customer-table th, .customer-table td { padding: 12px 10px; text-align: center; border-bottom: 1px solid #eee; font-size: 15px; }
.customer-table th { background-color: #007bff; color: white; text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px; }
.customer-table tr:hover { background-color: #f1faff; transition: 0.2s; }
.status.active { color: #00b894; font-weight: 600; }
.status.inactive { color: #d63031; font-weight: 600; }
.customer-action-icons { display: flex; justify-content: center; align-items: center; }
.customer-action-icons button { background: none; border: none; padding: 0; cursor: pointer; }
.customer-action-icons button img { width: 20px; height: 20px; transition: transform 0.2s, opacity 0.2s; }
.customer-action-icons button:hover img { transform: scale(1.2); opacity: 0.8; }
.add-btn { position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; font-size: 26px; background-color: #007bff; color: white; border: none; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
.add-btn:hover { background-color: #00a0ff; transform: scale(1.05); transition: 0.2s; }
.empty-message { text-align: center; margin-top: 20px; color: #777; font-size: 15px; }
</style>