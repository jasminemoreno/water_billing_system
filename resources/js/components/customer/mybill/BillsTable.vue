<script setup>
import Table from '@/components/admin/table.vue'
import dayjs from 'dayjs'
import { computed } from 'vue'

const props = defineProps({
  bills: Array
})

const columns = [
  { label: 'Bill ID', field: 'id' },
  { label: 'Meter No', field: 'meter_no' },
  { label: 'Consumption', field: 'consumption' },
  { label: 'Total', field: 'total' },
  { label: 'Billing Date', field: 'billing_date' },
  { label: 'Due Date', field: 'due_date' },
  { label: 'Status', field: 'status' }
]

const rows = computed(() =>
  props.bills.map(b => ({
    id: b.id,
    meter_no: b.meter_no,
    consumption: b.consumption + ' m³',
    total: '₱' + Number(b.total ?? 0).toFixed(2),
    billing_date: dayjs(b.billing_date).format('MMM DD, YYYY'),
    due_date: dayjs(b.due_date).format('MMM DD, YYYY'),
    status: b.status === 'Rejected' ? 'Unpaid' : b.status
  }))
)
</script>

<template>
  <Table
    :rows="rows"
    :columns="columns"
    :hasEdit="false"
    :hasDelete="false"
    :hasView="false"
  />
</template>