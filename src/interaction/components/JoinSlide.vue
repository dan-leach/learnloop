<script setup>
/**
 * @module interaction/components/JoinSlide
 * @summary Handles user response submission for interactive slides in Join view.
 * @description
 * This component manages the response submission lifecycle:
 * - Detects the current interactive slide via prop
 * - Submits user response to the backend
 * - Displays submission feedback via Toast or SweetAlert
 * - Handles submission limits and input reset logic
 *
 * @requires vue
 * @requires ../../data/api.js
 * @requires ../../data/interactionSession.js
 * @requires sweetalert2
 * @requires ../../assets/Toast.js
 */

import { ref } from "vue";
import { api } from "../../data/api.js";
import { interactionSession } from "../../data/interactionSession.js";
import WaitingRoom from "./join/WaitingRoom.vue";
import End from "./join/End.vue";
import TrueFalse from "./join/TrueFalse.vue";
import MultipleChoice from "./join/MultipleChoice.vue";
import FreeText from "./join/FreeText.vue";
import Gallery from "./join/Gallery.vue";
import TextStrings from "./join/TextStrings.vue";
import Video from "./join/Video.vue";
import TextStringsWithImage from "./join/TextStringsWithImage.vue";
import Swal from "sweetalert2";
import Toast from "../../assets/Toast.js";

// Props
const props = defineProps(["currentIndex"]);

// UI state
const spinner = ref(false);
const btnSubmitText = ref("Submit");
const btnSubmitBelowText = ref("");

/**
 * Submit the current response to the backend and handle submission logic.
 * @returns {Promise<void>}
 * @memberof module:interaction/components/JoinSlide
 */
const submit = async () => {
  const slide = interactionSession.slides[props.currentIndex];
  slide.interaction.closed = true; // prevent resubmission during request
  spinner.value = true;
  btnSubmitText.value = "Please wait";

  try {
    // Send submission to API
    await api("interaction/insertSubmission", {
      id: interactionSession.id,
      slideIndex: props.currentIndex,
      response: slide.interaction.response,
      isPreview: interactionSession.status.preview,
    });

    // On success
    Toast.fire({
      icon: "success",
      title: "Your response was submitted",
    });

    spinner.value = false;
    slide.interaction.submissionCount++;

    const limit = slide.interaction.settings.submissionLimit;
    const hasMoreSubmissions = slide.interaction.submissionCount < limit;

    if (hasMoreSubmissions) {
      // Reset for another response
      slide.interaction.closed = false;
      btnSubmitText.value = "Submit";
      btnSubmitBelowText.value = "You may submit multiple responses";
      slide.interaction.response = "";
    } else {
      // Submission limit reached
      btnSubmitText.value = "Done";
      btnSubmitBelowText.value =
        slide.interaction.submissionCount === limit
          ? "You have reached the submission limit"
          : "";
    }
  } catch (error) {
    // Format and show error
    const message = Array.isArray(error)
      ? error.map((e) => e.msg).join(" ")
      : error;

    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to submit your response",
      text: message,
      confirmButtonColor: "#17a2b8",
    });

    // Re-enable interaction for retry
    slide.interaction.closed = false;
    spinner.value = false;
    btnSubmitText.value = "Try again?";
  }
};
</script>

<template>
  <div>
    <p class="text-center">
      <strong>{{ interactionSession.slides[currentIndex].heading }}</strong>
    </p>
    <div class="d-flex justify-content-center">
      <div class="full-width">
        <!--content-->
        <div v-if="interactionSession.slides[currentIndex].hasContent">
          <div
            v-if="
              !interactionSession.slides[currentIndex].content
                .showContentForAttendees
            "
            class="p-3 text-center"
          >
            This content is shown on the host screen only.
          </div>
          <Gallery
            v-else-if="
              interactionSession.slides[currentIndex].content.layout ==
              'gallery'
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
        </div>

        <!--interaction-->
        <div
          v-if="
            interactionSession.slides[currentIndex].isInteractive ||
            interactionSession.slides[currentIndex].type
          "
        >
          <WaitingRoom
            v-if="interactionSession.slides[currentIndex].type == 'waitingRoom'"
          />
          <End
            v-else-if="interactionSession.slides[currentIndex].type == 'end'"
            :feedbackID="interactionSession.feedbackID"
          />
          <TrueFalse
            v-else-if="
              interactionSession.slides[currentIndex].interaction.type ==
              'trueFalse'
            "
            :slide="interactionSession.slides[currentIndex]"
            :spinner="spinner"
            :currentIndex="currentIndex"
            :btnSubmitText="btnSubmitText"
            :btnSubmitBelowText="btnSubmitBelowText"
            @submit="submit"
          />
          <MultipleChoice
            v-else-if="
              interactionSession.slides[currentIndex].interaction.type ==
              'multipleChoice'
            "
            :slide="interactionSession.slides[currentIndex]"
            :spinner="spinner"
            :currentIndex="currentIndex"
            :btnSubmitText="btnSubmitText"
            :btnSubmitBelowText="btnSubmitBelowText"
            @submit="submit"
          />
          <FreeText
            v-else-if="
              interactionSession.slides[currentIndex].interaction.type ==
                'freeText' ||
              interactionSession.slides[currentIndex].interaction.type ==
                'wordCloud'
            "
            :slide="interactionSession.slides[currentIndex]"
            :spinner="spinner"
            :currentIndex="currentIndex"
            :btnSubmitText="btnSubmitText"
            :btnSubmitBelowText="btnSubmitBelowText"
            @submit="submit"
          />
        </div>

        <!-- No content or interaction -->
        <div
          v-if="
            !interactionSession.slides[currentIndex].hasContent &&
            !interactionSession.slides[currentIndex].isInteractive &&
            !interactionSession.slides[currentIndex].type
          "
          class="text-center text-danger m-5"
        >
          Error: no content or interaction provided for this slide
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.full-width {
  width: 100%;
}
.slideComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
