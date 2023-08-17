<script setup>
import { interactSession } from '../../../data/interactSession.js';
import SingleChoice from './SingleChoice.vue';
import Toast from '../../../assets/toast.js';
const props = defineProps(['currentIndex']);

let submit = () => {
  switch (interactSession.interactions[props.currentIndex].type) {
    case 'singleChoice':
      if (interactSession.interactions[props.currentIndex].response !== '') {
        //submit api then if pass:
        if (true) {
          if (!interactSession.interactions[props.currentIndex].allowMultiple)
            interactSession.interactions[props.currentIndex].closed = true;
          Toast.fire({
            icon: 'success',
            title: 'Your response was submitted',
          });
        } else {
          //add an error notification
        }
        break;
      }
  }
};
</script>

<template>
  <div>
    <div class="d-flex justify-content-center">
      <div>
        <SingleChoice
          v-if="
            interactSession.interactions[currentIndex].type == 'singleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
          @submit="submit"
        />
      </div>
    </div>
    <div class="text-center m-2">
      <button
        type="button"
        id="submit"
        class="btn btn-teal"
        @click="submit"
        :disabled="interactSession.interactions[currentIndex].closed"
      >
        Submit
      </button>
      <p>{{ interactSession.interactions[currentIndex].msg }}</p>
    </div>
  </div>
</template>

<style scoped>
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
