<script setup>
/**
 * @module interaction/Join
 * @summary Join view logic for LearnLoop interactive sessions.
 * @description
 * This script handles joining an interaction session as a participant, including:
 * - Prompting for session ID
 * - Fetching session details from the server
 * - Handling real-time slide synchronization with facilitator
 * - Navigating between slides
 * - Displaying loading state and error alerts
 *
 * @requires vue
 * @requires vue-router
 * @requires sweetalert2
 * @requires ../components/Loading.vue
 * @requires ./components/JoinSlide.vue
 * @requires ../data/api.js
 * @requires ../data/interactionSession.js
 * @requires ../assets/promptSessionDetails.js
 */

import { onMounted, ref, inject } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import JoinSlide from "./components/JoinSlide.vue";
import { promptSessionDetails } from "../assets/promptSessionDetails";

const config = inject("config");

const loading = ref(true);
const showSlide = ref(true);
const currentIndex = ref(0);

/**
 * Navigate to a given slide index with a short fade effect.
 * @param {number} index - Target slide index
 * @memberof module:interaction/Join
 */
const goToSlide = (index) => {
  showSlide.value = false;
  currentIndex.value = index;
  // Small delay to allow re-rendering of JoinSlide with new index
  setTimeout(() => {
    showSlide.value = true;
  }, 250);
};

let fetchStatusFailCount = 0;

/**
 * Fetch live session status to keep participant synced with facilitator.
 * Called periodically based on poll interval.
 * @returns {Promise<void>}
 * @memberof module:interaction/Join
 */
const fetchStatus = async () => {
  try {
    const response = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });
    interactionSession.status = response;

    let awaitUser = false;
    const currentSlide = interactionSession.slides[currentIndex.value];

    // Determine if user should remain on current slide awaiting input
    if (currentSlide?.isInteractive) {
      if (currentSlide.interaction.response) awaitUser = true;
      if (
        currentSlide.interaction.closed ||
        interactionSession.status.lockedSlides[currentIndex.value - 1]
      ) {
        awaitUser = false;
      }
    }

    // Sync with facilitator if user is not expected to respond
    if (
      currentIndex.value !== interactionSession.status.facilitatorIndex &&
      !awaitUser
    ) {
      goToSlide(interactionSession.status.facilitatorIndex);
    }

    fetchStatusFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchStatusFailCount++;
    console.error(
      "fetchStatus failed - failCount: " + fetchStatusFailCount,
      error
    );

    // Show offline warning after several failed attempts
    if (fetchStatusFailCount > 5 && !Swal.isVisible()) {
      Swal.fire({
        toast: true,
        showConfirmButton: false,
        icon: "error",
        iconColor: "#17a2b8",
        title: "Connection to LearnLoop failed",
        text: "Please check your internet connection",
        position: "bottom",
        width: "450px",
      });
    }
  }
};

/**
 * Fetch session details and slide structure using session ID.
 * Initializes interactionSession and starts polling.
 * @returns {Promise<void>}
 * @memberof module:interaction/Join
 */
const fetchDetails = async () => {
  try {
    const response = await api("interaction/fetchDetailsJoin", {
      id: interactionSession.id,
    });

    //Ensure session ID of response matches request
    if (interactionSession.id !== response.id) {
      console.error(
        "interactionSession.id != response.id",
        interactionSession.id,
        response.id
      );
      return;
    }

    // Initialize interaction session object
    interactionSession.title = response.title;
    interactionSession.name = response.name;
    interactionSession.feedbackID = response.feedbackID;

    // Add start and end slides
    response.slides.unshift({ type: "waitingRoom" });
    response.slides.push({ type: "end" });

    interactionSession.slides = response.slides;

    // Reset submission counts
    for (const slide of interactionSession.slides) {
      if (slide.isInteractive) slide.interaction.submissionCount = 0;
    }

    interactionSession.status = response.status;
    currentIndex.value = interactionSession.status.facilitatorIndex;
    loading.value = false;

    // Start polling for status updates
    setInterval(
      fetchStatus,
      config.value.interaction.join.currentIndexPollInterval
    );
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");

    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to join interaction session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });

    router.push("/");
  }
};

/**
 * Lifecycle hook: Runs when component is mounted.
 * Handles join flow by reading session ID or prompting user.
 */
onMounted(async () => {
  interactionSession.id = useRouter().currentRoute.value.params.id;

  if (interactionSession.id) {
    fetchDetails();
    return;
  }

  // Prompt user for session ID
  const { isConfirmed, id } = await promptSessionDetails(
    interactionSession.id,
    "Join session",
    "You will need a session ID provided by your facilitator.",
    true,
    false
  );

  if (!isConfirmed) {
    router.push("/");
    return;
  }

  interactionSession.id = id;
  history.replaceState({}, "", interactionSession.id);
  fetchDetails();
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">
        Interaction {{ interactionSession.status.preview ? "Preview" : "" }}
      </h1>
      <p class="text-center">
        {{ interactionSession.title }} | {{ interactionSession.name }}
      </p>
      <Transition name="slide-up">
        <div class="d-flex justify-content-around flex-wrap" v-if="showSlide">
          <div class="side-content"></div>
          <!--side-content on left too to ensure Slide panel remains center-->
          <div class="container card m-2">
            <JoinSlide :currentIndex="currentIndex" />
          </div>
          <div class="side-content align-self-center">
            <Transition name="fade" appear>
              <div
                class="card bg-teal text-center p-2"
                v-show="
                  currentIndex != interactionSession.status.facilitatorIndex
                "
                @click="goToSlide(interactionSession.status.facilitatorIndex)"
              >
                <font-awesome-icon
                  :icon="['fas', 'circle-chevron-right']"
                  class="display-5"
                />
                Catch up to facilitator
              </div>
            </Transition>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<style scoped>
.side-content {
  width: 150px;
}
.container {
  max-width: 600px;
}
</style>
