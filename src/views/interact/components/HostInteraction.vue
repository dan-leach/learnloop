<script setup>
import { interactSession } from '../../../data/interactSession.js';
import SingleChoice from './host/SingleChoice.vue';
import Toast from '../../../assets/Toast.js';
const props = defineProps(['currentIndex']);
</script>

<template>
  <div>
    <div class="d-flex justify-content-center">
      <div>
        <SingleChoice
          v-if="
            interactSession.interactions[currentIndex].type == 'singleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
