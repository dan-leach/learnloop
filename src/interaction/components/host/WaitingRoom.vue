<script setup>
/**
 * @module interaction/components/host/WaitingRoom
 * @summary Handles deletion of interaction submissions in presenter view.
 * @description
 * This script provides functionality for presenters to delete all submissions
 * from the current interactive session. It uses SweetAlert2 for confirmation dialogs,
 * and a custom Toast utility for success messages. The API call is performed via `api()`
 * and targets the `interaction/deactivateSubmissions` endpoint.
 *
 * @requires vue
 * @requires sweetalert2
 * @requires ../../../data/interactionSession.js
 * @requires ../../../data/api.js
 * @requires ../../../assets/Toast.js
 */

import { inject } from "vue";
import Swal from "sweetalert2";
import Toast from "../../../assets/Toast.js";
import { interactionSession } from "../../../data/interactionSession.js";
import { api } from "../../../data/api.js";

// Inject global configuration
const config = inject("config");

// Props from parent component
const props = defineProps(["isPresenterView"]);

/**
 * Prompts the presenter to confirm deletion of all previous submissions.
 * On confirmation, it calls the API to deactivate submissions, resets local count,
 * and displays a success toast. Handles and displays any errors that occur.
 *
 * @async
 * @function deleteSubmissions
 * @memberof module:interaction/components/host/WaitingRoom
 * @returns {Promise<void>}
 */
const deleteSubmissions = async () => {
  // Show confirmation dialog
  const { isConfirmed } = await Swal.fire({
    title: "Delete submissions?",
    text: "Once previous submissions have been deleted they can't be restored.",
    icon: "warning",
    iconColor: "#17a2b8",
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    confirmButtonText: "Delete",
  });

  if (!isConfirmed) return false;

  try {
    // Call API to deactivate submissions for the current session
    await api("interaction/deactivateSubmissions", {
      id: interactionSession.id,
      pin: interactionSession.pin,
      isPreview: interactionSession.status.preview,
    });

    // Reset local submission count
    interactionSession.submissionCount = 0;

    // Show success message
    Toast.fire({
      icon: "success",
      iconColor: "#17a2b8",
      title: "Submissions have been cleared",
    });
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to delete previous submissions",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
  }
};
</script>

<template>
  <div>
    <div class="join-panel text-center m-2 p-2 d-flex justify-content-around">
      <div class="align-self-center me-4">
        <img
          :src="config.api.url + 'qrcode/?id=' + interactionSession.id"
          class="qr-code"
        />
      </div>
      <div class="align-self-center">
        <p class="join-instructions">
          Scan the QR code, or go to<br />
          <strong>{{ config.client.url.replace("https://", "") }}</strong
          ><br />
          and enter this code:
        </p>
        <p class="m-2">
          <span class="join-id px-4 py-1">{{
            interactionSession.id ? interactionSession.id : "preview"
          }}</span>
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
      <button
        class="btn btn-teal"
        @click="deleteSubmissions"
        v-if="!isPresenterView"
      >
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
  letter-spacing: 10px;
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
