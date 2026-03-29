<template>
  <div v-if="show" class="overlay">
    <div class="popup">

      <h2>{{ title }}</h2>

      <!-- YEAR & MONTH FILTER -->
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

      <!-- TABLE -->
      <div class="table-wrapper" ref="tableToExport">
        <table>
          <thead>
            <tr>
              <th v-for="h in headers" :key="h">{{ h }}</th>
            </tr>
          </thead>

          <tbody>
            <!-- NO DATA -->
            <tr v-if="rows.length === 0">
              <td :colspan="headers.length" class="no-data">
                No data available for this selection
              </td>
            </tr>

            <!-- DATA ROWS -->
            <tr v-for="(r, i) in rows" :key="i">
              <td v-for="k in keys" :key="k">

                <!-- SCREENSHOT COLUMN -->
                <template v-if="k === 'screenshot'">
                  <button 
                    v-if="r[k]" 
                    class="view-btn"
                    @click="openImage(r[k])"
                  >
                    View
                  </button>
                  <span v-else>-</span>
                </template>

                <!-- NORMAL COLUMN -->
                <template v-else>
                  {{ r[k] }}
                </template>

              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- DOWNLOAD BUTTONS ONLY FOR PAYMENT & BILL HISTORY -->
      <div v-if="type === 'history' || type === 'bill-history'" class="download-buttons">
        <button @click="downloadPDF">Download PDF</button>
        <button @click="downloadExcel">Download Excel</button>
      </div>

      <!-- IMAGE PREVIEW MODAL -->
      <div v-if="previewImage" class="image-preview-overlay">
        <div class="image-box">
          <span class="close-img" @click="previewImage = null">&times;</span>
          <img :src="previewImage" alt="Screenshot" />
        </div>
      </div>

      <!-- CLOSE BUTTON -->
      <button class="close-btn" @click="$emit('close')">Close</button>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import html2pdf from 'html2pdf.js'
import * as XLSX from 'xlsx'

const props = defineProps({
  show: Boolean,
  title: String,
  headers: Array,
  keys: Array,
  rows: Array,
  customContent: Object,
  type: String
})

const emit = defineEmits(['close','year-month-selected'])

const year = ref(null)
const month = ref(null)
const previewImage = ref(null)
const tableToExport = ref(null)

// MONTH OPTIONS
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

// YEAR OPTIONS
const years = []
const currentYear = new Date().getFullYear()
for (let y = 2024; y <= currentYear; y++) {
  years.push(y)
}

// INITIALIZE FILTER VALUES
onMounted(() => {
  if (props.customContent) {
    year.value = props.customContent.selectedYear || currentYear
    month.value = props.customContent.selectedMonth || new Date().getMonth() + 1
  }
})

// WATCH FOR POPUP OPEN (RESET FILTER PROPERLY)
watch(() => props.show, (val) => {
  if (val && props.customContent) {
    year.value = props.customContent.selectedYear || currentYear
    month.value = props.customContent.selectedMonth || new Date().getMonth() + 1
  }
})

// EMIT FILTER CHANGE
const change = () => {
  emit('year-month-selected', year.value, month.value)
}

// OPEN IMAGE PREVIEW
const openImage = (filename) => {
  previewImage.value = `http://localhost:8000/uploads/gcash/${filename}`
}

// -------------------------
// DOWNLOAD FUNCTIONS
// -------------------------
const downloadPDF = () => {
  if (!tableToExport.value) return

  // Get month name
  const monthName = months.find(m => m.value === month.value)?.label || ''
  const headerText = `${monthName} ${props.title}`

  // Create a temporary container with header + table for PDF
  const container = document.createElement('div')
  const header = document.createElement('h2')
  header.textContent = headerText
  header.style.textAlign = 'center'
  header.style.marginBottom = '15px'
  container.appendChild(header)
  container.appendChild(tableToExport.value.cloneNode(true)) // clone table

  const opt = {
    margin: 10,
    filename: `${headerText.replace(/\s/g,'_')}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  }

  html2pdf().set(opt).from(container).save()
}

const downloadExcel = () => {
  if (!props.rows || props.rows.length === 0) return

  const worksheet = XLSX.utils.json_to_sheet(props.rows)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, props.title)
  XLSX.writeFile(workbook, `${props.title.replace(/\s/g,'_')}_${year.value}-${month.value}.xlsx`)
}
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:1000;
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

h2 {
  text-align:center;
  margin-bottom:20px;
  font-weight:600;
}

/* FILTER */
.filter {
  display:flex;
  gap:20px;
  justify-content:center;
  margin-bottom:20px;
}

.filter label {
  display:flex;
  flex-direction:column;
  font-size:14px;
}

.filter select {
  margin-top:5px;
  padding:8px 12px;
  border-radius:6px;
  border:1px solid #ccc;
}

/* TABLE */
.table-wrapper {
  overflow-x:auto;
}

table {
  width:100%;
  border-collapse:collapse;
}

th, td {
  border:1px solid #e5e7eb;
  padding:10px;
  font-size:14px;
}

th {
  background:#f3f4f6;
  font-weight:600;
}

tr:nth-child(even) {
  background:#f9fafb;
}

.no-data {
  text-align:center;
  font-style:italic;
}

/* DOWNLOAD BUTTONS */
.download-buttons {
  display:flex;
  gap:10px;
  justify-content:center;
  margin-top:15px;
}

.download-buttons button {
  padding:8px 16px;
  border:none;
  border-radius:6px;
  background:#4f46e5;
  color:white;
  cursor:pointer;
}

.download-buttons button:hover {
  background:#4338ca;
}

/* BUTTON */
.view-btn {
  padding:5px 10px;
  background:#16a34a;
  color:white;
  border:none;
  border-radius:5px;
  cursor:pointer;
}

.view-btn:hover {
  background:#15803d;
}

/* IMAGE PREVIEW */
.image-preview-overlay {
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.7);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:2000;
}

.image-box {
  position:relative;
  background:white;
  padding:15px;
  border-radius:10px;
}

.image-box img {
  max-width:500px;
  max-height:400px;
}

.close-img {
  position:absolute;
  top:5px;
  right:10px;
  font-size:22px;
  cursor:pointer;
}

/* CLOSE BUTTON */
.close-btn {
  margin-top:20px;
  display:block;
  margin-left:auto;
  margin-right:auto;
  padding:10px 25px;
  background:#4f46e5;
  color:white;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.close-btn:hover {
  background:#4338ca;
}
</style>