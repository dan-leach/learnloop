<script setup>
import { ref } from 'vue';
import { api } from '../../data/api.js';
import { interactSession } from '../../data/interactSession.js';
import WaitingRoom from './join/WaitingRoom.vue';
import SingleChoice from './join/SingleChoice.vue';
import MultipleChoice from './join/MultipleChoice.vue';
import ShortText from './join/ShortText.vue';
import Swal from 'sweetalert2';
import Toast from '../../assets/Toast.js';
const props = defineProps(['currentIndex']);

let spinner = ref(false);
let btnSubmitText = ref('Submit');
let btnSubmitBelowText = ref('');

const submit = () => {
  let interaction = interactSession.interactions[props.currentIndex];
  interaction.closed = true;
  spinner.value = true;
  btnSubmitText.value = 'Please wait';
  api('interact', 'insertSubmission', interactSession.id, null, {
    interactionIndex: props.currentIndex,
    response: interaction.response,
  }).then(
    function (res) {
      Toast.fire({
        icon: 'success',
        title: 'Your response was submitted',
      });
      spinner.value = false;
      interaction.submissionCount++;
      console.log(interaction.submissionCount, interaction.settings.submissionLimit)
      if (interaction.submissionCount < interaction.settings.submissionLimit) {
        interaction.closed = false;
        btnSubmitText.value = 'Submit';
        btnSubmitBelowText.value = 'You may submit multiple responses'
        interaction.response = '';
      } else {
        btnSubmitText.value = 'Done';
        btnSubmitBelowText.value = (interaction.submissionCount == interaction.settings.submissionLimit) ? 'You have reached the submission limit' : ''
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
      <div class="full-width">
        <WaitingRoom
          v-if="
            interactSession.interactions[currentIndex].type == 'waitingRoom'
          "
        />
        <SingleChoice
          v-if="
            interactSession.interactions[currentIndex].type == 'singleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
          :spinner="spinner"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
        <MultipleChoice
          v-if="
            interactSession.interactions[currentIndex].type == 'multipleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
          :spinner="spinner"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
        <ShortText
          v-if="
            interactSession.interactions[currentIndex].type == 'shortText'
          "
          :interaction="interactSession.interactions[currentIndex]"
          :spinner="spinner"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.full-width {
  width: 100%
}
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
