<script setup>
import Table from '@/components/admin/table.vue'
import { computed } from 'vue'

const props = defineProps({
  payments: Array
})

const columns = [
  { label: 'Bill ID', field: 'bill_id' },
  { label: 'Month', field: 'month' },
  { label: 'Amount', field: 'amount' },
  { label: 'Date Paid', field: 'date_paid' },
  { label: 'Status', field: 'status' }
]

const rows = computed(() =>
  props.payments.map(p => ({
    bill_id: p.bill?.id ?? '-',
    month: p.bill?.billing_date
      ? new Date(p.bill.billing_date).toLocaleString('default',{month:'short',year:'numeric'})
      : '-',
    amount: '₱' + Number(p.amount).toFixed(2),
    date_paid: new Date(p.created_at).toLocaleDateString(),
    status: p.status
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