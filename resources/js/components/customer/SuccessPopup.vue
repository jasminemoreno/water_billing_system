<template>
  <div v-if="show" class="success-popup" @click.self="close">
    <div class="popup-content">
      
      <span class="close" @click="close">&times;</span>

      <!-- ✅ ADD THIS WRAPPER -->
      <div class="icon-wrapper">
        <div class="icon-wrapper">
  <img src="@/assets/icons/check.png" class="success-icon" />
</div>
      </div>

      <!-- ✅ ADD TITLE -->
      <h3>Success!</h3>

      <!-- MESSAGE -->
      <p>{{ message }}</p>

    </div>
  </div>
</template>
  <script setup>
  import { defineProps, defineEmits } from "vue"
  
  const props = defineProps({
    show: Boolean,
    message: { type: String, default: "Success!" }
  })
  
  const emit = defineEmits(["close"])
  
  const close = () => emit("close")
  </script>
  
  <style scoped>
  .success-popup {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(6px);

  display: flex;
  justify-content: center;
  align-items: center;

  z-index: 9999;

  animation: fadeBg 0.25s ease;
}

/* POPUP BOX */
.popup-content {
  background: linear-gradient(145deg, #ffffff, #f7f9fc);
  padding: 30px 35px;
  border-radius: 18px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);

  text-align: center;
  width: 320px;

  position: relative;

  animation: popIn 0.35s ease;
}

/* ICON WRAPPER */
.icon-wrapper {
  width: 85px;
  height: 85px;
  margin: 0 auto 15px;

  background: rgba(40, 167, 69, 0.1);
  border-radius: 50%;

  display: flex;
  justify-content: center;
  align-items: center;

  box-shadow: 0 0 20px rgba(40, 167, 69, 0.25);
}

/* ICON */
.success-icon {
  font-size: 42px;
  color: #28a745;
  animation: pulse 1.2s infinite;
}

/* TITLE */
.popup-content h3 {
  margin: 10px 0 5px;
  font-size: 20px;
  font-weight: 700;
  color: #222;
}

/* MESSAGE */
.popup-content p {
  font-size: 15px;
  color: #555;
  margin: 0;
}

/* CLOSE BUTTON */
.close {
  position: absolute;
  top: 12px;
  right: 16px;
  font-size: 20px;
  cursor: pointer;
  color: #888;
  transition: 0.2s;
}

.close:hover {
  color: #28a745;
  transform: scale(1.2);
}

/* ANIMATIONS */
@keyframes popIn {
  from {
    transform: scale(0.7);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes fadeBg {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.15);
    opacity: 0.8;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
  </style>