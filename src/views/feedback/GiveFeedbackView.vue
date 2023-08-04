<script setup>
/*
ToDo:
coookies...
remainder of api response handling - in comment
Do I still need the Vue.set or do something with ref()?
form validation
submit
why scoreText not appearing?
fix api failure defaulting to API timeout rather than offical message
*/

import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../../router';
import { feedbackSession } from '../../data/feedbackSession.js';
import { api } from '../../data/api.js';
import Swal from 'sweetalert2';

let fetchDetails = () => {};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.path.replace(
    '/feedback/',
    ''
  );
  api('feedback', 'fetchDetails', feedbackSession.id, null, null).then(
    function (res) {
      if (feedbackSession.id != res.id) {
        console.error(
          'feedbackSession.id != response.id',
          feedbackSession.id,
          response.id
        );
        return;
      }
      feedbackSession.title = res.title;
      feedbackSession.date = res.date;
      feedbackSession.name = res.name;
      /*
      feedbackSession.subsessions = (res.subsessions.length > 0) ? res.subsessions : false
      if (res.questions) feedbackSession.questions = JSON.parse(res.questions)
      feedbackSession.certificate = (res.certificate == true) ? true : false;
      feedbackSession.attendance = (res.attendance == true) ? true : false;
      if (feedbackSession.subsessions) {
        for (let x in feedbackSession.subsessions) Vue.set(app.session.subsessions[x],'state','To do') //using Vue.set to enable reactivity for subsession state such that 'skipped' and 'completed' appear appropriately
        app.hideComponent('loader')
        app.hideComponent('welcome')
        app.showComponent('giveFeedbackSeries')
      } else {
        app.hideComponent('loader')
        app.hideComponent('welcome')
        app.showComponent('giveFeedback')
      }
      */
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable to load feedback form',
        text: error,
      });
    }
  );
});

let saveProgress = (confirm) => {
  const d = new Date();
  d.setTime(d.getTime() + 1 * 24 * 60 * 60 * 1000); //1 day
  let expires = 'expires=' + d.toUTCString();
  if (feedbackSession.id)
    document.cookie =
      feedbackSession.id +
      '=' +
      JSON.stringify(feedbackSession) +
      ';' +
      expires +
      ';path=/;'; //stores as cookie with name of session ID
  console.log(
    document.cookie.includes(feedbackSession.id)
      ? 'saveProgress success'
      : 'saveProgress fail'
  );
  if (confirm) {
    if (document.cookie.includes(feedbackSession.id)) {
      Swal.fire({
        icon: 'success',
        title: 'Your progress has been saved',
        text: 'Return to this form on the same device within the next 24 hours to pick up where you left off.',
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Unable to save your progress',
        text: "You can still submit your feedback, but you'll need to fill in the form in one sitting, rather than saving and returning to it later.",
      });
    }
  }
  return document.cookie.includes(feedbackSession.id) ? true : false;
};

let cookieMsg = ref(
  saveProgress(false)
    ? "If you can't complete your feedback in one sitting, click the 'Save progress' button below and return to this form on the same device within the next 24 hours to pick up where you left off."
    : "LearnLoop isn't able to save your progress right now as cookies seem to be disabled. You won't be able to save your progress, but can still complete this form in one sitting."
);

let viewPrivacy = () => {
  router.push('/privacy-policy');
};

let scoreChange = () => {
  let x = feedbackSession.feedback.score;
  let y = 'slider error';
  if (x > 95) {
    y = "an overwhelmingly excellent session, couldn't be improved";
  } else if (x > 80) {
    y = 'an excellent sesssion, minimal grounds for improvement';
  } else if (x > 70) {
    y = 'a very good session, minor points for improvement';
  } else if (x > 60) {
    y = 'a fairly good session, could be improved further';
  } else if (x > 40) {
    y = 'basically sound, but needs further development';
  } else if (x >= 20) {
    y = 'not adequate in its current state';
  } else if (x < 20) {
    y = 'an extremely poor session';
  }

  feedbackSession.feedback.scoreText = y;
};
</script>

<template>
  <div class="alert alert-warning alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <span id="cookieMsg">{{ cookieMsg }}</span>
    <span class="alert-link" @click="viewPrivacy">
      Read about how LearnLoop uses cookies.</span
    >
  </div>
  <div class="text-center">
    <button
      class="btn btn-secondary btn-sm"
      id="saveProgress"
      @click="saveProgress(true)"
    >
      Save progress
    </button>
  </div>
  <br />Please provide some feedback to
  <strong>{{ feedbackSession.name }}</strong> regarding their session
  <strong>'{{ feedbackSession.title }}'</strong> delivered on
  <strong>{{ feedbackSession.date }}</strong
  >.<br /><br />
  <form id="giveFeedbackForm" class="needs-validation" novalidate>
    <div>
      <label for="positiveComments" class="form-label"
        >Positive Comments:</label
      >
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
    </div>
    <div class="mt-4">
      <label for="negativeComments" class="form-label"
        >Constructive Comments:</label
      >
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
    </div>
    <div v-for="(question, index) in feedbackSession.questions" class="mt-4">
      <label for="custom+index" class="form-label">{{ question.title }}:</label>
      <!--need to add custom+index name to inputs-->
      <div v-if="question.type == 'text'">
        <textarea
          rows="8"
          v-model="question.response"
          class="form-control"
          id="question.title"
          name="question.title"
          autocomplete="off"
          required
        ></textarea>
      </div>
      <div v-if="question.type == 'checkbox'">
        <span v-for="(option, index) in question.options">
          <label>
            <input
              type="checkbox"
              value="option.title"
              v-model="option.selected"
            />
            {{ option.title }}
          </label>
          <br />
        </span>
      </div>
      <div v-if="question.type == 'select'">
        <select v-model="question.response" class="form-control" required>
          <option disabled value="">Please select one</option>
          <option v-for="(option, index) in question.options">
            {{ option.title }}
          </option>
        </select>
        <div class="invalid-feedback">Please fill out this field.</div>
      </div>
      <br />
    </div>
    <div>
      <div class="mt-4">
        <label for="score" class="form-label"
          >Score: {{ feedbackSession.feedback.score }}/100</label
        >
        <input
          type="range"
          v-model="feedbackSession.feedback.score"
          style="width: 80%; margin: 10px"
          id="scoreRange"
          placeholder=""
          name="scoreRange"
          autocomplete="off"
          oninput="app.scoreChange()"
          onchange="app.scoreChange()"
        />
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
        <textarea
          rows="1"
          v-model="feedbackSession.feedback.scoreText"
          class="form-control"
          id="scoreText"
          placeholder=""
          name="scoreText"
          autocomplete="off"
          readonly
        ></textarea>
      </div>
    </div>
  </form>
  <div class="text-center mt-4">
    <button
      class="btn btn-primary"
      id="submitGiveFeedback"
      v-on:click="submitGiveFeedback"
    >
      Give Feedback
    </button>
  </div>
</template>

<style>
.form-label {
  font-size: 1.3rem;
}
.alert-link {
  cursor: pointer;
}
</style>
