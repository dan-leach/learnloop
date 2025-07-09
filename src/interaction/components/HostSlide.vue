<script setup>
/**
 * @module interaction/components/HostSlide
 * @summary Handles slide-level logic and user interactions in the host view of an interactive session.
 * @description
 * This component manages:
 * - Toggling content and response visibility
 * - Navigation between slides
 * - Triggering presenter view
 *
 * It communicates with parent components through emitted events and interacts
 * with global session state from `interactionSession`.
 *
 * @requires vue
 * @requires ../../data/interactionSession.js
 * @requires ./host/WaitingRoom.vue
 * @requires ./host/End.vue
 * @requires ./host/HideResponses.vue
 * @requires ./host/TrueFalse.vue
 * @requires ./host/MultipleChoice.vue
 * @requires ./host/FreeText.vue
 * @requires ./host/WordCloud.vue
 * @requires ./host/Gallery.vue
 */

import { inject, ref } from "vue";
import { interactionSession } from "../../data/interactionSession.js";

import WaitingRoom from "./host/WaitingRoom.vue";
import End from "./host/End.vue";
import HideResponses from "./host/HideResponses.vue";
import TrueFalse from "./host/TrueFalse.vue";
import MultipleChoice from "./host/MultipleChoice.vue";
import FreeText from "./host/FreeText.vue";
import WordCloud from "./host/WordCloud.vue";
import Gallery from "./host/Gallery.vue";
import TextStrings from "./host/TextStrings.vue";
import Video from "./host/Video.vue";
import TextStringsWithImage from "./host/TextStringsWithImage.vue";

const config = inject("config");

// Props from parent
const props = defineProps(["currentIndex", "isPresenterView"]);

// Emits to parent (for navigation and actions)
const emit = defineEmits([
  "goForward",
  "goBack",
  "goStart",
  "toggleLockSlide",
  "refreshSubmissions",
]);

/**
 * Shows interaction responses on the current slide.
 * Also clears existing submissions to restart the display.
 * @memberof module:interaction/components/HostSlide
 */
const showResponses = () => {
  const slide = interactionSession.slides[props.currentIndex];
  slide.interaction.settings.hideResponses = false;
  slide.interaction.submissions = [];
};

/**
 * Toggles content visibility on the current slide.
 * If content is hidden, it also triggers showing responses.
 * @memberof module:interaction/components/HostSlide
 */
const toggleContent = () => {
  const slide = interactionSession.slides[props.currentIndex];
  slide.content.show = !slide.content.show;
  slide.interaction.show = !slide.interaction.show;

  // If hiding the content, show responses immediately
  if (!slide.content.show) showResponses();
};

// Used to display validation markers during slide interaction
const showValidIndicators = ref(false);

/**
 * Emits navigation events to the parent and resets validation indicators.
 * @param {string} emitType - The name of the emit event (e.g., "goForward")
 * @memberof module:interaction/components/HostSlide
 */
const nav = (emitType) => {
  showValidIndicators.value = false;
  emit(emitType);
};

/**
 * Opens a new presenter view window for this session.
 * Session ID and PIN are stored in localStorage to enable access.
 * @memberof module:interaction/components/HostSlide
 */
const openPresenterView = () => {
  localStorage.setItem(
    "presenterViewSession",
    JSON.stringify({
      id: interactionSession.id,
      pin: interactionSession.pin,
    })
  );

  // Opens presenter view in a new tab
  window.open(`/interaction/presenter-view/${interactionSession.id}`, "_blank");
};
</script>

<template>
  <div id="slide-view" :class="{ focusView: config.client.isFocusView }">
    <ul class="nav nav-justified m-2" v-if="config.client.isFocusView">
      <li class="nav-item">
        <button
          v-if="currentIndex > 0"
          class="btn btn-lg"
          @click="nav('goBack')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
        </button>
      </li>
      <li>
        <p class="text-center m-1">
          To join go to {{ config.client.url.replace("https://", "") }}
          and use the code
          <span class="join-id-top p-1">{{ interactionSession.id }}</span>
        </p>
      </li>
      <li class="nav-item">
        <button
          v-if="currentIndex < interactionSession.slides.length - 1"
          class="btn btn-lg"
          @click="nav('goForward')"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
        </button>
      </li>
    </ul>
    <p class="display-6 text-center m-1">
      {{ interactionSession.slides[currentIndex].heading }}<br />
      <button
        v-if="
          interactionSession.slides[currentIndex].isInteractive &&
          !isPresenterView
        "
        class="btn btn-lg"
        @click="emit('toggleLockSlide')"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Lock this slide to prevent attendees submitting further responses"
      >
        <font-awesome-icon
          :icon="['fas', 'lock']"
          v-if="interactionSession.status.lockedSlides[currentIndex - 1]"
        />
        <font-awesome-icon
          :icon="['fas', 'lock-open']"
          v-if="!interactionSession.status.lockedSlides[currentIndex - 1]"
        />
      </button>
      <button
        v-if="
          interactionSession.slides[currentIndex].isInteractive &&
          !interactionSession.slides[currentIndex].content.show
        "
        class="btn btn-lg"
        @click="showValidIndicators = !showValidIndicators"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Toggle highlighting of correct and incorrect responses"
      >
        <font-awesome-icon
          :icon="['fas', 'circle-check']"
          :class="{ 'text-success': showValidIndicators }"
        />
      </button>
      <button
        v-if="
          interactionSession.slides[currentIndex].isInteractive &&
          !interactionSession.slides[currentIndex].content.show
        "
        class="btn btn-lg"
        @click="emit('refreshSubmissions')"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Refresh responses"
      >
        <font-awesome-icon :icon="['fas', 'arrows-rotate']" />
      </button>
      <button
        class="btn btn-teal btn-sm m-4"
        @click="toggleContent"
        v-if="
          interactionSession.slides[currentIndex].hasContent &&
          interactionSession.slides[currentIndex].isInteractive
        "
      >
        {{
          interactionSession.slides[currentIndex].content.show
            ? "Show responses"
            : "Show content"
        }}
      </button>
    </p>
    <div
      class="text-center"
      v-if="currentIndex == interactionSession.slides.length - 1"
    >
      <p class="display-6">The slides for this session have finished</p>
      <div>
        <button
          class="btn btn-lg btn-teal mx-2 mb-2"
          @click="emit('goBack')"
          v-if="!isPresenterView"
        >
          <font-awesome-icon :icon="['fas', 'circle-chevron-left']" /> Back
        </button>
        <button
          class="btn btn-lg btn-teal mx-2 mb-2"
          @click="emit('goStart')"
          v-if="!isPresenterView"
        >
          Restart
        </button>
      </div>
    </div>
    <div class="text-center" v-if="currentIndex == 0 && !isPresenterView">
      <button class="btn btn-sm btn-teal mb-2 me-4" @click="openPresenterView">
        Presenter view
        <font-awesome-icon :icon="['fas', 'arrow-up-right-from-square']" />
      </button>
      <button class="btn btn-lg btn-teal mb-2" @click="emit('goForward')">
        Start <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
      </button>
    </div>
    <!--content-->
    <div
      v-if="
        interactionSession.slides[currentIndex].hasContent &&
        interactionSession.slides[currentIndex].content.show
      "
    >
      <Gallery
        v-if="
          interactionSession.slides[currentIndex].content.layout == 'gallery'
        "
        :slide="interactionSession.slides[currentIndex]"
        :id="interactionSession.id"
      />
      <TextStrings
        v-else-if="
          interactionSession.slides[currentIndex].content.layout ==
          'textStrings'
        "
        :slide="interactionSession.slides[currentIndex]"
      />
      <Video
        v-else-if="
          interactionSession.slides[currentIndex].content.layout == 'video'
        "
        :slide="interactionSession.slides[currentIndex]"
      />
      <TextStringsWithImage
        v-else-if="
          interactionSession.slides[currentIndex].content.layout ==
          'textStringsWithImage'
        "
        :slide="interactionSession.slides[currentIndex]"
        :id="interactionSession.id"
      />
      <div v-else class="text-center text-danger m-5">
        Error: content layout [{{
          interactionSession.slides[currentIndex].content.layout
        }}] not recognised
      </div>
      <HideResponses
        v-if="interactionSession.slides[currentIndex].isInteractive"
        :slide="interactionSession.slides[currentIndex]"
      />
    </div>

    <!--interaction-->
    <div
      v-else-if="
        (interactionSession.slides[currentIndex].isInteractive &&
          interactionSession.slides[currentIndex].interaction.show) ||
        interactionSession.slides[currentIndex].type
      "
    >
      <div
        id="chart-area"
        class="d-flex justify-content-center chart-area mx-4"
        :class="{ focusView: config.client.isFocusView }"
        :key="'index' + currentIndex"
      >
        <WaitingRoom
          v-if="interactionSession.slides[currentIndex].type == 'waitingRoom'"
          :isPresenterView="isPresenterView"
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
        <TrueFalse
          v-else-if="
            interactionSession.slides[currentIndex].interaction.type ==
            'trueFalse'
          "
          :slide="interactionSession.slides[currentIndex]"
          :showValidIndicators="showValidIndicators"
        />
        <MultipleChoice
          v-else-if="
            interactionSession.slides[currentIndex].interaction.type ==
            'multipleChoice'
          "
          :slide="interactionSession.slides[currentIndex]"
          :showValidIndicators="showValidIndicators"
        />
        <FreeText
          v-else-if="
            interactionSession.slides[currentIndex].interaction.type ==
            'freeText'
          "
          :slide="interactionSession.slides[currentIndex]"
        />
        <WordCloud
          v-else-if="
            interactionSession.slides[currentIndex].interaction.type ==
            'wordCloud'
          "
          :slide="interactionSession.slides[currentIndex]"
        />
        <div v-else class="text-center text-danger m-5">
          Error: interaction type [{{
            interactionSession.slides[currentIndex].interaction.type
          }}]not recognised
        </div>
      </div>
    </div>

    <!--no content or interaction-->
    <div v-else class="text-center text-danger m-5">
      Error: no content or interaction provided for this slide
    </div>
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
