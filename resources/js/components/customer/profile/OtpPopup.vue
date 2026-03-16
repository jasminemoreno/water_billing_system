<template>
  <div class="otp-popup">
    <div class="otp-content">
      <h3>Enter OTP</h3>
      <div class="otp-inputs">
        <input
          v-for="(n, i) in 6"
          :key="i"
          maxlength="1"
          v-model="otp[i]"
          @input="focusNext(i)"
        >
      </div>
      <button class="resend-btn" @click="resendOtp">Resend OTP</button>
      <button class="submit-btn" @click="verifyOtp">Verify OTP</button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { defineProps, defineEmits } from 'vue'
import customerApi from '@/customerApi.js' // ✅ Use your customer API instance

const props = defineProps({ email: String })
const emit = defineEmits(['verified', 'close'])

const otp = reactive(['', '', '', '', '', ''])

const focusNext = (i) => {
  if (otp[i].length && i < 5) {
    document.querySelectorAll('.otp-inputs input')[i + 1].focus()
  }
}

const resendOtp = async () => {
  try {
    const res = await customerApi.post('/customer/forgot/send', { email: props.email })
    alert(res.data.message)
  } catch (error) {
    alert(error.response?.data?.message || 'Error sending OTP')
  }
}

const verifyOtp = async () => {
  const code = otp.join('')
  try {
    const res = await customerApi.post('/customer/forgot/verify', { email: props.email, otp: code })
    if (res.data.success) {
      alert(res.data.message)
      emit('verified')
    } else {
      alert(res.data.message)
    }
  } catch (error) {
    alert(error.response?.data?.message || 'OTP verification failed')
  }
}
</script>

<style scoped>
.otp-popup {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.7);
}

.otp-content {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  width: 350px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.otp-inputs input {
  width: 40px; height: 40px;
  text-align: center; margin:5px;
  font-size: 18px; border:1px solid #ccc; border-radius:6px;
}

.resend-btn, .submit-btn {
  margin-top: 15px;
  padding: 10px 20px;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.resend-btn:hover, .submit-btn:hover { opacity: 0.9; }
</style>