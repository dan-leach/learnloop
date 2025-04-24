<script setup>
/**
 * @module interaction/components/host/WordCloud
 * @summary Generates a word cloud image from user submissions.
 * @description
 * This script watches changes to the current `slide` prop and builds a formatted
 * string of all text responses. It then generates a request URL to QuickChart’s
 * word cloud API, including settings like stopword removal and phrase preservation.
 *
 * @requires vue
 * @see https://quickchart.io/documentation/wordcloud-api/
 */

import { ref, watch } from "vue";

// Props from parent component
const props = defineProps(["slide"]);

// Refs to store processed data and the resulting API URL
const responsesString = ref("");
const getRequest = ref("");

/**
 * Watches the `slide` prop for changes and rebuilds the word cloud query URL.
 * It collects all submitted text responses, removes line breaks, and constructs
 * a query string for QuickChart's word cloud API.
 *
 * @function buildWordCloudRequest
 * @memberof module:interaction/components/host/WordCloud
 */
watch(props.slide, () => {
  console.log(
    "preservePhrases:",
    props.slide.interaction.settings.preservePhrases
  );
  responsesString.value = "";

  // Flatten and clean all submission responses into a single string
  for (const submission of props.slide.interaction.submissions) {
    responsesString.value +=
      submission.response.replace(/[\r\n\x0B\x0C\u0085\u2028\u2029]+/g, " ") +
      ",";
  }

  // Build the full URL to the word cloud image
  getRequest.value = `https://quickchart.io/wordcloud?text=${responsesString.value}&removeStopwords=true&width=1000&format=png&useWordList=${props.slide.interaction.settings.preservePhrases}`;
});
</script>

<template>
  <div class="text-center">
    <p v-if="!responsesString.length">No responses yet</p>
    <img v-else :src="getRequest" class="img-fluid" />
  </div>
</template>

<style scoped></style>
