<script setup>
import { ref } from 'vue';
import { api } from '../../data/api.js';
import { interactSession } from '../../data/interactSession.js';
import SingleChoice from './join/SingleChoice.vue';
import Swal from 'sweetalert2';
import Toast from '../../assets/Toast.js';
const props = defineProps(['currentIndex']);

let spinner = ref(false);
let btnSubmitText = ref('Submit');

const submit = () => {
  let interaction = interactSession.interactions[props.currentIndex];
  interaction.closed = true;
  spinner.value = true;
  btnSubmitText.value = 'Please wait';
  api('interact', 'insertSubmission', interactSession.id, null, null).then(
    function (res) {
      Toast.fire({
        icon: 'success',
        title: 'Your response was submitted',
      });
      spinner.value = false;
      if (interaction.allowMultiple) {
        interaction.closed = false;
        btnSubmitText.value = 'Submit another response';
        interaction.response = '';
      } else {
        btnSubmitText.value = 'Done';
      }
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable to submit your response',
        text: error,
      });
      interaction.closed = false;
      spinner.value = false;
      btnSubmitText.value = 'Try again?';
    }
  );
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
          :spinner="spinner"
          :btnSubmitText="btnSubmitText"
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
