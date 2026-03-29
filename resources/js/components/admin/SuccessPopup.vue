<template>
  <div class="success-popup-overlay">
    <div class="success-popup" :class="type">
      
      <!-- ICON -->
      <div class="icon">
        <span v-if="type === 'success'">✅</span>
        <span v-else-if="type === 'warning'">⚠️</span>
        <span v-else-if="type === 'error'">❌</span>
      </div>

      <!-- MESSAGE -->
      <p>{{ message }}</p>

      <!-- BUTTONS -->
      <div v-if="isConfirm" class="button-group">
        <button class="confirm-btn" @click="handleConfirm">
          {{ type === 'warning' ? 'Continue' : 'Yes' }}
        </button>
        <button class="cancel-btn" @click="$emit('close')">Cancel</button>
      </div>
      <button v-else class="ok-btn" @click="$emit('close')">OK</button>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  message: { type: String, required: true },
  isConfirm: { type: Boolean, default: false },
  type: { type: String, default: 'success' } // success | warning | error
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
  min-width: 320px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  border-top: 6px solid #22c55e; /* default green */
}

/* TYPES */
.success-popup.success { border-top-color: #22c55e; }
.success-popup.warning { border-top-color: #facc15; }
.success-popup.error   { border-top-color: #ef4444; }

/* ICON */
.icon { font-size: 30px; margin-bottom: 10px; }

/* MESSAGE */
.success-popup p { margin-bottom: 20px; font-size: 16px; }

/* BUTTONS */
.button-group {
  display: flex;
  justify-content: center;
  gap: 15px;
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

/* Confirm Button */
.confirm-btn {
  background-color: #007bff;
  color: white;
}
.confirm-btn:hover { background-color: #00a0ff; }

/* Cancel Button */
.cancel-btn {
  background-color: #e5e7eb;
  color: #111;
}
.cancel-btn:hover { background-color: #d1d5db; }

/* OK Button */
.ok-btn {
  background-color: #007bff;
  color: white;
}
.ok-btn:hover { background-color: #00a0ff; }
</style>