<template>
  <div class="table-container">

    <!-- SEARCH -->
    <div class="search-bar">
      <input type="text" v-model="searchQueryLocal" placeholder="Search..." />
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th v-for="col in columns" :key="col.field">{{ col.label }}</th>
          <th v-if="hasPay || hasDownload">Action</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="row in filteredRows" :key="row.id">
          <td v-for="col in columns" :key="col.field">
            <span v-if="col.field === 'total'">
              ₱{{ Number(row[col.field]).toFixed(2) }}
            </span>
            <span v-else>
              {{ row[col.field] }}
            </span>
          </td>

          <!-- PAY + DOWNLOAD BUTTONS (ADDED ONLY DOWNLOAD) -->
          <td v-if="hasPay || hasDownload">

            <!-- Pay Button -->
            <button
              v-if="hasPay"
              class="pay-btn"
              @click="$emit('pay', row)"
            >
              Pay
            </button>

            <!-- Download Button (NEW ONLY) -->
            <button
              v-if="hasDownload"
              class="download-btn"
              @click="$emit('download', row)"
            >
              Download
            </button>

          </td>
        </tr>

        <tr v-if="filteredRows.length === 0">
          <td :colspan="columns.length + ((hasPay || hasDownload) ? 1 : 0)">
            No data found.
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
rows: { type: Array, default: () => [] },
columns: { type: Array, default: () => [] },
searchQuery: { type: String, default: '' },
hasPay: { type: Boolean, default: false },

// ✅ ADDED ONLY THIS
hasDownload: { type: Boolean, default: false }
})

const emit = defineEmits(['pay', 'download'])

const searchQueryLocal = ref(props.searchQuery)

watch(searchQueryLocal, val => emit('update:searchQuery', val))
watch(() => props.searchQuery, val => searchQueryLocal.value = val)

const filteredRows = computed(() => {
if (!searchQueryLocal.value) return props.rows
const query = searchQueryLocal.value.toLowerCase()
return props.rows.filter(row =>
  props.columns.some(col => {
    const value = row[col.field]
    return value != null &&
      String(value).toLowerCase().includes(query)
  })
)
})
</script>

<style scoped>
.table-container { width: 100%; margin-top: 20px; }
.search-bar { margin-bottom: 15px; }
.search-bar input {
width: 100%; max-width: 400px; padding: 10px 14px; border-radius: 8px;
border: 1px solid #ccc; outline: none; font-size: 14px;
}
.data-table {
width: 100%; border-collapse: collapse; background: white;
border-radius: 12px; overflow: hidden;
box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.data-table th, .data-table td {
padding: 12px 10px; text-align: center; border-bottom: 1px solid #eee;
font-size: 14px;
}
.data-table th {
background-color: #2872A1; color: white; text-transform: uppercase; font-size: 13px;
}
.data-table tr:hover { background-color: #f1faff; transition: 0.2s; }

.pay-btn {
padding: 6px 12px;
background-color: #2872A1;
color: white;
border-radius: 6px;
font-size: 14px;
font-weight: 500;
border: none;
cursor: pointer;
transition: all 0.2s ease-in-out;
}

.pay-btn:hover {
background-color: #0056b3;
transform: scale(1.05);
}

/* ✅ NEW ONLY */
.download-btn {
padding: 6px 12px;
background-color: #28a745;
color: white;
border-radius: 6px;
font-size: 14px;
font-weight: 500;
border: none;
cursor: pointer;
transition: all 0.2s ease-in-out;
margin-left: 5px;
}

.download-btn:hover {
background-color: #218838;
transform: scale(1.05);
}
</style>