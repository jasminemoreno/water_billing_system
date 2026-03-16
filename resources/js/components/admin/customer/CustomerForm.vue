<template>
  <div class="popup-overlay">
    <div class="popup">
      <h2>Add Customer</h2>

      <form @submit.prevent="handleSave">
        <input v-model="localForm.customer_name" placeholder="Full Name" />
        <span v-if="errors.customer_name" class="error-message">{{ errors.customer_name[0] }}</span>

        <input v-model="localForm.email" type="email" placeholder="Email" />
        <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>

        <input v-model="localForm.meter_no" placeholder="Meter No." />
        <span v-if="errors.meter_no" class="error-message">{{ errors.meter_no[0] }}</span>

        <select v-model="localForm.status">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <div>
          <button type="submit">Save</button>
          <button type="button" class="close-btn" @click="$emit('close')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  form: Object,
  errors: Object
})
const emit = defineEmits(['save', 'close'])

// Local copy to avoid mutating parent directly
const localForm = reactive({ ...props.form })

watch(
  () => props.form,
  (newVal) => Object.assign(localForm, newVal),
  { deep: true, immediate: true }
)

function handleSave() {
  emit('save', { ...localForm })
}
</script>

<style scoped>
.popup-overlay {
  z-index: 9999;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  inset: 0;
}
.popup {
  background: white;
  padding: 25px;
  border-radius: 12px;
  width: 380px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
.popup h2 { color: #007bff; margin-bottom: 15px; }
.popup input, .popup select {
  width: 90%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
}
.popup button {
  margin-top: 12px;
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  transition: 0.3s;
  font-size: 15px;
}
.close-btn { background-color: #ccc; color: #333; }
.close-btn:hover { background-color: #999; }
.error-message { color: red; font-size: 12px; margin-top: 3px; display: block; }
</style>