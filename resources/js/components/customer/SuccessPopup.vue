<template>
    <div v-if="show" class="success-popup">
      <div class="popup-content">
        <span class="close" @click="close">&times;</span>
        <i class="fas fa-check-circle success-icon"></i>
        <p>{{ message }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from "vue"
  
  const props = defineProps({
    show: Boolean,
    message: { type: String, default: "Success!" }
  })
  
  const emit = defineEmits(["close"])
  
  const close = () => emit("close")
  </script>
  
  <style scoped>
  .success-popup {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  
  .popup-content {
    background: #ffffff;
    padding: 25px 30px;
    border-radius: 14px;
    box-shadow: 0 14px 28px rgba(0,0,0,0.25);
    text-align: center;
    max-width: 400px;
    position: relative;
  }
  
  .success-icon {
    font-size: 48px;
    color: #28a745;
    margin-bottom: 15px;
  }
  
  .popup-content p {
    font-size: 16px;
    color: #333;
  }
  
  .close {
    position: absolute;
    top: 12px;
    right: 18px;
    font-size: 22px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s ease;
  }
  
  .close:hover {
    color: #28a745;
    transform: scale(1.2);
  }
  </style>