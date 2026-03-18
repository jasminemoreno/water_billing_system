<template>
  <div class="popup-overlay">
    <div class="popup">

      <h2>{{ mode === 'add' ? 'Add Customer' : 'Edit Customer' }}</h2>

      <form @submit.prevent="handleSubmit">

        <input v-model="localForm.customer_name" placeholder="Full Name" />
        <span v-if="errors.customer_name" class="error-message">
          {{ getError(errors.customer_name) }}
        </span>

        <input v-model="localForm.email" type="email" placeholder="Email" />
        <span v-if="errors.email" class="error-message">
          {{ getError(errors.email) }}
        </span>

        <input v-model="localForm.meter_no" placeholder="Meter No." />
        <span v-if="errors.meter_no" class="error-message">
          {{ getError(errors.meter_no) }}
        </span>

        <select v-model="localForm.status">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <div class="btn-group">
          <button type="submit">
            {{ mode === 'add' ? 'Save' : 'Update' }}
          </button>

          <button type="button" class="close-btn" @click="$emit('close')">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) },
  mode: { type: String, default: 'add' } // 'add' or 'edit'
})

const emit = defineEmits(['save', 'update', 'close'])

// Local copy to avoid mutating parent directly
const localForm = reactive({ ...props.form })

// Watch parent form changes
watch(
  () => props.form,
  (newVal) => Object.assign(localForm, newVal),
  { deep: true, immediate: true }
)

// Helper to display first error
function getError(field) {
  return Array.isArray(field) ? field[0] : field
}

// Handle submit based on mode
function handleSubmit() {
  if (props.mode === 'add') {
    emit('save', { ...localForm })
  } else {
    emit('update', { ...localForm })
  }
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

.popup h2 {
  color: #007bff;
  margin-bottom: 15px;
}

.popup input,
.popup select {
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

.popup button:hover {
  background-color: #00a0ff;
}

.close-btn {
  background-color: #ccc;
  color: #333;
}

.close-btn:hover {
  background-color: #999;
}

.error-message {
  color: red;
  font-size: 12px;
  margin-top: 3px;
  display: block;
}

.btn-group {
  display: flex;
  justify-content: space-around;
  margin-top: 10px;
}
</style>