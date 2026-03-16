<template>
    <div class="dashboard-box" @click="goTo">
      <h3>{{ title }}</h3>
      <p>{{ formattedValue }}</p>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { useRouter } from 'vue-router'
  
  const props = defineProps({
    title: String,
    value: [Number, String],
    route: String,
    isMoney: {
      type: Boolean,
      default: false
    }
  })
  
  const router = useRouter()
  
  const formattedValue = computed(() => {
    if (props.isMoney) {
      return '₱' + Number(props.value).toLocaleString(undefined, { minimumFractionDigits: 2 })
    }
    return props.value
  })
  
  const goTo = () => {
    if (props.route) {
      router.push(props.route)
    }
  }
  </script>

<style scoped>
.dashboard-box {
  flex: 1;
  background: white;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s;
  cursor: pointer;
}

.dashboard-box:hover {
  transform: translateY(-5px);
}

.dashboard-box h3 {
  color: #007bff;
  font-size: 17px;
  margin-bottom: 10px;
}

.dashboard-box p {
  font-size: 28px;
  color: #004b9a;
  font-weight: 700;
}
</style>