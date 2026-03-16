<template>
    <div class="setting-section">
      <h3>{{ title }}</h3>
      <div class="info-box">
        <p v-for="(item, index) in info" :key="index">
          <strong>{{ item.label }}:</strong> {{ item.value || 'N/A' }}
        </p>
      </div>
      <slot name="action"></slot>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    title: String,
    info: Array
  });
  </script>
  
  <style scoped>
  .setting-section {
    margin-top: 25px;
  }
  
  .setting-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
  }
  
  .info-box {
    background: #f7f7f7;
    padding: 15px;
    border-radius: 10px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
  }
  </style>