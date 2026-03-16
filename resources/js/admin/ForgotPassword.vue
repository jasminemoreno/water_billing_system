<template>
    <div class="forgot-page">
      <div class="container">
        <h2>Forgot Password</h2>
        <p>Enter your registered admin email to receive a password reset OTP</p>
  
        <input v-model="email" type="email" placeholder="Email address" required />
        <button @click="sendOtp">Send OTP</button>
  
        <div class="back-link">
          <router-link to="/admin/login">← Back to Login</router-link>
        </div>
      </div>
  
      <!-- OTP Popup -->
      <OtpPopupAdmin
        v-if="showOtp"
        :email="email"
        @verified="handleOtpVerified"
        @close="showOtp = false"
      />
  
      <!-- Reset Password Popup -->
      <ResetPasswordPopupAdmin
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
  
  import OtpPopupAdmin from '@/components/admin/profile/OtpPopup.vue'
  import ResetPasswordPopupAdmin from '@/components/admin/profile/ResetPasswordPopup.vue'
  
  const email = ref('')
  const showOtp = ref(false)
  const showReset = ref(false)
  
  const sendOtp = async () => {
    if (!email.value) return alert('Enter your email')
  
    try {
      const res = await axios.post('/api/send', { email: email.value })
      if (res.data.success) {
        alert(res.data.success)
        showOtp.value = true
      }
    } catch (error) {
      alert(error.response?.data?.error || 'Server error')
    }
  }
  
  const handleOtpVerified = () => {
    showOtp.value = false
    showReset.value = true
  }
  
  const handleResetSuccess = () => {
    showReset.value = false
    alert('Password reset successfully!')
    window.location.href = '/admin/login'
  }
  </script>
  
  <style scoped>
  /* Background */
  .forgot-page {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #4a90e2, #50c9c3);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }
  
  /* Card */
  .container {
    background: #ffffff;
    padding: 40px 35px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    width: 400px;
    max-width: 90%;
    text-align: center;
    animation: fadeIn 0.4s ease-in-out;
  }
  
  /* Title and text */
  .container h2 {
    font-size: 26px;
    color: #333;
    margin-bottom: 10px;
  }
  
  .container p {
    font-size: 14px;
    color: #666;
    margin-bottom: 25px;
  }
  
  /* Input */
  .container input {
    width: 100%;
    padding: 14px 15px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    font-size: 16px;
    outline: none;
    transition: 0.3s all;
  }
  
  .container input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 12px rgba(74, 144, 226, 0.3);
  }
  
  /* Button */
  .container button {
    width: 100%;
    padding: 14px 0;
    font-size: 16px;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #4a90e2, #357ab8);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .container button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(74, 144, 226, 0.4);
  }
  
  /* Back link */
  .back-link {
    margin-top: 18px;
  }
  
  .back-link a {
    text-decoration: none;
    color: #4a90e2;
    font-weight: 500;
    transition: 0.3s;
  }
  
  .back-link a:hover {
    color: #357ab8;
  }
  
  /* Fade-in animation */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  /* Responsive */
  @media (max-width: 480px) {
    .container {
      padding: 30px 20px;
    }
  
    .container h2 {
      font-size: 22px;
    }
  }
  </style>