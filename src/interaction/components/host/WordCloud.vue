<script setup>
import { ref, watch } from "vue";
const props = defineProps(["slide"]);
let responsesString = ref("");
let getRequest = ref("");
let cloudImg = ref("");

watch(props.slide, (a, b) => {
  responsesString.value = "";
  for (let submission of props.slide.interaction.submissions) {
    responsesString.value +=
      "" +
      submission.response.replace(/[\r\n\x0B\x0C\u0085\u2028\u2029]+/g, " ") +
      ",";
  }
  getRequest.value =
    "https://quickchart.io/wordcloud?text=" +
    responsesString.value +
    "&removeStopwords=true&width=1000&format=png&useWordList=" +
    props.slide.interaction.settings.preservePhrases;
});
</script>

<template>
  <div class="text-center">
    <p v-if="!responsesString.length">No responses yet</p>
    <img v-else :src="getRequest" class="img-fluid" />
  </div>
</template>

<style scoped></style>
