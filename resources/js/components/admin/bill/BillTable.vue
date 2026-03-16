<template>
  <div class="bill-table-container">

    <!-- Search bar -->
    <div class="search-bar">
      <input
        type="text"
        v-model="searchQueryLocal"
        placeholder="Search customer, bill ID, or meter no..."
      />
    </div>

    <!-- Bill table -->
    <table class="bill-table">
      <thead>
        <tr>
          <th>Bill ID</th>
          <th>Customer Name</th>
          <th>Meter No.</th>
          <th>Consumption (m³)</th>
          <th>Total (₱)</th>
          <th>Billing Date</th>
          <th>Due Date</th>
          <th>Disconnection Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="bill in bills" :key="bill.id">
          <td>{{ bill.id }}</td>
          <td>{{ bill.customer?.customer_name || '-' }}</td>
          <td>{{ bill.meter_no || '-' }}</td>
          <td>{{ bill.consumption || 0 }}</td>
          <td>{{ Number(bill.total || 0).toFixed(2) }}</td>
          <td>{{ formatDate(bill.billing_date) }}</td>
          <td>{{ formatDate(bill.due_date) }}</td>
          <td>{{ formatDate(bill.disconnection_date) }}</td>
          <td>
            <img 
              :src="editIcon" 
              alt="Edit" 
              class="edit-icon"
              @click="$emit('edit', bill)"
            />
          </td>
        </tr>

        <tr v-if="bills.length === 0">
          <td colspan="9">No bills found.</td>
        </tr>
      </tbody>
    </table>

    <!-- Add Bill Button -->
    <button class="add-btn" @click="$emit('add')">+</button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import editIconImg from '@/assets/icons/edit.png'

const props = defineProps({
  bills: { type: Array, default: () => [] },
  searchQuery: { type: String, default: '' }
})

const emit = defineEmits(['edit', 'add', 'update:searchQuery'])

const searchQueryLocal = ref(props.searchQuery)
watch(searchQueryLocal, val => emit('update:searchQuery', val))
watch(() => props.searchQuery, val => searchQueryLocal.value = val)

const editIcon = editIconImg

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>

<style scoped>
.bill-table-container { width: 100%; }
.search-bar { margin-bottom: 20px; }
.search-bar input { width: 350px; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
.bill-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.bill-table th, .bill-table td { padding: 10px; text-align: center; border-bottom: 1px solid #eee; }
.bill-table th { background: #007bff; color: white; text-transform: uppercase; font-size: 13px; }
.bill-table tr:hover { background: #f1faff; }
.add-btn { position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; font-size: 26px; background-color: #007bff; color:white; border-radius: 50%; border:none; cursor:pointer; }
.add-btn:hover { background-color: #00a0ff; transform: scale(1.05); transition:0.2s; }
/* Edit icon size */
.edit-icon{
  width: 20px;
  height: 20px;
  object-fit: contain;
}
</style>