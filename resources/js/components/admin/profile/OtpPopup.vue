<template>
  <div class="popup">
    <h3>Enter OTP sent to {{ email }}</h3>
    <p class="info-text">A 6-digit code has been sent to your email. Please enter it below.</p>
    <input v-model="otp" type="number" placeholder="6-digit OTP" />
    <div class="buttons">
      <button class="verify-btn" @click="verifyOtp">Verify OTP</button>
      <button class="close-btn" @click="$emit('close')">Cancel</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({ email: String })
const emit = defineEmits(['verified', 'close'])

const otp = ref('')

const verifyOtp = async () => {
  if (!otp.value) return alert('Enter OTP')
  try {
    const res = await axios.post('/api/verify', {
      email: props.email,
      otp: otp.value
    })
    if (res.data.success) {
      alert(res.data.success)
      emit('verified')
    }
  } catch (error) {
    alert(error.response?.data?.error || 'Invalid OTP')
  }
}
</script>

<style scoped>
.popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: linear-gradient(135deg, #4a90e2, #50c9c3);
  padding: 30px 35px;
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
  width: 380px;
  max-width: 90%;
  text-align: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  animation: fadeIn 0.3s ease-in-out;
}

.popup h3 {
  margin-bottom: 10px;
  font-size: 22px;
  color: #333;
}

.info-text {
  font-size: 14px;
  color: #666;
  margin-bottom: 20px;
}

.popup input {
  width: 100%;
  padding: 12px 15px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 16px;
  outline: none;
  transition: border 0.3s, box-shadow 0.3s;
}

.popup input:focus {
  border-color: #4a90e2;
  box-shadow: 0 0 8px rgba(74, 144, 226, 0.3);
}

.buttons {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.verify-btn {
  flex: 1;
  padding: 12px 0;
  background: linear-gradient(135deg, #4a90e2, #357ab8);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.verify-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(74, 144, 226, 0.4);
}

.close-btn {
  flex: 1;
  padding: 12px 0;
  background: #f0f0f0;
  color: #555;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.2s, background 0.2s;
}

.close-btn:hover {
  transform: translateY(-2px);
  background: #e0e0e0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, -48%); }
  to { opacity: 1; transform: translate(-50%, -50%); }
}
</style>