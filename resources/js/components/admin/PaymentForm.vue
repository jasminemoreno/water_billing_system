<template>
  <div class="payment-popup-overlay">
    <div class="payment-popup">
      <h2>{{ mode==='add' ? 'Add Payment' : 'View Payment' }}</h2>

      <!-- ADD FORM -->
      <form v-if="mode==='add'" @submit.prevent="submitForm">
        <input v-model="customerSearch" placeholder="Search customer..." @input="filterCustomers" class="search-input" />
        <div v-if="filteredCustomers.length" class="search-results">
          <div v-for="c in filteredCustomers" :key="c.bill_id" class="result-item" @click="selectCustomer(c)">
            {{ c.customer_name }} - Bill ID: {{ c.bill_id }} - Meter: {{ c.meter_no }} - ₱{{ c.amount }}
          </div>
        </div>

        <div v-if="form.customer_name" class="customer-tags">
          <span><b>Customer:</b> {{ form.customer_name }}</span>
          <span><b>Meter No:</b> {{ form.meter_no }}</span>
          <span><b>Bill Amount:</b> ₱{{ form.amount }}</span>
          <span><b>Total:</b> ₱{{ totalAmount }}</span>
          <span><b>Payment Method:</b> {{ form.payment_method }}</span>
        </div>

        <button v-if="!penaltyApplied && form.customer_name" type="button" class="penalty-btn" @click="applyPenalty">
          Apply Penalty ₱{{ defaultPenalty }}
        </button>

        <div class="button-group">
          <button type="submit" class="main-btn">Add Payment</button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>
      </form>

      <!-- VIEW PAYMENT -->
      <!-- VIEW PAYMENT -->
        <div v-if="mode==='view'" class="view-payment">
          <p><b>Customer:</b> {{ form.customer_name || '-' }}</p>
          <p><b>Meter:</b> {{ form.meter_no || '-' }}</p>
          <p><b>Amount:</b> ₱{{ form.amount || 0 }}</p>
          <p><b>Status:</b> {{ form.status || '-' }}</p>
          <p><b>Payment Method:</b> {{ form.payment_method || '-' }}</p>

          <!-- SHOW GCash screenshot ONLY IF IT EXISTS -->
          <div v-if="form.payment_method === 'GCash' && form.gcash_screenshot" class="gcash-img-wrapper">
            <label><b>GCash Screenshot:</b></label>
            <img :src="`/uploads/gcash/${form.gcash_screenshot}`" alt="GCash Screenshot" class="gcash-img"/>
          </div>

          <div class="button-group">
            <button v-if="form.status==='Pending'" class="status-btn" @click="verify">Verify</button>
            <button v-if="form.status==='Pending'" class="status-btn reject" @click="reject">Reject</button>
            <button class="close-btn" @click="$emit('close')">Close</button>
          </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import api from '@/api'

const props = defineProps({ mode: String, payment: Object, customers: Array, bills: Array })
const emit = defineEmits(['saved','close','verify','reject'])

const defaultPenalty = 20
const penaltyApplied = ref(false)
const customerSearch = ref('')
const filteredCustomers = ref([])

const form = ref({
  customer_id: '',
  bill_id: '',
  customer_name: '',
  meter_no: '',
  amount: 0,
  status: 'Pending',
  payment_method: 'Cash'
})

const totalAmount = computed(() => Number(form.value.amount || 0) + (penaltyApplied.value ? defaultPenalty : 0))

watch(() => props.payment, (val) => {
  if(val){ form.value = {...val}; penaltyApplied.value=false }
  else { form.value={customer_id:'',bill_id:'',customer_name:'',meter_no:'',amount:0,status:'Pending',payment_method:'Cash'}; penaltyApplied.value=false }
}, { immediate:true })

const filterCustomers = async () => {
  if(customerSearch.value.trim().length < 1){ filteredCustomers.value=[]; return }
  filteredCustomers.value = (await api.get(`/payments/search-customer?query=${customerSearch.value}`)).data
}

const selectCustomer = (c) => {
  form.value.customer_id=c.customer_id
  form.value.bill_id=c.bill_id
  form.value.customer_name=c.customer_name
  form.value.meter_no=c.meter_no
  form.value.amount=c.amount || 0
  form.value.payment_method='Cash'
  penaltyApplied.value=false
  customerSearch.value=''
  filteredCustomers.value=[]
}

const applyPenalty = () => { penaltyApplied.value = true }

const submitForm = async () => {
  const payload={ ...form.value, amount: totalAmount.value }
  const res = await api.post('/payments', payload)
  emit('saved','Payment added successfully!', res.data.payment)
  emit('close')
}

const verify = () => emit('verify', form.value)
const reject = () => emit('reject', form.value)
</script>

<style scoped>
.payment-popup-overlay{ z-index:999; position: fixed; inset:0; background: rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; }
.payment-popup{ background:white; padding:30px 25px; border-radius:12px; width:450px; max-width:95%; box-shadow:0 8px 30px rgba(0,0,0,0.3); font-family:'Segoe UI',sans-serif; }
.payment-popup h2{ margin-bottom:20px; font-size:1.5rem; text-align:center; color:#333; }
.search-input{ width:100%; margin:8px 0; padding:12px; border:1px solid #ccc; border-radius:6px; font-size:1rem; margin-bottom:5px; }
.button-group{ display:flex; justify-content:center; gap:10px; margin-top:15px; flex-wrap:wrap; }
.button-group button{ flex:1; min-width:100px; padding:10px 12px; font-size:1rem; border-radius:6px; cursor:pointer; transition:0.2s; }
.main-btn{ background:#007bff; color:white; } .main-btn:hover{ background:#0056b3; }
.close-btn{ background:#ccc; color:#333; } .close-btn:hover{ background:#999; }
.penalty-btn{ background:#f0ad4e; color:white; width:100%; margin:8px 0; } .penalty-btn:hover{ background:#ec971f; }
.status-btn{ background:#28a745; color:white; } .status-btn.reject{ background:#dc3545; } .status-btn:hover{ opacity:0.9; }
.search-results{ border:1px solid #ccc; max-height:160px; overflow-y:auto; background:white; border-radius:6px; }
.result-item{ padding:8px 10px; cursor:pointer; } .result-item:hover{ background:#f1faff; }
.customer-tags{ display:flex; flex-direction:column; gap:6px; margin:10px 0; padding:10px; border:1px solid #ccc; border-radius:6px; background:#f9f9f9; }
.gcash-img-wrapper {
  margin: 12px 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.gcash-img-wrapper label {
  font-weight: bold;
  color: #333;
}

.gcash-img {
  max-width: 100%;        /* scale down to fit container width */
  max-height: 400px;      /* limit height */
  object-fit: contain;    /* keeps aspect ratio */
  border: 1px solid #ddd;
  border-radius: 6px;
}
</style>