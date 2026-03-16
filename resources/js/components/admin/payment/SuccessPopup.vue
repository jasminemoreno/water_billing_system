<template>

  <div class="payment-success-popup" style="display:flex">
  
  <div class="payment-success-popup-content">
  
  <div class="checkmark">✔</div>
  
  <p>{{message}}</p>
  
  <button class="payment-close-btn" @click="$emit('close')">
  OK
  </button>
  
  </div>
  
  </div>
  
  </template>
  
  <script setup>
  
  defineProps({
  message:String
  })
  
  defineEmits(["close"])
  
  </script>
  <style scoped>

  .payment-success-popup{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.4);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:9999;
  }
  
  .payment-success-popup-content{
  background:white;
  padding:30px 40px;
  border-radius:15px;
  text-align:center;
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
  min-width:280px;
  }
  
  .checkmark{
  font-size:48px;
  color:#28a745;
  margin-bottom:12px;
  }
  .payment-close-btn{
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  .payment-success-popup-content p{
  font-size:17px;
  color:#333;
  font-weight:500;
  }
  
  </style>