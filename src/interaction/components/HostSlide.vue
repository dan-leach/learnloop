<script setup>
import { interactionSession } from "../../data/interactionSession.js";
import WaitingRoom from "./host/WaitingRoom.vue";
import End from "./host/End.vue";
import HideResponses from "./host/HideResponses.vue";
import SingleChoice from "./host/SingleChoice.vue";
import MultipleChoice from "./host/MultipleChoice.vue";
import FreeText from "./host/FreeText.vue";
import Static from "./host/Static.vue";
import { config } from "../../data/config.js";

const props = defineProps(["currentIndex"]);
const emit = defineEmits(["goForward", "goBack", "toggleLockSlide"]);

const showResponses = () => {
  interactionSession.slides[
    props.currentIndex
  ].interaction.settings.hideResponses = false;
  interactionSession.slides[props.currentIndex].submissions = [];
};
</script>

<template>
  <div id="slide-view" :class="{ focusView: config.client.isFocusView }">
    <p v-if="config.client.isFocusView" class="text-center m-1">
      To join go to LearnLoop.co.uk and use the code
      <span class="join-id-top p-1">{{ interactionSession.id }}</span>
    </p>
    <p
      v-if="interactionSession.slides[currentIndex].type != 'waitingRoom'"
      class="display-6 text-center m-1"
    >
      {{ interactionSession.slides[currentIndex].prompt }}
    </p>
    <Transition mode="out-in">
      <div
        id="chart-area"
        class="d-flex justify-content-center chart-area mx-4"
        :class="{ focusView: config.client.isFocusView }"
        :key="'index' + currentIndex"
      >
        <WaitingRoom
          v-if="interactionSession.slides[currentIndex].type == 'waitingRoom'"
        />
        <End
          v-else-if="interactionSession.slides[currentIndex].type == 'end'"
          :feedbackID="interactionSession.feedbackID"
        />
        <HideResponses
          v-else-if="
            interactionSession.slides[currentIndex].interaction.settings
              .hideResponses
          "
          @showResponses="showResponses"
          :slide="interactionSession.slides[currentIndex]"
        />
        <SingleChoice
          v-else-if="
            interactionSession.slides[currentIndex].type == 'singleChoice'
          "
          :slide="interactionSession.slides[currentIndex]"
        />
        <MultipleChoice
          v-else-if="
            interactionSession.slides[currentIndex].type == 'multipleChoice'
          "
          :slide="interactionSession.slides[currentIndex]"
        />
        <FreeText
          v-else-if="interactionSession.slides[currentIndex].type == 'freeText'"
          :slide="interactionSession.slides[currentIndex]"
        />
        <Static
          v-else-if="interactionSession.slides[currentIndex].type == 'static'"
          :slide="interactionSession.slides[currentIndex]"
        />
        <p v-else>
          Error: slide type [{{ interactionSession.slides[currentIndex].type }}]
          not recognised
        </p>
      </div>
    </Transition>
    <ul class="nav nav-justified m-2">
      <li class="nav-item">
        <button
          v-if="currentIndex > 0"
          class="btn btn-lg"
          @click="emit('goBack')"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          title="Go to previous slide"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
        </button>
      </li>
      <li class="nav-item">
        <button
          v-if="interactionSession.slides[currentIndex].isInteractive"
          class="btn btn-lg"
          @click="emit('toggleLockSlide')"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          title="Lock this slide to prevent attendees submitting further responses"
        >
          <font-awesome-icon
            :icon="['fas', 'lock']"
            v-if="interactionSession.hostStatus.lockedSlides[currentIndex]"
          />
          <font-awesome-icon
            :icon="['fas', 'lock-open']"
            v-if="!interactionSession.hostStatus.lockedSlides[currentIndex]"
          />
        </button>
      </li>
      <li class="nav-item">
        <button
          v-if="currentIndex < interactionSession.slides.length - 1"
          class="btn btn-lg"
          @click="emit('goForward')"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          title="Go to next slide"
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
#slide-view.focusView {
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
