<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Toast from "../assets/toast.js";
import { inject } from "vue";
const config = inject("config");
import Modal from "bootstrap/js/dist/modal";
import Swal from "sweetalert2";

if (
  !interactionSession.isNew &&
  !interactionSession.useTemplate &&
  !interactionSession.isEdit
) {
  if (useRouter().currentRoute.value.path.includes("/edit/")) {
    router.push(
      "/interaction/edit/" + useRouter().currentRoute.value.params.id
    );
  } else {
    router.push("/interaction/create/type");
  }
}

let btnNext = ref({
  text: "Continue",
  wait: false,
});
const next = async () => {
  if (!sessionDetailsAreValid()) return false;
  btnNext.value.text = "Please wait...";
  btnNext.value.wait = true;
  let insertUpdateSuccess;
  if (interactionSession.id) {
    insertUpdateSuccess = await updateSession();
  } else {
    insertUpdateSuccess = await insertSession();
  }
  if (insertUpdateSuccess) {
    btnNext.value.text = "Continue";
    btnNext.value.wait = false;
    router.push(
      `/interaction/${
        interactionSession.isEdit
          ? "edit/slides/" + interactionSession.id
          : "create/login"
      }`
    );
  } else {
    btnNext.value.text = "Retry?";
    btnNext.value.wait = false;
  }
};
const back = () => {
  router.push("/interaction/create/type");
};

// Insert the session on the server and get a session ID and PIN
const insertSession = async () => {
  try {
    // Perform the insert
    const response = await api("interaction/insertSession", {
      title: interactionSession.title,
      feedbackId: interactionSession.feedbackID,
      name: interactionSession.name,
      email: interactionSession.email,
    });

    // Locally record the response
    interactionSession.id = response.id;
    interactionSession.pin = response.pin;
    interactionSession.emailOutcome = response.emailOutcome;

    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to create interaction session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
};

/**
 * Validates an email address.
 *
 * This function checks if the provided email follows the standard email format.
 *
 * @param {string} email - The email address to validate.
 * @returns {boolean} - Returns true if the email is valid, otherwise false.
 */
function emailIsValid(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Check the session details are valid
const sessionDetailsAreValid = () => {
  document.getElementById("createSessionForm").classList.add("was-validated");
  if (
    !interactionSession.title ||
    !interactionSession.name ||
    !emailIsValid(interactionSession.email)
  )
    return false;
  return true;
};

// Update the session on the server
const updateSession = async () => {
  try {
    const response = await api("interaction/updateSession", interactionSession);
    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Toast.fire({
      icon: "error",
      title: `Error saving session: ${error}`,
    });
    return false;
  }
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
