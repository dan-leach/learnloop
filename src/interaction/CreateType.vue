<script setup>
import { onMounted, ref } from "vue";
import { interactionSession } from "../data/interactionSession.js";
import router from "../router/index.js";
import Collapse from "bootstrap/js/dist/collapse";
import Swal from "sweetalert2";
import { api } from "../data/api.js";

let newHelpInstance;
let templateHelpInstance;
const nextDisabled = ref(true);

const selectNew = async (forceOn) => {
  if (interactionSession.isNew && !forceOn) {
    interactionSession.isNew = false;
    newHelpInstance.hide();
    nextDisabled.value = true;
    return;
  }
  if (interactionSession.useTemplate && interactionSession.title) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "This will remove the template you loaded.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      interactionSession.reset();
    } else {
      return;
    }
  }

  interactionSession.useTemplate = false;
  interactionSession.isNew = true;
  templateHelpInstance.hide();
  newHelpInstance.show();
  nextDisabled.value = false;
};

const selectTemplate = async (forceOn) => {
  if (interactionSession.useTemplate && !forceOn) {
    if (interactionSession.title) {
      const { isConfirmed } = await Swal.fire({
        title: "Lose progress?",
        text: "This will remove the template you loaded.",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
      });
      if (isConfirmed) {
        interactionSession.reset();
      } else {
        return;
      }
    }
    interactionSession.useTemplate = false;
    templateHelpInstance.hide();
    nextDisabled.value = true;
    interactionSession.reset();
    return;
  }
  if (!interactionSession.useTemplate && interactionSession.title) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "Loading a template will remove any details or slides you already added.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      interactionSession.reset();
    } else {
      return;
    }
  }

  interactionSession.isNew = false;
  interactionSession.useTemplate = true;
  newHelpInstance.hide();
  templateHelpInstance.show();
  nextDisabled.value = false;
};

// Fetch the details of the interaction session to be edited
const fetchTemplate = async () => {
  try {
    const response = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id != response.id) {
      throw new error("Response session ID does not match request session ID");
    }

    interactionSession.title = `Copy of ${response.title}`;
    interactionSession.name = response.name;
    interactionSession.email = response.email;
    interactionSession.slides = response.slides;

    interactionSession.id = "";
    interactionSession.pin = "";

    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to fetch interaction session details",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
    return false;
  }
};
const loadTemplate = async () => {
  // Get the id and pin for the session to be used as template
  const { isConfirmed } = await Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "<div class='overflow-hidden'>You will need the session ID and PIN for the session you want to use as the template. You can find these details in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      interactionSession.id = document
        .getElementById("swalFormId")
        .value.trim();
      interactionSession.pin = document
        .getElementById("swalFormPin")
        .value.trim();
      if (interactionSession.pin == "") {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }
      if (interactionSession.id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      return true;
    },
  });

  // If form not confirmed, cancel the create from template process
  if (isConfirmed) {
    await fetchTemplate();
    return true;
  }
  return false;
};

const next = async () => {
  if (interactionSession.useTemplate && !interactionSession.title) {
    const templateLoaded = await loadTemplate();
    if (!templateLoaded) {
      selectTemplate(false);
      return;
    }
  }
  router.push("/interaction/create/details");
};

onMounted(async () => {
  const newHelpElement = document.getElementById("newHelp");
  newHelpInstance = new Collapse(newHelpElement, { toggle: false });

  const templateHelpElement = document.getElementById("templateHelp");
  templateHelpInstance = new Collapse(templateHelpElement, {
    toggle: false,
  });

  if (interactionSession.useTemplate) {
    selectTemplate(true);
  } else if (interactionSession.isNew) {
    selectNew(true);
  }
});
</script>

<template>
  <div class="container d-flex flex-column align-items-center">
    <h1 class="text-center display-4">Interaction</h1>
    <div class="text-center">
      <p>Let's get started creating your interaction session.</p>
    </div>
    <div class="alert alert-danger" alert-dismissible fade show role="alert">
      <div class="d-flex justify-content-between">
        <h4 class="alert-heading">Closed beta</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
      LearnLoop Interaction is in private beta and can only be used by
      invitation. Unless you have joined the beta-testing group and had your
      email approved, you will not be able to create an interaction session.
    </div>
    <!--option buttons-->
    <div class="d-flex flex-column align-items-stretch w-100">
      <!--new-->
      <div
        class="border border-2 rounded p-1"
        :class="{
          'border-teal': !interactionSession.useTemplate,
        }"
        @click="selectNew(false)"
      >
        <div class="d-flex justify-content-between align-items-center">
          <span>Create a new interaction session</span>
          <button
            class="btn"
            :class="{
              'btn-teal': interactionSession.isNew,
            }"
          >
            <font-awesome-icon
              :icon="['far', 'square-check']"
              v-if="interactionSession.isNew"
            />
            <font-awesome-icon :icon="['far', 'square']" v-else />
          </button>
        </div>
        <!--new help-->
        <div class="collapse form-text mx-1" id="newHelp">
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Create an session with interactive slides.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Attendees join on a personal device via a link, QR code or by
            typing in a session ID.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Add images, video and text to your slides.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Use true/false, multiple choice, word clouds or free-text
            interactive slides.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Edit as many times as you need.</span
          ><br />
        </div>
      </div>

      <!--template-->
      <div
        class="border border-2 rounded p-1 mt-4"
        :class="{
          'border-teal': !interactionSession.isNew,
        }"
        @click="selectTemplate(false)"
      >
        <div class="d-flex justify-content-between align-items-center">
          <span>Use a previous interaction session as a template</span>
          <button
            class="btn"
            :class="{
              'btn-teal': interactionSession.useTemplate,
            }"
          >
            <font-awesome-icon
              :icon="['far', 'square-check']"
              v-if="interactionSession.useTemplate"
            />
            <font-awesome-icon :icon="['far', 'square']" v-else />
          </button>
        </div>
        <!--template help-->
        <div class="collapse form-text mx-1" id="templateHelp">
          <span
            ><font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Use a previous interaction session as a template to save
            time.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />You will need the session ID and PIN.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />You can make changes before creating the new session.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />The session used as a template will not be changed.</span
          ><br />
        </div>
      </div>
    </div>

    <!--back/next button-->
    <div class="d-flex justify-content-evenly my-3">
      <button
        class="btn btn-teal btn-lg me-2 mb-2"
        id="next"
        @click="next"
        :disabled="nextDisabled"
      >
        Continue
      </button>
    </div>
  </div>
</template>

<style>
.container {
  max-width: 750px;
}
</style>
