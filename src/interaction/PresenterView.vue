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
const currentIndex = ref(0);

const goToSlide = (index) => {
  currentIndex.value = index;
  if (interactionSession.slides[currentIndex.value].interaction)
    interactionSession.slides[currentIndex.value].interaction.submissions = [];
  console.log(interactionSession.slides[currentIndex.value]);
};
const toggleLockSlide = () => {
  interactionSession.status.lockedSlides[currentIndex.value] =
    !interactionSession.status.lockedSlides[currentIndex.value];
  if (!isPreview.value) updateStatus();
};

let fetchNewSubmissionsFailCount = 0;
const fetchNewSubmissions = () => {
  console.log(currentIndex.value);
  if (!interactionSession.slides[currentIndex.value].interaction) return;
  const submissions =
    interactionSession.slides[currentIndex.value].interaction.submissions;
  const lastSubmissionId = submissions.length
    ? submissions[submissions.length - 1].id
    : 0;
  api("interaction/fetchNewSubmissions", {
    id: interactionSession.id,
    pin: interactionSession.pin,
    slideIndex: currentIndex.value,
    lastSubmissionId: lastSubmissionId,
  }).then(
    function (res) {
      for (let submission of res) submissions.push(submission);
      fetchNewSubmissionsFailCount = 0;
      Swal.close();
    },
    function (error) {
      fetchNewSubmissionsFailCount++;
      console.log(
        "fetchNewSubmissions failed - failCount: " +
          fetchNewSubmissionsFailCount,
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
  );
};

const fetchNewSubmissionsAndStatus = () => {
  fetchNewSubmissions();
  fetchStatus();
};

let myInterval; //declared here to be accessible by onMounted and onBeforeUnmount

const fetchDetailsHost = () => {
  api("interaction/fetchDetailsHost", {
    id: interactionSession.id,
    pin: interactionSession.pin,
  }).then(
    function (res) {
      if (interactionSession.id != res.id) {
        console.error(
          "interactionSession.id != res.id",
          interactionSession.id,
          response.id
        );
        return;
      }
      interactionSession.title = res.title;
      interactionSession.name = res.name;
      interactionSession.feedbackID = res.feedbackID;
      interactionSession.status = res.status;
      res.slides.unshift({ type: "waitingRoom" });
      res.slides.push({ type: "end" });
      interactionSession.slides = res.slides;
      for (let slide of interactionSession.slides) {
        if (slide.hasContent) slide.content.show = true;
        if (slide.interaction) {
          slide.interaction.submissions = [];
          slide.interaction.submissionsCount = 0;
        }
      }
      loading.value = false;
      fetchNewSubmissions();
      myInterval = setInterval(
        fetchNewSubmissionsAndStatus,
        config.value.interaction.host.newSubmissionsPollInterval
      );
    },
    function (error) {
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
  );
};

let fetchStatusFailCount = 0;
const fetchStatus = () => {
  api("interaction/fetchStatus", {
    id: interactionSession.id,
  }).then(
    function (res) {
      interactionSession.status = res;

      goToSlide(interactionSession.status.facilitatorIndex);

      fetchStatusFailCount = 0;
      Swal.close();
    },
    function (error) {
      fetchStatusFailCount++;
      console.log(
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
  );
};

onMounted(() => {
  interactionSession.id = useRouter().currentRoute.value.params.id;
  Swal.fire({
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
      interactionSession.id = document.getElementById("swalFormId").value;
      interactionSession.pin = document.getElementById("swalFormPin").value;
      if (interactionSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (interactionSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  }).then((result) => {
    if (result.isConfirmed) {
      history.replaceState({}, "", interactionSession.id);
      fetchDetailsHost();
    } else {
      router.push("/");
    }
  });
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interaction</h1>
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
          :currentIndex="currentIndex + 1"
          :isPresenterView="true"
          class="m-2"
          :class="{ container: true }"
        />
      </div>
    </div>
  </Transition>
</template>

<style scoped></style>
