<template>
  <div v-if="show" class="modal" @click.self="closeModal">
    <div class="modal-content" @click.stop>

      <span class="close" @click="closeModal">&times;</span>

      <div class="modal-header">
        <i class="fas fa-money-bill-wave fa-2x" style="color:#0f4c75;"></i>
        <h3>Pay Bill</h3>

        <p v-if="isPending">This payment is pending for approval.</p>
        <p v-else>Upload your payment screenshot after sending the payment.</p>
      </div>

      <form v-if="bill" @submit.prevent="submitPayment">

        <!-- AMOUNT -->
        <div class="form-group">
          <label>Amount</label>
          <input
            type="text"
            :value="formattedAmount"
            readonly
          />
        </div>

        <!-- MESSAGE -->
        <div class="form-group">
          <label>Message (optional)</label>
          <textarea
            v-model="message"
            rows="3"
            :readonly="isPending"
          ></textarea>
        </div>

        <!-- FILE UPLOAD -->
        <div class="form-group">
          <label>Screenshot</label>

          <input type="file" @click="console.log('FILE CLICKED')" @change="handleFile" />

          <p v-if="file" class="file-name">
            Selected: {{ file.name }}
          </p>
        </div>

        <!-- SUBMIT -->
        <button
          v-if="!isPending"
          type="submit"
          class="send-btn"
          :disabled="loading"
        >
          {{ loading ? "Submitting..." : "Send Payment" }}
        </button>

        <p v-if="error" class="error-message">
          {{ error }}
        </p>

      </form>

      <!-- CLOSE (PENDING) -->
      <div v-if="isPending" class="text-center">
        <button class="send-btn" @click="closeModal">
          Close
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue"
import api from "@/customerApi"

const props = defineProps({
  show: Boolean,
  bill: Object
})

const emit = defineEmits(["close", "submitted"])

// STATE
const message = ref("")
const file = ref(null)
const loading = ref(false)
const error = ref("")

// COMPUTED
const isPending = computed(() => props.bill?.status === "Pending")

const formattedAmount = computed(() =>
  props.bill?.total ? `₱${Number(props.bill.total).toFixed(2)}` : "₱0.00"
)

// RESET WHEN CLOSED
watch(() => props.show, (val) => {
  if (!val) {
    message.value = ""
    file.value = null
    error.value = ""
    loading.value = false
  }
})

// CLOSE MODAL
const closeModal = () => {
  emit("close")
}

// FILE HANDLER (IMPORTANT)
const handleFile = (e) => {
  const selected = e.target.files?.[0]
  if (selected) file.value = selected
}

// SUBMIT
const submitPayment = async () => {
  if (!props.bill) {
    error.value = "No bill selected"
    return
  }

  if (!file.value) {
    error.value = "Please select a file"
    return
  }

  loading.value = true
  error.value = ""

  const formData = new FormData()
  formData.append("amount", props.bill.total)
  formData.append("message", message.value)
  formData.append("screenshot", file.value)

  try {
    await api.post(`/customer/paybills/${props.bill.id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" }
    })

    emit("submitted")

  } catch (err) {
    console.error(err)
    error.value = "Failed to submit payment"
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;

  z-index: 999999 !important;
}

.modal-content {
  background: #fff;
  width: 100%;
  max-width: 480px;
  padding: 30px;
  border-radius: 16px;
  position: relative;
  pointer-events: auto; /* IMPORTANT FIX */
}

.close {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 24px;
  cursor: pointer;
}

.form-group {
  margin-bottom: 18px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

.send-btn {
  width: 100%;
  padding: 14px;
  background: #0f4c75;
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}

.send-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  color: red;
  text-align: center;
  margin-top: 10px;
}

.file-name {
  font-size: 13px;
  color: #555;
  margin-top: 5px;
}
</style>