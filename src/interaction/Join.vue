<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import { inject } from "vue";
const config = inject("config");
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import JoinSlide from "./components/JoinSlide.vue";

const loading = ref(true);
const showSlide = ref(true);
const currentIndex = ref(0);
const facilitatorIndex = ref(0);

const goToSlide = (index) => {
  showSlide.value = false;
  currentIndex.value = index;
  setTimeout(() => {
    showSlide.value = true;
  }, 250);
};

let fetchHostStatusFailCount = 0;
const fetchHostStatus = () => {
  api("interaction", "fetchHostStatus", interactionSession.id, null, null).then(
    function (res) {
      facilitatorIndex.value = res.facilitatorIndex;
      interactionSession.hostStatus.lockedSlides = res.lockedSlides;
      let awaitUser = false;
      if (interactionSession.slides[currentIndex.value].interaction) {
        if (interactionSession.slides[currentIndex.value].interaction.response)
          awaitUser = true;
        if (
          interactionSession.slides[currentIndex.value].interaction.closed ||
          interactionSession.hostStatus.lockedSlides[currentIndex.value]
        )
          awaitUser = false;
      }
      if (currentIndex.value != facilitatorIndex.value && !awaitUser)
        goToSlide(facilitatorIndex.value);
      fetchHostStatusFailCount = 0;
      Swal.close();
    },
    function (error) {
      fetchHostStatusFailCount++;
      console.log(
        "fetchHostStatus failed - failCount: " + fetchHostStatusFailCount,
        error
      );
      if (fetchHostStatusFailCount > 5 && !Swal.isVisible())
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

const fetchDetails = () => {
  api("interaction", "fetchDetails", interactionSession.id, null, null).then(
    function (res) {
      if (interactionSession.id != res.id) {
        console.error(
          "interactionSession.id != res.id",
          interactionSession.id,
          res.id
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
        if (slide.interaction) slide.interaction.submissionCount = 0;
      }
      facilitatorIndex.value = res.hostStatus.facilitatorIndex;
      currentIndex.value = facilitatorIndex.value;
      loading.value = false;
      setInterval(
        fetchHostStatus,
        config.interaction.join.currentIndexPollInterval
      );
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to join interaction session",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  interactionSession.id = useRouter().currentRoute.value.params.id;
  if (!interactionSession.id) {
    Swal.fire({
      title: "Enter session ID",
      html:
        "<div class='overflow-hidden'>You will need a session ID provided by your facilitator. <br>" +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        interactionSession.id = document.getElementById("swalFormId").value;
        if (interactionSession.id == "")
          Swal.showValidationMessage("Please enter a session ID");
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState({}, "", interactionSession.id);
        fetchDetails();
      } else {
        router.push("/");
      }
    });
  } else {
    fetchDetails();
  }
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
                v-show="currentIndex != facilitatorIndex"
                @click="goToSlide(facilitatorIndex)"
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
