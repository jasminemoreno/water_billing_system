<template>
  <div class="bill-popup-overlay">
    <div class="bill-popup">

      <h2>{{ mode === 'add' ? 'Add Bill' : 'Edit Bill' }}</h2>

      <form @submit.prevent="handleSubmit">

        <!-- Customer Search (only in Add mode) -->
        <input
          v-if="mode === 'add'"
          v-model="customerSearch"
          placeholder="Search customer..."
          @input="filterCustomers"
        />

        <div v-if="filteredCustomers.length && mode==='add'" class="search-results">
          <div
            v-for="c in filteredCustomers"
            :key="c.id"
            class="result-item"
            @click="selectCustomer(c)"
          >
            {{ c.customer_name }} ({{ c.meter_no || 'No Meter' }})
          </div>
        </div>

        <input v-model="form.customer_name" placeholder="Customer Name" readonly required>
        <input v-model="form.meter_no" placeholder="Meter No." readonly required>

        <input
          type="number"
          v-model.number="form.consumption"
          placeholder="Consumption (m³)"
          required
          @input="calculateTotal"
        >

        <p>Total: ₱{{ Number(form.total).toFixed(2) }}</p>

        <label>Billing Date:</label>
        <input type="date" v-model="form.billing_date" @change="setDates">

        <label>Due Date:</label>
        <input type="date" v-model="form.due_date" readonly>

        <label>Disconnection Date:</label>
        <input type="date" v-model="form.disconnection_date" readonly>

        <div class="button-group">
          <button type="submit">{{ mode === 'add' ? 'Save' : 'Update' }}</button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>

      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  form: Object,
  customers: Array,
  mode: String
})

const emit = defineEmits(['save','close'])

const form = props.form
const customerSearch = ref('')
const filteredCustomers = ref([])

// --- Filter customers while typing ---
function filterCustomers() {
  const q = customerSearch.value.toLowerCase()
  filteredCustomers.value = props.customers.filter(c =>
    c.customer_name.toLowerCase().includes(q)
  )
}

// --- Select a customer from search results ---
function selectCustomer(c) {
  form.customer_id = c.id
  form.customer_name = c.customer_name
  form.meter_no = c.meter_no

  customerSearch.value = ''
  filteredCustomers.value = []
}

// --- Calculate total (example: ₱10 per m³) ---
function calculateTotal() {
  form.total = (form.consumption || 0) * 10
}

// --- Set due and disconnection dates automatically ---
function setDates() {
  if (!form.billing_date) return

  const billDate = new Date(form.billing_date)
  const dueDate = new Date(billDate)
  dueDate.setDate(dueDate.getDate() + 7) // 7 days later
  const disconnectDate = new Date(dueDate)
  disconnectDate.setDate(disconnectDate.getDate() + 5) // +5 days after due

  form.due_date = dueDate.toISOString().split('T')[0]
  form.disconnection_date = disconnectDate.toISOString().split('T')[0]
}

// --- Emit form submit ---
function handleSubmit() {
  emit('save', { ...form })
}
</script>

<style scoped>
.bill-popup-overlay {
  z-index: 999;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.bill-popup {
  background: white;
  padding: 25px;
  border-radius: 12px;
  width: 400px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.bill-popup input {
  width: 100%;
  margin: 6px 0;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.bill-popup button {
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  background: #007bff;
  color: white;
  cursor: pointer;
  font-size: 14px;
}

.bill-popup button:hover {
  background: #00a0ff;
}

.close-btn {
  background: #ccc;
  color: #333;
}

.close-btn:hover {
  background: #999;
}

/* Center Add/Save and Cancel buttons */
.button-group {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
}

/* Search results */
.search-results {
  border: 1px solid #ccc;
  max-height: 120px;
  overflow-y: auto;
  background: #fff;
  margin-bottom: 5px;
}

.result-item {
  padding: 6px 8px;
  cursor: pointer;
}

.result-item:hover {
  background: #f1faff;
}
</style>