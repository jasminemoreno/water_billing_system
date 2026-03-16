<template>
  <div class="setting-page-wrapper">
    <div class="setting-page-container">

      <button class="setting-page-back-btn" @click="goBack">
        <span class="back-icon">←</span> Back
      </button>

      <SettingSection
        title="Account Information"
        :info="[ { label:'Email', value:admin.email }, { label:'Phone number', value:admin.phone } ]"
      >
        <template #action>
          <button class="setting-page-action-btn" @click="showEditAccount = true">Edit Account</button>
        </template>
      </SettingSection>

      <SettingSection
        title="Privacy & Security"
        :info="[ { label:'Password', value:'•••••••••' } ]"
      >
        <template #action>
          <button class="setting-page-action-btn" @click="showChangePassword = true">Change Password</button>
        </template>
      </SettingSection>

    </div>

    <!-- Popups -->
    <EditAccountPopup 
      v-if="showEditAccount" 
      :admin="admin"
      @close="showEditAccount = false" 
      @updated="fetchAdmin" 
      @success="showSuccess($event)" 
    />
    <ChangePasswordPopup
      v-if="showChangePassword"
      @close="showChangePassword = false"
      @success="showSuccess($event)"
    />

    <SuccessPopup v-if="successMessage" :message="successMessage" @close="successMessage=''"/>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import SettingSection from '@/components/admin/profile/SettingSection.vue'
import EditAccountPopup from '@/components/admin/profile/EditAccountPopup.vue'
import ChangePasswordPopup from '@/components/admin/profile/ChangePasswordPopup.vue'
import SuccessPopup from '@/components/admin/profile/SuccessPopup.vue'

const admin = ref({ email:'', phone:'' })
const showEditAccount = ref(false)
const showChangePassword = ref(false)
const successMessage = ref('')

const goBack = () => window.history.back()

const showSuccess = (msg) => successMessage.value = msg

const fetchAdmin = async () => {
  try {
    const token = sessionStorage.getItem('authToken')
    if (!token) throw new Error('Admin not logged in')
    const headers = { Authorization: `Bearer ${token}` }

    const profileRes = await axios.get('/api/admin/profile',{ headers })
    const authRes = await axios.get('/api/admin/user',{ headers })

    admin.value = { phone:profileRes.data.phone || '', email:authRes.data.admin.email || '' }

  } catch(err){
    console.error('Failed to fetch admin info:',err)
    admin.value = { phone:'', email:'' }
  }
}

onMounted(fetchAdmin)
</script>

<style scoped>
.setting-page-wrapper { padding:50px 20px; background:#f9f9f9; min-height:100vh; display:flex; justify-content:center; font-family:"Inter",sans-serif; }
.setting-page-container { width:100%; max-width:700px; background:#fff; padding:35px 30px; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,0.08); }
.setting-page-back-btn { background:linear-gradient(90deg,#007bff,#0056b3); color:#fff; border:none; padding:10px 18px; border-radius:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; margin-bottom:20px; transition: all 0.25s ease; }
.setting-page-back-btn:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(0,123,255,0.4); }
.back-icon { font-weight:bold; font-size:18px; }
.setting-page-action-btn { margin-top:12px; background:#007bff; color:#fff; border:none; padding:8px 18px; border-radius:10px; cursor:pointer; font-weight:600; transition:all 0.25s; }
.setting-page-action-btn:hover { background:#0056b3; transform:translateY(-1px); }
</style>