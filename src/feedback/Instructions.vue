<script setup>
/**
 * @module feedback/Instructions
 * @summary Displays session-specific instructions and links for giving feedback
 * @description This component loads session info and generates both a feedback URL and a QR code.
 * It also includes clipboard copy functionality for sharing.
 * @requires vue
 * @requires vue-router
 * @requires ../data/api.js
 * @requires ../data/feedbackSession.js
 * @requires sweetalert2
 * @requires ../assets/Toast.js
 * @requires ../components/Loading.vue
 */

import { ref, onMounted, inject } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { api } from "../data/api.js";
import { feedbackSession } from "../data/feedbackSession.js";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";
import Loading from "../components/Loading.vue";

const config = inject("config");

const link = ref({}); // Holds URL and QR code links
const loading = ref(true); // Controls loading spinner

const clipboard = ref(!!navigator.clipboard);

/**
 * Copies a string to clipboard and shows a Toast notification.
 * @async
 * @function copyText
 * @param {string} string - Text to copy.
 * @returns {void}
 */
const copyText = async (string) => {
  if (!clipboard.value) return;

  try {
    await navigator.clipboard.writeText(string);
    Toast.fire({
      icon: "success",
      iconColor: "#17a2b8",
      title: "Copied",
    });
  } catch (error) {
    Toast.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Error copying to clipboard: " + error,
    });
  }
};

/**
 * Fetches feedback session details to generate shareable links.
 * @async
 * @function fetchDetails
 * @throws Will show a SweetAlert error and redirect to home on failure.
 */
const fetchDetails = async () => {
  try {
    const response = await api("feedback/loadGiveFeedback", {
      id: feedbackSession.id,
    });

    if (feedbackSession.id !== response.id) {
      console.error("Mismatched session ID", feedbackSession.id, response.id);
      return;
    }

    feedbackSession.title = response.title;
    feedbackSession.name = response.name;

    link.value.give = `${config.value.client.url}/${feedbackSession.id}`;
    link.value.qr = `${config.value.api.url}qrcode/?id=${feedbackSession.id}`;

    loading.value = false;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load instructions page",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

/**
 * Prompts for a session ID if not already provided, then loads details.
 * @memberof module:feedback/Instructions
 */
onMounted(async () => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;

  // Prompt if session ID is missing from route
  if (!feedbackSession.id) {
    const { isConfirmed } = await Swal.fire({
      title: "Enter session ID",
      html: `
        <div class="overflow-hidden">
          <input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">
        </div>`,
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        feedbackSession.id = document.getElementById("swalFormId").value.trim();
        if (!feedbackSession.id)
          Swal.showValidationMessage("Please enter a session ID");
      },
    });

    if (isConfirmed) {
      history.replaceState(
        {},
        "",
        `/feedback/instructions/${feedbackSession.id}`
      );
      fetchDetails();
    } else {
      router.push("/");
    }
  } else {
    fetchDetails();
  }
});
</script>

<template v-once>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Feedback</h1>
      <p class="text-center display-6">
        How to give feedback for '{{ feedbackSession.title }}'
      </p>
      <div class="give-panel text-center m-2 p-2 d-flex justify-content-around">
        <div class="align-self-center me-4">
          <img
            :src="link.qr"
            class="me-2"
            alt="QR code to the feedback form"
            height="250"
          />
        </div>
        <div class="align-self-center">
          <p class="give-instructions">
            Scan the QR code, or go to<br />
            <strong>{{ config.client.url.replace("https://", "") }}</strong
            ><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="give-id px-4 py-1">{{ feedbackSession.id }}</span>
          </p>
        </div>
      </div>
      <p class="text-center fs-4 m-4">
        Direct link:
        <a :href="link.give" target="_blank">{{
          link.give.replace("https://", "")
        }}</a>
        <font-awesome-icon
          :icon="['fas', 'copy']"
          size="lg"
          class="mx-2 btn-copy p-1 align-middle"
          style="color: black"
          @click="copyText(link.give)"
        />
      </p>
    </div>
  </Transition>
</template>

<style>
.give-instructions {
  margin: 0;
  font-size: 2rem;
  font-weight: 300;
}
.give-id {
  font-size: 3rem;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 15px;
  color: #17a2b8;
  letter-spacing: 10px;
}
.give-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
a {
  color: black;
}
.btn-copy {
  border: 5px solid transparent;
  border-radius: 5px;
}
.btn-copy:hover {
  border: 5px solid #17a2b8;
}
</style>
