<template>
  <div class="popup-overlay">
    <div class="popup">
      <h2>Edit Customer</h2>

      <form @submit.prevent="handleUpdate">
        <input v-model="localForm.customer_name" placeholder="Full Name" />
        <span v-if="errors.customer_name" class="error-message">{{ errors.customer_name }}</span>

        <input v-model="localForm.email" type="email" placeholder="Email" />
        <span v-if="errors.email" class="error-message">{{ errors.email }}</span>

        <input v-model="localForm.meter_no" placeholder="Meter No." />
        <span v-if="errors.meter_no" class="error-message">{{ errors.meter_no }}</span>

        <select v-model="localForm.status">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <div>
          <button type="submit">Update</button>
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

const emit = defineEmits(['update', 'close'])

const localForm = reactive({ ...props.form })

watch(() => props.form, (newVal) => Object.assign(localForm, newVal), { deep: true, immediate: true })

function handleUpdate() {
  emit('update', { ...localForm })
}
</script>

<style scoped>
.popup-overlay { z-index: 9999; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; position: fixed; inset: 0; }
.popup { background: white; padding: 25px; border-radius: 12px; width: 380px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.popup input, .popup select { width: 90%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 8px; font-size: 15px; }
.popup button { margin-top: 12px; padding: 10px 18px; border: none; border-radius: 8px; background-color: #007bff; color: white; cursor: pointer; transition: 0.3s; font-size: 15px; }
.popup button:hover { background-color: #00a0ff; }
.close-btn { background-color: #ccc; color: #333; }
.close-btn:hover { background-color: #999; }
.error-message { color: red; font-size: 12px; margin-top: 3px; display: block; }
</style>