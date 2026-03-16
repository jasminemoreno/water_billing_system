<template>
  <div class="report-card" :style="{ backgroundColor: color }" @click="$emit('click')">
    <img :src="icon" :alt="title" />
    <h3>{{ title }}</h3>
    <!-- Display stat number -->
    <p v-if="stat !== undefined" class="stat">{{ stat }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  icon: String,
  title: String,
  stat: [Number, String],   // NEW: prop to show summary/stat
  color: {
    type: String,
    default: '#00bcd4' // default color if none provided
  }
})
</script>

<style scoped>
.report-card {
  color: #001f3f;
  border-radius: 15px;
  width: 300px;
  height: 400px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
  border: 3px solid transparent;
}

.report-card:hover {
  transform: translateY(-8px);
  border: 3px solid #007bff;
}

.report-card img {
  width: 60px;
  height: 60px;
  margin-bottom: 12px;
}

.report-card h3 {
  font-size: 1.05rem;
  font-weight: 700;
  text-align: center;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

/* Stat number styling */
.stat {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin-top: 8px;
}
</style>