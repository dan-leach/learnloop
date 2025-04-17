<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import { inject } from "vue";
const config = inject("config");
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import HostSlide from "./components/HostSlide.vue";
import Toast from "../assets/Toast.js";

const loading = ref(true);
let pollingInterval; // Declared here to be accessible by onBeforeUnmount

// If in preview mode should already have id and pin, else prompt for them, then load host view
onMounted(async () => {
  if (interactionSession.id && interactionSession.pin) {
    loadHostView();
  } else {
    interactionSession.id = useRouter().currentRoute.value.params.id;
    const { isConfirmed } = await Swal.fire({
      title: "Enter session ID and PIN",
      html:
        "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
        interactionSession.id +
        '">' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        interactionSession.id = document
          .getElementById("swalFormId")
          .value.trim();
        interactionSession.pin = document
          .getElementById("swalFormPin")
          .value.trim();
        if (interactionSession.pin == "") {
          Swal.showValidationMessage("Please enter your PIN");
          return false;
        }
        if (interactionSession.id == "") {
          Swal.showValidationMessage("Please enter a session ID");
          return false;
        }
        return true;
      },
    });

    if (isConfirmed) {
      history.replaceState({}, "", interactionSession.id);
      loadHostView();
    } else {
      router.push("/");
    }
  }
});

// Load and organise the data then remove the loading screen
const loadHostView = async () => {
  try {
    // Check if session ID and PIN are set
    if (!interactionSession.id || !interactionSession.pin)
      throw new Error("Session ID or PIN is empty");

    // If not in preview mode, fetch details
    if (!interactionSession.status?.preview) {
      await fetchDetailsHost();
    }

    // Add the waiting room and end slides
    interactionSession.slides.unshift({ type: "waitingRoom" });
    interactionSession.slides.push({ type: "end" });

    // Ensure starts on the waiting room slide
    if (interactionSession.status.facilitatorIndex != 0) {
      interactionSession.status.facilitatorIndex = 0;
      updateStatus();
    }

    // Set the default for each slide to start with content rather than interaction showing, and clear submissions
    for (let slide of interactionSession.slides) {
      if (slide.hasContent) slide.content.show = true;
      if (slide.interaction) {
        slide.interaction.submissions = [];
        slide.interaction.submissionsCount = 0;
      }
    }

    // Fetch the submission count for the waiting room host view
    fetchSubmissionCount();

    // Set up the regular new submissions polling
    pollingInterval = setInterval(
      fetchNewSubmissions,
      config.value.interaction.host.newSubmissionsPollInterval
    );

    // Remove the loading screen
    loading.value = false;
  } catch (error) {
    console.error("loadHostView failed", error);
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to launch interaction session hosting",
      text: error.message,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

// Update the status of the session on the server (e.g. preview true/false)
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

// Fetch the details of the session from the server
const fetchDetailsHost = async () => {
  const res = await api("interaction/fetchDetailsHost", {
    id: interactionSession.id,
    pin: interactionSession.pin,
  });

  if (interactionSession.id != res.id) {
    throw new error("Response session ID does not match request session ID");
  }

  interactionSession.title = res.title;
  interactionSession.name = res.name;
  interactionSession.feedbackID = res.feedbackID;
  interactionSession.status = res.status;
  interactionSession.slides = res.slides;

  return true;
};

// Fetch the submission count for the waiting room host view
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

// Fetch new submissions for the current slide
let fetchNewSubmissionsFailCount = 0;
const fetchNewSubmissions = async () => {
  if (
    !interactionSession.slides[interactionSession.status.facilitatorIndex]
      .interaction
  )
    return;
  const submissions =
    interactionSession.slides[interactionSession.status.facilitatorIndex]
      .interaction.submissions;
  const lastSubmissionId = submissions.length
    ? submissions[submissions.length - 1].id
    : 0;

  try {
    const response = await api("interaction/fetchNewSubmissions", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      slideIndex: interactionSession.status.facilitatorIndex,
      lastSubmissionId: lastSubmissionId,
      isPreview: interactionSession.status.preview,
    });

    for (let submission of response) submissions.push(submission);
    fetchNewSubmissionsFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchNewSubmissionsFailCount++;
    console.error(
      "fetchNewSubmissions failed - failCount: " + fetchNewSubmissionsFailCount,
      error
    );
    if (fetchNewSubmissionsFailCount > 5 && !Swal.isVisible())
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
};

// Fetch submissions for the current slide
const refreshSubmissions = () => {
  if (
    interactionSession.slides[interactionSession.status.facilitatorIndex]
      .interaction
  )
    interactionSession.slides[
      interactionSession.status.facilitatorIndex
    ].interaction.submissions = [];
  fetchNewSubmissions();
};

// Go to a specific slide index
const goToSlide = (index) => {
  config.value.client.isFocusView =
    index == 0 || index == interactionSession.slides.length - 1 ? false : true;
  if (
    (index == 1 && interactionSession.status.facilitatorIndex == 0) ||
    index == interactionSession.slides.length - 1
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
  if (index == 0) fetchSubmissionCount();
  if (
    interactionSession.slides[interactionSession.status.facilitatorIndex]
      .interaction
  )
    interactionSession.slides[
      interactionSession.status.facilitatorIndex
    ].interaction.submissions = [];
};

// Toggle the lock on the current slide
const toggleLockSlide = () => {
  //Need to -1 from current index as the lockedSlides array does not include the waiting room slide
  interactionSession.status.lockedSlides[
    interactionSession.status.facilitatorIndex - 1
  ] =
    !interactionSession.status.lockedSlides[
      interactionSession.status.facilitatorIndex - 1
    ];
  updateStatus();
};

// Exit the preview session and return to the create view
const exitPreviewSession = () => {
  // Remove the waiting room and end slides before returning to create view
  interactionSession.slides.shift();
  interactionSession.slides.pop();

  // Return to crteate view (in either edit or create mode)
  if (interactionSession.editMode) {
    router.push("/interaction/edit/slides/" + interactionSession.id);
  } else {
    router.push("/interaction/create/slides");
  }
};

// Reset the host view when the component is unmounted
onBeforeUnmount(() => {
  clearInterval(pollingInterval);
  loading.value = true;
  config.value.client.isFocusView = false;
  interactionSession.status.facilitatorIndex = 0;
  interactionSession.status.preview = false;
  updateStatus();
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
