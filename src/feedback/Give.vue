<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { feedbackSession } from "../data/feedbackSession.js";
import { cookies } from "../data/cookies.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";
import Modal from "bootstrap/js/dist/modal";
import SubsessionFeedbackForm from "./components/SubsessionFeedbackForm.vue";

// Reactive variables
let loading = ref(true);
let submitted = ref(false);

/**
 * Opens the privacy policy in a new tab.
 */
const openPrivacyPolicy = () => window.open("/privacy-policy", "_blank");

/**
 * Saves the current feedback session progress in cookies.
 * @param {boolean} confirm - Whether to show a confirmation popup.
 * @returns {boolean} - True if progress was successfully saved, false otherwise.
 */
const saveProgress = (confirm) => {
  const d = new Date();
  d.setTime(d.getTime() + 1 * 24 * 60 * 60 * 1000); //1 day expiration
  let expires = "expires=" + d.toUTCString();

  if (feedbackSession.id) {
    document.cookie =
      feedbackSession.id +
      "=" +
      JSON.stringify(feedbackSession) +
      ";" +
      expires +
      ";path=/;";
  }

  const success = document.cookie.includes(feedbackSession.id);
  if (confirm) {
    Swal.fire({
      icon: success ? "success" : "error",
      iconColor: "#17a2b8",
      title: success
        ? "Your progress has been saved"
        : "Unable to save your progress",
      text: success
        ? "Return to this form within the next 24 hours to continue where you left off."
        : "You can still submit feedback, but you won't be able to save and resume later.",
      confirmButtonColor: "#17a2b8",
    });
  }
  return success;
};

// Cookie message
const cookieMsg = ref(
  saveProgress(false)
    ? "If you can't complete your feedback in one sitting, click the 'Save progress' button below and return to this form on the same device within the next 24 hours to pick up where you left off."
    : "LearnLoop isn't able to save your progress right now as cookies seem to be disabled. You won't be able to save your progress, but can still complete this form in one sitting."
);

/**
 * Updates the feedback session score text based on the score value.
 */
const scoreChange = () => {
  let x = feedbackSession.feedback.score;
  let y = "slider error";
  if (x > 95) y = "an overwhelmingly excellent session, couldn't be improved";
  else if (x > 80) y = "an excellent session, minimal grounds for improvement";
  else if (x > 70) y = "a very good session, minor points for improvement";
  else if (x > 60) y = "a fairly good session, could be improved further";
  else if (x > 40) y = "basically sound, but needs further development";
  else if (x >= 20) y = "not adequate in its current state";
  else y = "an extremely poor session";

  feedbackSession.feedback.scoreText = y;
};

/**
 * Validates the number of options selected for a checkbox question.
 * @param {Object} question - The feedback question.
 * @returns {boolean} - True if the selected options are within the valid range, false otherwise.
 */
const validNumberOfOptionsSelected = (question) => {
  let count = question.options.filter((option) => option.selected).length;
  return (
    !question.settings.required ||
    (count >= question.settings.selectedLimit.min &&
      count <= question.settings.selectedLimit.max)
  );
};

// Reactive variables for submit button
let btnSubmit = ref({
  text: "Give feedback",
  wait: false,
});

/**
 * Validates the feedback form before submission.
 * @returns {boolean} - True if the form is valid, false otherwise.
 */
const formIsValid = () => {
  document.getElementById("giveFeedbackForm").classList.add("was-validated");
  let subsessionsTodo = false;
  feedbackSession.subsessions.forEach((subsession, i) => {
    let statusElement = document.getElementById("subsession" + i + "Status");
    if (subsession.status == "To do") {
      statusElement.classList.add("is-invalid");
      statusElement.classList.remove("is-valid");
      subsessionsTodo = true;
    } else if (["Skipped", "Complete"].includes(subsession.status)) {
      statusElement.classList.add("is-valid");
      statusElement.classList.remove("is-invalid");
    }
  });

  if (subsessionsTodo) return false;
  if (
    !feedbackSession.feedback.positive ||
    !feedbackSession.feedback.negative ||
    feedbackSession.feedback.score === null
  )
    return false;

  for (let question of feedbackSession.questions) {
    if (question.settings.required) {
      if (
        (question.type === "text" || question.type === "select") &&
        !question.response
      )
        return false;
    }
    if (question.type === "checkbox" && !validNumberOfOptionsSelected(question))
      return false;
  }
  return true;
};

/**
 * Submits the feedback form.
 */
const submit = async () => {
  submitted.value = true;
  if (!formIsValid()) return;

  feedbackSession.subsessions.forEach((subsession) => {
    if (subsession.status === "Skipped") delete subsession.score;
  });

  btnSubmit.value.text = "Please wait...";
  btnSubmit.value.wait = true;

  try {
    await api("feedback/giveFeedback", {
      id: feedbackSession.id,
      feedback: feedbackSession.feedback,
      subsessions: feedbackSession.subsessions,
      questions: feedbackSession.questions,
    });
    cookies.forEach((cookie) => {
      if (cookie.id === feedbackSession.id) {
        document.cookie =
          feedbackSession.id +
          "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      }
    });
    btnSubmit.value.text = "Give feedback";
    btnSubmit.value.wait = false;
    router.push("/feedback/complete");
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to submit your feedback",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    btnSubmit.value.text = "Retry give feedback?";
    btnSubmit.value.wait = false;
  }
};

// Instance of the modal for subsession feedback
let subsessionFeedbackModal;

/**
 * Displays the modal for subsession feedback.
 * @param {number} index - The index of the subsession.
 */
const showSubsessionFeedbackModal = (index) => {
  subsessionFeedbackModal = new Modal(
    document.getElementById("subsessionFeedbackModal" + index),
    { backdrop: "static", keyboard: false, focus: true }
  );
  subsessionFeedbackModal.show();
};

/**
 * Hides the modal for subsession feedback.
 * @param {number} index - The index of the subsession.
 */
const hideSubsessionFeedbackModal = (index) => {
  subsessionFeedbackModal.hide();
  let statusElement = document.getElementById("subsession" + index + "Status");
  if (
    ["Skipped", "Complete"].includes(feedbackSession.subsessions[index].status)
  ) {
    statusElement.classList.add("is-valid");
    statusElement.classList.remove("is-invalid");
  }
};

// Instance of the modal for subsession feedback
let skipSubsessionFeedbackInfoModal;
/**
 * Displays the modal with information about skipping subsession feedback.
 */
const showSkipSubsessionFeedbackInfo = () => {
  skipSubsessionFeedbackInfoModal = new Modal(
    document.getElementById("skipSubsessionFeedbackInfo"),
    { backdrop: true, keyboard: true, focus: true }
  );
  skipSubsessionFeedbackInfoModal.show();
};

/**
 * Hides the modal with information about skipping subsession feedback.
 */
const hideSkipSubsessionFeedbackInfo = () =>
  skipSubsessionFeedbackInfoModal.hide();

/**
 * Skips feedback for a specific subsession.
 * @param {number} index - The index of the subsession.
 */
const skipSubsessionFeedback = async (index) => {
  const statusElement = document.getElementById(
    "subsession" + index + "Status"
  );
  const subsession = feedbackSession.subsessions[index];

  if (
    !subsession.positive &&
    !subsession.negative &&
    subsession.score === null
  ) {
    subsession.status = "Skipped";
    statusElement.classList.remove("is-invalid");
    statusElement.classList.add("is-valid");
  } else {
    const { isConfirmed } = await Swal.fire({
      title: "Skip session?",
      text: `Your existing feedback for ${subsession.title} will be lost.`,
      icon: "warning",
      iconColor: "#17a2b8",
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      confirmButtonText: "Skip",
    });

    if (isConfirmed) {
      subsession.status = "Skipped";
      subsession.positive = "";
      subsession.negative = "";
      subsession.score = null;
      statusElement.classList.remove("is-invalid");
      statusElement.classList.add("is-valid");
    }
  }
};

/**
 * Loads the feedback form data.
 */
const loadGiveFeedback = async () => {
  try {
    const response = await api("feedback/loadGiveFeedback", {
      id: feedbackSession.id,
    });
    if (feedbackSession.id !== response.id) {
      console.error(
        "feedbackSession.id != response.id",
        feedbackSession.id,
        response.id
      );
      return;
    }

    // Initialize feedback session data
    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.name = response.name;
    feedbackSession.subsessions = response.subsessions.map((subsession) => ({
      ...subsession,
      status: "To do",
      positive: "",
      negative: "",
      score: null,
      scoreText: "Please use the slider to indicate an overall score.",
    }));
    feedbackSession.questions = response.questions.map((question) => ({
      ...question,
      response: "",
      settings: question.settings || {
        selectedLimit: { min: 1, max: 100 },
        characterLimit: 500,
      },
    }));
    feedbackSession.certificate = response.certificate;
    feedbackSession.attendance = response.attendance;

    // Check for saved session in cookies
    for (let cookie of cookies) {
      if (cookie.id === feedbackSession.id) {
        const { isConfirmed } = await Swal.fire({
          title: "Resume feedback session?",
          icon: "info",
          iconColor: "#17a2b8",
          text: "You previously started filling in this feedback form. Would you like to pick up where you left off?",
          confirmButtonText: "Yes, let's go",
          confirmButtonColor: "#17a2b8",
          showDenyButton: true,
          denyButtonText: "No, start over",
          allowOutsideClick: false,
          allowEscapeKey: false,
        });

        if (isConfirmed) {
          console.log("Load feedback data from cookie with ID: " + cookie.id);
          feedbackSession.feedback = cookie.feedback;
          feedbackSession.questions = cookie.questions;
          feedbackSession.subsessions = cookie.subsessions;
        }
      }
    }
    loading.value = false;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load feedback form",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

// Initialize the feedback session
onMounted(async () => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;
  if (!feedbackSession.id) {
    const { isConfirmed } = await Swal.fire({
      title: "Enter session ID",
      html:
        "<div class='overflow-hidden'>You will need a session ID provided by your facilitator. <br>" +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        feedbackSession.id = document.getElementById("swalFormId").value.trim();
        if (!feedbackSession.id)
          Swal.showValidationMessage("Please enter a session ID");
      },
    });

    if (isConfirmed) {
      history.replaceState({}, "", feedbackSession.id);
      loadGiveFeedback();
    } else {
      router.push("/");
    }
  } else {
    loadGiveFeedback();
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Feedback</h1>
      <div class="alert alert-warning alert-dismissible">
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
        ></button>
        <span id="cookieMsg">{{ cookieMsg }}</span>
        <span class="alert-link" @click="openPrivacyPolicy">
          Read about how LearnLoop uses cookies.</span
        >
      </div>
      <br />Please provide some feedback to
      <strong>{{ feedbackSession.name }}</strong> regarding their session
      <strong>'{{ feedbackSession.title }}'</strong> delivered on
      <strong>{{ feedbackSession.date }}</strong
      >.<br /><br />
      <sup
        ><font-awesome-icon
          :icon="['fas', 'asterisk']"
          size="2xs"
          style="color: #ff0000"
          class="float-right"
      /></sup>
      Indicates questions to which a response is required <br /><br />
      <form id="giveFeedbackForm" class="needs-validation" novalidate>
        <div v-if="feedbackSession.subsessions.length">
          <label class="form-label"
            >Feedback on sessions
            <sup
              ><font-awesome-icon
                :icon="['fas', 'asterisk']"
                size="2xs"
                style="color: #ff0000"
                class="float-right" /></sup
          ></label>
          <table class="table" id="subsessionTable">
            <thead>
              <tr>
                <th class="bg-transparent p-0 ps-2">Session</th>
                <th class="bg-transparent p-0 ps-2">Status</th>
                <th class="bg-transparent p-0 ps-2">Feedback</th>
                <th class="bg-transparent p-0 ps-2">
                  Skip<font-awesome-icon
                    :icon="['fas', 'fa-question-circle']"
                    class="mx-2"
                    @click.prevent="showSkipSubsessionFeedbackInfo"
                  />
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(subsession, index) in feedbackSession.subsessions">
                <td class="bg-transparent p-0 ps-2">
                  <strong>{{ subsession.title }}</strong
                  ><br />{{ subsession.name }}
                </td>
                <td class="subsession-status bg-transparent p-0 ps-2">
                  <span
                    :id="'subsession' + index + 'Status'"
                    class="subsession-status form-control"
                    >{{ subsession.status }}</span
                  >
                </td>
                <td
                  class="text-center subsession-button bg-transparent p-0 ps-2"
                >
                  <button
                    class="btn btn-teal btn-sm"
                    id="loadGiveSubsessionFeedback"
                    v-on:click.prevent="showSubsessionFeedbackModal(index)"
                  >
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                  </button>
                </td>
                <td
                  class="text-center subsession-button bg-transparent p-0 ps-2"
                >
                  <button
                    class="btn btn-danger btn-sm"
                    id="skipSubsessionFeedback"
                    v-on:click.prevent="skipSubsessionFeedback(index)"
                  >
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <SubsessionFeedbackForm
            v-for="(subsession, index) in feedbackSession.subsessions"
            :index="index"
            :subsession="subsession"
            @hideSubsessionFeedbackModal="hideSubsessionFeedbackModal"
          />
          <div class="modal" id="skipSubsessionFeedbackInfo">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">
                    Skipping feedback for a subsession
                  </h4>
                  <button
                    type="button"
                    class="close"
                    @click.prevent="hideSkipSubsessionFeedbackInfo"
                  >
                    &times;
                  </button>
                </div>
                <div class="modal-body">
                  If you did not attend a particular session within a series you
                  can skip providing feedback by clicking the 'Skip this
                  session' button.<br />
                  <br />
                  The feedback you submit for the other sessions will still be
                  submitted as usual.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button
            class="btn btn-teal btn-sm"
            id="saveProgress"
            v-on:click.prevent="saveProgress(true)"
          >
            Save progress
          </button>
        </div>
        <div>
          <label for="positiveComments" class="form-label"
            >Positive Comments
            <sup
              ><font-awesome-icon
                :icon="['fas', 'asterisk']"
                size="2xs"
                style="color: #ff0000"
                class="float-right" /></sup
          ></label>
          <textarea
            rows="5"
            v-model="feedbackSession.feedback.positive"
            class="form-control"
            id="positiveComments"
            placeholder="Please provide some feedback about what you enjoyed about this session..."
            name="positiveComments"
            autocomplete="off"
            required
          ></textarea>
          <div class="invalid-feedback">
            Please provide some positive comments.
          </div>
        </div>
        <div class="mt-4">
          <label for="negativeComments" class="form-label"
            >Constructive Comments
            <sup
              ><font-awesome-icon
                :icon="['fas', 'asterisk']"
                size="2xs"
                style="color: #ff0000"
                class="float-right" /></sup
          ></label>
          <textarea
            rows="5"
            v-model="feedbackSession.feedback.negative"
            class="form-control"
            id="negativeComments"
            placeholder="Please provide some feedback about ways this session could be improved..."
            name="negativeComments"
            autocomplete="off"
            required
          ></textarea>
          <div class="invalid-feedback">
            Please provide some constructive comments.
          </div>
        </div>
        <div
          v-for="(question, questionIndex) in feedbackSession.questions"
          class="mt-4"
        >
          <label for="custom+index" class="form-label"
            >{{ question.title }}
            <sup v-if="question.settings.required"
              ><font-awesome-icon
                :icon="['fas', 'asterisk']"
                size="2xs"
                style="color: #ff0000"
                class="float-right" /></sup
          ></label>
          <div v-if="question.type == 'text'">
            <textarea
              :rows="parseInt(question.settings.characterLimit.max / 100)"
              :minlength="question.settings.characterLimit.min"
              :maxlength="question.settings.characterLimit.max"
              v-model="question.response"
              class="form-control"
              id="question.title"
              name="question.title"
              autocomplete="off"
              :required="question.settings.required"
            ></textarea>
            <div class="invalid-feedback">Please answer this question.</div>
          </div>
          <div v-if="question.type == 'checkbox'">
            <span
              v-for="(option, optionIndex) in question.options"
              class="form-check"
            >
              <input
                :id="
                  'question[' + questionIndex + '];option[' + optionIndex + ']'
                "
                class="form-check-input"
                :class="
                  !validNumberOfOptionsSelected(question) && submitted
                    ? 'is-invalid'
                    : ''
                "
                type="checkbox"
                value="option.title"
                v-model="option.selected"
                :required="
                  !validNumberOfOptionsSelected(question) &&
                  question.settings.required
                "
              />
              <label
                class="form-check-label"
                :for="
                  'question[' + questionIndex + '];option[' + optionIndex + ']'
                "
                >{{ option.title }}</label
              >
              <div
                v-if="optionIndex == question.options.length - 1"
                class="invalid-feedback"
              >
                {{
                  question.settings.selectedLimit.min ==
                  question.settings.selectedLimit.max
                    ? "Please select exactly " +
                      question.settings.selectedLimit.min +
                      " options"
                    : "Please select between " +
                      question.settings.selectedLimit.min +
                      " and " +
                      question.settings.selectedLimit.max +
                      " options"
                }}{{
                  question.settings.required
                    ? "."
                    : " (or none at all for this optional question)."
                }}
              </div>
              <br />
            </span>
          </div>
          <div v-if="question.type == 'select'">
            <select
              v-model="question.response"
              class="form-control"
              :required="question.settings.required"
            >
              <option disabled value="">Please select an option...</option>
              <option v-for="option in question.options" :value="option.title">
                {{ option.title }}
              </option>
            </select>
            <div class="invalid-feedback">Please select an option.</div>
          </div>
          <br />
        </div>
        <div class="mt-4">
          <label for="score" class="form-label"
            >Score
            <sup
              ><font-awesome-icon
                :icon="['fas', 'asterisk']"
                size="2xs"
                style="color: #ff0000"
                class="float-right"
            /></sup>
            &nbsp;&nbsp;&nbsp;&nbsp;{{
              feedbackSession.feedback.score
            }}/100</label
          >
          <input
            type="range"
            v-model="feedbackSession.feedback.score"
            id="scoreRange"
            placeholder=""
            class="form-range mx-2"
            name="scoreRange"
            autocomplete="off"
            @input="scoreChange"
            @change="scoreChange"
            required
          />
          <p class="text-center">{{ feedbackSession.feedback.scoreText }}</p>
          <input
            type="text"
            v-model="feedbackSession.feedback.score"
            class="form-control-range"
            id="score"
            placeholder=""
            name="score"
            autocomplete="off"
            required
            hidden
          />
          <div class="invalid-feedback">
            Please use the slider to indicate an overall score.
          </div>
        </div>
      </form>
      <div class="text-center mt-4 mb-3">
        <button
          class="btn btn-teal btn-lg"
          id="submitGiveFeedback"
          @click="submit"
          :disabled="btnSubmit.wait"
        >
          <span
            v-if="btnSubmit.wait"
            class="spinner-border spinner-border-sm me-2"
          ></span
          >{{ btnSubmit.text }}
        </button>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.form-label {
  font-size: 1.3rem;
}
.alert-link {
  cursor: pointer;
}
.form-range::-webkit-slider-thumb {
  background-color: #17a2b8;
  margin-top: -0.35rem;
}
.form-range::-webkit-slider-runnable-track {
  background-color: #17a2b8;
  height: 0.2rem;
  margin-top: 0.8rem;
}
.subsession-button {
  width: 90px;
}
.subsession-status {
  width: 120px;
}
</style>
