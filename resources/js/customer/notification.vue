<template>
    <div class="notifications-page">
      <section class="notifications-section">
        <h2>Notifications</h2>
  
        <div class="notifications-list">
          <NotificationItem
            v-for="note in notifications"
            :key="note.id"
            :note="note"
            @deleted="removeNotification"
            @read="markAsRead"
          />
  
          <p v-if="notifications.length === 0" class="no-notifs">
            No notifications yet.
          </p>
        </div>
      </section>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import api from '@/customerApi' // Axios instance with customer token
  import NotificationItem from '@/components/customer/notification/NotificationItem.vue'
  
  const notifications = ref([])
  
  // Fetch all notifications
  const fetchNotifications = async () => {
    try {
      const res = await api.get('/customer/notifications')
      notifications.value = res.data.notifications || []
    } catch (err) {
      console.error('Failed to load notifications:', err)
    }
  }
  
  // Remove notification locally after deletion
  const removeNotification = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }
  
  // Mark notification as read locally (optional call to API)
  const markAsRead = async (id) => {
    try {
      await api.patch(`/customer/notifications/${id}/read`)
      const note = notifications.value.find(n => n.id === id)
      if(note) note.read = true
    } catch (err) {
      console.error('Failed to mark notification as read:', err)
    }
  }
  
  onMounted(() => {
    fetchNotifications()
  })
  </script>
  
  <style scoped>
  .notifications-page {
    margin-left: 250px;
    padding: 80px 30px 30px 30px;
    min-height: 100vh;
    background-color: #f4f6f8;
  }
  h2 {
    margin-bottom: 20px;
    color: #007bff;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    text-align: center;
    border-color: black ;
   
    font-weight: 600;
  }
  
  .notifications-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .no-notifs {
    text-align: center;
    font-size: 1em;
    color: #6c757d;
    margin-top: 20px;
  }
  </style>