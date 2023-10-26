<script setup>
import Toast from '../../../assets/Toast.js';

const props = defineProps(['interaction', 'spinner', 'btnSubmitText', 'btnSubmitBelowText']);
const emit = defineEmits(['submit']);

let submit = () => {
  let length = props.interaction.response.length
  let minLength = props.interaction.settings.characterLimit.min
  let maxLength = props.interaction.settings.characterLimit.max
  if (length < minLength) {
    Toast.fire({
      icon: 'error',
      title: 'Your response must be at least '+minLength+' characters in length',
    });
  } else if (length > maxLength) {
    Toast.fire({
      icon: 'error',
      title: 'Your response cannot be more than '+maxLength+' characters in length',
    });
  } else {
    emit('submit');
  }
};
</script>

<template>
  <p class="text-center"><strong>{{ interaction.prompt }}</strong></p>
  <textarea class="response-field form-control" rows="5" v-model="interaction.response" :disabled="interaction.closed" :maxLength="interaction.settings.characterLimit.max"></textarea>
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
.text-center {
  width: 100%
}
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
  
}
</style>
