<script setup>
import { interactionSession } from "../../data/interactionSession.js";
import WaitingRoom from "./host/WaitingRoom.vue";
import End from "./host/End.vue";
import HideResponses from "./host/HideResponses.vue";
import SingleChoice from "./host/SingleChoice.vue";
import MultipleChoice from "./host/MultipleChoice.vue";
import FreeText from "./host/FreeText.vue";
import WordCloud from "./host/WordCloud.vue";
import Content from "./host/Content.vue";
import { inject } from "vue";
const config = inject("config");

const props = defineProps(["currentIndex", "isPreview"]);
const emit = defineEmits(["goForward", "goBack", "goStart", "toggleLockSlide"]);

const showResponses = () => {
  interactionSession.slides[
    props.currentIndex
  ].interaction.settings.hideResponses = false;
  interactionSession.slides[props.currentIndex].interaction.submissions = [];
};

const toggleContent = () => {
  interactionSession.slides[props.currentIndex].content.show =
    !interactionSession.slides[props.currentIndex].content.show;
  if (!interactionSession.slides[props.currentIndex].content.show)
    showResponses();
};
</script>

<template>
  <div id="slide-view" :class="{ focusView: config.client.isFocusView }">
    <ul class="nav nav-justified m-2" v-if="config.client.isFocusView">
      <li class="nav-item">
        <button
          v-if="currentIndex > 0"
          class="btn btn-lg"
          @click="emit('goBack')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
        </button>
      </li>
      <li>
        <p class="text-center m-1">
          To join go to {{ config.client.url.replace("https://", "") }}
          and use the code
          <span class="join-id-top p-1">{{
            isPreview ? "preview" : interactionSession.id
          }}</span>
        </p>
      </li>
      <li class="nav-item">
        <button
          v-if="currentIndex < interactionSession.slides.length - 1"
          class="btn btn-lg"
          @click="emit('goForward')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
        </button>
      </li>
    </ul>
    <p class="display-6 text-center m-1">
      {{ interactionSession.slides[currentIndex].prompt }}
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
      <button
        class="btn btn-teal btn-sm m-4"
        @click="toggleContent"
        v-if="
          interactionSession.slides[currentIndex].hasContent &&
          interactionSession.slides[currentIndex].isInteractive
        "
      >
        Toggle content / responses
      </button>
    </p>
    <div
      class="text-center"
      v-if="currentIndex == interactionSession.slides.length - 1"
    >
      <p class="display-6">The slides for this session have finished</p>
      <div>
        <button class="btn btn-lg btn-teal mx-2 mb-2" @click="emit('goBack')">
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" /> Back
        </button>
        <button class="btn btn-lg btn-teal mx-2 mb-2" @click="emit('goStart')">
          Restart
        </button>
      </div>
    </div>
    <div class="text-center">
      <button
        v-if="currentIndex == 0"
        class="btn btn-lg btn-teal mb-2"
        @click="emit('goForward')"
      >
        Start <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
      </button>
    </div>
    <Content
      :slide="interactionSession.slides[currentIndex]"
      v-if="
        interactionSession.slides[currentIndex].hasContent &&
        interactionSession.slides[currentIndex].content.show
      "
    />
    <Transition mode="out-in" v-else>
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
        <WordCloud
          v-else-if="
            interactionSession.slides[currentIndex].type == 'wordCloud'
          "
          :slide="interactionSession.slides[currentIndex]"
        />
        <p v-else>
          Error: slide type [{{ interactionSession.slides[currentIndex].type }}]
          not recognised
        </p>
      </div>
    </Transition>
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
  letter-spacing: 2px;
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
