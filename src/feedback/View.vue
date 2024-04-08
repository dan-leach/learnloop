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

let loading = ref(true);
let isSeries = ref(false);

const fetchFeedback = () => {
  api(
    "feedback",
    "fetchFeedback",
    feedbackSession.id,
    feedbackSession.pin,
    null
  ).then(
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
      feedbackSession.feedback.positive = res.positive;
      feedbackSession.feedback.negative = res.negative;
      let sum = 0;
      for (let score of res.score) sum += parseInt(score);
      feedbackSession.feedback.score =
        Math.round((sum / res.score.length) * 10) / 10;
      if (Number.isNaN(feedbackSession.feedback.score))
        feedbackSession.feedback.score = "-";
      feedbackSession.questions = res.questions;
      if (res.subsessions) {
        for (let sub of res.subsessions) {
          let parsed = JSON.parse(sub);
          let sum = 0;
          for (let score of parsed.score) sum += parseInt(score);
          parsed.score = Math.round((sum / parsed.score.length) * 10) / 10;
          if (Number.isNaN(parsed.score)) parsed.score = "-";
          feedbackSession.subsessions.push(parsed);
          isSeries = true;
        }
      }
      loading.value = false;
    },
    function (error) {
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
      <form
        v-if="!isSeries"
        method="post"
        action="https://dev.learnloop.co.uk/api/"
      >
        <input type="text" name="module" value="feedback" readonly hidden />
        <input
          type="text"
          name="route"
          value="fetchFeedbackPDF"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.id"
          type="text"
          name="id"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.pin"
          type="text"
          name="pin"
          readonly
          hidden
        />
        <button class="btn btn-teal mb-3" id="btnDownloadFeedback">
          Download feedback as PDF
        </button>
      </form>
      <div v-else>
        <button
          class="btn btn-teal mb-3"
          id="btnDownloadFeedback"
          @click.prevent="showDownloadFeedbackForm"
        >
          Download feedback as PDF
        </button>
      </div>
      <DownloadFeedbackForm
        @hideDownloadFeedbackModal="hideDownloadFeedbackModal"
      />
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
            <tr v-for="(sub, index) in feedbackSession.subsessions">
              <td class="bg-transparent p-0 ps-2">
                <strong>{{ sub.title }}</strong
                ><br />
                {{ sub.name }}
              </td>
              <td class="bg-transparent p-0 ps-2">
                <span v-for="item in sub.positive">{{ item }}<br /></span>
              </td>
              <td class="bg-transparent p-0 ps-2">
                <span v-for="item in sub.negative">{{ item }}<br /></span>
              </td>
              <td class="bg-transparent p-0 ps-2">{{ sub.score }}/100</td>
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
