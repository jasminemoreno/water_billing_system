<template>
    <div :class="['notification-item', note.read ? '' : 'unread']">
      <!-- Icon -->
      <div class="icon">
        <img v-if="note.type === 'bill_created'" :src="billIcon" alt="Bill Created" />
        <img v-else-if="note.type === 'payment_status' && approved" :src="approvedIcon" alt="Approved" />
        <img v-else-if="note.type === 'payment_status' && rejected" :src="rejectedIcon" alt="Rejected" />
        <img v-else :src="bellIcon" alt="Notification" />
      </div>
  
      <!-- Content -->
      <div class="content">
        <p class="message">{{ note.message }}</p>
        <span class="date">{{ formattedDate }}</span>
      </div>
  
      <!-- Delete button -->
      <button class="delete-btn" @click="openConfirm">
        <img :src="trashIcon" alt="Delete Notification" />
      </button>
  
      <!-- Confirmation Modal -->
      <ConfirmModal
        v-if="showConfirm"
        :show="showConfirm"
        message="Are you sure you want to delete this notification?"
        @confirm="deleteNotif"
        @cancel="cancelDelete"
      />
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  import api from '@/customerApi'
  
  // ✅ Import ConfirmModal
  import ConfirmModal from '@/components/customer/notification/ConfirmModal.vue'
  
  // Props & emits
  const props = defineProps({ note: Object })
  const emit = defineEmits(['deleted'])
  
  // Icons
  import billIconImg from '@/assets/icons/bill.png'
  import approvedIconImg from '@/assets/icons/check.png'
  import rejectedIconImg from '@/assets/icons/cross.png'
  import bellIconImg from '@/assets/icons/bell.png'
  import trashIconImg from '@/assets/icons/delete.png'
  
  const billIcon = billIconImg
  const approvedIcon = approvedIconImg
  const rejectedIcon = rejectedIconImg
  const bellIcon = bellIconImg
  const trashIcon = trashIconImg
  
  // Computed
  const approved = computed(() => props.note.message.toLowerCase().includes('approved'))
  const rejected = computed(() => props.note.message.toLowerCase().includes('rejected'))
  const formattedDate = computed(() => new Date(props.note.created_at).toLocaleString())
  
  // Modal state
  const showConfirm = ref(false)
  const openConfirm = () => (showConfirm.value = true)
  const cancelDelete = () => (showConfirm.value = false)
  
  // Delete notification
  const deleteNotif = async () => {
    try {
      await api.delete(`/customer/notifications/${props.note.id}`)
      emit('deleted', props.note.id)
    } catch (err) {
      console.error('Failed to delete notification:', err)
      alert('Failed to delete notification.')
    } finally {
      showConfirm.value = false
    }
  }
  </script>
  
  <style scoped>
  .notification-item {
    display: flex;
    align-items: flex-start;
    background-color: #fff;
    padding: 18px 22px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  
  .notification-item.unread {
    background-color: #f0f8ff;
    border-left: 5px solid #007bff;
  }
  
  .icon {
    margin-right: 18px;
    flex-shrink: 0;
  }
  .icon img {
    width: 28px;
    height: 28px;
    object-fit: contain;
  }
  
  .content {
    flex: 1;
  }
  .message {
    margin: 0;
    font-size: 1rem;
    color: #333;
    font-weight: 500;
  }
  .date {
    display: block;
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 4px;
  }
  
  .delete-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: transform 0.2s ease;
  }
  .delete-btn img {
    width: 20px;
    height: 20px;
    object-fit: contain;
  }
  .delete-btn:hover {
    transform: scale(1.2);
  }
  
  .notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
  }
  </style>