<template>
    <div class="popup">
      <h3>Reset Password for {{ email }}</h3>
      <p class="info-text">Enter a new password and confirm it.</p>
      <input v-model="password" type="password" placeholder="New Password" />
      <input v-model="password_confirmation" type="password" placeholder="Confirm Password" />
      <div class="buttons">
        <button class="reset-btn" @click="resetPassword">Reset Password</button>
        <button class="close-btn" @click="$emit('close')">Cancel</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  
  const props = defineProps({ email: String })
  const emit = defineEmits(['reset-success', 'close'])
  
  const password = ref('')
  const password_confirmation = ref('')
  
  const resetPassword = async () => {
    if (!password.value || !password_confirmation.value) return alert('Fill all fields')
    try {
      const res = await axios.post('/api/reset', {
        email: props.email,
        password: password.value,
        password_confirmation: password_confirmation.value
      })
      if (res.data.success) {
        alert(res.data.success)
        emit('reset-success')
      }
    } catch (error) {
      alert(error.response?.data?.error || 'Error resetting password')
    }
  }
  </script>
  
  <style scoped>
  .popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ffffff;
    padding: 35px 30px;
    border-radius: 18px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
    text-align: center;
    width: 380px;
    max-width: 90%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    animation: fadeIn 0.3s ease-in-out;
  }
  
  .popup h3 {
    font-size: 22px;
    color: #333;
    margin-bottom: 8px;
  }
  
  .info-text {
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
  }
  
  .popup input {
    width: 100%;
    padding: 14px 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 12px;
    font-size: 16px;
    outline: none;
    transition: all 0.3s;
  }
  
  .popup input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 10px rgba(74, 144, 226, 0.3);
  }
  
  .buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
  }
  
  .reset-btn {
    flex: 1;
    padding: 12px 0;
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #4a90e2, #357ab8);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .reset-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(74, 144, 226, 0.4);
  }
  
  .close-btn {
    flex: 1;
    padding: 12px 0;
    font-size: 16px;
    font-weight: 500;
    background: #f0f0f0;
    color: #555;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .close-btn:hover {
    transform: translateY(-2px);
    background: #e0e0e0;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -48%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
  }
  
  /* Responsive */
  @media (max-width: 480px) {
    .popup {
      padding: 30px 20px;
    }
    .popup h3 {
      font-size: 20px;
    }
  }
  </style>