<script setup>
import { ref } from "vue";
import Toast from "../../../assets/Toast.js";

const props = defineProps([
  "interaction",
  "spinner",
  "btnSubmitText",
  "btnSubmitBelowText",
]);
const emit = defineEmits(["submit"]);

let selection = ref([]);

let submit = () => {
  let selectedCount = selection.value.length;
  let selectedMin = props.interaction.settings.selectedLimit.min;
  let selectedMax = props.interaction.settings.selectedLimit.max;
  if (selectedCount < selectedMin) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Please select at least " + selectedMin + " option(s)",
    });
  } else if (selectedCount > selectedMax) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Please select no more than " + selectedMax + " option(s)",
    });
  } else {
    props.interaction.response = JSON.stringify(selection.value);
    emit("submit");
  }
};
</script>

<template>
  <p class="text-center">
    <strong>{{ interaction.prompt }}</strong>
  </p>
  <p
    class="text-center"
    v-if="
      interaction.settings.selectedLimit.min > 1 ||
      interaction.settings.selectedLimit.max < interaction.options.length
    "
  >
    <small>
      Please select
      {{
        interaction.settings.selectedLimit.min ==
        interaction.settings.selectedLimit.max
          ? interaction.settings.selectedLimit.min
          : " between " +
            interaction.settings.selectedLimit.min +
            " and " +
            interaction.settings.selectedLimit.max
      }}
      options(s)
    </small>
  </p>
  <div v-for="(option, index) in interaction.options" class="form-check">
    <input
      type="checkbox"
      class="form-check-input"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="selection"
      :disabled="interaction.closed"
    />{{ option }}
    <label class="form-check-label" :for="'option-' + index"></label>
  </div>
  <div class="text-center">
    <button
      type="button"
      id="submit"
      class="btn btn-teal mt-4"
      @click="submit"
      :disabled="interaction.closed"
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
