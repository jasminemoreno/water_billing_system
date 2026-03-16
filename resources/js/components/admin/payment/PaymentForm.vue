<template>
  <div class="payment-popup-overlay">
    <div class="payment-popup">

      <h2>
        {{ mode === 'add' ? 'Add Payment' : 
           mode === 'edit' ? 'Edit Payment' : 
           mode === 'view' ? 'View Payment' : 'Delete Payment' }}
      </h2>

      <!-- ADD / EDIT FORM -->
      <form v-if="mode==='add' || mode==='edit'" @submit.prevent="submitForm">

        <!-- Customer Search -->
        <input
          v-if="mode==='add'"
          v-model="customerSearch"
          placeholder="Search customer..."
          @input="filterCustomers"
          class="search-input"
        />

        <!-- Search results -->
        <div v-if="filteredCustomers.length && mode==='add'" class="search-results">
          <div
            v-for="c in filteredCustomers"
            :key="c.bill_id"
            class="result-item"
            @click="selectCustomer(c)"
          >
            {{ c.customer_name }} - Bill ID: {{ c.bill_id }} - Meter: {{ c.meter_no }} - ₱{{ c.amount }}
          </div>
        </div>

        <!-- Customer info tags -->
        <div v-if="form.customer_name" class="customer-tags">
          <span><b>Customer:</b> {{ form.customer_name }}</span>
          <span><b>Meter No:</b> {{ form.meter_no }}</span>
          <span><b>Bill Amount:</b> ₱{{ form.amount }}</span>
          <span><b>Total Amount:</b> ₱{{ totalAmount }}</span>
        </div>

        <!-- Penalty button -->
        <button
          v-if="!penaltyApplied && form.customer_name"
          type="button"
          class="penalty-btn"
          @click="applyPenalty"
        >
          Apply Penalty ₱{{ defaultPenalty }}
        </button>

        <!-- Status dropdown -->
        <div v-if="mode==='edit'" class="status-group">
          <label>Status:</label>
          <select v-model="form.status">
            <option>Pending</option>
            <option>Verified</option>
          </select>
        </div>

        <!-- Form buttons -->
        <div class="button-group">
          <button type="submit">{{ mode==='add' ? 'Add Payment' : 'Update Payment' }}</button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>

      </form>

      <!-- VIEW PAYMENT -->
      <div v-if="mode==='view'" class="view-payment">
        <p><b>Customer:</b> {{ form.customer_name || '-' }}</p>
        <p><b>Meter:</b> {{ form.meter_no || '-' }}</p>
        <p><b>Amount:</b> ₱{{ form.amount || 0 }}</p>
        <p><b>Penalty:</b> ₱{{ penaltyApplied ? defaultPenalty : 0 }}</p>
        <p><b>Total:</b> ₱{{ totalAmount }}</p>
        <p><b>Status:</b> {{ form.status || '-' }}</p>

        <img v-if="form.gcash_screenshot" :src="`/uploads/gcash/${form.gcash_screenshot}`" class="gcash-img">

        <button @click="updateStatus('Verified')" class="status-btn">Verify</button>
        <button @click="updateStatus('Rejected')" class="status-btn reject">Reject</button>
        <button class="close-btn" @click="$emit('close')">Close</button>
      </div>

      <!-- DELETE PAYMENT -->
      <div v-if="mode==='delete'" class="delete-payment">
        <p>This action cannot be undone.</p>
        <div class="button-group">
          <button class="confirm-btn" @click="deletePayment">Delete</button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import api from '@/api.js'

const props = defineProps({
  mode: { type: String, default: 'add' },
  payment: { type: Object, default: () => null }
})

const emit = defineEmits(['saved','close'])

// Default penalty
const defaultPenalty = 20
const penaltyApplied = ref(false)

// Form state
const form = ref({
  customer_id: '',
  bill_id: '',
  customer_name: '',
  meter_no: '',
  amount: 0,
  status: 'Pending',
  gcash_screenshot: ''
})

// Computed total amount
const totalAmount = computed(() => {
  return Number(form.value.amount || 0) + (penaltyApplied.value ? defaultPenalty : 0)
})

// Customer search state
const customerSearch = ref('')
const filteredCustomers = ref([])

// Watch for edit/view mode
watch(
  () => props.payment,
  (val) => {
    if(val) {
      form.value = {
        customer_id: val.customer_id || '',
        bill_id: val.bill_id || '',
        customer_name: val.customer?.customer_name || '',
        meter_no: val.meter_no || '',
        amount: val.amount || 0,
        status: val.status || 'Pending',
        gcash_screenshot: val.gcash_screenshot || ''
      }
      penaltyApplied.value = false
    } else {
      form.value = { customer_id:'', bill_id:'', customer_name:'', meter_no:'', amount:0, status:'Pending', gcash_screenshot:'' }
      penaltyApplied.value = false
    }
  },
  { immediate: true }
)

// Filter customers (calls backend, backend already returns only unpaid bills)
const filterCustomers = async () => {
  if(customerSearch.value.trim().length < 1){
    filteredCustomers.value = []
    return
  }

  try {
    const res = await api.get(`/payments/search-customer?query=${customerSearch.value}`)
    // No need to filter unpaid bills here, backend handles it
    filteredCustomers.value = res.data
  } catch(err) {
    console.error('Customer search error:', err)
  }
}

// Select customer
const selectCustomer = (c) => {
  form.value.customer_id = c.customer_id
  form.value.bill_id = c.bill_id
  form.value.customer_name = c.customer_name
  form.value.meter_no = c.meter_no
  form.value.amount = c.amount || 0
  penaltyApplied.value = false

  customerSearch.value = ''
  filteredCustomers.value = []
}

// Apply penalty
const applyPenalty = () => {
  penaltyApplied.value = true
}

// Submit
const submitForm = async () => {
  try{
    const payload = { ...form.value, amount: totalAmount.value }

    if(props.mode==='add'){
      await api.post('/payments', payload)
      emit('saved','Payment added successfully')
    } else if(props.mode==='edit'){
      await api.put(`/payments/${props.payment.id}`, payload)
      emit('saved','Payment updated successfully')
    }
    emit('close')
  } catch(err){
    console.error('Submit error:', err)
  }
}

// Delete
const deletePayment = async () => {
  try{
    await api.delete(`/payments/${props.payment.id}`)
    emit('saved','Payment deleted successfully')
    emit('close')
  } catch(err){
    console.error('Delete error:', err)
  }
}

// Update status
const updateStatus = async (status) => {
  try{
    await api.patch(`/payments/${props.payment.id}/status`, { status })
    emit('saved','Payment status updated')
    emit('close')
  } catch(err){
    console.error('Status update error:', err)
  }
}
</script>

<style scoped>
.payment-popup-overlay{
  z-index:999;
  position: fixed; inset:0;
  background: rgba(0,0,0,0.4);
  display:flex;
  justify-content:center;
  align-items:center;
}
.payment-popup{
  background:white;
  padding:30px;
  border-radius:12px;
  width:450px;
  max-width:95%;
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
  font-family: 'Segoe UI', sans-serif;
}
.payment-popup h2{
  margin-bottom:20px;
  font-size:1.4rem;
  text-align:center;
}
.payment-popup input, .payment-popup select{
  width:100%;
  margin:8px 0;
  padding:10px;
  border:1px solid #ccc;
  border-radius:6px;
  font-size:1rem;
}
.search-input{
  margin-bottom:5px;
}
.payment-popup button{
  padding:10px 18px;
  border:none;
  border-radius:8px;
  background:#007bff;
  color:white;
  cursor:pointer;
  font-size:1rem;
}
.penalty-btn{
  background:#f0ad4e;
  margin:8px 0;
  width:100%;
}
.penalty-btn:hover{
  background:#ec971f;
}
.close-btn{
  background:#ccc;
  color:#333;
}
.close-btn:hover{
  background:#999;
}
.confirm-btn{
  background:#d9534f;
}
.confirm-btn:hover{
  background:#c9302c;
}
.status-btn{
  background:#28a745;
  margin:5px 5px 5px 0;
}
.status-btn.reject{
  background:#dc3545;
}
.status-btn:hover{
  opacity:0.9;
}
.button-group{
  display:flex;
  justify-content:center;
  gap:10px;
  margin-top:15px;
}
.search-results{
  border:1px solid #ccc;
  max-height:150px;
  overflow-y:auto;
  background:white;
  border-radius:6px;
}
.result-item{
  padding:8px 10px;
  cursor:pointer;
}
.result-item:hover{
  background:#f1faff;
}
.customer-tags{
  display:flex;
  flex-direction:column;
  gap:4px;
  margin:8px 0;
  padding:8px;
  border:1px solid #ccc;
  border-radius:6px;
  background:#f9f9f9;
}
.gcash-img{
  max-width:100%;
  margin:10px 0;
  border-radius:6px;
}
</style>