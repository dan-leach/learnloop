<script setup>
import { interactionSession } from "../../../data/interactionSession.js";
import Toast from "../../../assets/Toast.js";

const props = defineProps([
  "slide",
  "spinner",
  "btnSubmitText",
  "btnSubmitBelowText",
  "currentIndex",
]);
const emit = defineEmits(["submit"]);

let submit = () => {
  let length = props.slide.interaction.response.length;
  let minLength = props.slide.interaction.settings.characterLimit.min;
  let maxLength = props.slide.interaction.settings.characterLimit.max;
  if (length < minLength) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title:
        "Your response must be at least " + minLength + " characters in length",
    });
  } else if (length > maxLength) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title:
        "Your response cannot be more than " +
        maxLength +
        " characters in length",
    });
  } else {
    emit("submit");
  }
};
</script>

<template>
  <textarea
    class="response-field form-control"
    rows="5"
    v-model="slide.interaction.response"
    :disabled="
      slide.interaction.closed ||
      interactionSession.status.lockedSlides[currentIndex - 1]
    "
    :maxLength="slide.interaction.settings.characterLimit.max"
  ></textarea>
  <div class="text-center">
    <button
      type="button"
      id="submit"
      class="btn btn-teal mt-4"
      @click="submit"
      :disabled="
        slide.interaction.closed ||
        interactionSession.status.lockedSlides[currentIndex - 1]
      "
    >
      <span v-if="spinner" class="spinner-border spinner-border-sm"></span>
      {{ btnSubmitText }}
    </button>
    <p class="btnSubmitBelowText">{{ btnSubmitBelowText }}</p>
    <p
      class="btnSubmitBelowText"
      v-if="interactionSession.status.lockedSlides[currentIndex - 1]"
    >
      The facilitator has locked the current slide to further responses
    </p>
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
