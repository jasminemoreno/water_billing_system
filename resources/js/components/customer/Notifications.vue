<template>
    <div class="notifications-card">
      <h3>Quick Notifications</h3>
  
      <div v-if="currentBill" class="notification-item yellow">
        <p class="title">New Bill Available</p>
        <p class="desc">
          Your bill for the month of ----  is ready.<br>
          Due Date: {{ formatDate(currentBill.due_date) }}<br>
          Disconnection Date: {{ formatDate(currentBill.disconnection_date) }}
        </p>
      </div>
  
      <div v-if="!lastPayment" class="notification-item blue">
        <p class="title">No Payments Yet</p>
        <p class="desc">You have not made any payments.</p>
      </div>
  
      <div v-else class="notification-item blue">
        <p class="title">Last Payment Received</p>
        <p class="desc">{{ formatDate(lastPayment.created_at) }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    currentBill: Object,
    lastPayment: Object
  })
  
  const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('en-US', {
      month: 'short',
      day: '2-digit',
      year: 'numeric'
    })
  }
  </script>
  
  <style scoped>
  .notifications-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  }
  
  .notifications-card h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #2c3e50;
  }
  
  .notification-item {
    background-color: #fdfdfd;
    border-left: 5px solid;
    border-radius: 10px;
    padding: 12px 15px;
    margin-bottom: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  
  .notification-item.yellow { border-color: #B2965F; background-color: #FFEDCD; }
  .notification-item.blue { border-color: #075B93; background-color: #B4EEF2; }
  
  .notification-item .title { font-weight: 600; color: #2c3e50; margin-bottom: 4px; }
  .notification-item .desc { font-size: 0.9rem; color: #2c3e50; line-height: 1.3; }
  
  .notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  </style>