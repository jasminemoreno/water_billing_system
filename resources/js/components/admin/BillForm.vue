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

        <!-- WARNING MESSAGE -->
        <p v-if="duplicateWarning" class="warning">
          ⚠️ This customer already has a bill for this month!
        </p>

        <div class="button-group">
          <button type="submit" :disabled="duplicateWarning">
            {{ mode === 'add' ? 'Save' : 'Update' }}
          </button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>

      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  form: Object,
  customers: Array,
  bills: Array,   // <-- bills prop needed for duplicate check
  mode: String
})

const emit = defineEmits(['save','close'])

const form = props.form
const customerSearch = ref('')
const filteredCustomers = ref([])

// FILTER CUSTOMERS
function filterCustomers() {
  const q = customerSearch.value.toLowerCase()
  filteredCustomers.value = props.customers.filter(c =>
    c.customer_name.toLowerCase().includes(q)
  )
}

// SELECT CUSTOMER
function selectCustomer(c) {
  form.customer_id = c.id
  form.customer_name = c.customer_name
  form.meter_no = c.meter_no

  customerSearch.value = ''
  filteredCustomers.value = []
}

// CALCULATE TOTAL
function calculateTotal() {
  form.total = (form.consumption || 0) * 10
}

// DATES
function setDates() {
  if (!form.billing_date) return

  const billDate = new Date(form.billing_date)
  const dueDate = new Date(billDate)
  dueDate.setDate(dueDate.getDate() + 7)
  const disconnectDate = new Date(dueDate)
  disconnectDate.setDate(disconnectDate.getDate() + 5)

  form.due_date = dueDate.toISOString().split('T')[0]
  form.disconnection_date = disconnectDate.toISOString().split('T')[0]
}

// CHECK DUPLICATE BILL FOR SAME MONTH
const duplicateWarning = computed(() => {
  if (!form.customer_id || !form.billing_date) return false

  const billMonth = new Date(form.billing_date).getMonth()
  const billYear = new Date(form.billing_date).getFullYear()

  return props.bills.some(b => {
    if (b.customer_id !== form.customer_id) return false
    const bDate = new Date(b.billing_date)
    return bDate.getMonth() === billMonth && bDate.getFullYear() === billYear
  })
})

// SUBMIT
function handleSubmit() {
  if (duplicateWarning.value) return // prevent saving
  emit('save', { ...form })
}
</script>

<style scoped>
.bill-popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
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
.close-btn {
  background: #ccc;
  color: #333;
}
.button-group {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
}
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
.warning {
  color: red;
  font-size: 13px;
  margin-top: 6px;
}
</style>