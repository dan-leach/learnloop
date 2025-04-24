<script setup>
/**
 * @module interaction/components/join/MultipleChoice
 * @summary Validates number of selected options before submitting a multiple-choice response.
 * @description
 * Handles user input validation for a multiple-choice interaction slide by ensuring
 * the number of selected options is within the allowed min and max range.
 * If valid, the selection is serialized and submitted.
 *
 * @requires ../../../data/interactionSession.js
 * @requires ../../../assets/Toast.js
 */

import { ref } from "vue";
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

// Stores user-selected options
const selection = ref([]);

/**
 * Validate selection count and emit submit event if valid.
 * @function
 * @returns {void}
 * @memberof module:interaction/components/join/MultipleChoice
 */
const submit = () => {
  const selectedCount = selection.value.length;
  const { min: selectedMin, max: selectedMax } =
    props.slide.interaction.settings.selectedLimit;

  // Too few selections
  if (selectedCount < selectedMin) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: `Please select at least ${selectedMin} option(s)`,
    });
    return;
  }

  // Too many selections
  if (selectedCount > selectedMax) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: `Please select no more than ${selectedMax} option(s)`,
    });
    return;
  }

  // Selection is valid – serialize and assign to interaction response
  props.slide.interaction.response = JSON.stringify(selection.value);
  emit("submit");
};
</script>

<template>
  <p
    class="text-center"
    v-if="
      slide.interaction.settings.selectedLimit.min > 1 ||
      slide.interaction.settings.selectedLimit.max <
        slide.interaction.options.length
    "
  >
    <small>
      Please select
      {{
        slide.interaction.settings.selectedLimit.min ==
        slide.interaction.settings.selectedLimit.max
          ? slide.interaction.settings.selectedLimit.min
          : " between " +
            slide.interaction.settings.selectedLimit.min +
            " and " +
            slide.interaction.settings.selectedLimit.max
      }}
      options(s)
    </small>
  </p>
  <div
    v-for="(option, index) in slide.interaction.options"
    class="form-check d-flex align-items-center"
  >
    <input
      type="checkbox"
      class="form-check-input check-lg"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="selection"
      :disabled="
        slide.interaction.closed ||
        interactionSession.status.lockedSlides[currentIndex - 1]
      "
    />
    <label
      class="form-check-label check-label-lg ms-3"
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
.check-lg {
  transform: scale(1.5);
}
.check-label-lg {
  font-size: 1.5rem;
  vertical-align: middle;
}
</style>
