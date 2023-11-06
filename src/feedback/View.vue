<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../router';
import { feedbackSession } from '../data/feedbackSession.js';
import { api } from '../data/api.js';
import Loading from '../components/Loading.vue';
import Swal from 'sweetalert2';

let loading = ref(true);

const fetchFeedback = () => {
  api(
    'feedback',
    'fetchFeedback',
    feedbackSession.id,
    feedbackSession.pin,
    null
  ).then(
    function (res) {
      if (feedbackSession.id != res.id) {
        console.error(
          'feedbackSession.id != res.id',
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
        feedbackSession.feedback.score = '-';
      feedbackSession.questions = res.questions;
      if (res.subsessions) {
        for (let sub of res.subsessions) {
          let parsed = JSON.parse(sub);
          let sum = 0;
          for (let score of parsed.score) sum += parseInt(score);
          parsed.score = Math.round((sum / parsed.score.length) * 10) / 10;
          if (Number.isNaN(parsed.score)) parsed.score = '-';
          feedbackSession.subsessions.push(parsed);
        }
      }
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable to load feedback form',
        text: error,
      });
      router.push('/');
    }
  );
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.path.replace(
    '/feedback/view/',
    ''
  );
  if (
    feedbackSession.id == '/feedback/view' ||
    feedbackSession.id == '/feedback/view/' ||
    feedbackSession.id == ''
  ) {
    Swal.fire({
      title: 'Enter session ID and PIN',
      html:
        'You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        feedbackSession.id = document.getElementById('swalFormId').value;
        feedbackSession.pin = document.getElementById('swalFormPin').value;
        if (feedbackSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
        if (feedbackSession.id == '')
          Swal.showValidationMessage('Please enter a session ID');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState({}, '', feedbackSession.id);
        fetchFeedback();
      } else {
        router.push('/');
      }
    });
  } else {
    Swal.fire({
      title: 'Enter session PIN',
      html:
        'You will need your session PIN which you can find in the email you received when your session was created. <br>' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        feedbackSession.pin = document.getElementById('swalFormPin').value;
        if (feedbackSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        fetchFeedback();
      } else {
        router.push('/');
      }
    });
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
      <p>
        Viewing feedback for <strong>'{{ feedbackSession.title }}'</strong> by
        <strong>{{ feedbackSession.name }}</strong> on
        <strong>{{ feedbackSession.date }}</strong
        >.
      </p>
      <form method="post" action="https://dev.learnloop.co.uk/api/">
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

        <div class="text-center">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Download feedback as PDF</span>
            </div>
            <select
              id="selectSubID"
              name="subID"
              class="form-control"
              v-if="feedbackSession.subsessions.length"
            >
              <option value="">Overall feedback and all sessions</option>
              <option
                v-for="subsession in feedbackSession.subsessions"
                :value="subsession.id"
                name="subID"
              >
                Just '{{ subsession.title }}'
              </option>
            </select>
            <div class="input-group-append">
              <button class="btn btn-primary" id="btnFetchFeedbackPDF">
                Go
              </button>
            </div>
          </div>
        </div>
      </form>
      <h4>Positive comments:</h4>
      <span v-for="item in feedbackSession.feedback.positive"
        >{{ item }}<br
      /></span>
      <br />
      <h4>Constructive comments:</h4>
      <span v-for="item in feedbackSession.feedback.negative"
        >{{ item }}<br
      /></span>
      <div v-for="(question, index) in feedbackSession.questions">
        <br />
        <h4>{{ question.title }}</h4>
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
      <h4>Overall Score: {{ feedbackSession.feedback.score }}/100</h4>
      <br />
      <div v-if="feedbackSession.subsessions.length > 0">
        <h4>Sessions:</h4>
        <table id="subsessionFeedback" class="table">
          <thead>
            <tr>
              <th>Session</th>
              <th>Positive</th>
              <th>Constructive</th>
              <th>Average Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sub, index) in feedbackSession.subsessions">
              <td>
                <strong>{{ sub.title }}</strong
                ><br />
                {{ sub.name }}
              </td>
              <td>
                <span v-for="item in sub.positive">{{ item }}<br /></span>
              </td>
              <td>
                <span v-for="item in sub.negative">{{ item }}<br /></span>
              </td>
              <td>{{ sub.score }}/100</td>
            </tr>
          </tbody>
        </table>
      </div>
      Score guide:
      <ul>
        <li>>95: an overwhelmingly excellent session, couldn't be improved</li>
        <li>>80: an excellent sesssion, minimal grounds for improvement</li>
        <li>>70: a very good session, minor points for improvement</li>
        <li>>60: a fairly good session, could be improved further</li>
        <li>>40: basically sound, but needs further development</li>
        <li>>=20: not adequate in its current state</li>
        <li>&#60;20: an extremely poor session</li>
      </ul>
    </div>
  </Transition>
</template>

<style scoped></style>
