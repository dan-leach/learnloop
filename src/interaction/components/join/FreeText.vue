<script setup>
/**
 * @module interaction/components/join/FreeText
 * @summary Validates character length before submitting free-text responses.
 * @description
 * This module validates the user's input on a free-text slide before allowing submission.
 * It uses character length settings from the slide's configuration and provides user feedback via Toast notifications.
 * If the input passes validation, the "submit" event is emitted to the parent.
 *
 * @requires ../../../data/interactionSession.js
 * @requires ../../../assets/Toast.js
 */

import { interactionSession } from "../../../data/interactionSession.js";
import Toast from "../../../assets/Toast.js";

// Props passed from parent component
const props = defineProps([
  "slide",
  "spinner",
  "btnSubmitText",
  "btnSubmitBelowText",
  "currentIndex",
]);

// Emits 'submit' event to the parent when validation passes
const emit = defineEmits(["submit"]);

/**
 * Validate character length and emit submit event if valid.
 * @function
 * @returns {void}
 * @memberof module:interaction/components/join/FreeText
 */
const submit = () => {
  const length = props.slide.interaction.response.length;
  const { min, max } = props.slide.interaction.settings.characterLimit;

  // Input too short
  if (length < min) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: `Your response must be at least ${min} characters in length`,
    });
    return;
  }

  // Input too long
  if (length > max) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: `Your response cannot be more than ${max} characters in length`,
    });
    return;
  }

  // Input valid – emit submit to parent
  emit("submit");
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
