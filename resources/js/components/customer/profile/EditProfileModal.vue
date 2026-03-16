<template>
    <div class="modal" @click.self="$emit('close')">
      <div class="modal-content">
        <span class="close-btn" @click="$emit('close')">&times;</span>
        <h3>Edit Profile</h3>
  
        <form @submit.prevent="submitProfile" class="form-container">
          <!-- Avatar -->
          <div class="avatar-edit-wrapper">
            <img :src="avatarPreview" class="avatar-preview" />
            <input type="file" @change="onFileChange" class="file-input" />
          </div>
  
          <!-- Full Name -->
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" v-model="form.customer_name" placeholder="Enter full name" required />
          </div>
  
          <!-- Phone -->
          <div class="form-group">
            <label>Phone</label>
            <input type="text" v-model="form.phone" placeholder="Enter phone number" />
          </div>
  
          <!-- Gender -->
          <div class="form-group">
            <label>Gender</label>
            <select v-model="form.gender">
              <option value="">Select...</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
  
          <!-- Birth Date -->
          <div class="form-group">
            <label>Birth Date</label>
            <input type="date" v-model="form.birth" />
          </div>
  
          <!-- Save Button -->
          <button type="submit" class="save-btn">Save Changes</button>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, watch, toRaw } from 'vue'
  import api from '@/customerApi'
  
  const props = defineProps({ customer: Object })
  const emit = defineEmits(['close','updated','success'])
  
  const form = reactive({ ...props.customer, avatar: null })
  const avatarPreview = ref(props.customer.avatar_url)
  
  watch(() => props.customer, (val) => {
    Object.assign(form, toRaw(val))
    avatarPreview.value = val.avatar_url
  })
  
  function onFileChange(e){
    if(e.target.files[0]){
      form.avatar = e.target.files[0]
      avatarPreview.value = URL.createObjectURL(e.target.files[0])
    }
  }
  
  async function submitProfile(){
    const formData = new FormData()
    Object.keys(form).forEach(k => formData.append(k, form[k]))
  
    try{
      const res = await api.post('/customer/profile/update', formData)
      if(res.data.success){
        emit('updated', res.data.customer)
        emit('success', 'Profile updated successfully!') // <-- emit success
      }
    } catch(err){
      console.error(err)
      emit('success', 'Failed to update profile.')
    }
  }
  </script>
  
  <style scoped>
  /* Modal Background */
  .modal {
    display: flex;
    position: fixed;
    z-index: 1000;
    left:0; top:0; width:100%; height:100%;
    background: rgba(0,0,0,0.65);
    justify-content:center; align-items:center;
    padding: 15px;
  }
  
  /* Modal Card */
  .modal-content {
    width: 100%;
    max-width: 500px;
    background: #ffffff;
    border-radius: 20px;
    padding: 30px 25px;
    position: relative;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.3s ease-in-out;
  }
  
  /* Close Button */
  .close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: #555;
    transition: color 0.2s;
  }
  .close-btn:hover {
    color: #6c63ff;
  }
  
  /* Form Container */
  .form-container {
    display: flex;
    flex-direction: column;
    gap: 18px;
    margin-top: 10px;
  }
  
  /* Avatar */
  .avatar-edit-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 15px;
  }
  .avatar-preview {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 4px solid #6c63ff;
    object-fit: cover;
    margin-bottom: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }
  .file-input {
    cursor: pointer;
    color: #6c63ff;
    font-weight: 600;
    transition: color 0.2s;
  }
  .file-input:hover {
    color: #574bff;
  }
  
  /* Form Inputs */
  .form-group {
    display: flex;
    flex-direction: column;
  }
  .form-group label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
  }
  .form-group input,
  .form-group select {
    padding: 12px 15px;
    border-radius: 12px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border 0.2s, box-shadow 0.2s;
  }
  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: #6c63ff;
    box-shadow: 0 0 8px rgba(108,99,255,0.3);
  }
  
  /* Save Button */
  .save-btn {
    width: 100%;
    padding: 14px 0;
    background: linear-gradient(135deg,#6c63ff,#9a85ff);
    color: white;
    font-weight: 600;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s ease, transform 0.2s ease;
  }
  .save-btn:hover {
    background: linear-gradient(135deg,#574bff,#7f6eff);
    transform: translateY(-2px);
  }
  
  /* Animations */
  @keyframes fadeIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  </style>