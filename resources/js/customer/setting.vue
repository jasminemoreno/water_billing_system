<template>
    <div class="settings-page">
    
      <div class="settings-container">
    
        <div class="header">
          <button class="back-btn" @click="goBack">← Back</button>
          <h1>Account Settings</h1>
          <p>Manage your personal information and security</p>
        </div>
    
        <!-- Account Info -->
        <div class="section account-section">
    
          <div class="section-header">
            <h3>Account Information</h3>
          </div>
    
          <div class="info-box">
            <div class="info-item">
              <span class="label">Phone number</span>
              <span class="value">{{ customer.phone || 'Not set' }}</span>
            </div>
    
            <div class="info-item">
              <span class="label">Email</span>
              <span class="value">{{ customer.email || 'Not set' }}</span>
            </div>
          </div>
    
          <button class="action-btn edit-btn" @click="showEdit = true">
             Edit Account
          </button>
    
        </div>
    
    
        <!-- Security -->
        <div class="section security-section">
    
          <div class="section-header">
            <h3>Privacy & Security</h3>
          </div>
    
          <div class="info-box">
            <div class="info-item">
              <span class="label">Password</span>
              <span class="value">•••••••••</span>
            </div>
          </div>
    
          <button class="action-btn security-btn" @click="showPassword = true">
             Change Password
          </button>
    
        </div>
    
      </div>
    
    
      <!-- Modals -->
      <EditAccountModal
        v-if="showEdit"
        :customer="customer"
        @close="showEdit = false"
        @updated="handleUpdated"
      />
    
      <ChangePasswordModal
  v-if="showPassword"
  @close="showPassword = false"
  @updated="handlePasswordUpdated"
/>
      <SuccessPopup
  :show="showSuccess"
  :message="successMessage"
  @close="showSuccess = false"
/>
    
    </div>
    </template>
    
    <script setup>
    import { ref, onMounted } from "vue"
    import api from "@/customerApi"
    
    import EditAccountModal from "@/components/customer/profile/EditAccountModal.vue"
    import ChangePasswordModal from "@/components/customer/profile/ChangePasswordModal.vue"
    import SuccessPopup from "@/components/customer/SuccessPopup.vue"
    
    const customer = ref({})
    const showEdit = ref(false)
    const showPassword = ref(false)
    const successMessage = ref("")
    const showSuccess = ref(false)
    
    onMounted(() => {
      loadCustomer()
    })
    
    async function loadCustomer(){
      const res = await api.get("/customer/settings")
    
      if(res.data.success){
        customer.value = res.data.customer
      }
    }
    function handleUpdated() {
  loadCustomer()

  successMessage.value = "Account updated successfully!"
  showSuccess.value = true

  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}

function handlePasswordUpdated() {
  successMessage.value = "Password updated successfully!"
  showSuccess.value = true

  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
}
    
    function goBack(){
      window.history.back()
    }
    </script>
    
    <style scoped>
    
    /* PAGE BACKGROUND */
    
    .settings-page{
      min-height:100vh;
      background:#CBDDE9 ;
      display:flex;
      justify-content:center;
      align-items:flex-start;
      padding-top:40px;
    }
    
    
    /* MAIN CONTAINER */
    
    .settings-container{
      width:100%;
      max-width:750px;
    }
    
    
    /* HEADER */
    
    .header{
      text-align:center;
      margin-bottom:30px;
    }
    
    .header h1{
      margin:10px 0 5px;
      font-size:28px;
      font-weight:700;
    }
    
    .header p{
      color:#666;
      font-size:14px;
    }
    
    
    /* BACK BUTTON */
    
    .back-btn{
      background:#FFFFFF;
      color:#5D89C2;
      border:none;
      padding:8px 15px;
      border-radius:6px;
      cursor:pointer;
      transition:.2s;
    }
    
    .back-btn:hover{
      background:#555;
    }
    
    
    /* SECTION CARDS */
    
    .section{
      background:white;
      padding:25px;
      margin-bottom:20px;
      border-radius:14px;
      box-shadow:0 10px 25px rgba(0,0,0,0.08);
      transition:all .25s ease;
    }
    
    .section:hover{
      transform:translateY(-4px);
      box-shadow:0 15px 35px rgba(0,0,0,0.12);
    }
    
    
    /* SECTION HEADER */
    
    .section-header{
      display:flex;
      align-items:center;
      gap:10px;
      margin-bottom:20px;
    }
    
    .section-header h3{
      margin:0;
      font-size:18px;
    }
    
    .icon{
      font-size:22px;
    }
    
    
    /* INFO ITEMS */
    
    .info-box{
      margin-bottom:15px;
    }
    
    .info-item{
      display:flex;
      justify-content:space-between;
      padding:10px 0;
      border-bottom:1px solid #eee;
    }
    
    .label{
      color:#666;
    }
    
    .value{
      font-weight:600;
    }
    
    
    /* BUTTONS */
    
    .action-btn{
      margin-top:15px;
      border:none;
      padding:10px 16px;
      border-radius:8px;
      font-weight:600;
      cursor:pointer;
      transition:.25s;
    }
    
    .edit-btn{
      background:#2872A1;
      color:white;
    }
    
    .edit-btn:hover{
      background:#2872A1;
    }
    
    .security-btn{
      background:#2872A1;
      color:white;
    }
    
    .security-btn:hover{
      background:#2872A1;
    }
    
    </style>