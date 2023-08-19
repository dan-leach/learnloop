<script setup>
import { interactSession } from '../../data/interactSession.js';
import SingleChoice from './host/SingleChoice.vue';
import { config } from '../../data/config.js';
import Toast from '../../assets/Toast.js';

const props = defineProps(['currentIndex']);
const emit = defineEmits(['goForward', 'goBack']);

const toggleFullscreen = () => {
  config.isFullscreen = !config.isFullscreen;
  const element = document.documentElement;
  if (config.isFullscreen) {
    const fullMethod =
      element.requestFullScreen ||
      element.webkitRequestFullScreen ||
      element.mozRequestFullScreen ||
      element.msRequestFullScreen;

    if (fullMethod) {
      fullMethod.call(element);
    } else {
      Toast.fire({
        icon: 'error',
        title: 'Error launching full screen. Try pressing F11 instead.',
      });
    }
  } else {
    const exitMethod =
      element.exitFullscreen ||
      element.webkitExitFullscreen ||
      element.mozCancelFullScreen ||
      element.msExitFullscreen;
    if (exitMethod) exitMethod.call(element);
  }
};
</script>

<template>
  <div id="interaction-view">
    <p class="display-6 text-center">
      {{ interactSession.interactions[currentIndex].title }}
    </p>
    <div
      id="chart-area"
      class="d-flex justify-content-center chart-area m-4"
      :class="{ fullscreen: config.isFullscreen }"
    >
      <SingleChoice
        v-if="interactSession.interactions[currentIndex].type == 'singleChoice'"
        :interaction="interactSession.interactions[currentIndex]"
      />
    </div>
    <ul class="nav nav-justified m-2">
      <li class="nav-item">
        <button class="btn btn-lg" @click="emit('goBack')">
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
        </button>
      </li>
      <li class="nav-item">
        <button class="btn btn-lg" @click="toggleFullscreen">
          <font-awesome-icon
            v-if="!config.isFullscreen"
            :icon="['fas', 'maximize']"
          />
          <font-awesome-icon v-else :icon="['fas', 'minimize']" />
        </button>
      </li>
      <li class="nav-item">
        <button class="btn btn-lg" @click="emit('goForward')">
          <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
        </button>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.chart-area {
  height: 50vh;
}
.chart-area.fullscreen {
  height: 80vh;
}
</style>
