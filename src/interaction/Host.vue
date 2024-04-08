<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import { config } from "../data/config.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import HostSlide from "./components/HostSlide.vue";
import Toast from "../assets/Toast.js";

const loading = ref(true);
const currentIndex = ref(0);

const updateHostStatus = () => {
  interactionSession.hostStatus.facilitatorIndex = currentIndex.value;
  api(
    "interaction",
    "updateHostStatus",
    interactionSession.id,
    interactionSession.pin,
    interactionSession.hostStatus
  ).then(
    function () {},
    function (error) {
      console.log("updateHostStatus failed", error);
    }
  );
};

const fetchSubmissionCount = () => {
  api(
    "interaction",
    "fetchSubmissionCount",
    interactionSession.id,
    interactionSession.pin,
    null
  ).then(
    function (res) {
      interactionSession.submissionCount = res;
    },
    function (error) {
      console.log("fetchSubmissionCount failed", error);
    }
  );
};

const goToSlide = (index) => {
  config.client.isFocusView =
    index == 0 || index == interactionSession.slides.length - 1 ? false : true;
  if (
    (index == 1 && currentIndex.value == 0) ||
    index == interactionSession.slides.length - 1
  ) {
    Toast.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Press F11 to toggle fullscreen.",
      position: "center",
    });
  }
  currentIndex.value = index;
  updateHostStatus();
  if (index == 0) fetchSubmissionCount();
  if (interactionSession.slides[currentIndex.value].interaction)
    interactionSession.slides[currentIndex.value].interaction.submissions = [];
  console.log(interactionSession.slides[currentIndex.value]);
};
const toggleLockSlide = () => {
  interactionSession.hostStatus.lockedSlides[currentIndex.value] =
    !interactionSession.hostStatus.lockedSlides[currentIndex.value];
  updateHostStatus();
};

let fetchNewSubmissionsFailCount = 0;
const fetchNewSubmissions = () => {
  if (!interactionSession.slides[currentIndex.value].interaction) return;
  const submissions =
    interactionSession.slides[currentIndex.value].interaction.submissions;
  const lastSubmissionId = submissions.length
    ? submissions[submissions.length - 1].id
    : 0;
  api(
    "interaction",
    "fetchNewSubmissions",
    interactionSession.id,
    interactionSession.pin,
    {
      slideIndex: currentIndex.value,
      lastSubmissionId: lastSubmissionId,
    }
  ).then(
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

const fetchDetailsHost = () => {
  api(
    "interaction",
    "fetchDetailsHost",
    interactionSession.id,
    interactionSession.pin,
    null
  ).then(
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
      fetchSubmissionCount();
      updateHostStatus();
      fetchNewSubmissions();
      setInterval(
        fetchNewSubmissions,
        config.interaction.host.newSubmissionsPollInterval
      );
    },
    function (error) {
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

const isPreview = ref(useRouter().currentRoute.value.params.id == "preview");
const exitPreviewSession = () => {
  router.push("/interaction/create");
};

onMounted(() => {
  if (isPreview.value) {
    console.log("preview", interactionSession);
    if (!interactionSession.slides.length) {
      Swal.fire({
        title: "Unable to preview session",
        text: "The session contains no slides to preview. You will now be returned to the session creation page",
        confirmButtonColor: "#17a2b8",
      }).then(() => {
        router.push("/interaction/create");
      });
    } else {
      for (let slide of interactionSession.slides) {
        if (slide.hasContent) slide.content.show = true;
        if (slide.interaction) {
          slide.interaction.submissions = [];
          slide.interaction.submissionsCount = 0;
        }
      }
      loading.value = false;
    }
  } else {
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
  }
});

onBeforeUnmount(() => {
  if (!isPreview) {
    let hostStatus = { facilitatorIndex: 0 };
    api(
      "interaction",
      "updateHostStatus",
      interactionSession.id,
      interactionSession.pin,
      hostStatus
    ).then(
      function () {},
      function (error) {
        console.log("updateHostStatus failed", error);
      }
    );
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <div class="text-center my-3" v-if="isPreview">
        <button
          class="btn btn-teal"
          id="exitPreviewSession"
          @click="exitPreviewSession"
        >
          Exit preview
        </button>
      </div>
      <h1 v-if="!config.client.isFocusView" class="text-center display-4">
        Interaction
      </h1>
      <p v-if="!config.client.isFocusView" class="text-center">
        {{ interactionSession.title }} | {{ interactionSession.name }}
      </p>
      <HostSlide
        :currentIndex="currentIndex"
        class="m-2"
        :class="{ container: !config.client.isFocusView }"
        @goForward="goToSlide(currentIndex + 1)"
        @goBack="goToSlide(currentIndex - 1)"
        @toggleLockSlide="toggleLockSlide"
      />
    </div>
  </Transition>
</template>

<style scoped></style>
