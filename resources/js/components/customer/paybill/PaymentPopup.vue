<template>
    <div v-if="show" class="modal">
      <div class="modal-content">
        <span class="close" @click="$emit('close')">&times;</span>
  
        <div class="modal-header">
          <i class="fas fa-money-bill-wave fa-2x" style="color:#0f4c75;"></i>
          <h3>Pay Bill</h3>
          <p>Upload your payment screenshot after sending the payment.</p>
        </div>
  
        <form @submit.prevent="submitPayment" enctype="multipart/form-data">
          <div class="form-group">
            <label>Amount</label>
            <input type="text":value="bill?.total !== undefined ? '₱' + Number(bill.total).toFixed(2) : ''" readonly>
          </div>
  
          <div class="form-group">
            <label>Message (optional)</label>
            <textarea v-model="message" placeholder="Optional message..." rows="3"></textarea>
          </div>
  
          <div class="form-group">
            <label>Screenshot</label>
            <input type="file" @change="handleFile" accept="image/*" required>
          </div>
  
          <button type="submit" class="send-btn" :disabled="loading">
            {{ loading ? "Submitting..." : "Send Payment" }}
          </button>
  
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, defineProps, defineEmits, watch } from "vue"
  import api from "@/customerApi"
  
  const props = defineProps({
    show: Boolean,
    bill: Object
  })
  
  const emit = defineEmits(["close", "submitted"])
  
  const message = ref("")
  const screenshot = ref(null)
  const errorMessage = ref("")
  const loading = ref(false)
  
  watch(() => props.show, (val) => {
    if (!val) {
      // Reset form when modal closes
      message.value = ""
      screenshot.value = null
      errorMessage.value = ""
      loading.value = false
    }
  })
  
  const handleFile = (e) => {
    screenshot.value = e.target.files[0]
  }
  
  const submitPayment = async () => {
    if (!props.bill) return (errorMessage.value = "No bill selected")
    if (!screenshot.value) return (errorMessage.value = "Please upload a screenshot")
  
    loading.value = true
    errorMessage.value = ""
  
    const formData = new FormData()
    formData.append("amount", props.bill.total)
    formData.append("message", message.value)
    formData.append("screenshot", screenshot.value)
    formData.append("amount", props.bill?.total ?? 0)
  
    try {
      await api.post(`/customer/paybills/${props.bill.id}`, formData, {
        headers: { "Content-Type": "multipart/form-data" }
      })
  
      emit("submitted")  // Trigger SuccessPopup in parent
    } catch (err) {
      console.error(err)
      errorMessage.value = "Failed to submit payment. Try again."
    } finally {
      loading.value = false
    }
  }
  </script>
  
  <style scoped>
  .modal {
    display: flex;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding:20px;
    animation: fadeIn 0.25s ease;
  }
  
  .modal-content {
    background:#fff;
    width:100%;
    max-width:480px;
    border-radius:16px;
    padding:30px 25px;
    position:relative;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: slideUp 0.3s ease;
  }
  
  .close {
    position:absolute;
    top:15px;
    right:20px;
    font-size:24px;
    font-weight:bold;
    color:#555;
    cursor:pointer;
    transition: all 0.2s ease;
  }
  .close:hover { color:#0f4c75; transform:scale(1.2); }
  
  .modal-header {
    text-align:center;
    margin-bottom:25px;
  }
  .modal-header h3 { margin:10px 0 5px; color:#0f4c75; font-weight:600; }
  .modal-header p { margin-top:10px; size:14px; color:#666; }
  
  .form-group { margin-bottom:18px; }
  .form-group label {
    display:block;
    font-weight:600;
    margin-bottom:6px;
    color:#0f4c75;
  }
  .form-group input[type="text"],
  .form-group textarea,
  .form-group input[type="file"] {
    width:100%;
    padding:12px 14px;
    border:1px solid #ccc;
    border-radius:12px;
    font-size:14px;
    outline:none;
    transition: all 0.2s ease;
  }
  .form-group input[type="text"]:focus,
  .form-group textarea:focus,
  .form-group input[type="file"]:focus {
    border-color:#0f4c75;
    box-shadow:0 0 8px rgba(15,76,117,0.3);
  }
  
  .send-btn {
    background: linear-gradient(90deg, #007bff, #0f4c75);
    color:#fff;
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
    transition: all 0.25s ease;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
  }
  .send-btn:hover {
    background: linear-gradient(90deg, #0d3a5a, #0f4c75);
    transform:translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
  }
  .send-btn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
  }
  
  .error-message {
    color: #dc3545;
    font-size: 14px;
    text-align: center;
    margin-top: 10px;
  }
  
  @keyframes slideUp {
    0% { opacity:0; transform:translateY(-30px); }
    100% { opacity:1; transform:translateY(0); }
  }
  @keyframes fadeIn {
    0% { opacity:0; }
    100% { opacity:1; }
  }
  </style>