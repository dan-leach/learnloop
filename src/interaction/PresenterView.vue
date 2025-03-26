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

const loading = ref(true);
const currentIndex = ref(0);
const nextIndex = ref(1);

const goToSlide = (index) => {
  currentIndex.value = index;
  nextIndex.value = index + 1;
  if (interactionSession.slides[nextIndex.value].interaction)
    interactionSession.slides[nextIndex.value].interaction.submissions = [];
};

const refreshSubmissions = () => {
  if (interactionSession.slides[nextIndex.value].interaction)
    interactionSession.slides[nextIndex.value].interaction.submissions = [];
  fetchNewSubmissions();
};

let fetchNewSubmissionsFailCount = 0;
const fetchNewSubmissions = async () => {
  if (!interactionSession.slides[nextIndex.value].interaction) return;
  const lastSubmissionId = interactionSession.slides[nextIndex.value]
    .interaction.submissions.length
    ? interactionSession.slides[nextIndex.value].interaction.submissions[
        interactionSession.slides[nextIndex.value].interaction.submissions
          .length - 1
      ].id
    : 0;
  try {
    const response = await api("interaction/fetchNewSubmissions", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      slideIndex: nextIndex.value,
      lastSubmissionId: lastSubmissionId,
      isPreview: interactionSession.status.preview,
    });

    for (let submission of response) {
      interactionSession.slides[nextIndex.value].interaction.submissions.push(
        submission
      );
    }
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

const fetchNewSubmissionsAndStatus = () => {
  fetchStatus();
  //add a delay to allow fetchStatus to complete before fetchNewSubmissions
  setTimeout(fetchNewSubmissions, 1000);
};

let myInterval; //declared here to be accessible by onMounted and onBeforeUnmount

const fetchDetailsHost = async () => {
  try {
    const response = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id != response.id) {
      console.error(
        "interactionSession.id != response.id",
        interactionSession.id,
        response.id
      );
      return;
    }
    interactionSession.title = response.title;
    interactionSession.name = response.name;
    interactionSession.feedbackID = response.feedbackID;
    interactionSession.status = response.status;
    response.slides.unshift({ type: "waitingRoom" });
    response.slides.push({ type: "end" });
    interactionSession.slides = response.slides;
    for (let slide of interactionSession.slides) {
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

let fetchStatusFailCount = 0;
const fetchStatus = async () => {
  try {
    const response = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });
    interactionSession.status = response;

    if (currentIndex.value != interactionSession.status.facilitatorIndex)
      goToSlide(interactionSession.status.facilitatorIndex);

    fetchStatusFailCount = 0;
    Swal.close();
  } catch (error) {
    fetchStatusFailCount++;
    console.error(
      "fetchStatus failed - failCount: " + fetchStatusFailCount,
      error
    );
    if (fetchStatusFailCount > 5 && !Swal.isVisible())
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

onMounted(async () => {
  interactionSession.id = useRouter().currentRoute.value.params.id;

  //check to see if there is a sessionStorage object with an id and pin
  const presenterViewSession = JSON.parse(
    localStorage.getItem("presenterViewSession")
  );
  if (
    presenterViewSession &&
    presenterViewSession.id == interactionSession.id
  ) {
    //if there is, set the interactionSession pin to the pin in the sessionStorage object
    interactionSession.pin = presenterViewSession.pin;
    //remove the sessionStorage object
    localStorage.removeItem("presenterViewSession");
    fetchDetailsHost();
    return;
  }

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
      if (interactionSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (interactionSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  });

  if (isConfirmed) {
    history.replaceState({}, "", interactionSession.id);
    fetchDetailsHost();
  } else {
    router.push("/");
  }
});

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
