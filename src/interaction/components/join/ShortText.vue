<script setup>
import { interactionSession } from '../../../data/interactionSession.js';
import Toast from '../../../assets/Toast.js';

const props = defineProps([
  'slide',
  'spinner',
  'btnSubmitText',
  'btnSubmitBelowText',
  'currentIndex',
]);
const emit = defineEmits(['submit']);

let submit = () => {
  let length = props.slide.response.length;
  let minLength = props.slide.settings.characterLimit.min;
  let maxLength = props.slide.settings.characterLimit.max;
  if (length < minLength) {
    Toast.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title:
        'Your response must be at least ' + minLength + ' characters in length',
    });
  } else if (length > maxLength) {
    Toast.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title:
        'Your response cannot be more than ' +
        maxLength +
        ' characters in length',
    });
  } else {
    emit('submit');
  }
};
</script>

<template>
  <p class="text-center">
    <strong>{{ slide.prompt }}</strong>
  </p>
  <textarea
    class="response-field form-control"
    rows="5"
    v-model="slide.response"
    :disabled="slide.closed"
    :maxLength="slide.settings.characterLimit.max"
  ></textarea>
  <div class="text-center">
    <button
      type="button"
      id="submit"
      class="btn btn-teal mt-4"
      @click="submit"
      :disabled="
        slide.closed || interactionSession.hostStatus.lockedSlides[currentIndex]
      "
    >
      <span v-if="spinner" class="spinner-border spinner-border-sm"></span>
      {{ btnSubmitText }}
    </button>
    <p class="btnSubmitBelowText">{{ btnSubmitBelowText }}</p>
  </div>
</template>

<style scoped>
.text-center {
  width: 100%;
}
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
}
</style>
