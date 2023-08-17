<script setup>
import SingleChoice from './SingleChoice.vue';
import Toast from '../../../assets/toast.js';
const props = defineProps(['index', 'currentIndex', 'interaction']);

let submit = () => {
  switch (props.interaction.type) {
    case 'singleChoice':
      if (props.interaction.response !== '') {
        //submit api then if pass:
        if (true) {
          if (!props.interaction.allowMultiple) props.interaction.closed = true;
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
  <div v-if="index == currentIndex">
    <div class="d-flex justify-content-center">
      <div>
        <SingleChoice
          v-if="interaction.type == 'singleChoice'"
          :interaction="interaction"
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
        :disabled="interaction.closed"
      >
        Submit
      </button>
      <p>{{ interaction.msg }}</p>
    </div>
  </div>
</template>

<style scoped>
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
