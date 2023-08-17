<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { interactSession } from '../../data/interactSession.js';
import Interaction from './components/Interaction.vue';

let currentIndex = ref(0);

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.path.replace(
    '/interact/',
    ''
  );
});
</script>

<template>
  <p class="text-center">
    You have joined {{ interactSession.title }} organised by
    {{ interactSession.name }}.
  </p>
  <div class="container card">
    <Interaction :currentIndex="currentIndex" />
  </div>
  <div class="text-center m-2">
    <button
      v-if="currentIndex > 0"
      @click="currentIndex--"
      class="btn btn-teal me-2"
    >
      Previous
    </button>
    <button
      v-if="currentIndex < interactSession.interactions.length - 1"
      @click="currentIndex++"
      class="btn btn-teal mw-2"
    >
      Next
    </button>
    <br />Interaction: {{ currentIndex + 1 }} of
    {{ interactSession.interactions.length }}
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
}
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
