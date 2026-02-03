<script setup>
import {ref} from 'vue'
import Store from '@/composables/store'
import { useRouter } from 'vue-router' 

const router = useRouter() 
const label = ref('')
const startTime = ref('')
const endTime = ref('')
const emit = defineEmits(['data-submitted']);
const response = ref(null)
const errors = ref(null)

const handleSubmit = async ()=> {
    errors.value = null
    const result =await Store('/api/time-entries',{
        label: label.value,
        start_time: startTime.value,
        end_time: endTime.value
    })
    if(result.success)
        emit('data-submitted')
        
    else if(!result.success && result.errors){
        errors.value = result.errors
    }
    else{
        router.push({name: 'Home'})
    }
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <div class="field">
      <label>{{ $t("addForm.label") }}</label>
      <input type="text" v-model="label" required>
      <span v-if="errors?.label" class="err">{{ errors.label[0] }}</span>
    </div>

    <div class="field">
      <label>{{ $t("addForm.start_time") }}</label>
      <input type="time" v-model="startTime" required>
      <span v-if="errors?.start_time" class="err">{{ errors.start_time[0] }}</span>
    </div>

    <div class="field">
      <label>{{ $t("addForm.end_time") }}</label>
      <input type="time" v-model="endTime" required>
      <span v-if="errors?.end_time" class="err">{{ errors.end_time[0] }}</span>
    </div>

    <button type="submit" class="submit">{{ $t("addForm.add_entry") }}</button>
    <p v-if="response" class="success">{{ response.data }}</p>
  </form>
</template>

<style scoped>
form {
  max-width: 500px;
}

.field {
  margin-bottom: 1.2rem;
}

label {
  display: block;
  margin-bottom: 0.4rem;
  color: #555;
  font-size: 0.9rem;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.95rem;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: #007bff;
}

.err {
  display: block;
  margin-top: 0.3rem;
  color: #c33;
  font-size: 0.85rem;
}

.submit {
  padding: 0.7rem 1.5rem;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s;
}

.submit:hover {
  background: #218838;
}

.success {
  margin-top: 1rem;
  padding: 0.8rem;
  background: #d4edda;
  color: #155724;
  border-radius: 4px;
}
</style>
