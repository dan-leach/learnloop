<script setup>
import Toast from '../../../assets/Toast.js';

const props = defineProps(['interaction', 'spinner', 'btnSubmitText']);
const emit = defineEmits(['submit']);

let submit = () => {
  if (props.interaction.response !== '') {
    emit('submit');
  } else {
    Toast.fire({
      icon: 'error',
      title: 'Please choose an option',
    });
  }
};
</script>

<template>
  <p class="text-center">{{ interaction.title }}</p>
  <div v-for="(option, index) in interaction.options" class="form-check">
    <input
      type="radio"
      class="form-check-input"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="interaction.response"
      :disabled="interaction.closed"
    />{{ option }}
    <label class="form-check-label" :for="'option-' + index"></label>
  </div>
  <div class="text-center m-2">
    <button
      type="button"
      id="submit"
      class="btn btn-teal"
      @click="submit"
      :disabled="interaction.closed"
    >
      <span v-if="spinner" class="spinner-border spinner-border-sm"></span>
      {{ btnSubmitText }}
    </button>
  </div>
</template>

<style scoped></style>
