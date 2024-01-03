<script setup>
import { interactSession } from '../../data/interactSession.js';
import WaitingRoom from './host/WaitingRoom.vue';
import End from './host/End.vue';
import HideResponses from './host/HideResponses.vue';
import SingleChoice from './host/SingleChoice.vue';
import MultipleChoice from './host/MultipleChoice.vue';
import ShortText from './host/ShortText.vue';
import { config } from '../../data/config.js';

const props = defineProps(['currentIndex']);
const emit = defineEmits(['goForward', 'goBack']);

const showResponses = () => {
  interactSession.interactions[
    props.currentIndex
  ].settings.hideResponses = false;
  interactSession.interactions[props.currentIndex].submissions = [];
};
</script>

<template>
  <div id="interaction-view" :class="{ focusView: config.client.isFocusView }">
    <p v-if="config.client.isFocusView" class="text-center m-1">
      To join go to LearnLoop.co.uk and use the code
      <span class="join-id-top p-1">{{ interactSession.id }}</span>
    </p>
    <p
      v-if="interactSession.interactions[currentIndex].type != 'waitingRoom'"
      class="display-6 text-center m-1"
    >
      {{ interactSession.interactions[currentIndex].prompt }}
    </p>
    <Transition mode="out-in">
      <div
        id="chart-area"
        class="d-flex justify-content-center chart-area mx-4"
        :class="{ focusView: config.client.isFocusView }"
        :key="'index' + currentIndex"
      >
        <WaitingRoom
          v-if="
            interactSession.interactions[currentIndex].type == 'waitingRoom'
          "
        />
        <End
          v-else-if="interactSession.interactions[currentIndex].type == 'end'"
          :feedbackID="interactSession.feedbackID"
        />
        <HideResponses
          v-else-if="
            interactSession.interactions[currentIndex].settings.hideResponses
          "
          @showResponses="showResponses"
          :interaction="interactSession.interactions[currentIndex]"
        />
        <SingleChoice
          v-else-if="
            interactSession.interactions[currentIndex].type == 'singleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
        />
        <MultipleChoice
          v-else-if="
            interactSession.interactions[currentIndex].type == 'multipleChoice'
          "
          :interaction="interactSession.interactions[currentIndex]"
        />
        <ShortText
          v-else-if="
            interactSession.interactions[currentIndex].type == 'shortText'
          "
          :interaction="interactSession.interactions[currentIndex]"
        />
      </div>
    </Transition>
    <ul class="nav nav-justified m-2">
      <li class="nav-item">
        <button
          v-if="currentIndex > 0"
          class="btn btn-lg"
          @click="emit('goBack')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
        </button>
      </li>
      <li class="nav-item">
        <button
          v-if="currentIndex < interactSession.interactions.length - 1"
          class="btn btn-lg"
          @click="emit('goForward')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
        </button>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.chart-area {
  height: 60vh;
}
.chart-area.focusView {
  height: 80vh;
}
#interaction-view.focusView {
  background-color: transparent;
}
.join-id-top {
  background-color: #17a2b8;
  color: white;
  font-family: serif;
  font-size: 1.2rem;
  border-radius: 5px;
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
