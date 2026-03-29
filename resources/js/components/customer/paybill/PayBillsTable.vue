<script setup>
import Table from '@/components/admin/table.vue'
import dayjs from 'dayjs'
import { computed } from 'vue'

const props = defineProps({
  bills: Array
})

const emit = defineEmits(['pay'])

const columns = [
  { label: 'Bill ID', field: 'id' },
  { label: 'Month', field: 'month' },
  { label: 'Amount', field: 'total' },
  { label: 'Status', field: 'status' }
]

const rows = computed(() =>
  props.bills.map(b => ({
    id: b.id,
    month: dayjs(b.billing_date).format('MMMM YYYY'),
    total: b.total,
    status: b.status
  }))
)
</script>

<template>
  <Table
    :rows="rows"
    :columns="columns"
    :hasView="true"
    :hasEdit="false"
    :hasDelete="false"
  >
    <!-- Override default view button with Pay text -->
    <template #view="{ row }">
      <button
        class="pay-btn"
        @click="$emit('pay', row)"
      >
        Pay
      </button>
    </template>
  </Table>
</template>

<style scoped>
.pay-btn {
  padding: 6px 12px;
  background-color: #007bff;
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
</style>