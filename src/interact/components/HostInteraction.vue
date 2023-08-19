<script setup>
import { interactSession } from '../../data/interactSession.js';
import SingleChoice from './host/SingleChoice.vue';
import Toast from '../../assets/Toast.js';
const props = defineProps(['currentIndex']);
</script>

<template>
  <div>
    <p class="text-center">
      {{ interactSession.interactions[currentIndex].title }}
    </p>
    <div class="d-flex justify-content-center chart-area">
      <SingleChoice
        v-if="interactSession.interactions[currentIndex].type == 'singleChoice'"
        :interaction="interactSession.interactions[currentIndex]"
      />
    </div>
  </div>
</template>

<style scoped>
.chart-area {
  height: 50vh;
}
</style>
