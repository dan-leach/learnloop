<script setup>
import Toast from '../../../assets/Toast.js';

const props = defineProps(['interaction', 'spinner', 'btnSubmitText', 'btnSubmitBelowText']);
const emit = defineEmits(['submit']);

let submit = () => {
  if (props.interaction.response !== '') {
    emit('submit');
  } else {
    Toast.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title: 'Please choose an option',
    });
  }
};
</script>

<template>
  <p class="text-center"><strong>{{ interaction.prompt }}</strong></p>
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
  <div class="text-center">
    <button
      type="button"
      id="submit"
      class="btn btn-teal mt-4"
      @click="submit"
      :disabled="interaction.closed"
    >
      <span v-if="spinner" class="spinner-border spinner-border-sm"></span>
      {{ btnSubmitText }}
    </button>
    <p class="btnSubmitBelowText">{{ btnSubmitBelowText }}</p>
  </div>
</template>

<style scoped>
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
  
}
</style>
