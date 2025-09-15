<script setup>
/**
 * @module feedback/View
 * @summary View and download feedback session report
 * @description Handles fetching and displaying feedback data for a single session or series,
 * and allows exporting as a PDF. Authenticates user using session ID and PIN prompt.
 * @requires vue
 * @requires vue-router
 * @requires bootstrap/js/dist/modal
 * @requires ../data/feedbackSession.js
 * @requires ../data/api.js
 * @requires ../components/Loading.vue
 * @requires ./components/DownloadFeedbackForm.vue
 * @requires sweetalert2
 * @requires ../assets/Toast.js
 * @requires ../assets/promptSessionDetails.js
 */

import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import Modal from "bootstrap/js/dist/modal";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import DownloadFeedbackForm from "./components/DownloadFeedbackForm.vue";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";
import { promptSessionDetails } from "../assets/promptSessionDetails";

let loading = ref(true);
let isSeries = ref(false);

let downloadFeedbackModal; // Bootstrap modal instance

/**
 * Fetches feedback data from the server and updates the shared feedbackSession object.
 * @async
 * @function fetchFeedback
 * @throws Will show a SweetAlert modal and redirect on error.
 */
const fetchFeedback = async () => {
  try {
    const response = await api("feedback/viewFeedback", {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
    });

    if (feedbackSession.id !== response.id) {
      console.error("Mismatched session ID", feedbackSession.id, response.id);
      return;
    }

    // Main session feedback
    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.name = response.name;
    feedbackSession.feedback.positive = response.feedback.positive;
    feedbackSession.feedback.negative = response.feedback.negative;

    // Average score
    let sum = 0;
    for (let score of response.feedback.score) sum += parseInt(score);
    feedbackSession.feedback.score =
      Math.round((sum / response.feedback.score.length) * 10) / 10;
    if (Number.isNaN(feedbackSession.feedback.score))
      feedbackSession.feedback.score = "-";

    feedbackSession.questions = response.questions;

    // Subsession data
    if (response.subsessions) {
      for (let subsession of response.subsessions) {
        let subSum = 0;
        for (let score of subsession.feedback.score) subSum += parseInt(score);
        subsession.feedback.score =
          Math.round((subSum / subsession.feedback.score.length) * 10) / 10;
        if (Number.isNaN(subsession.feedback.score))
          subsession.feedback.score = "-";
        feedbackSession.subsessions.push(subsession);
        isSeries.value = true;
      }
    }

    loading.value = false;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load feedback report",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

/**
 * Opens the modal for selecting download options.
 * @function showDownloadFeedbackForm
 * @param {number} index - (optional) Index of a subsession.
 */
const showDownloadFeedbackForm = (index) => {
  downloadFeedbackModal = new Modal(
    document.getElementById("downloadFeedbackModal"),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  downloadFeedbackModal.show();
};

/** Hides the download modal. */
const hideDownloadFeedbackModal = () => downloadFeedbackModal.hide();

/**
 * Downloads the feedback report PDF.
 * @async
 * @function fetchFeedbackPDF
 * @param {string} [downloadId] - Optional subsession ID for downloading a report from a series.
 */
const fetchFeedbackPDF = async (downloadId) => {
  const requestObject = {
    id: feedbackSession.id,
    pin: feedbackSession.pin,
  };

  let downloadTitle = feedbackSession.title;

  if (downloadId) {
    requestObject.id = downloadId;
    requestObject.parentSessionId = feedbackSession.id;
    const subsession = feedbackSession.subsessions.find(
      (s) => s.id === downloadId
    );
    if (subsession) downloadTitle = subsession.title;
  }

  try {
    const response = await api(
      "feedback/fetchFeedbackPDF",
      requestObject,
      "blob"
    );

    const a = document.createElement("a");
    a.href = response;
    a.download = `${downloadTitle} feedback report.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(response);

    Toast.fire({
      icon: "success",
      iconColor: "#17a2b8",
      title: "Your feedback report should now be downloading.",
    });
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Error",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
  }
};

/**
 * Runs when the component is mounted: prompts for ID + PIN, then loads session data.
 * @memberof module:feedback/View
 */
onMounted(async () => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;

  const { isConfirmed, id, pin } = await promptSessionDetails(
    feedbackSession.id
  );

  if (!isConfirmed) {
    router.push("/");
    return;
  }

  feedbackSession.id = id;
  feedbackSession.pin = pin;
  history.replaceState({}, "", feedbackSession.id);
  fetchFeedback();
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Feedback</h1>
      <p>
        Viewing feedback for <strong>'{{ feedbackSession.title }}'</strong> by
        <strong>{{ feedbackSession.name }}</strong> on
        <strong>{{ feedbackSession.date }}</strong>
      </p>
      <div>
        <button
          v-if="feedbackSession.subsessions.length"
          class="btn btn-teal mb-3"
          id="btnShowDownloadFeedbackForm"
          @click="showDownloadFeedbackForm"
        >
          Download feedback report as PDF
        </button>

        <button
          v-else
          class="btn btn-teal mb-3"
          id="btnDownloadFeedback"
          @click="fetchFeedbackPDF(feedbackSession.id)"
        >
          Download feedback report as PDF
        </button>

        <DownloadFeedbackForm
          @hideDownloadFeedbackModal="hideDownloadFeedbackModal"
          @fetchFeedbackPDF="fetchFeedbackPDF"
        />
      </div>
      <label class="form-label">Positive comments</label><br />
      <span v-for="item in feedbackSession.feedback.positive"
        >{{ item }}<br
      /></span>
      <br />
      <label class="form-label">Constructive comments</label><br />
      <span v-for="item in feedbackSession.feedback.negative"
        >{{ item }}<br
      /></span>
      <div v-for="(question, index) in feedbackSession.questions">
        <br />
        <label class="form-label">{{ question.title }}</label
        ><br />
        <div v-if="question.type == 'text'">
          <span v-for="response in question.responses"
            >{{ response }}<br
          /></span>
        </div>
        <div v-if="question.type == 'checkbox' || question.type == 'select'">
          <span v-for="option in question.options"
            >{{ option.title }}: {{ option.count }}<br
          /></span>
        </div>
      </div>
      <br />
      <label class="form-label"
        >Overall Score {{ feedbackSession.feedback.score }}/100</label
      ><br />
      <br />
      <div v-if="feedbackSession.subsessions.length > 0">
        <label class="form-label">Sessions</label><br />
        <table id="subsessionFeedback" class="table">
          <thead>
            <tr>
              <th class="bg-transparent p-0 ps-2">Session</th>
              <th class="bg-transparent p-0 ps-2">Positive</th>
              <th class="bg-transparent p-0 ps-2">Constructive</th>
              <th class="bg-transparent p-0 ps-2">Average Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(subsession, index) in feedbackSession.subsessions">
              <td class="bg-transparent p-0 ps-2">
                <strong>{{ subsession.title }}</strong
                ><br />
                {{ subsession.name }}
              </td>
              <td class="bg-transparent p-0 ps-2">
                <span v-for="item in subsession.feedback.positive"
                  >{{ item }}<br
                /></span>
              </td>
              <td class="bg-transparent p-0 ps-2">
                <span v-for="item in subsession.feedback.negative"
                  >{{ item }}<br
                /></span>
              </td>
              <td class="bg-transparent p-0 ps-2">
                {{ subsession.feedback.score }}/100
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        <div class="card bg-transparent shadow p-2 mb-3">
          <label class="form-label">Score guide</label>
          <ul>
            <li>
              >95: an overwhelmingly excellent session, couldn't be improved
            </li>
            <li>>80: an excellent sesssion, minimal grounds for improvement</li>
            <li>>70: a very good session, minor points for improvement</li>
            <li>>60: a fairly good session, could be improved further</li>
            <li>>40: basically sound, but needs further development</li>
            <li>>=20: not adequate in its current state</li>
            <li>&#60;20: an extremely poor session</li>
          </ul>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.form-label {
  font-size: 1.3rem;
}
</style>
