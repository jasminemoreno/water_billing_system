<template>
  <div class="success-popup-overlay">
    <div class="success-popup">

      <!-- Message -->
      <p>{{ message }}</p>

      <!-- Confirm Buttons (optional) -->
      <div v-if="isConfirm" class="button-group">
        <button @click="handleConfirm">Yes</button>
        <button @click="$emit('close')">Cancel</button>
      </div>

      <!-- OK Button for normal success -->
      <button v-else @click="$emit('close')">OK</button>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  message: { type: String, required: true },
  isConfirm: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'confirm'])

function handleConfirm() {
  emit('confirm')
}
</script>

<style scoped>
.success-popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.success-popup {
  background: white;
  padding: 25px;
  border-radius: 12px;
  min-width: 300px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.success-popup p {
  margin-bottom: 20px;
  font-size: 16px;
}

.success-popup button {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  font-size: 14px;
}

.success-popup button:hover {
  background-color: #00a0ff;
}

/* Confirm button group */
.button-group {
  display: flex;
  justify-content: center;
  gap: 15px;
}
</style>