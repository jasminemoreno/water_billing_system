<template>
    <div class="profile-box" v-if="profileLoaded">
      <!-- Profile Header -->
      <div class="profile-header">
        <button class="back-btn" @click="goBack">← Go Back</button>
  
        <div class="avatar-wrapper">
          <img :src="profile.avatar_url" class="avatar-img" />
        </div>
        <h2>{{ profile.customer_name }}</h2>
        <p class="subtitle">Customer Profile</p>
      </div>
  
      <!-- Profile Details -->
      <div class="profile-details">
        <div class="detail-item">
          <span class="label">Phone:</span>
          <span class="value">{{ profile.phone || 'Not set' }}</span>
        </div>
        <div class="detail-item">
          <span class="label">Gender:</span>
          <span class="value">{{ profile.gender || 'Not set' }}</span>
        </div>
        <div class="detail-item">
          <span class="label">Birth Date:</span>
          <span class="value">{{ profile.birth || 'Not set' }}</span>
        </div>
      </div>
  
      <!-- Edit Button -->
      <button class="edit-btn" @click="showModal = true">Edit Profile</button>
  
      <!-- Edit Profile Modal -->
      <EditProfileModal
        v-if="showModal"
        :customer="profile"
        @close="showModal = false"
        @updated="handleProfileUpdate"
        @success="handleSuccess"
      />
  
      <!-- Success Popup -->
      <SuccessPopup v-if="successMessage" :message="successMessage" />
    </div>
  
    <!-- Loading State -->
    <div v-else class="loading-container">
      Loading profile...
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from "vue"
  import api from "@/customerApi"
  import EditProfileModal from "@/components/customer/profile/EditProfileModal.vue"
  import SuccessPopup from "@/components/customer/profile/SuccessPopup.vue"
  
  const profile = reactive({})
  const showModal = ref(false)
  const profileLoaded = ref(false)
  const successMessage = ref("")
  
  // Load profile on mount
  onMounted(async () => {
    await loadProfile()
  })
  
  async function loadProfile() {
    try {
      const res = await api.get("/customer/profile")
      if (res.data.success) {
        Object.assign(profile, res.data.customer)
        profileLoaded.value = true
      }
    } catch (err) {
      console.error("Profile API error:", err)
      alert("Failed to load profile. Make sure you are logged in.")
    }
  }
  
  // Handle profile update from modal
  function handleProfileUpdate(updatedProfile) {
    Object.assign(profile, updatedProfile)
    showModal.value = false
  }
  
  // Handle success popup from modal
  function handleSuccess(msg) {
    successMessage.value = msg
    setTimeout(() => successMessage.value = "", 3000) // auto-hide after 3s
  }
  
  // Go back
  function goBack() {
    if (window.history.length > 1) {
      window.history.back()
    } else {
      window.location.href = "/customer/dashboard"
    }
  }
  </script>
  
  <style scoped>
  /* Container */
  .profile-box {
    width: 600px;
    margin: 50px auto;
    background: #CBDDE9;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  
  /* Profile Header */
  .profile-header {
    text-align: center;
    background: #2872A1;
    padding: 60px 20px 40px 20px;
    color: white;
    position: relative;
  }
  
  /* Back Button inside Header */
  .back-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #FFFFFF;
    color:  #5D89C2;
    font-weight: 600;
    padding: 10px 15px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: background 0.3s ease, transform 0.2s ease;
  }
  .back-btn:hover {
    background: rgba(255,255,255,1);
    transform: translateY(-2px);
  }
  
  /* Avatar */
  .avatar-wrapper {
    width: 160px;
    height: 160px;
    margin: 0 auto 15px auto;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.6);
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
  }
  .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  /* Header Text */
  .profile-header h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
  }
  .profile-header .subtitle {
    margin-top: 5px;
    font-size: 16px;
    font-weight: 400;
    opacity: 0.85;
  }
  
  /* Profile Details */
  .profile-details {
    padding: 30px 40px;
    background: #CBDDE9;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  .detail-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .detail-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08);
  }
  .detail-item .label {
    font-weight: 600;
    color: #555;
  }
  .detail-item .value {
    font-weight: 500;
    color: #333;
  }
  
  /* Edit Button */
  .edit-btn {
    display: block;
    width: calc(100% - 80px);
    margin: 25px auto 30px auto;
    background: #2872A1;
    color: white;
    padding: 15px 0;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }
  .edit-btn:hover {
    background: #574bff;
    transform: translateY(-2px);
  }
  
  /* Loading state */
  .loading-container {
    text-align: center;
    font-size: 18px;
    margin-top: 100px;
    color: #555;
  }
  </style>