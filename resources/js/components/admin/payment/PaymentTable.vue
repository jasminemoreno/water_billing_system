<template>
  <div class="payment-table-container">

    <!-- Search bar -->
    <div class="search-bar">
      <input
        type="text"
        v-model="searchQueryLocal"
        placeholder="Search customer, meter no, or payment ID..."
      />
    </div>

    <!-- Payment Table -->
    <table class="payment-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Meter No</th>
          <th>Amount (₱)</th>
          <th>Method</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="payment in filteredPayments" :key="payment.id">
          <td>{{ payment.id }}</td>
          <td>{{ payment.customer?.customer_name || '-' }}</td>
          <td>{{ payment.meter_no || '-' }}</td>
          <td>{{ Number(payment.amount || 0).toFixed(2) }}</td>
          <td>{{ payment.payment_method || '-' }}</td>
          <td>{{ payment.status || '-' }}</td>
          <td class="action-icons">
            <!-- Edit icon -->
            <button @click="$emit('edit', payment)">
              <img :src="editIcon" alt="Edit" />
            </button>

            <!-- View icon only for online payments -->
            <button
              v-if="payment.payment_method && payment.payment_method.toLowerCase() !== 'cash'"
              @click="$emit('view', payment)"
            >
              <img :src="viewIcon" alt="View" />
            </button>

            <!-- Delete icon -->
            <button @click="$emit('delete', payment)">
              <img :src="deleteIcon" alt="Delete" />
            </button>
          </td>
        </tr>

        <tr v-if="filteredPayments.length === 0">
          <td colspan="7">No payments found.</td>
        </tr>
      </tbody>
    </table>

    <!-- Add Payment Button -->
    <button class="add-btn" @click="$emit('add')">+</button>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";

// Import icons
import editIconImg from '@/assets/icons/edit.png';
import viewIconImg from '@/assets/icons/view.png';
import deleteIconImg from '@/assets/icons/delete.png';

const props = defineProps({
  payments: { type: Array, default: () => [] },
  searchQuery: { type: String, default: '' }
});

const emit = defineEmits(['edit', 'view', 'delete', 'add', 'update:searchQuery']);

// Local search input
const searchQueryLocal = ref(props.searchQuery);

// Sync local input with parent
watch(searchQueryLocal, val => emit('update:searchQuery', val));
watch(() => props.searchQuery, val => searchQueryLocal.value = val);

// Filter payments by search query
const filteredPayments = computed(() => {
  return props.payments.filter(p =>
    (p.customer?.customer_name || '').toLowerCase().includes(searchQueryLocal.value.toLowerCase()) ||
    (p.meter_no || '').toLowerCase().includes(searchQueryLocal.value.toLowerCase()) ||
    String(p.id || '').includes(searchQueryLocal.value)
  );
});

// Icons
const editIcon = editIconImg;
const viewIcon = viewIconImg;
const deleteIcon = deleteIconImg;
</script>

<style scoped>
.payment-table-container {
  width: 100%;
}

/* Search bar */
.search-bar {
  margin-bottom: 20px;
}
.search-bar input {
  width: 350px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  outline: none;
}

/* Table style */
.payment-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.payment-table th,
.payment-table td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid #eee;
}

.payment-table th {
  background: #007bff;
  color: white;
  text-transform: uppercase;
  font-size: 13px;
}

.payment-table tr:hover {
  background: #f1faff;
}

/* Action icons */
.action-icons {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.action-icons button {
  background: none;
  border: none;
  cursor: pointer;
}

.action-icons img {
  width: 20px;
  height: 20px;
  object-fit: contain;
  transition: 0.2s;
}

.action-icons button:hover img {
  transform: scale(1.2);
  opacity: 0.8;
}

/* Add button */
.add-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 55px;
  height: 55px;
  font-size: 26px;
  background-color: #007bff;
  color: white;
  border-radius: 50%;
  border: none;
  cursor: pointer;
}

.add-btn:hover {
  background-color: #00a0ff;
  transform: scale(1.05);
  transition: 0.2s;
}
</style>