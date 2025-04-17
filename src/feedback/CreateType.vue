<script setup>
import { onMounted, ref } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Collapse from "bootstrap/js/dist/collapse";
import Swal from "sweetalert2";
import { api } from "../data/api.js";

let singleHelpInstance;
let seriesHelpInstance;
let templateHelpInstance;
const nextDisabled = ref(true);

const selectSingle = async (forceOn) => {
  if (feedbackSession.isSingle && !forceOn) {
    feedbackSession.isSingle = false;
    singleHelpInstance.hide();
    nextDisabled.value = true;
    return;
  }
  if (feedbackSession.useTemplate && feedbackSession.title) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "This will remove the template you loaded.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      feedbackSession.reset();
    } else {
      return;
    }
  } else if (feedbackSession.subsessions.length) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "This will remove the sessions you've added to your teaching event.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      feedbackSession.subsessions = [];
    } else {
      return;
    }
  }

  feedbackSession.isSeries = false;
  feedbackSession.useTemplate = false;
  feedbackSession.isSingle = true;
  seriesHelpInstance.hide();
  templateHelpInstance.hide();
  singleHelpInstance.show();
  nextDisabled.value = false;
};

const selectSeries = async (forceOn) => {
  if (feedbackSession.isSeries && !forceOn) {
    if (feedbackSession.subsessions.length) {
      const { isConfirmed } = await Swal.fire({
        title: "Lose progress?",
        text: "This will remove the sessions you've added to your teaching event.",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
      });
      if (isConfirmed) {
        feedbackSession.subsessions = [];
      } else {
        return;
      }
    }
    feedbackSession.isSeries = false;
    seriesHelpInstance.hide();
    nextDisabled.value = true;
    return;
  }
  if (feedbackSession.useTemplate && feedbackSession.title) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "This will remove the template you loaded.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      feedbackSession.reset();
    } else {
      return;
    }
  }

  feedbackSession.isSingle = false;
  feedbackSession.useTemplate = false;
  feedbackSession.isSeries = true;
  singleHelpInstance.hide();
  templateHelpInstance.hide();
  seriesHelpInstance.show();
  nextDisabled.value = false;
};

const selectTemplate = async (forceOn) => {
  if (feedbackSession.useTemplate && !forceOn) {
    if (feedbackSession.title) {
      const { isConfirmed } = await Swal.fire({
        title: "Lose progress?",
        text: "This will remove the template you loaded.",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
      });
      if (isConfirmed) {
        feedbackSession.reset();
      } else {
        return;
      }
    }
    feedbackSession.useTemplate = false;
    templateHelpInstance.hide();
    nextDisabled.value = true;
    feedbackSession.reset();
    return;
  }
  if (!feedbackSession.useTemplate && feedbackSession.title) {
    const { isConfirmed } = await Swal.fire({
      title: "Lose progress?",
      text: "Loading a template will remove any details you already added.",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
    });
    if (isConfirmed) {
      feedbackSession.reset();
    } else {
      return;
    }
  }

  feedbackSession.isSingle = false;
  feedbackSession.isSeries = false;
  feedbackSession.useTemplate = true;
  singleHelpInstance.hide();
  seriesHelpInstance.hide();
  templateHelpInstance.show();
  nextDisabled.value = false;
};

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
    feedbackSession.multipleDates = response.multipleDates ? true : false;
    feedbackSession.name = response.name;

    feedbackSession.certificate = response.certificate;
    feedbackSession.attendance = response.attendance;

    if (response.subsessions.length) {
      feedbackSession.subsessions = response.subsessions;
      feedbackSession.isSeries = true;
      feedbackSession.isSingle = false;
    } else {
      feedbackSession.isSingle = true;
      feedbackSession.isSeries = false;
    }
    if (response.questions.length) {
      feedbackSession.questions = response.questions;
      hasQuestions.value = true;
      for (let question of feedbackSession.questions) {
        if (!question.settings) {
          //for pre-v5 custom questions
          question.settings = {
            selectedLimit: {
              min: 1,
              max: 100,
            },
            characterLimit: 500,
          };
        }
        if (question.settings.required == undefined) {
          //for older sessions with undefined 'required' paramenter default to required for text and select but not for checkboxes
          if (question.type == "text" || question.type == "select")
            question.settings.required = true;
        }
      }
    }

    feedbackSession.organisers = response.organisers;
    for (let organiser of feedbackSession.organisers) organiser.existing = true;

    //remove the id and pin so that a new session is created rather than overwriting the template
    //save id in templateId to show template message at top
    feedbackSession.templateId = feedbackSession.id;
    feedbackSession.id = "";
    feedbackSession.pin = "";

    //remove the ids from subsessions so new subsessions are created for a template based create
    for (let subsession of feedbackSession.subsessions) {
      subsession.id = "";
    }
    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load feedback session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
};
const loadTemplate = async () => {
  const { isConfirmed } = await Swal.fire({
    title: "Enter ID and PIN for the session you want to use as a template",
    html:
      "You will need your session ID and PIN which you can find in the email you received when the session you want to use as a template was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      feedbackSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value.trim();
      feedbackSession.pin = document.getElementById("swalFormPin").value.trim();
      if (feedbackSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (feedbackSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  });

  if (isConfirmed) {
    if (await fetchTemplate()) return true;
  }
  return false;
};

const next = async () => {
  if (feedbackSession.useTemplate && !feedbackSession.title) {
    const templateLoaded = await loadTemplate();
    if (!templateLoaded) {
      selectTemplate(false);
      return;
    }
  }
  router.push("/feedback/create/details");
};

onMounted(async () => {
  const singleHelpElement = document.getElementById("singleHelp");
  singleHelpInstance = new Collapse(singleHelpElement, { toggle: false });

  const seriesHelpElement = document.getElementById("seriesHelp");
  seriesHelpInstance = new Collapse(seriesHelpElement, { toggle: false });

  const templateHelpElement = document.getElementById("templateHelp");
  templateHelpInstance = new Collapse(templateHelpElement, {
    toggle: false,
  });

  if (feedbackSession.useTemplate) {
    selectTemplate(true);
  } else if (feedbackSession.isSeries) {
    selectSeries(true);
  } else if (feedbackSession.isSingle) {
    selectSingle(true);
  }
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
        @click="selectSingle(false)"
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
          ><br />
        </div>
      </div>

      <!--series-->
      <div
        class="border border-2 rounded p-1 mt-4"
        :class="{
          'border-teal':
            !feedbackSession.isSingle && !feedbackSession.useTemplate,
        }"
        @click="selectSeries(false)"
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
            />Add additional custom questions if required (just for the overall
            feedback, not each session).</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />Add as many co-organisers as you need.</span
          ><br />
        </div>
      </div>

      <!--template-->
      <div
        class="border border-2 rounded p-1 mt-4"
        :class="{
          'border-teal': !feedbackSession.isSeries && !feedbackSession.isSingle,
        }"
        @click="selectTemplate(false)"
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
            />You will need the session ID and PIN.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />You can make changes before creating the new form.</span
          ><br />
          <span>
            <font-awesome-icon
              :icon="['fas', 'check']"
              style="color: #17a2b8"
              class="me-1"
            />The form used as a template will not be changed.</span
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
