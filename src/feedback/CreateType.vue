<script setup>
/**
 * @module feedback/CreateType
 * @summary Step 1 of the feedback session create process.
 * @description Allows users to choose between creating a single session, a series, or using a previous template.
 * @requires vue
 * @requires ../data/feedbackSession.js
 * @requires ../router/index.js
 * @requires bootstrap/js/dist/collapse
 * @requires sweetalert2
 * @requires ../data/api.js
 */

import { onMounted, ref } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Collapse from "bootstrap/js/dist/collapse";
import Swal from "sweetalert2";
import { api } from "../data/api.js";

// Bootstrap collapse instances for help sections
let singleHelpInstance, seriesHelpInstance, templateHelpInstance;

// Controls whether the Next button is disabled
const nextDisabled = ref(true);

/**
 * Confirm reset if necessary and activate selected mode.
 * Helper function to handle confirmation before switching modes.
 * @param {string} mode - One of 'single', 'series', or 'template'.
 * @param {boolean} forceOn - Whether to force activation without toggling off.
 * @returns {Promise<boolean>} Whether the switch was successful.
 */
const handleModeSelection = async (mode, forceOn) => {
  const isActive = {
    single: feedbackSession.isSingle,
    series: feedbackSession.isSeries,
    template: feedbackSession.useTemplate,
  }[mode];

  // If changing from template, confirm loss of template
  if (feedbackSession.useTemplate && feedbackSession.title && !forceOn) {
    const confirmed = await confirmLoss(
      "This will remove the template you loaded."
    );
    if (!confirmed) return false;
    feedbackSession.reset();
    if (isActive) {
      setModeNone(mode);
      return false;
    }
  }

  // If changing mode when the subsessions exist, confirm loss of sessions
  if (feedbackSession.subsessions.length && !forceOn) {
    const confirmed = await confirmLoss(
      "This will remove the sessions you've added to your teaching event."
    );
    if (!confirmed) return false;
    feedbackSession.subsessions = [];
    if (isActive) {
      setModeNone(mode);
      return false;
    }
  }

  // If change to template mode and there is already details addded, confirm loss
  if (mode === "template" && feedbackSession.title && !isActive) {
    const confirmed = await confirmLoss(
      "This will remove any details you've added."
    );
    if (!confirmed) return false;
    feedbackSession.reset();
  }

  setMode(mode);
  return true;
};

/**
 * @function confirmLoss
 * @description Displays a confirmation modal to warn about lost progress.
 * @param {string} text - Warning message to show.
 * @returns {Promise<boolean>} Whether the user confirmed.
 */
const confirmLoss = async (text) => {
  const { isConfirmed } = await Swal.fire({
    title: "Lose progress?",
    text,
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });
  return isConfirmed;
};

/**
 * @function setMode
 * @description Updates feedbackSession flags and UI based on the selected mode.
 * @param {string} mode - The selected mode.
 */
const setMode = (mode) => {
  feedbackSession.isSingle = mode === "single";
  feedbackSession.isSeries = mode === "series";
  feedbackSession.useTemplate = mode === "template";

  singleHelpInstance.hide();
  seriesHelpInstance.hide();
  templateHelpInstance.hide();

  if (mode === "single") singleHelpInstance.show();
  if (mode === "series") seriesHelpInstance.show();
  if (mode === "template") templateHelpInstance.show();

  nextDisabled.value = false;
};

/**
 * @function setModeNone
 * @description Deactivates the currently selected mode and hides its help.
 * @param {string} mode - Mode to deactivate.
 */
const setModeNone = (mode) => {
  if (mode === "single") singleHelpInstance.hide();
  if (mode === "series") seriesHelpInstance.hide();
  if (mode === "template") templateHelpInstance.hide();

  feedbackSession.isSingle = false;
  feedbackSession.isSeries = false;
  feedbackSession.useTemplate = false;
  nextDisabled.value = true;
};

/**
 * @function fetchTemplate
 * @description Loads a saved feedback session to use as a template.
 * @returns {Promise<boolean>} Whether the fetch succeeded.
 */
const fetchTemplate = async () => {
  try {
    const response = await api("feedback/loadUpdateSession", {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
      isTemplate: true,
    });

    if (feedbackSession.id != response.id) {
      console.error(
        "feedbackSession.id != response.id",
        feedbackSession.id,
        response.id
      );
      return false;
    }

    feedbackSession.title = `Copy of ${response.title}`;
    feedbackSession.date = response.date;
    feedbackSession.multipleDates = !!response.multipleDates;
    feedbackSession.name = response.name;
    feedbackSession.certificate = response.certificate;
    feedbackSession.attendance = response.attendance;
    feedbackSession.questions = response.questions || [];
    feedbackSession.subsessions = response.subsessions || [];
    feedbackSession.organisers = response.organisers.map((o) => ({
      ...o,
      existing: true,
    }));

    // Compatibility patch for old formats
    for (const q of feedbackSession.questions) {
      q.settings = q.settings || {
        selectedLimit: { min: 1, max: 100 },
        characterLimit: 500,
      };
      if (
        q.settings.required === undefined &&
        (q.type === "text" || q.type === "select")
      ) {
        q.settings.required = true;
      }
    }

    // Determine mode
    feedbackSession.isSeries = feedbackSession.subsessions.length > 0;
    feedbackSession.isSingle = !feedbackSession.isSeries;

    feedbackSession.templateId = feedbackSession.id;
    feedbackSession.id = "";
    feedbackSession.pin = "";
    feedbackSession.subsessions.forEach((s) => (s.id = ""));

    return true;
  } catch (error) {
    const message = Array.isArray(error)
      ? error.map((e) => e.msg).join(" ")
      : error;
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load feedback session",
      text: message,
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
};

/**
 * @function loadTemplate
 * @description Prompts user for ID/PIN and attempts to load a session template.
 * @returns {Promise<boolean>} Whether the session was successfully loaded.
 */
const loadTemplate = async () => {
  const { isConfirmed } = await Swal.fire({
    title: "Enter ID and PIN for the session you want to use as a template",
    html: `You will need your session ID and PIN which you can find in the email you received when the session you want to use as a template was created.<br>
      <input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="${feedbackSession.id}">
      <input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">`,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value.trim();
      feedbackSession.pin = document.getElementById("swalFormPin").value.trim();
      if (!feedbackSession.id)
        Swal.showValidationMessage("Please enter a session ID");
      if (!feedbackSession.pin)
        Swal.showValidationMessage("Please enter your PIN");
    },
  });
  return isConfirmed && (await fetchTemplate());
};

/**
 * @function next
 * @description Validates and routes to the next step.
 */
const next = async () => {
  if (feedbackSession.useTemplate && !feedbackSession.title) {
    const templateLoaded = await loadTemplate();
    if (!templateLoaded) {
      setModeNone("template");
      return;
    }
  }
  router.push("/feedback/create/details");
};

onMounted(() => {
  // Initialize help collapses
  singleHelpInstance = new Collapse(document.getElementById("singleHelp"), {
    toggle: false,
  });
  seriesHelpInstance = new Collapse(document.getElementById("seriesHelp"), {
    toggle: false,
  });
  templateHelpInstance = new Collapse(document.getElementById("templateHelp"), {
    toggle: false,
  });

  // Restore state based on session object
  if (feedbackSession.useTemplate) setMode("template");
  else if (feedbackSession.isSeries) setMode("series");
  else if (feedbackSession.isSingle) setMode("single");
});
</script>

<template>
  <div class="container d-flex flex-column align-items-center">
    <h1 class="text-center display-4">Feedback</h1>
    <div class="text-center">
      <p>Let's get started creating your feedback request</p>
    </div>
    <!--option buttons-->
    <div class="d-flex flex-column align-items-stretch w-100">
      <!--single-->
      <div
        class="border border-2 rounded p-1"
        :class="{
          'border-teal':
            !feedbackSession.isSeries && !feedbackSession.useTemplate,
        }"
        @click="handleModeSelection('single', false)"
      >
        <div class="d-flex justify-content-between align-items-center">
          <span>Collect feedback on a teaching event with one session</span>
          <button
            class="btn"
            :class="{
              'btn-teal': feedbackSession.isSingle,
            }"
          >
            <font-awesome-icon
              :icon="['far', 'square-check']"
              v-if="feedbackSession.isSingle"
            />
            <font-awesome-icon :icon="['far', 'square']" v-else />
          </button>
        </div>
        <!--single help-->
        <div class="collapse form-text mx-1" id="singleHelp">
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Create a simple feedback form for a single session.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Certificate of attendance option.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Register of attendance option.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Add additional custom questions if required.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Add as many co-organisers as you need.</span
          >
        </div>
      </div>

      <!--series-->
      <div
        class="border border-2 rounded p-1 mt-4"
        :class="{
          'border-teal':
            !feedbackSession.isSingle && !feedbackSession.useTemplate,
        }"
        @click="handleModeSelection('series', false)"
      >
        <div class="d-flex justify-content-between align-items-center">
          <span
            >Collect feedback on a teaching event with multiple sessions</span
          >
          <button
            class="btn"
            :class="{
              'btn-teal': feedbackSession.isSeries,
            }"
          >
            <font-awesome-icon
              :icon="['far', 'square-check']"
              v-if="feedbackSession.isSeries"
            />
            <font-awesome-icon :icon="['far', 'square']" v-else />
          </button>
        </div>
        <!--series help-->
        <div class="collapse form-text mx-1" id="seriesHelp">
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />
            Gather feedback on multiple sessions using a single form.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />
            Attendees wil be asked to provide feedback for each session you add
            and for the event as a whole.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />
            You can give facilitators of each session access to view the
            feedback (just for their session).</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />
            Plus all the features from a feedback request for a single
            session.</span
          >
        </div>
      </div>

      <!--template-->
      <div
        class="border border-2 rounded p-1 mt-4"
        :class="{
          'border-teal': !feedbackSession.isSeries && !feedbackSession.isSingle,
        }"
        @click="handleModeSelection('template', false)"
      >
        <div class="d-flex justify-content-between align-items-center">
          <span>Use a previous event as a template to collect feedback</span>
          <button
            class="btn"
            :class="{
              'btn-teal': feedbackSession.useTemplate,
            }"
          >
            <font-awesome-icon
              :icon="['far', 'square-check']"
              v-if="feedbackSession.useTemplate"
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
            />Use a previous feedback form as a template to save time.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />You will need the ID and PIN of the session you want to use as a
            template.</span
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
          >
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
