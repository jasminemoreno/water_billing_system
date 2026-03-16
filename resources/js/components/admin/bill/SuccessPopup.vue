<template>
    <div class="popup-overlay">
      <div class="popup">
        <h3>Success!</h3>
        <p>{{ message }}</p>
        <button @click="$emit('close')">OK</button>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    message: String
  })
  const emit = defineEmits(['close'])
  </script>
  
  <style scoped>
  .popup-overlay {
    z-index: 9999;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    inset: 0;
  }
  .popup {
    background: white;
    padding: 25px;
    border-radius: 12px;
    width: 320px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  }
  .popup h3 { margin-bottom: 10px; color: #00b894; }
  .popup p { font-size: 14px; margin-bottom: 20px; }
  .popup button {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  .popup button:hover { background-color: #00a0ff; }
  </style>