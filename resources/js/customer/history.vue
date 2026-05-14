<template>
  <div class="payment-history-page">
    <h2>Payment History</h2>

    <!-- ✅ Use reusable CustomerTable -->
    <CustomerTable
      :columns="columns"
      :rows="rows"
      :hasPay="false"
      :hasDownload="true"
      @download="downloadReceipt"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import customerApi from "@/customerApi.js"
import CustomerTable from "@/components/customer/table.vue"
import dayjs from "dayjs"

const payments = ref([])

// ✅ FIXED FETCH
const fetchPayments = async () => {
  try {
    const res = await customerApi.get("/customer/history")

    // IMPORTANT FIX: use res.data.payments
    payments.value = res.data.payments || []

  } catch (error) {
    console.error("Error fetching payment history:", error)
    payments.value = []
  }
}

onMounted(fetchPayments)

// columns unchanged
const columns = [
  { label: "Bill ID", field: "bill_id" },
  { label: "Month", field: "month" },
  { label: "Amount", field: "amount" },
  { label: "Date Paid", field: "date_paid" },
  { label: "Status", field: "status" }
]

// rows unchanged (just safe)
const rows = computed(() =>
  payments.value.map(p => ({
    id: p.id,
    bill_id: p.bill?.id ?? "-",
    month: p.bill?.billing_date
      ? dayjs(p.bill.billing_date).format("MMMM YYYY")
      : "-",
    amount: "₱" + Number(p.amount || 0).toFixed(2),
    date_paid: p.created_at
      ? dayjs(p.created_at).format("MMM DD, YYYY")
      : "-",
    status: p.status ?? "-"
  }))
)

// download unchanged
const downloadReceipt = async (payment) => {
  try {
    const res = await customerApi.get(
      `/customer/payment/${payment.id}/receipt`,
      { responseType: "blob" }
    )

    const url = window.URL.createObjectURL(new Blob([res.data]))

    const link = document.createElement("a")
    link.href = url
    link.download = `receipt-${payment.id}.pdf`

    document.body.appendChild(link)
    link.click()
    link.remove()

  } catch (error) {
    console.error(error)
    alert("Failed to download receipt")
  }
}
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