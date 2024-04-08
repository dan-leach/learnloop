<script setup>
import { interactionSession } from "../../../data/interactionSession.js";
import { api } from "../../../data/api.js";
import { config } from "../../../data/config.js";
import Swal from "sweetalert2";
import Toast from "../../../assets/Toast.js";

const deleteSubmissions = () => {
  Swal.fire({
    title: "Delete submissions?",
    text: "Once previous submissions have been deleted they can't be restored.",
    icon: "warning",
    iconColor: "#17a2b8",
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    confirmButtonText: "Delete",
  }).then((result) => {
    if (result.isConfirmed)
      api(
        "interaction",
        "deleteSubmissions",
        interactionSession.id,
        interactionSession.pin,
        null
      ).then(
        function () {
          Toast.fire({
            icon: "success",
            iconColor: "#17a2b8",
            title: "Submissions have been cleared",
          });
          interactionSession.submissionCount = 0;
        },
        function (error) {
          Swal.fire({
            icon: "error",
            iconColor: "#17a2b8",
            title: "Unable to delete previous submissions",
            text: error,
            confirmButtonColor: "#17a2b8",
          });
        }
      );
  });
};
</script>

<template>
  <div>
    <div class="join-panel text-center m-2 p-2 d-flex justify-content-around">
      <div class="align-self-center me-4">
        <img
          :src="
            'https://dev.learnloop.co.uk/api/shared/QRcode/?id=' +
            interactionSession.id
          "
          class="qr-code"
        />
      </div>
      <div class="align-self-center">
        <p class="join-instructions">
          Scan the QR code, or go to<br />
          <strong>LearnLoop.co.uk/interaction</strong><br />
          and enter this code:
        </p>
        <p class="m-2">
          <span class="join-id px-4 py-1">{{ interactionSession.id }}</span>
        </p>
      </div>
    </div>
    <div v-if="interactionSession.submissionCount" class="text-center m-5">
      <p>
        This interaction session already has
        {{ interactionSession.submissionCount }} submission{{
          interactionSession.submissionCount == 1 ? "" : "s"
        }}.
      </p>
      <button class="btn btn-teal" @click="deleteSubmissions">
        Clear submissions
      </button>
    </div>
  </div>
</template>

<style>
.join-instructions {
  margin: 0;
  font-size: 2rem;
  font-weight: 300;
}
.join-id {
  font-size: 3rem;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 15px;
  color: #17a2b8;
}
.join-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
.qr-code {
  width: 200px;
}
</style>
