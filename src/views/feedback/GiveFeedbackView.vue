<script setup>
/*
Current task:
See todo in SubsessionFeedbackForm
skipSubsession()
Swal confirm lose details if skipping after adding details
New icon for skip button

ToDo:
redirect to homepage if unable to load form e.g. due to api failure
check and load cookies if found
Do all the subsession give/edit/skip functions - can this be done directly on the array object rather than shifting back and forth from a separate subsessions object?
Do I still need the Vue.set or do something with ref()?
  previously: for (let subsession of feedbackSession.subsessions) Vue.set(subsession,'status','To do') //using Vue.set to enable reactivity for subsession status such that 'skipped' and 'completed' appear appropriately
*/

import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../../router';
import { feedbackSession } from '../../data/feedbackSession.js';
import { api } from '../../data/api.js';
import Swal from 'sweetalert2';
import Modal from 'bootstrap/js/dist/modal';
import SubsessionFeedbackForm from '../../components/SubsessionFeedbackForm.vue';

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
      feedbackSession.subsessions = res.subsessions;
      for (let subsession of feedbackSession.subsessions) {
        subsession.status = 'To do';
        subsession.positive = '';
        subsession.negative = '';
        subsession.score = null;
        subsession.scoreText =
          'Please use the slider to indicate an overall score.';
      }
      feedbackSession.questions = res.questions;
      for (let question of feedbackSession.questions) {
        if (question.required == undefined) {
          //for older sessions with undefined 'required' paramenter default to required for text and select but not for checkboxes
          if (question.type == 'text' || question.type == 'select')
            question.required = true;
        }
      }
      feedbackSession.certificate = res.certificate;
      feedbackSession.attendance = res.attendance;
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

let noOptionsSelected = (question) => {
  for (let option of question.options) if (option.selected) return false;
  return true;
};

let formIsValid = () => {
  document.getElementById('giveFeedbackForm').classList.add('was-validated');
  for (let i in feedbackSession.subsessions) {
    //needs to be first check to ensure correct styling of subsession status table cells before a return false ends the function
    let subsession = feedbackSession.subsessions[i];
    let statusElement = document.getElementById('subsession' + i + 'Status');
    if (subsession.status == 'To do') {
      statusElement.classList.add('is-invalid');
      statusElement.classList.remove('is-valid');
    } else if (
      subsession.status == 'Skipped' ||
      subsession.status == 'Complete'
    ) {
      statusElement.classList.add('is-valid');
      statusElement.classList.remove('is-invalid');
    }
  }
  if (
    feedbackSession.feedback.positive == '' ||
    feedbackSession.feedback.negative == '' ||
    feedbackSession.feedback.score == null
  )
    return false;
  for (let question of feedbackSession.questions) {
    if (question.required) {
      if (question.type == 'text' || question.type == 'select') {
        if (question.response == null || question.response === 'undefined')
          return false;
      } else if (question.type == '"checkbox') {
        if (noOptionsSelected(question)) return false;
      }
    }
  }
  return true;
};

let submit = () => {
  if (!formIsValid()) {
    console.log('form validation failed');
    return;
  }
  api(
    'feedback',
    'insertFeedback',
    feedbackSession.id,
    null,
    feedbackSession
  ).then(
    function () {
      router.push('/feedback/feedbackComplete');
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable submit your feedback',
        text: error,
      });
    }
  );
};

let subsessionFeedbackModal;
let showSubsessionFeedbackModal = (index) => {
  subsessionFeedbackModal = new Modal(
    document.getElementById('subsessionFeedbackModal' + index),
    {
      backdrop: 'static',
      keyboard: false,
      focus: true,
    }
  );
  subsessionFeedbackModal.show();
};
let hideSubsessionFeedbackForm = (index) => {
  subsessionFeedbackModal.hide();
  feedbackSession.subsessions[index].status = 'Complete';
};

let skipSubsessionFeedbackInfoModal;
let showSkipSubsessionFeedbackInfo = (index) => {
  skipSubsessionFeedbackInfoModal = new Modal(
    document.getElementById('skipSubsessionFeedbackInfo'),
    {
      backdrop: true,
      keyboard: true,
      focus: true,
    }
  );
  skipSubsessionFeedbackInfoModal.show();
};
let hideSkipSubsessionFeedbackInfo = (index) => {
  skipSubsessionFeedbackInfoModal.hide();
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
    <div v-if="feedbackSession.subsessions.length">
      <label class="form-label">Feedback on sessions:</label>
      <table class="table" id="subsessionTable">
        <thead>
          <tr>
            <th>Session</th>
            <th>Status</th>
            <th>Feedback</th>
            <th>
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
            <td>
              <strong>{{ subsession.title }}</strong
              ><br />{{ subsession.name }}
            </td>
            <td>
              <span
                :id="'subsession' + index + 'Status'"
                class="subsession-status form-control"
                >{{ subsession.status }}</span
              >
            </td>
            <td class="text-center subsession-button">
              <button
                class="btn btn-secondary btn-sm"
                id="loadGiveSubsessionFeedback"
                v-on:click.prevent="showSubsessionFeedbackModal(index)"
              >
                <font-awesome-icon :icon="['fas', 'pen-to-square']" />
              </button>
            </td>
            <td class="text-center subsession-button">
              <button
                class="btn btn-secondary btn-sm"
                id="loadGiveSubsessionFeedback"
                v-on:click.prevent="showSubsessionFeedbackModal(index)"
              >
                <font-awesome-icon :icon="['fas', 'pen-to-square']" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <SubsessionFeedbackForm
        v-for="(subsession, index) in feedbackSession.subsessions"
        :index="index"
        :subsession="subsession"
        @hideSubsessionFeedbackForm="hideSubsessionFeedbackForm"
      />
      <div class="modal" id="skipSubsessionFeedbackInfo">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Skipping feedback for a subsession</h4>
              <button
                type="button"
                class="close"
                @click.prevent="hideSkipSubsessionFeedbackInfo"
              >
                &times;
              </button>
            </div>
            <div class="modal-body">
              If you did not attend a particular session within a series you can
              skip providing feedback by clicking the 'Skip this session'
              button.<br />
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
        class="btn btn-secondary btn-sm"
        id="saveProgress"
        v-on:click="saveProgress(true)"
      >
        Save progress
      </button>
    </div>
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
      <div class="invalid-feedback">Please provide some positive comments.</div>
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
      <div class="invalid-feedback">
        Please provide some constructive comments.
      </div>
    </div>
    <div
      v-for="(question, questionIndex) in feedbackSession.questions"
      class="mt-4"
    >
      <label for="custom+index" class="form-label">{{ question.title }}:</label>
      <div v-if="question.type == 'text'">
        <textarea
          rows="5"
          v-model="question.response"
          class="form-control"
          id="question.title"
          name="question.title"
          autocomplete="off"
          :required="question.required"
        ></textarea>
        <div class="invalid-feedback">Please answer this question.</div>
      </div>
      <div v-if="question.type == 'checkbox'">
        <span
          v-for="(option, optionIndex) in question.options"
          class="form-check"
        >
          <input
            :id="'question[' + questionIndex + '];option[' + optionIndex + ']'"
            class="form-check-input"
            type="checkbox"
            value="option.title"
            v-model="option.selected"
            :required="noOptionsSelected(question) && question.required"
          />
          <label
            class="form-check-label"
            :for="'question[' + questionIndex + '];option[' + optionIndex + ']'"
            >{{ option.title }}</label
          >
          <div
            v-if="optionIndex == question.options.length - 1"
            class="invalid-feedback"
          >
            Please select at least one checkbox.
          </div>
          <br />
        </span>
      </div>
      <div v-if="question.type == 'select'">
        <select
          v-model="question.response"
          class="form-control"
          :required="question.required"
        >
          <option disabled value="">Please select one</option>
          <option v-for="(option, index) in question.options">
            {{ option.title }}
          </option>
        </select>
        <div class="invalid-feedback">Please select an option.</div>
      </div>
      <br />
    </div>
    <div class="mt-4">
      <label for="score" class="form-label"
        >Score: {{ feedbackSession.feedback.score }}/100</label
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
  <div class="text-center mt-4">
    <button class="btn btn-primary" id="submitGiveFeedback" @click="submit">
      Give Feedback
    </button>
  </div>
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
</style>
