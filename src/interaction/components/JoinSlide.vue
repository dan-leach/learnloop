<script setup>
import { ref } from 'vue';
import { api } from '../../data/api.js';
import { interactionSession } from '../../data/interactionSession.js';
import WaitingRoom from './join/WaitingRoom.vue';
import End from './join/End.vue';
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
  let slide = interactionSession.slides[props.currentIndex];
  slide.closed = true;
  spinner.value = true;
  btnSubmitText.value = 'Please wait';
  api('interaction', 'insertSubmission', interactionSession.id, null, {
    slideIndex: props.currentIndex,
    response: slide.response,
  }).then(
    function (res) {
      Toast.fire({
        icon: 'success',
        iconColor: '#17a2b8',
        title: 'Your response was submitted',
      });
      spinner.value = false;
      slide.submissionCount++;
      console.log(slide.submissionCount, slide.settings.submissionLimit);
      if (slide.submissionCount < slide.settings.submissionLimit) {
        slide.closed = false;
        btnSubmitText.value = 'Submit';
        btnSubmitBelowText.value = 'You may submit multiple responses';
        slide.response = '';
      } else {
        btnSubmitText.value = 'Done';
        btnSubmitBelowText.value =
          slide.submissionCount == slide.settings.submissionLimit
            ? 'You have reached the submission limit'
            : '';
      }
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        iconColor: '#17a2b8',
        title: 'Unable to submit your response',
        text: error,
        confirmButtonColor: '#17a2b8',
      });
      slide.closed = false;
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
          v-if="interactionSession.slides[currentIndex].type == 'waitingRoom'"
        />
        <End
          v-else-if="interactionSession.slides[currentIndex].type == 'end'"
          :feedbackID="interactionSession.feedbackID"
        />
        <SingleChoice
          v-else-if="
            interactionSession.slides[currentIndex].type == 'singleChoice'
          "
          :slide="interactionSession.slides[currentIndex]"
          :spinner="spinner"
          :currentIndex="currentIndex"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
        <MultipleChoice
          v-else-if="
            interactionSession.slides[currentIndex].type == 'multipleChoice'
          "
          :slide="interactionSession.slides[currentIndex]"
          :spinner="spinner"
          :currentIndex="currentIndex"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
        <ShortText
          v-else-if="
            interactionSession.slides[currentIndex].type == 'shortText'
          "
          :slide="interactionSession.slides[currentIndex]"
          :spinner="spinner"
          :currentIndex="currentIndex"
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
  width: 100%;
}
.slideComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
