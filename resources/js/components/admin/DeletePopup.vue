<template>
  <div class="delete-popup-overlay">
    <div class="delete-popup-box">
      <h2>Delete User?</h2>
      <p>
        Are you sure you want to delete
        <strong>{{ customerName }}</strong>?
        <br>
        This action cannot be undone. All related bills and payments will also be removed.
      </p>
      <div class="delete-popup-buttons">
        <button type="button" class="delete-confirm" @click="$emit('confirm')">
          Delete
        </button>
        <button type="button" class="delete-cancel" @click="$emit('cancel')">
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  customerName: {
    type: String,
    default: 'this user'
  }
})
</script>

<style scoped>
.delete-popup-overlay {
  display: flex;
  position: fixed;
  inset: 0;
  justify-content: center;
  align-items: center;
  background: rgba(0,0,0,0.3);
  z-index: 999;
}
.delete-popup-box {
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  width: 400px;
  text-align: center;
}
.delete-popup-buttons {
  margin-top: 20px;
  display: flex;
  justify-content: space-around;
}
.delete-confirm {
  background-color: #ff4d4d;
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}
.delete-cancel {
  background-color: #ccc;
  color: #333;
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}
</style>