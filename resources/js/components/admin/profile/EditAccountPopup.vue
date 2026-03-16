<template>
  <div class="popup-overlay">
    <div class="popup-box">
      <h2>Edit Account</h2>

      <form @submit.prevent="updateAccount">
        <label>Email</label>
        <input type="email" v-model="form.email" required />

        <label>Phone number</label>
        <input type="text" v-model="form.phone" />

        <button type="submit" class="save-btn">Save Changes</button>
        <button type="button" class="close-btn" @click="emit('close')">Close</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({ admin: Object })
const emit = defineEmits(['close','success','updated'])

const form = ref({ email: '', phone: '' })

watch(() => props.admin, val => {
  if (val) form.value = { email: val.email, phone: val.phone }
}, { immediate: true })

const updateAccount = async () => {
  try {
    const token = sessionStorage.getItem('authToken')
    if (!token) throw new Error('Admin token missing')
    const headers = { Authorization: `Bearer ${token}` }

    // Update email via AuthController
    await axios.put('/api/admin/user', { email: form.value.email }, { headers })

    // Update phone via AdminProfileController
    await axios.put('/api/admin/profile/phone', { phone: form.value.phone }, { headers })

    // Emit success to parent
    emit('success', 'Account updated successfully!')
    emit('updated') // refresh admin data
    emit('close')   // close popup

  } catch (err) {
    console.error(err)
    alert(err.response?.data?.message || 'Update failed')
  }
}
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.35);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup-box {
  background: #fff;
  padding: 30px 25px;
  border-radius: 15px;
  width: 350px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}

.popup-box h2 {
  margin-bottom: 20px;
  color: #333;
}

.popup-box label {
  display: block;
  margin-top: 10px;
  margin-bottom: 5px;
}

.popup-box input {
  width: 100%;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.save-btn {
  margin-top: 15px;
  background: #007bff;
  color: #fff;
  border: none;
  padding: 10px 15px;
  border-radius: 10px;
  cursor: pointer;
}

.save-btn:hover { background: #0056b3; }

.close-btn {
  margin-left: 10px;
  margin-top: 15px;
  background: #ccc;
  border: none;
  padding: 10px 15px;
  border-radius: 10px;
  cursor: pointer;
}
</style>