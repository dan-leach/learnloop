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
import { promptSessionDetails } from "../assets/promptSessionDetails";

const loading = ref(true);
const showSlide = ref(true);
const currentIndex = ref(0);

const goToSlide = (index) => {
  showSlide.value = false;
  currentIndex.value = index;
  setTimeout(() => {
    showSlide.value = true;
  }, 250);
};

let fetchStatusFailCount = 0;
const fetchStatus = async () => {
  try {
    const response = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });
    interactionSession.status = response;

    let awaitUser = false;
    if (interactionSession.slides[currentIndex.value].interaction) {
      if (interactionSession.slides[currentIndex.value].interaction.response)
        awaitUser = true;
      if (
        interactionSession.slides[currentIndex.value].interaction.closed ||
        interactionSession.status.lockedSlides[currentIndex.value - 1]
      )
        awaitUser = false;
    }

    if (
      currentIndex.value != interactionSession.status.facilitatorIndex &&
      !awaitUser
    )
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

const fetchDetails = async () => {
  try {
    const response = await api("interaction/fetchDetailsJoin", {
      id: interactionSession.id,
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
    response.slides.unshift({ type: "waitingRoom" });
    response.slides.push({ type: "end" });
    interactionSession.slides = response.slides;
    for (let slide of interactionSession.slides) {
      if (slide.interaction) slide.interaction.submissionCount = 0;
    }
    interactionSession.status = response.status;
    currentIndex.value = interactionSession.status.facilitatorIndex;
    loading.value = false;
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

onMounted(async () => {
  interactionSession.id = useRouter().currentRoute.value.params.id;

  if (interactionSession.id) {
    fetchDetails();
    return;
  }

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
