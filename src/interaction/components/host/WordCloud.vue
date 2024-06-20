<script setup>
import { ref, watch } from 'vue';
const props = defineProps(['slide']);
let responsesString = ref('');
let getRequest = ref('');
let cloudImg = ref('');

watch(props.slide, (a, b) => {
  for (let submission of props.slide.interaction.submissions)
    responsesString.value += submission.response + ' ';
  getRequest.value =
    'https://quickchart.io/wordcloud?text=' +
    responsesString.value +
    '&removeStopwords=true&width=1000&format=png';
});
</script>

<template>
  <div class="text-center">
    <p v-if="!responsesString.length">No responses yet</p>
    <img v-else :src="getRequest" class="img-fluid" />
  </div>
</template>

<style scoped></style>
