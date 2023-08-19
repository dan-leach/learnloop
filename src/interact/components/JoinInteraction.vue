<script setup>
import { interactSession } from '../../data/interactSession.js';
import SingleChoice from './join/SingleChoice.vue';
import Toast from '../../assets/Toast.js';
const props = defineProps(['currentIndex']);

let submit = () => {
  let interaction = interactSession.interactions[props.currentIndex];
  switch (interaction.type) {
    case 'singleChoice':
      //submit api then if pass:
      if (true) {
        if (!interaction.allowMultiple) interaction.closed = true;
        Toast.fire({
          icon: 'success',
          title: 'Your response was submitted',
        });
      } else {
        //add an error notification
      }
      break;
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
  </div>
</template>

<style scoped>
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
