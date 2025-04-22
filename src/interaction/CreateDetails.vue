<script setup>
/**
 * @module interaction/CreateDetails
 * @summary Step 2 of the interaction session creation process.
 * @description Validates, inserts, or updates interaction sessions. Routes users appropriately based on session type.
 * @requires vue
 * @requires vue-router
 * @requires ../router
 * @requires ../data/interactionSession.js
 * @requires ../data/api.js
 * @requires ../assets/toast.js
 * @requires bootstrap/js/dist/modal
 * @requires sweetalert2
 */

import { ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Toast from "../assets/toast.js";
import Swal from "sweetalert2";

// Redirect user if they try to access this view incorrectly
if (
  !interactionSession.isNew &&
  !interactionSession.useTemplate &&
  !interactionSession.isEdit
) {
  const currentPath = useRouter().currentRoute.value.path;
  const currentParams = useRouter().currentRoute.value.params;
  router.push(
    currentPath.includes("/edit/")
      ? `/interaction/edit/${currentParams.id}`
      : "/interaction/create/type"
  );
}

// Button state for "Continue"
const btnNext = ref({
  text: "Continue",
  wait: false,
});

/**
 * Moves the user to the next step if session is valid and insert/update succeeds.
 * @memberof module:interaction/CreateDetails
 * @returns {Promise<void>}
 */
const next = async () => {
  if (!sessionDetailsAreValid()) return;

  btnNext.value = { text: "Please wait...", wait: true };

  const success = interactionSession.id
    ? await updateSession()
    : await insertSession();

  if (success) {
    btnNext.value = { text: "Continue", wait: false };
    const routePath = interactionSession.isEdit
      ? `/interaction/edit/slides/${interactionSession.id}`
      : "/interaction/create/login";
    router.push(routePath);
  } else {
    btnNext.value = { text: "Retry?", wait: false };
  }
};

/**
 * Navigates the user back to session type selection.
 * @memberof module:interaction/CreateDetails
 */
const back = () => {
  router.push("/interaction/create/type");
};

/**
 * Validates the interaction session details before submission.
 * @memberof module:interaction/CreateDetails
 * @returns {boolean} - True if valid, false otherwise.
 */
const sessionDetailsAreValid = () => {
  document.getElementById("createSessionForm").classList.add("was-validated");

  return (
    interactionSession.title &&
    interactionSession.name &&
    emailIsValid(interactionSession.email)
  );
};

/**
 * Validates an email address using a standard pattern.
 * @memberof module:interaction/CreateDetails
 * @param {string} email - The email to validate.
 * @returns {boolean} - True if valid, false if not.
 */
const emailIsValid = (email) => {
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return pattern.test(email);
};

/**
 * Sends a request to create a new interaction session.
 * On success, updates interactionSession with ID, PIN, and email outcome.
 * @memberof module:interaction/CreateDetails
 * @returns {Promise<boolean>} - True if successful, false if error occurs.
 */
const insertSession = async () => {
  try {
    const response = await api("interaction/insertSession", {
      title: interactionSession.title,
      feedbackId: interactionSession.feedbackID,
      name: interactionSession.name,
      email: interactionSession.email,
    });

    interactionSession.id = response.id;
    interactionSession.pin = response.pin;
    interactionSession.emailOutcome = response.emailOutcome;

    return true;
  } catch (error) {
    showError("Unable to create interaction session", formatError(error));
    return false;
  }
};

/**
 * Sends a request to update an existing interaction session.
 * @memberof module:interaction/CreateDetails
 * @returns {Promise<boolean>} - True if update succeeds, false otherwise.
 */
const updateSession = async () => {
  try {
    await api("interaction/updateSession", interactionSession);
    return true;
  } catch (error) {
    Toast.fire({
      icon: "error",
      title: `Error saving session: ${formatError(error)}`,
    });
    return false;
  }
};

/**
 * Displays an error dialog using SweetAlert2.
 * @memberof module:interaction/CreateDetails
 * @param {string} title - Dialog title.
 * @param {string} text - Error message.
 */
const showError = (title, text) => {
  Swal.fire({
    icon: "error",
    iconColor: "#17a2b8",
    title,
    text,
    confirmButtonColor: "#17a2b8",
  });
};

/**
 * Formats API error responses for display.
 * @memberof module:interaction/CreateDetails
 * @param {Error|string|Array} error - The error object or message.
 * @returns {string} - A user-friendly error message.
 */
const formatError = (error) => {
  return Array.isArray(error) ? error.map((e) => e.msg).join(" ") : error;
};
</script>

<template>
  <div class="container my-4">
    <h1 class="text-center display-4">Interaction</h1>
    <div class="text-center">
      <p v-if="interactionSession.isEdit" class="form-label ms-2">
        Editing interaction session
        <span class="id-box">{{ interactionSession.id }}</span>
      </p>
      <p v-else>Add some details about your interaction session</p>
    </div>
    <form id="createSessionForm" class="needs-validation" novalidate>
      <!--title-->
      <div class="form-floating mb-3">
        <input
          type="text"
          v-model="interactionSession.title"
          class="form-control"
          id="title"
          placeholder=""
          name="title"
          autocomplete="off"
          v-focus-collapse="'titleHelp'"
          required
        />
        <label for="title">Session title</label>
        <div class="invalid-feedback">
          Please provide a title for your session.
        </div>
        <div class="collapse form-text mx-1" id="titleHelp">
          <span>The title appears on-screen at the start of your session.</span>
        </div>
      </div>

      <!--feedback id-->
      <div class="form-floating mb-3">
        <input
          type="text"
          v-model="interactionSession.feedbackID"
          class="form-control"
          placeholder=""
          id="feedbackID"
          name="title"
          autocomplete="off"
          v-focus-collapse="'feedbackIdHelp'"
        />
        <label for="feedbackID">Feedback session ID (optional) </label>
        <div class="collapse form-text mx-1" id="feedbackIdHelp">
          <span
            >Enter the session ID of a LearnLoop feedback request (<a
              href="/feedback/create"
              target="_blank"
              >click here to do this now in a new tab</a
            >)</span
          ><br />
          <span
            >Attendees will be directed to the feedback form at the end of your
            interaction session.</span
          ><br />
          <span
            >If reusing this interaction session consider creating a new
            feedback request and updating this value.</span
          >
        </div>
      </div>

      <!--name-->
      <div class="form-floating mb-3">
        <input
          type="text"
          v-model="interactionSession.name"
          class="form-control"
          id="name"
          placeholder=""
          name="name"
          autocomplete="off"
          v-focus-collapse="'nameHelp'"
          required
        />
        <label for="name">Facilitator name</label>
        <div class="invalid-feedback">Please provide a facilitator name.</div>
        <div class="collapse form-text mx-1" id="nameHelp">
          <span
            >The facilitator name appears on-screen at the start of your
            session.</span
          >
        </div>
      </div>

      <!--email-->
      <div class="form-floating mb-3">
        <input
          type="email"
          v-model="interactionSession.email"
          class="form-control"
          id="email"
          placeholder=""
          name="email"
          autocomplete="off"
          v-focus-collapse="'emailHelp'"
          required
        />
        <label for="email">Facilitator email</label>
        <div class="invalid-feedback">Please provide a valid email.</div>
        <div class="collapse form-text mx-1" id="emailHelp">
          <span
            >Use a valid email to ensure you receive your session ID and
            PIN.</span
          >
        </div>
      </div>
    </form>
    <!--back/next button-->
    <div class="d-flex justify-content-evenly mb-3">
      <button
        class="btn btn-secondary btn-lg me-2 mb-2"
        id="back"
        @click="back"
        v-if="!interactionSession.isEdit"
      >
        Back
      </button>
      <button
        class="btn btn-teal btn-lg me-2 mb-2"
        id="next"
        @click="next"
        :disabled="btnNext.wait"
      >
        <span
          v-if="btnNext.wait"
          class="spinner-border spinner-border-sm me-2"
        ></span
        >{{ btnNext.text }}
      </button>
    </div>
  </div>
</template>

<style>
.container {
  max-width: 750px;
}
.id-box {
  padding: 2px;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 10px;
  background-color: #17a2b8;
  color: white;
  letter-spacing: 3px;
}
</style>
