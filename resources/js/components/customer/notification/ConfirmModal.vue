<template>
    <div v-if="show" class="modal-overlay">
      <div class="modal-box">
        <p>{{ message }}</p>
        <div class="button-group">
          <button class="confirm-btn" @click="confirm">Yes</button>
          <button class="cancel-btn" @click="cancel">Cancel</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    show: Boolean,
    message: { type: String, default: 'Are you sure?' }
  })
  
  const emit = defineEmits(['confirm', 'cancel'])
  
  const confirm = () => emit('confirm')
  const cancel = () => emit('cancel')
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  .modal-box {
    background: white;
    padding: 25px 30px;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    text-align: center;
  }
  .modal-box p {
    font-size: 1.1rem;
    margin-bottom: 20px;
  }
  .button-group {
    display: flex;
    justify-content: center;
    gap: 15px;
  }
  .confirm-btn {
    background: #d9534f;
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    cursor: pointer;
  }
  .confirm-btn:hover { background: #c9302c; }
  .cancel-btn {
    background: #ccc;
    color: #333;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    cursor: pointer;
  }
  .cancel-btn:hover { background: #999; }
  </style>