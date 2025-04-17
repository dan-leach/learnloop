<script setup>
import { ref } from "vue";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { inject } from "vue";
const config = inject("config");
import Toast from "../assets/Toast.js";

if (!interactionSession.isNew && !interactionSession.useTemplate) {
  router.push("/interaction/create/type");
}

let clipboard = ref(false);
if (navigator.clipboard) clipboard.value = true;
const copyText = async (string) => {
  if (!clipboard.value) return;
  try {
    await navigator.clipboard.writeText(string);
    Toast.fire({
      icon: "success",
      title: "Copied",
    });
  } catch (error) {
    Toast.fire({
      icon: "error",
      title: "Error copying to clipboard: " + error,
    });
  }
};

const next = async () => {
  router.push("/interaction/create/slides");
};
const back = () => {
  router.push("/interaction/create/details");
};
</script>

<template v-once>
  <h1 class="text-center display-4">Interaction</h1>
  <div class="text-center">
    <p>
      Please make a note of your ID and PIN for this interaction session before
      continuing to add slides.
    </p>
  </div>
  <div class="d-flex flex-wrap flex-fill justify-content-around">
    <p
      @click="copyText(interactionSession.id)"
      class="display-6 mx-4 p-4 shadow align-top"
    >
      ID: <strong>{{ interactionSession.id }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle ms-2">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
    <p
      @click="copyText(interactionSession.pin)"
      class="display-6 mx-4 p-4 shadow"
    >
      PIN: <strong>{{ interactionSession.pin }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle ms-2">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
  </div>
  <p v-if="interactionSession.emailOutcome?.sendSuccess">
    <strong
      >An email with these details has also been sent to
      {{ interactionSession.email }}.</strong
    ><br />
    Please check your inbox (or your junk mail) to ensure you received it. If it
    didn't arrive be sure to make a note of these details. Add
    {{ config.email }} your safe senders list for next time.
  </p>
  <p v-else>
    <strong
      >Please ensure you save your ID and PIN in a safe place as the
      confirmation email failed to send.</strong
    >
  </p>
  <!--back/next button-->
  <div class="d-flex justify-content-evenly mb-3">
    <button class="btn btn-secondary btn-lg me-2 mb-2" id="back" @click="back">
      Back
    </button>
    <button class="btn btn-teal btn-lg me-2 mb-2" id="next" @click="next">
      Continue
    </button>
  </div>
</template>

<style scoped>
@media only screen and (max-width: 600px) {
  .display-6 {
    font-size: 1.4rem;
  }
}
</style>
