<template>
  <div class="reset-popup">
    <div class="reset-content">
      <h3>Reset Password</h3>
      <input type="password" v-model="password" placeholder="New Password">
      <input type="password" v-model="confirmPassword" placeholder="Confirm Password">
      <button @click="resetPassword">Reset Password</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { defineProps, defineEmits } from 'vue'
import customerApi from '@/customerApi.js' // ✅ Use your customer API instance

const props = defineProps({ email: String })
const emit = defineEmits(['reset-success', 'close'])

const password = ref('')
const confirmPassword = ref('')

const resetPassword = async () => {
  if (password.value !== confirmPassword.value) {
    return alert("Passwords do not match")
  }

  try {
    const res = await customerApi.post('/customer/forgot/reset', {
      email: props.email,
      password: password.value,
      password_confirmation: confirmPassword.value
    })

    if (res.data.success) {
      alert(res.data.message)
      emit('reset-success')
    } else {
      alert(res.data.message)
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Password reset failed')
  }
}
</script>

<style scoped>
.reset-popup {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.7);
}

.reset-content {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  width: 350px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.reset-content input {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border:1px solid #ccc; border-radius:6px;
}

.reset-content button {
  width: 100%;
  padding: 10px;
  margin-top:10px;
  background: #28a745;
  color:white;
  border:none;
  border-radius:6px;
  cursor:pointer;
}

.reset-content button:hover { opacity:0.9; }
</style>