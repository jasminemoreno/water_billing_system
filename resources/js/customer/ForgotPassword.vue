<template>
    <div class="forgot-page">
      <div class="container">
        <h2>Forgot Password</h2>
        <p>Enter your registered email to receive a password reset OTP</p>
  
        <input v-model="email" type="email" placeholder="Email address" required>
        <button @click="sendOtp">Send OTP</button>
  
        <div class="back-link">
          <router-link to="/customer/login">← Back to Login</router-link>
        </div>
      </div>
  
      <!-- OTP Popup -->
      <OtpPopup
        v-if="showOtp"
        :email="email"
        @verified="handleOtpVerified"
        @close="showOtp = false"
      />
  
      <!-- Reset Password Popup -->
      <ResetPasswordPopup
        v-if="showReset"
        :email="email"
        @reset-success="handleResetSuccess"
        @close="showReset = false"
      />
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import OtpPopup from '@/components/customer/profile/OtpPopup.vue'
  import ResetPasswordPopup from '@/components/customer/profile/ResetPasswordPopup.vue'
  
  const email = ref('')
  const showOtp = ref(false)
  const showReset = ref(false)
  
  const sendOtp = async () => {
    if (!email.value) return alert('Enter your email')
  
    try {
      const res = await axios.post('/api/customer/forgot/send', { email: email.value })
      if (res.data.success) {
        alert(res.data.message)
        showOtp.value = true
      }
    } catch (error) {
      alert(error.response?.data?.message || 'Server error')
    }
  }
  
  const handleOtpVerified = () => {
    showOtp.value = false
    showReset.value = true
  }
  
  const handleResetSuccess = () => {
    showReset.value = false
    alert('Password reset successfully!')
    // Redirect to login
    window.location.href = '/customer/login'
  }
  </script>
  
  <style scoped>
  .forgot-page {
    font-family: Arial, sans-serif;
    background: #f5f7fa;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    width: 400px;
    text-align: center;
  }
  
  .container input {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  
  .container button {
    width: 100%;
    padding: 12px;
    background: #4a90e2;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
  }
  
  .container button:hover {
    background: #357ab8;
  }
  
  .back-link {
    margin-top: 15px;
  }
  .back-link a {
    text-decoration: none;
    color: #4a90e2;
  }
  </style>