<template>
  <div v-if="show" class="overlay">
    <div class="popup">

      <h2>{{ title }}</h2>

      <!-- Year/Month Selector -->
      <div v-if="customContent?.type === 'year-month-select'" class="filter">
        <label>
          Year:
          <select v-model="year" @change="change">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </label>

        <label>
          Month:
          <select v-model="month" @change="change">
            <option v-for="m in months" :key="m.value" :value="m.value">
              {{ m.label }}
            </option>
          </select>
        </label>
      </div>

      <!-- Table -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th v-for="h in headers" :key="h">{{ h }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="rows.length === 0">
              <td :colspan="headers.length" class="no-data">
                No data available for this selection
              </td>
            </tr>
            <tr v-for="(r, i) in rows" :key="i">
              <td v-for="k in keys" :key="k">{{ r[k] }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <button class="close-btn" @click="$emit('close')">Close</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  show: Boolean,
  title: String,
  headers: Array,
  keys: Array,
  rows: Array,
  customContent: Object
})

const emit = defineEmits(['close','year-month-selected'])

const year = ref()
const month = ref()

const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' }
]

const years = []
const currentYear = new Date().getFullYear()
for(let y=2026; y<=currentYear; y++) years.push(y)

onMounted(()=>{
  if(props.customContent){
    year.value = props.customContent.selectedYear || currentYear
    month.value = props.customContent.selectedMonth || 1
  }
})

const change = () => {
  emit('year-month-selected', year.value, month.value)
}
</script>

<style scoped>
.overlay {
  position: fixed;
  inset:0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup {
  background:white;
  padding:30px 25px;
  border-radius:12px;
  width:85%;
  max-width:1000px;
  max-height:85%;
  overflow-y:auto;
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
}

h2{
  text-align:center;
  font-size:1.5rem;
  font-weight:700;
  margin-bottom:20px;
  color:#111827;
}

.filter{
  display:flex;
  gap:20px;
  justify-content:center;
  margin-bottom:20px;
}

.filter label{
  display:flex;
  flex-direction:column;
  font-weight:500;
  font-size:14px;
  color:#374151;
}

.filter select{
  margin-top:5px;
  padding:8px 12px;
  border-radius:6px;
  border:1px solid #d1d5db;
  font-size:14px;
  cursor:pointer;
  background-color:#f9fafb;
}

.filter select:hover{
  border-color:#4f46e5;
}

.table-wrapper{
  overflow-x:auto;
}

table{
  width:100%;
  border-collapse:collapse;
  font-size:14px;
}

th, td{
  border:1px solid #e5e7eb;
  padding:10px 12px;
  text-align:left;
}

th{
  background-color:#f3f4f6;
  font-weight:600;
  color:#374151;
}

tr:nth-child(even){
  background-color:#f9fafb;
}

tr:hover{
  background-color:#eef2ff;
}

.no-data{
  text-align:center;
  font-style:italic;
  color:#6b7280;
}

.close-btn{
  display:block;
  margin:20px auto 0 auto;
  padding:10px 25px;
  background-color:#4f46e5;
  color:#fff;
  border:none;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
  font-size:14px;
  transition:all 0.3s ease;
}

.close-btn:hover{
  background-color:#4338ca;
}
</style>