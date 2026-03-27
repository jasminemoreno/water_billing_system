<template>
  <div class="table-container">

    <!-- SEARCH -->
    <div class="search-bar">
      <input
        type="text"
        v-model="searchQueryLocal"
        placeholder="Search..."
      />
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th v-for="col in columns" :key="col.field">{{ col.label }}</th>
          <th v-if="hasEdit || hasDelete || hasView">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="row in filteredRows" :key="row.id">

          <!-- DYNAMIC CELLS -->
          <td v-for="col in columns" :key="col.field">{{ row[col.field] }}</td>

          <!-- ACTION ICONS -->
          <td v-if="hasEdit || hasDelete || hasView" class="action-icons">

            <!-- Slot for overriding buttons -->
            <slot name="edit" :row="row">
              <button v-if="hasEdit" @click="$emit('edit', row)">
                <img src="@/assets/icons/edit.png" alt="Edit">
              </button>
            </slot>

            <slot name="delete" :row="row">
              <button v-if="hasDelete" @click="$emit('delete', row)">
                <img src="@/assets/icons/delete.png" alt="Delete">
              </button>
            </slot>

            <slot name="view" :row="row">
              <button v-if="hasView" @click="$emit('view', row)">
                <img src="@/assets/icons/view.png" alt="View">
              </button>
            </slot>

          </td>
        </tr>

        <tr v-if="filteredRows.length === 0">
          <td :colspan="columns.length + (hasEdit || hasDelete || hasView ? 1 : 0)">
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
  hasEdit: { type: Boolean, default: true },
  hasDelete: { type: Boolean, default: true },
  hasView: { type: Boolean, default: false }
})

const emit = defineEmits(['edit', 'delete', 'view', 'update:searchQuery'])

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
  width: 320px; padding: 10px 14px; border-radius: 8px;
  border: 1px solid #ccc; outline: none; font-size: 14px;
}
.search-bar input:focus {
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0,123,255,0.3);
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
  background-color: #007bff; color: white;
  text-transform: uppercase; font-size: 13px;
}
.data-table tr:hover { background-color: #f1faff; transition: 0.2s; }
.action-icons {
  display: flex; justify-content: center; align-items: center; gap: 10px;
}
.action-icons button {
  background: none; border: none; padding: 0; cursor: pointer;
}
.action-icons img {
  width: 20px; height: 20px; object-fit: contain; transition: 0.2s;
}
.action-icons button:hover img {
  transform: scale(1.2); opacity: 0.8;
}
</style>