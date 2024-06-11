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
  if (props.slide.interaction.response !== "") {
    emit("submit");
  } else {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Please choose an option",
    });
  }
};
</script>

<template>
  <div v-for="(option, index) in slide.interaction.options" class="form-check">
    <input
      type="radio"
      class="form-check-input"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="slide.interaction.response"
      :disabled="slide.interaction.closed"
    />{{ option }}
    <label class="form-check-label" :for="'option-' + index"></label>
  </div>
  <div class="text-center">
    <button
      type="button"
      id="submit"
      class="btn btn-teal mt-4"
      @click="submit"
      :disabled="
        slide.interaction.closed ||
        interactionSession.hostStatus.lockedSlides[currentIndex]
      "
    >
      <span v-if="spinner" class="spinner-border spinner-border-sm"></span>
      {{ btnSubmitText }}
    </button>
    <p class="btnSubmitBelowText">{{ btnSubmitBelowText }}</p>
  </div>
</template>

<style scoped>
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
}
</style>
