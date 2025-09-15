<script setup>
/**
 * @module interaction/components/join/TrueFalse
 * @summary Validates a single-choice selection before submission.
 * @description
 * Ensures that a user has selected an option before allowing submission.
 * Displays a toast error if no option is selected.
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

// Emits 'submit' event to parent if validation passes
const emit = defineEmits(["submit"]);

/**
 * Handles validation of a single-choice response and emits submit if valid.
 * @function
 * @returns {void}
 * @memberof module:interaction/components/join/TrueFalse
 */
const submit = () => {
  if (props.slide.interaction.response !== "") {
    emit("submit"); // Proceed if user has selected an option
  } else {
    // Display error if nothing was selected
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Please choose an option",
    });
  }
};
</script>

<template>
  <div
    v-for="(option, index) in slide.interaction.options"
    class="form-check d-flex align-items-center"
  >
    <input
      type="radio"
      class="form-check-input radio-lg"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="slide.interaction.response"
      :disabled="
        slide.interaction.closed ||
        interactionSession.status.lockedSlides[currentIndex - 1]
      "
    />
    <label
      class="form-check-label radio-label-lg ms-3"
      :for="'option-' + index"
      >{{ option.text }}</label
    >
  </div>
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
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
}
.radio-lg {
  transform: scale(1.5);
}
.radio-label-lg {
  font-size: 1.5rem;
  vertical-align: middle;
}
</style>
