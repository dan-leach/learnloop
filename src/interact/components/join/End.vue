<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["feedbackID"]);
const openFeedback = () =>
  window.open(config.client.url + "/" + props.feedbackID, "_blank");
</script>

<template>
  <div class="text-center m-4">
    <p class="display-6">The interactions for this session have finished</p>
    <div class="text-center" v-if="feedbackID">
      <p>
        Please click below to provide feedback to the session facilitator<br />
        <button
          type="button"
          id="feedback"
          class="btn btn-teal mt-4"
          @click="openFeedback"
        >
          Give feedback
        </button>
      </p>
    </div>
  </div>
</template>

<style scoped></style>
