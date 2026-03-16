<template>
    <div class="success-popup">
      <div class="success-popup-content">
        <div class="checkmark">✔</div>
        <p class="message">{{ message }}</p>
        <button class="ok-btn" @click="$emit('close')">OK</button>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({ message: String })
  defineEmits(['close'])
  </script>
  
  <style scoped>
  .success-popup {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  
  .success-popup-content {
    background: #fff;
    padding: 30px 40px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    width: 350px;
    max-width: 90%;
    animation: fadeIn 0.3s ease;
  }
  
  .checkmark {
    font-size: 50px;
    color: #28a745;
    margin-bottom: 20px;
    animation: popIn 0.3s ease;
  }
  
  .message {
    font-size: 16px;
    color: #333;
    margin-bottom: 20px;
  }
  
  .ok-btn {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    background-color: #007bff;
    color: #fff;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
  }
  
  .ok-btn:hover {
    background-color: #0056b3;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  @keyframes popIn {
    0% { transform: scale(0); }
    70% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }
  </style>