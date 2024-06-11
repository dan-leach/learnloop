<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["feedbackID"]);
const openFeedback = () =>
  window.open(config.client.url + "/" + props.feedbackID, "_blank");
</script>

<template>
  <div class="text-center m-4">
    <div class="text-center" v-if="feedbackID">
      <p class="display-6">
        Please click below to provide feedback to the session facilitator
      </p>
      <button
        type="button"
        id="feedback"
        class="btn btn-teal mt-4"
        @click="openFeedback"
      >
        Give feedback
      </button>
    </div>
    <p class="display-6" v-else>The slides for this session have finished</p>
  </div>
</template>

<style scoped></style>
