<template>
  <div class="payment-history-page">
    <h2>Payment History</h2>

    <!-- ✅ Use reusable CustomerTable -->
    <CustomerTable
      :columns="columns"
      :rows="rows"
      :hasPay="false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import customerApi from "@/customerApi.js"
import CustomerTable from "@/components/customer/table.vue"
import dayjs from "dayjs"

const payments = ref([])

// ✅ Fetch data
const fetchPayments = async () => {
  try {
    const res = await customerApi.get("/customer/history")

    payments.value = (res.data.payments || []).filter(
      p => p.status === "Verified"
    )
  } catch (error) {
    console.error("Error fetching payment history:", error)
  }
}

onMounted(fetchPayments)

// ✅ Columns (parent handles it)
const columns = [
  { label: "Bill ID", field: "bill_id" },
  { label: "Month", field: "month" },
  { label: "Amount", field: "amount" },
  { label: "Date Paid", field: "date_paid" },
  { label: "Status", field: "status" }
]

// ✅ Rows (parent formats data)
const rows = computed(() =>
  payments.value.map(p => ({
    bill_id: p.bill?.id ?? "-",
    month: p.bill?.billing_date
      ? dayjs(p.bill.billing_date).format("MMMM YYYY")
      : "-",
    amount: "₱" + Number(p.amount).toFixed(2),
    date_paid: dayjs(p.created_at).format("MMM DD, YYYY"),
    status: p.status
  }))
)
</script>

<style scoped>
.payment-history-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background: #CBDDE9;
  font-family: 'Roboto', sans-serif;
}

h2 {
  margin-bottom: 20px;
  color: #2872A1;
  text-align: center;
  font-weight: 600;
}
</style>