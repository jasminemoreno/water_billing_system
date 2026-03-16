<template>
    <div class="popup" v-if="show">
      <div class="popup-content">
        <svg viewBox="0 0 24 24" class="checkmark">
          <path fill="none" stroke="#fff" stroke-width="2" d="M4 12l6 6L20 6"/>
        </svg>
        <p>{{ message }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'
  
  const props = defineProps({
    message: { type: String, default: 'Success!' },
    duration: { type: Number, default: 2500 }
  })
  
  const show = ref(false)
  
  watch(() => props.message, () => {
    if (props.message) {
      show.value = true
      setTimeout(() => show.value = false, props.duration)
    }
  })
  </script>
  
  <style scoped>
  .popup {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 2000;
  }
  .popup-content {
    background: #28a745;
    color: white;
    padding: 15px 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    animation: fadeIn 0.3s ease forwards;
  }
  .checkmark {
    width: 24px;
    height: 24px;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  </style>