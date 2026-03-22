<template>
  <div class="admin-profile-page page-container">

    <!-- ACCOUNT CARD -->
    <div class="account-card">
      <!-- Back Button -->
      <button class="back-btn" @click="goBack">
        <span class="arrow">←</span> Back
      </button>

      <!-- Profile Avatar -->
      <div class="avatar-container">
        <img :src="adminProfilePhoto" alt="Profile Photo" class="avatar-img" />
      </div>

      <!-- Basic Info -->
      <div class="basic-info">
        <h2 class="admin-name">{{ admin.fullname }}</h2>
        <p class="admin-role">Administrator</p>
        <p class="admin-contact">{{ admin.phone || 'No Contact' }}</p>
      </div>

      <!-- Profile Details -->
      <div class="details-header">
        <h3>Profile Information</h3>
        <button class="edit-btn" @click="showEdit = true">Edit</button>
      </div>
      <div class="details-vertical">
        <p><strong>Full Name:</strong> {{ admin.fullname }}</p>
        <p><strong>Phone:</strong> {{ admin.phone || 'N/A' }}</p>
        <p><strong>Gender:</strong> {{ admin.gender || 'N/A' }}</p>
        <p><strong>Birth Date:</strong> {{ admin.birthdate || 'N/A' }}</p>
      </div>
    </div>

    <!-- Edit Popup Component -->
    <EditProfilePopup
      :visible="showEdit"
      :admin="admin"
      @close="showEdit = false"
      @success="handleProfileUpdate"
    />

    <!-- Toast -->
    <div class="toast" v-if="toastMessage">{{ toastMessage }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/api.js'; // use api.js with withCredentials
import EditProfilePopup from '@/components/admin/profile/EditProfilePopup.vue';

const showEdit = ref(false);
const toastMessage = ref('');
const admin = ref({
  id: null,
  fullname: '',
  phone: '',
  gender: '',
  birthdate: '',
  profile_photo: ''
});

const adminProfilePhoto = computed(() => {
  return admin.value.profile_photo
    ? `/storage/profile_photos/${admin.value.profile_photo}`
    : '/assets/default-avatar.png';
});

const fetchAdminProfile = async () => {
  try {
    const response = await api.get('/admin/profile'); // no token header needed
    admin.value = response.data;
  } catch (error) {
    console.error(error);
    alert('Failed to fetch profile.');
  }
};

const handleProfileUpdate = (message, updatedAdmin) => {
  toastMessage.value = message;
  if (updatedAdmin) admin.value = updatedAdmin;
  setTimeout(() => (toastMessage.value = ''), 3000);
};

const goBack = () => history.back();

onMounted(fetchAdminProfile);
</script>

<style scoped>
.admin-profile-page {
  margin-top: 50px;
  display: flex;
  justify-content: center;
  padding: 60px 20px;
  background: #f9f9f9;
  min-height: 100vh;
  font-family: "Inter", "Segoe UI", sans-serif;
}

.account-card {
  position: relative;
  background: #fff;
  padding: 40px 35px;
  border-radius: 20px;
  width: 420px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s, box-shadow 0.3s;
}

.account-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
}

.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  background: #007bff;
  border: none;
  color: #fff;
  padding: 8px 14px;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
}

.back-btn:hover {
  background: #0056b3;
  transform: scale(1.05);
}

.back-btn .arrow {
  font-weight: bold;
  font-size: 18px;
}

.avatar-container {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.avatar-img {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #007bff;
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.25);
  transition: transform 0.3s;
}

.avatar-img:hover {
  transform: scale(1.05);
}

.basic-info {
  text-align: center;
  margin-bottom: 30px;
}

.admin-name {
  font-size: 22px;
  font-weight: 700;
  color: #333;
  margin-bottom: 5px;
}

.admin-role {
  font-size: 15px;
  color: #555;
  margin-bottom: 5px;
}

.admin-contact {
  font-size: 14px;
  color: #777;
}

.details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  margin-bottom: 10px;
}

.details-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.details-vertical {
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #f7f8fa;
  padding: 20px;
  border-radius: 12px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.details-vertical p {
  margin: 0;
  font-size: 15px;
  color: #444;
}

.details-vertical strong {
  color: #333;
}

.edit-btn {
  background: linear-gradient(90deg, #007bff, #0056b3);
  color: #fff;
  border: none;
  padding: 8px 20px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
  transition: all 0.25s ease;
}

.edit-btn:hover {
  background: linear-gradient(90deg, #0056b3, #003f7f);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.35);
}

.toast {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #28a745;
  color: white;
  padding: 14px 28px;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  font-size: 15px;
  font-weight: 500;
  animation: fadeInOut 3s forwards;
}

@keyframes fadeInOut {
  0% { opacity: 0; transform: translateY(20px); }
  10% { opacity: 1; transform: translateY(0); }
  90% { opacity: 1; transform: translateY(0); }
  100% { opacity: 0; transform: translateY(20px); }
}
</style>