<template>
  <div class="card" :style="{backgroundColor: color}" @click="$emit('click')">
    <img :src="icon" class="icon" alt="icon">
    <h3>{{ title }}</h3>
  </div>
</template>

<script setup>
const props = defineProps({
  icon: String,
  title: String,
  color: String
})
</script>

<style scoped>
.card{
  width:180px;
  height:180px;
  border-radius:12px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  color:white;
  cursor:pointer;
  transition: all 0.2s ease;
}
.card:hover{
  transform: translateY(-5px);
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
}
.icon{
  width:50px;
  height:50px;
  margin-bottom:10px;
}
h3{
  text-align:center;
  font-size:16px;
}
</style>