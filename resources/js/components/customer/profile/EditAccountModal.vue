<template>
    <div class="popup" @click.self="$emit('close')">
    
      <div class="box">
    
        <div class="modal-header">
          <h2>Edit Account</h2>
          <span class="close-x" @click="$emit('close')">✕</span>
        </div>
    
        <div class="form-group">
          <label>Phone</label>
          <input v-model="phone" placeholder="Enter phone number"/>
        </div>
    
        <div class="form-group">
          <label>Email</label>
          <input v-model="email" placeholder="Enter email"/>
        </div>
    
        <div class="buttons">
          <button class="save-btn" @click="save">
            Save Changes
          </button>
    
          <button class="close-btn" @click="$emit('close')">
            Cancel
          </button>
        </div>
    
      </div>
    
    </div>
    </template>
    
    <script setup>
    import { ref } from "vue"
    import api from "@/customerApi"
    
    const props = defineProps({
      customer:Object
    })
    
    const emit = defineEmits(["close","updated"])
    
    const phone = ref(props.customer.phone)
    const email = ref(props.customer.email)
    
    async function save(){
    
    const res = await api.post("/customer/settings/update-account",{
      phone: phone.value,
      email: email.value
    })
    
    if(res.data.success){
      alert("Account updated successfully")
      emit("updated")
      emit("close")
    }
    
    }
    </script>
    
    <style scoped>
    
    /* BACKGROUND OVERLAY */
    
    .popup{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.55);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:999;
    animation:fadeIn .25s ease;
    }
    
    
    /* MODAL BOX */
    
    .box{
    background:white;
    padding:30px;
    border-radius:14px;
    width:420px;
    box-shadow:0 15px 40px rgba(0,0,0,0.25);
    animation:slideUp .25s ease;
    }
    
    
    /* HEADER */
    
    .modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    }
    
    .modal-header h2{
    margin:0;
    font-size:22px;
    }
    
    .close-x{
    cursor:pointer;
    font-size:18px;
    color:#888;
    }
    
    .close-x:hover{
    color:#333;
    }
    
    
    /* FORM */
    
    .form-group{
    margin-bottom:18px;
    display:flex;
    flex-direction:column;
    }
    
    .form-group label{
    font-weight:600;
    margin-bottom:6px;
    color:#555;
    }
    
    .form-group input{
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ddd;
    font-size:14px;
    transition:all .2s ease;
    }
    
    .form-group input:focus{
    outline:none;
    border-color:#4a90e2;
    box-shadow:0 0 0 3px rgba(74,144,226,0.15);
    }
    
    
    /* BUTTONS */
    
    .buttons{
    margin-top:15px;
    display:flex;
    flex-direction:column;
    gap:10px;
    }
    
    .save-btn{
    background:#2872A1;
    border:none;
    color:#FFFFFF;
    padding:12px;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
    }
    
    .save-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 15px rgba(0,0,0,0.15);
    }
    
    .close-btn{
    background:#f1f1f1;
    border:none;
    padding:12px;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
    }
    
    .close-btn:hover{
    background:#e4e4e4;
    }
    
    
    /* ANIMATIONS */
    
    @keyframes fadeIn{
    from{opacity:0}
    to{opacity:1}
    }
    
    @keyframes slideUp{
    from{
    opacity:0;
    transform:translateY(20px);
    }
    to{
    opacity:1;
    transform:translateY(0);
    }
    }
    
    </style>