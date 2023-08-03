<script setup>
/*
ToDo:
cookie message
form validation
submit
*/

import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { feedbackSession } from '../../data/feedbackSession.js';
import { api } from '../../data/api.js';

let fetchDetails = () => {
  let response = api(
    'feedback',
    'fetchDetails',
    feedbackSession.id,
    null,
    null
  );
  if (response) {
    if (feedbackSession.id != response.id) {
      console.error(
        'feedbackSession.id != response.id',
        feedbackSession.id,
        response.id
      );
      return;
    }
    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.name = response.name;
  } else {
    console.error('api response error');
  }
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.path.replace(
    '/feedback/',
    ''
  );
  fetchDetails();
});
</script>

<template>
  <div class="alert alert-warning alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <span id="cookieMsg" v-html="cookieMsg"></span>
    <a href="/privacy-policy" class="alert-link"
      >Click here to read about how LearnLoop uses cookies.
    </a>
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
</style>
