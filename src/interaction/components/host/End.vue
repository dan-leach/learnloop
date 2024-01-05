<script setup>
const props = defineProps(['feedbackID']);
import { config } from '../../../data/config.js';
</script>

<template>
  <div>
    <p class="display-6 text-center">
      The slides for this session have finished
    </p>
    <div v-if="feedbackID">
      <p class="text-center">
        Please follow the instructions below to provide feedback to the session
        facilitator
      </p>
      <div
        class="feedback-panel text-center m-2 p-2 d-flex justify-content-around"
      >
        <div class="align-self-center me-4">
          <img
            :src="
              'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' +
              config.client.url +
              '/' +
              feedbackID +
              '&choe=UTF-8&chld=q|1'
            "
          />
        </div>
        <div class="align-self-center">
          <p class="feedback-instructions">
            Click the link on your device,<br />
            scan the QR code, or<br />go to <strong>LearnLoop.co.uk</strong
            ><br />
            and enter this code <br />in the feedback panel:
          </p>
          <p class="m-2">
            <span class="feedback-id px-4 py-1">{{ feedbackID }}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.feedback-instructions {
  margin: 0;
  font-size: 2rem;
  font-weight: 300;
}
.feedback-id {
  font-size: 3rem;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 15px;
  color: #17a2b8;
}
.feedback-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
</style>
