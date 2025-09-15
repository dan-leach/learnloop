<script setup>
/**
 * @module interaction/PresenterView
 * @summary Shows notes for the current slide and a preview of the next slide during an interaction session.
 * @description This component is used by the facilitator to view slide notes, preview next slide,
 * fetch live status updates and submissions from attendees, and handle session state.
 * It periodically polls for new submissions and session status.
 *
 * @requires vue
 * @requires vue-router
 * @requires ../router
 * @requires ../data/api.js
 * @requires ../data/interactionSession.js
 * @requires ../components/Loading.vue
 * @requires ./components/HostSlide.vue
 * @requires ../assets/promptSessionDetails
 * @requires sweetalert2
 */

import { onBeforeUnmount, onMounted, ref, inject } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import HostSlide from "./components/HostSlide.vue";
import { promptSessionDetails } from "../assets/promptSessionDetails";

const config = inject("config");

const loading = ref(true);
const currentIndex = ref(0);
const nextIndex = ref(1);

let myInterval = null; // For storing the polling interval

/**
 * Navigates to a given slide index and clears any stale submissions on the next slide.
 * @function goToSlide
 * @param {number} index - The slide index to navigate to.
 */
const goToSlide = (index) => {
  currentIndex.value = index;
  nextIndex.value = index + 1;

  // Clear submissions on the next slide to prepare for incoming data
  const nextSlide = interactionSession.slides[nextIndex.value];
  if (nextSlide?.interaction) nextSlide.interaction.submissions = [];
};

/**
 * Manually refresh submissions for the next slide.
 * @function refreshSubmissions
 */
const refreshSubmissions = () => {
  const nextSlide = interactionSession.slides[nextIndex.value];
  if (nextSlide?.interaction) nextSlide.interaction.submissions = [];
  fetchNewSubmissions();
};

let fetchNewSubmissionsFailCount = 0;

/**
 * Polls backend for new submissions on the next slide.
 * @function fetchNewSubmissions
 * @async
 */
const fetchNewSubmissions = async () => {
  const slide = interactionSession.slides[nextIndex.value];
  if (!slide?.interaction) return;

  const lastSubmissionId = slide.interaction.submissions.at(-1)?.id || 0;

  try {
    const response = await api("interaction/fetchNewSubmissions", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      slideIndex: nextIndex.value,
      lastSubmissionId,
      isPreview: interactionSession.status.preview,
    });

    slide.interaction.submissions.push(...response);
    fetchNewSubmissionsFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchNewSubmissionsFailCount++;
    console.error(
      "fetchNewSubmissions failed",
      fetchNewSubmissionsFailCount,
      error
    );
    if (fetchNewSubmissionsFailCount > 5 && !Swal.isVisible()) {
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
 * Fetches both session status and submissions, with a short delay between them.
 * @function fetchNewSubmissionsAndStatus
 */
const fetchNewSubmissionsAndStatus = () => {
  fetchStatus();
  setTimeout(fetchNewSubmissions, 1000); // allow status to update before fetching submissions
};

let fetchStatusFailCount = 0;

/**
 * Fetches current session status and updates local index if facilitator has moved.
 * @function fetchStatus
 * @async
 */
const fetchStatus = async () => {
  try {
    const response = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });

    interactionSession.status = response;

    if (currentIndex.value !== response.facilitatorIndex) {
      goToSlide(response.facilitatorIndex);
    }

    fetchStatusFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchStatusFailCount++;
    console.error("fetchStatus failed", fetchStatusFailCount, error);
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
 * Fetches session details as the host and initializes session state.
 * @function fetchDetailsHost
 * @async
 */
const fetchDetailsHost = async () => {
  try {
    const response = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id !== response.id) {
      console.error("Session ID mismatch", interactionSession.id, response.id);
      return;
    }

    Object.assign(interactionSession, {
      title: response.title,
      name: response.name,
      feedbackID: response.feedbackID,
      status: response.status,
      slides: [{ type: "waitingRoom" }, ...response.slides, { type: "end" }],
    });

    // Initialize each slide's interaction fields
    for (const slide of interactionSession.slides) {
      if (slide.hasContent) slide.content.show = true;
      if (slide.interaction) {
        slide.interaction.submissions = [];
        slide.interaction.submissionsCount = 0;
      }
    }

    fetchNewSubmissionsAndStatus();
    loading.value = false;

    myInterval = setInterval(
      fetchNewSubmissionsAndStatus,
      config.value.interaction.host.newSubmissionsPollInterval
    );
  } catch (error) {
    const errorMessage = Array.isArray(error)
      ? error.map((e) => e.msg).join(" ")
      : error;
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to launch interaction session hosting",
      text: errorMessage,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

/**
 * Lifecycle hook: on component mount, initialize session from route or prompt.
 */
onMounted(async () => {
  const { value: route } = useRouter().currentRoute;
  interactionSession.id = route.params.id;

  // Check for session credentials from presenterView
  const presenterViewSession = JSON.parse(
    localStorage.getItem("presenterViewSession")
  );
  if (presenterViewSession?.id === interactionSession.id) {
    interactionSession.pin = presenterViewSession.pin;
    localStorage.removeItem("presenterViewSession");
    fetchDetailsHost();
    return;
  }

  // Prompt user for session details if not in storage
  const { isConfirmed, id, pin } = await promptSessionDetails(
    interactionSession.id
  );

  if (!isConfirmed) {
    router.push("/");
    return;
  }

  interactionSession.id = id;
  interactionSession.pin = pin;
  history.replaceState({}, "", interactionSession.id);
  fetchDetailsHost();
});

/**
 * Lifecycle hook: on component unmount, clear polling interval.
 */
onBeforeUnmount(() => clearInterval(myInterval));
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
        {{
          interactionSession.title
            ? interactionSession.title
            : "[Session title]"
        }}
        |
        {{
          interactionSession.name
            ? interactionSession.name
            : "[Facilitator name]"
        }}
        | Presenter view
      </p>
      <div class="mb-3">
        <h4>Current slide notes</h4>
        {{
          interactionSession.slides[currentIndex].notes
            ? interactionSession.slides[currentIndex].notes
            : "No notes for this slide"
        }}
      </div>
      <div class="card bg-transparent shadow p-2 m-2">
        <h4>Next slide</h4>
        <HostSlide
          :currentIndex="nextIndex"
          :isPresenterView="true"
          class="m-2"
          :class="{ container: true }"
          @refreshSubmissions="refreshSubmissions"
        />
      </div>
    </div>
  </Transition>
</template>

<style scoped></style>
