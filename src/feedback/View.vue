<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import Modal from "bootstrap/js/dist/modal";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import DownloadFeedbackForm from "./components/DownloadFeedbackForm.vue";
import Swal from "sweetalert2";
import { inject } from "vue";
const config = inject("config");

let loading = ref(true);
let isSeries = ref(false);

const fetchFeedback = () => {
  api("feedback/viewFeedback", {
    id: feedbackSession.id,
    pin: feedbackSession.pin,
  }).then(
    function (res) {
      if (feedbackSession.id != res.id) {
        console.error(
          "feedbackSession.id != res.id",
          feedbackSession.id,
          res.id
        );
        return;
      }
      feedbackSession.title = res.title;
      feedbackSession.date = res.date;
      feedbackSession.name = res.name;
      feedbackSession.feedback.positive = res.feedback.positive;
      feedbackSession.feedback.negative = res.feedback.negative;
      let sum = 0;
      for (let score of res.feedback.score) sum += parseInt(score);
      feedbackSession.feedback.score =
        Math.round((sum / res.feedback.score.length) * 10) / 10;
      if (Number.isNaN(feedbackSession.feedback.score))
        feedbackSession.feedback.score = "-";
      feedbackSession.questions = res.questions;
      if (res.subsessions) {
        for (let subsession of res.subsessions) {
          let sum = 0;
          for (let score of subsession.feedback.score) sum += parseInt(score);
          subsession.feedback.score =
            Math.round((sum / subsession.feedback.score.length) * 10) / 10;
          if (Number.isNaN(subsession.feedback.score))
            subsession.feedback.score = "-";
          feedbackSession.subsessions.push(subsession);
          isSeries = true;
        }
      }
      loading.value = false;
    },
    function (error) {
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
  );
};

let downloadFeedbackModal;
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
const hideDownloadFeedbackModal = () => downloadFeedbackModal.hide();

const fetchFeedbackPDF = () => {
  api(
    "feedback/fetchFeedbackPDF",
    {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
    },
    "blob"
  ).then(
    function (res) {
      // Create a new HTML page to display the PDF with a custom title
      const htmlContent = `
        <html>
          <head>
            <title>Feedback report</title>
          </head>
          <body style="margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4;">
            <embed src="${res}" type="application/pdf" width="100%" height="100%" />
          </body>
        </html>
        `;

      // Open a new window and write the content
      const newTab = window.open();
      newTab.document.write(htmlContent);

      Swal.fire({
        icon: "success",
        iconColor: "#17a2b8",
        title: "Success",
        text: "Feedback report should now be open in a new tab.",
        confirmButtonColor: "#17a2b8",
      });
    },
    function (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Error",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
    }
  );
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;
  Swal.fire({
    title: "Enter session ID and PIN",
    html:
      '<div class="overflow-hidden">You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      feedbackSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value;
      feedbackSession.pin = document.getElementById("swalFormPin").value;
      if (feedbackSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (feedbackSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  }).then((result) => {
    if (result.isConfirmed) {
      history.replaceState({}, "", feedbackSession.id);
      fetchFeedback();
    } else {
      router.push("/");
    }
  });
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
          class="btn btn-teal mb-3"
          id="btnDownloadFeedback"
          @click="fetchFeedbackPDF"
        >
          Download feedback report as PDF
        </button>
        <DownloadFeedbackForm
          @hideDownloadFeedbackModal="hideDownloadFeedbackModal"
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
