<script setup>
/**
 * @module interaction/Host
 * @summary Host view for interaction sessions.
 * @description
 * This module powers the hosting view for an interactive session, managing:
 * - Fetching session and slide data
 * - Real-time polling of new submissions
 * - Slide navigation, locking, and status updates
 * - Preview session exit and session cleanup
 *
 * @requires vue
 * @requires vue-router
 * @requires ../data/api.js
 * @requires ../data/interactionSession.js
 * @requires ../assets/promptSessionDetails
 * @requires ../assets/Toast.js
 * @requires ../components/Loading.vue
 * @requires ./components/HostSlide.vue
 */

import { onBeforeUnmount, onMounted, ref, inject } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";
import { promptSessionDetails } from "../assets/promptSessionDetails";
import Loading from "../components/Loading.vue";
import HostSlide from "./components/HostSlide.vue";

const config = inject("config");
const loading = ref(true);
let pollingInterval;

/**
 * Handles initial mount behavior: fetch or prompt for session ID and PIN,
 * then load host view.
 * @memberof module:interaction/Host
 */
onMounted(async () => {
  if (interactionSession.id && interactionSession.pin) {
    loadHostView();
  } else {
    interactionSession.id = useRouter().currentRoute.value.params.id;

    if (interactionSession.id === "preview") {
      router.push("/interaction/edit");
      return;
    }

    const { isConfirmed, id, pin } = await promptSessionDetails(
      interactionSession.id
    );

    if (!isConfirmed) return router.push("/");

    interactionSession.id = id;
    interactionSession.pin = pin;
    history.replaceState({}, "", interactionSession.id);
    loadHostView();
  }
});

/**
 * Loads the host session view including fetching session details,
 * initializing slides, and starting polling.
 * @memberof module:interaction/Host
 */
const loadHostView = async () => {
  try {
    if (!interactionSession.id || !interactionSession.pin) {
      throw new Error("Session ID or PIN is empty");
    }

    if (!interactionSession.status?.preview) {
      await fetchDetailsHost();
    }

    // Inject waiting room and end slides
    interactionSession.slides.unshift({ type: "waitingRoom" });
    interactionSession.slides.push({ type: "end" });

    // Ensure starting at the first slide
    if (interactionSession.status.facilitatorIndex !== 0) {
      interactionSession.status.facilitatorIndex = 0;
      updateStatus();
    }

    // Set defaults per slide
    for (const slide of interactionSession.slides) {
      if (slide.hasContent) slide.content.show = true;
      if (slide.isInteractive) {
        slide.interaction.submissions = [];
        slide.interaction.submissionsCount = 0;
        slide.interaction.show = slide.hasContent ? false : true;
      }
    }

    fetchSubmissionCount();

    pollingInterval = setInterval(
      fetchNewSubmissions,
      config.value.interaction.host.newSubmissionsPollInterval
    );

    loading.value = false;
  } catch (error) {
    console.error("loadHostView failed", error);
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to launch interaction session hosting",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

/**
 * Updates session status on the server.
 * @memberof module:interaction/Host
 */
const updateStatus = async () => {
  try {
    await api("interaction/updateStatus", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      status: interactionSession.status,
    });
  } catch (error) {
    console.error("updateStatus failed", error);
  }
};

/**
 * Fetches session details for the host.
 * @memberof module:interaction/Host
 * @returns {Promise<boolean>}
 * @throws {Error} If session ID mismatch occurs
 */
const fetchDetailsHost = async () => {
  const response = await api("interaction/fetchDetailsHost", {
    id: interactionSession.id,
    pin: interactionSession.pin,
  });

  if (interactionSession.id !== response.id) {
    throw new Error("Response session ID does not match request session ID");
  }

  Object.assign(interactionSession, {
    title: response.title,
    name: response.name,
    feedbackID: response.feedbackID,
    status: response.status,
    slides: response.slides,
  });

  return true;
};

/**
 * Fetches initial submission count for waiting room.
 * @memberof module:interaction/Host
 */
const fetchSubmissionCount = async () => {
  try {
    const response = await api("interaction/fetchSubmissionCount", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      isPreview: interactionSession.status.preview,
    });

    interactionSession.submissionCount = response;
  } catch (error) {
    console.error("fetchSubmissionCount failed", error);
  }
};

let fetchNewSubmissionsFailCount = 0;

/**
 * Fetches new submissions for the current slide, with failure handling.
 * @memberof module:interaction/Host
 */
const fetchNewSubmissions = async () => {
  const currentSlide =
    interactionSession.slides[interactionSession.status.facilitatorIndex];
  if (!currentSlide.isInteractive) return;

  const submissions = currentSlide.interaction.submissions;
  const lastId = submissions.length ? submissions.at(-1).id : 0;

  try {
    const newSubs = await api("interaction/fetchNewSubmissions", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      slideIndex: interactionSession.status.facilitatorIndex,
      lastSubmissionId: lastId,
      isPreview: interactionSession.status.preview,
    });

    submissions.push(...newSubs);
    fetchNewSubmissionsFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchNewSubmissionsFailCount++;
    console.error("fetchNewSubmissions failed", error);
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
 * Clears submissions for the current slide and fetches new ones.
 * @memberof module:interaction/Host
 */
const refreshSubmissions = () => {
  const currentSlide =
    interactionSession.slides[interactionSession.status.facilitatorIndex];
  if (currentSlide.isInteractive) currentSlide.interaction.submissions = [];
  fetchNewSubmissions();
};

/**
 * Navigate to a specific slide index and update relevant UI state.
 * @memberof module:interaction/Host
 * @param {number} index - Slide index to navigate to
 */
const goToSlide = (index) => {
  config.value.client.isFocusView =
    index > 0 && index < interactionSession.slides.length - 1;

  // Offer fullscreen tip when entering/exiting interactive phase
  if (
    (index === 1 && interactionSession.status.facilitatorIndex === 0) ||
    index === interactionSession.slides.length - 1
  ) {
    Toast.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Press F11 to toggle fullscreen.",
      position: "center",
    });
  }

  interactionSession.status.facilitatorIndex = index;
  updateStatus();

  if (index === 0) fetchSubmissionCount();

  const currentSlide = interactionSession.slides[index];
  if (currentSlide.isInteractive) currentSlide.interaction.submissions = [];
};

/**
 * Toggle whether the current slide is locked.
 * @memberof module:interaction/Host
 */
const toggleLockSlide = () => {
  const i = interactionSession.status.facilitatorIndex - 1; // exclude waiting room
  interactionSession.status.lockedSlides[i] =
    !interactionSession.status.lockedSlides[i];
  updateStatus();
};

/**
 * Exit preview session and return to the appropriate editor view.
 * @memberof module:interaction/Host
 */
const exitPreviewSession = () => {
  interactionSession.slides.shift(); // Remove waiting room
  interactionSession.slides.pop(); // Remove end slide

  const target = interactionSession.isEdit
    ? `/interaction/edit/slides/${interactionSession.id}`
    : "/interaction/create/slides";

  router.push(target);
};

/**
 * Cleanup interval and session state when unmounted.
 * @memberof module:interaction/Host
 */
onBeforeUnmount(() => {
  clearInterval(pollingInterval);
  loading.value = true;
  config.value.client.isFocusView = false;
  if (interactionSession.status) {
    interactionSession.status.facilitatorIndex = 0;
    interactionSession.status.preview = false;
    updateStatus();
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <div class="text-center my-3" v-if="interactionSession.status.preview">
        <button
          class="btn btn-teal"
          id="exitPreviewSession"
          @click="exitPreviewSession"
        >
          Exit preview
        </button>
      </div>
      <h1 v-if="!config.client.isFocusView" class="text-center display-4">
        Interaction {{ interactionSession.status.preview ? "Preview" : "" }}
      </h1>
      <p v-if="!config.client.isFocusView" class="text-center">
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
      </p>
      <HostSlide
        :currentIndex="interactionSession.status.facilitatorIndex"
        class="m-2"
        :class="{ container: !config.client.isFocusView }"
        @goForward="goToSlide(interactionSession.status.facilitatorIndex + 1)"
        @goBack="goToSlide(interactionSession.status.facilitatorIndex - 1)"
        @goStart="goToSlide(0)"
        @toggleLockSlide="toggleLockSlide"
        @refreshSubmissions="refreshSubmissions"
      />
    </div>
  </Transition>
</template>

<style scoped></style>
