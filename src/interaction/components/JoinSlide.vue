<script setup>
import { ref } from "vue";
import { api } from "../../data/api.js";
import { interactionSession } from "../../data/interactionSession.js";
import WaitingRoom from "./join/WaitingRoom.vue";
import End from "./join/End.vue";
import TrueFalse from "./join/TrueFalse.vue";
import MultipleChoice from "./join/MultipleChoice.vue";
import FreeText from "./join/FreeText.vue";
import Content from "./join/Content.vue";
import Swal from "sweetalert2";
import Toast from "../../assets/Toast.js";
const props = defineProps(["currentIndex"]);

let spinner = ref(false);
let btnSubmitText = ref("Submit");
let btnSubmitBelowText = ref("");

const submit = () => {
  let slide = interactionSession.slides[props.currentIndex];
  slide.interaction.closed = true;
  spinner.value = true;
  btnSubmitText.value = "Please wait";
  api("interaction/insertSubmission", {
    id: interactionSession.id,
    slideIndex: props.currentIndex,
    response: slide.interaction.response,
  }).then(
    function (res) {
      Toast.fire({
        icon: "success",
        title: "Your response was submitted",
      });
      spinner.value = false;
      slide.interaction.submissionCount++;
      console.log(
        slide.interaction.submissionCount,
        slide.interaction.settings.submissionLimit
      );
      if (
        slide.interaction.submissionCount <
        slide.interaction.settings.submissionLimit
      ) {
        slide.interaction.closed = false;
        btnSubmitText.value = "Submit";
        btnSubmitBelowText.value = "You may submit multiple responses";
        slide.interaction.response = "";
      } else {
        btnSubmitText.value = "Done";
        btnSubmitBelowText.value =
          slide.interaction.submissionCount ==
          slide.interaction.settings.submissionLimit
            ? "You have reached the submission limit"
            : "";
      }
    },
    function (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to submit your response",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      slide.interaction.closed = false;
      spinner.value = false;
      btnSubmitText.value = "Try again?";
    }
  );
};
</script>

<template>
  <div>
    <p class="text-center">
      <strong>{{ interactionSession.slides[currentIndex].prompt }}</strong>
    </p>
    <div class="d-flex justify-content-center">
      <div class="full-width">
        <Content :slide="interactionSession.slides[currentIndex]" />
        <WaitingRoom
          v-if="interactionSession.slides[currentIndex].type == 'waitingRoom'"
        />
        <End
          v-else-if="interactionSession.slides[currentIndex].type == 'end'"
          :feedbackID="interactionSession.feedbackID"
        />
        <TrueFalse
          v-else-if="
            interactionSession.slides[currentIndex].type == 'trueFalse'
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
        <FreeText
          v-else-if="
            interactionSession.slides[currentIndex].type == 'freeText' ||
            interactionSession.slides[currentIndex].type == 'wordCloud'
          "
          :slide="interactionSession.slides[currentIndex]"
          :spinner="spinner"
          :currentIndex="currentIndex"
          :btnSubmitText="btnSubmitText"
          :btnSubmitBelowText="btnSubmitBelowText"
          @submit="submit"
        />
        <div
          v-else-if="interactionSession.slides[currentIndex].type == 'static'"
        ></div>
        <p v-else>
          Error: slide type [{{ interactionSession.slides[currentIndex].type }}]
          not recognised
        </p>
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
