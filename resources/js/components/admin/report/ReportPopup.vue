<template>
  <div v-if="show" class="report-popup-overlay">
    <div class="report-popup">

      <!-- Popup Title -->
      <h2>{{ title }}</h2>

      <!-- Table -->
      <div class="table-wrapper">
        <table class="report-table">
          <thead>
            <tr>
              <th v-for="header in headers" :key="header">{{ header }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in rows" :key="row.id">
              <td v-for="key in keys" :key="key">{{ row[key] }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Close Button -->
      <button class="close-btn" @click="$emit('close')">Close</button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  title: String,
  headers: Array,
  keys: Array,
  rows: Array
})
</script>

<style scoped>
/* Overlay */
.report-popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0,0,0,0.45);
  z-index: 1000;
}

/* Modal Popup */
.report-popup {
  text-align: center;
  background: #ffffff;
  padding: 30px;
  border-radius: 12px;
  max-width: 90%;
  max-height: 80%;
  overflow-y: auto;
  box-shadow: 0 15px 40px rgba(0,0,0,0.2);
  animation: popup-fade 0.3s ease-in-out;
}

/* Title */
.report-popup h2 {
  font-size: 24px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 20px;
  text-align: center;
}

/* Table */
.table-wrapper {
  overflow-x: auto;
  margin-bottom: 20px;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.report-table th, .report-table td {
  border: 1px solid #e5e7eb;
  padding: 10px 12px;
  text-align: left;
}

.report-table th {
  background-color: #f3f4f6;
  font-weight: 600;
  color: #374151;
}

.report-table tr:nth-child(even) {
  background-color: #f9fafb;
}

.report-table tr:hover {
  background-color: #eef2ff;
}

/* Close button */
.close-btn {
  display: block;
  margin: 0 auto;
  padding: 10px 25px;
  background-color: #4f46e5;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.close-btn:hover {
  background-color: #4338ca;
}

/* Animation */
@keyframes popup-fade {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>