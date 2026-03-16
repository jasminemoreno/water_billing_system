<template>
  <div class="popup-overlay">
    <div class="popup-box">
      <h2>Change Password</h2>
      <form @submit.prevent="changePassword">
        <label>Current Password</label>
        <input type="password" v-model="form.current_password" required />

        <label>New Password</label>
        <input type="password" v-model="form.new_password" required />

        <label>Confirm Password</label>
        <input type="password" v-model="form.new_password_confirmation" required />

        <button type="submit" class="save-btn">Update Password</button>
        <button type="button" class="close-btn" @click="emit('close')">Close</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['close', 'success'])

const form = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const changePassword = async () => {
  try {
    const token = sessionStorage.getItem('authToken')
    if (!token) throw new Error('Admin token missing')

    const headers = { Authorization: `Bearer ${token}` }

    await axios.put('/api/admin/user/password', form.value, { headers })

    emit('success', 'Password updated successfully!')
    emit('close')

  } catch (err) {
    console.error(err)

    // Handle Laravel validation errors
    if (err.response?.status === 422) {
      if (err.response.data.errors) {
        // Validation errors like new_password too short or confirmation mismatch
        const messages = Object.values(err.response.data.errors).flat().join('\n')
        alert(messages)
      } else if (err.response.data.error) {
        // Custom error (wrong current password)
        alert(err.response.data.error)
      } else {
        alert('Password update failed due to validation.')
      }
    } else {
      alert(err.response?.data?.message || 'Password update failed')
    }
  }
}
</script>

<style scoped>
.popup-overlay {
  position: fixed; inset: 0;
  display: flex; justify-content: center; align-items: center;
  background: rgba(0,0,0,0.35); z-index: 1000;
}
.popup-box {
  background: #fff; padding: 30px 25px; border-radius: 15px;
  width: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.popup-box h2 { margin-bottom: 15px; color: #007bff; }
.popup-box input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 10px; border: 1px solid #ccc; }
.save-btn { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; margin-right: 10px; }
.save-btn:hover { background: #0056b3; }
.close-btn { background: #ccc; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; }
</style>