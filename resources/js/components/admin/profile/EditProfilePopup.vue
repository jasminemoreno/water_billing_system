<template>
  <div class="popup-overlay" v-if="visible">
    <div class="popup">
      <h2>Edit Profile</h2>
      <form @submit.prevent="updateProfile" enctype="multipart/form-data">

        <label>Full Name</label>
        <input type="text" v-model="form.fullname" required />

        <label>Phone Number</label>
        <input type="text" v-model="form.phone" placeholder="Optional" />

        <label>Gender</label>
        <select v-model="form.gender" required>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <label>Birth Date</label>
        <input type="date" v-model="form.birthdate" />

        <label>Profile Photo</label>
        <input type="file" @change="handleFileUpload" accept="image/*" />

        <div class="popup-buttons">
          <button type="submit" class="save-btn">Save Changes</button>
          <button type="button" class="cancel-btn" @click="$emit('close')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
import api from '@/api.js'; // use api.js

const props = defineProps({
  visible: Boolean,
  admin: Object
});
const emit = defineEmits(['close', 'success']);

const form = reactive({
  fullname: '',
  phone: '',
  gender: '',
  birthdate: '',
  profile_photo: null
});

watch(() => props.admin, (newAdmin) => {
  form.fullname = newAdmin.fullname || '';
  form.phone = newAdmin.phone || '';
  form.gender = newAdmin.gender || '';
  form.birthdate = newAdmin.birthdate || '';
  form.profile_photo = null;
}, { immediate: true });

const handleFileUpload = (e) => {
  form.profile_photo = e.target.files[0];
};

const updateProfile = async () => {
  const data = new FormData();
  data.append('fullname', form.fullname);
  data.append('phone', form.phone);
  data.append('gender', form.gender);
  data.append('birthdate', form.birthdate);
  if (form.profile_photo) data.append('profile_photo', form.profile_photo);

  try {
    const response = await api.post('/admin/profile/update', data); // cookie auth
    emit('success', response.data.message || 'Profile updated successfully', response.data.admin);
    emit('close');
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || 'Failed to update profile');
  }
};
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  top:0; left:0; right:0; bottom:0;
  background: rgba(0,0,0,0.5);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:1000;
}
.popup {
  background:white;
  padding:30px 25px;
  border-radius:12px;
  width:420px;
  box-shadow:0 15px 40px rgba(0,0,0,0.2);
  font-family:"Inter","Segoe UI",sans-serif;
}
.popup h2 {
  margin-bottom:20px;
  color:#007bff;
  text-align:center;
}
.popup label {
  display:block;
  margin-top:12px;
  font-weight:600;
  color:#333;
  font-size:14.5px;
}
.popup input[type="text"],
.popup input[type="date"],
.popup select,
.popup input[type="file"] {
  width:100%;
  padding:10px 12px;
  margin-top:5px;
  border-radius:8px;
  border:1px solid #ccc;
  font-size:14.5px;
}
.popup-buttons {
  margin-top:20px;
  display:flex;
  justify-content:flex-end;
  gap:10px;
}
.save-btn {
  background:#007bff;
  color:white;
  border:none;
  padding:10px 22px;
  border-radius:8px;
  font-weight:600;
  cursor:pointer;
}
.cancel-btn {
  background:#ccc;
  color:#333;
  border:none;
  padding:10px 20px;
  border-radius:8px;
  cursor:pointer;
}
</style>