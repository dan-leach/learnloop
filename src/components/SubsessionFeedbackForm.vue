<script setup>
/*
To do:
skipSubsessionFeedbackForm()
Styling of subsessionFeedbackForm on Submit if fails validation
Swal confirm lose details if skipping after adding details
Move skip button up?
*/

import Modal from 'bootstrap/js/dist/modal';
const props = defineProps(['index', 'subsession']);

const emit = defineEmits(['closeSubsessionFeedbackForm']);

let skipSubsessionFeedbackForm = () => {
  if (
    subsession.positive == '' ||
    subsession.negative == '' ||
    subsession.score == null
  ) {
    /*
    Swal confirm lose details
    if cancel then return false
    if confirm then
      subsession.positive == '' 
      subsession.negative == '' 
      subsession.score == null
      subsession.status = 'Skipped';
      emit('closeSubsessionFeedbackForm', props.index);
    */
  }
};

let submitSubsessionFeedbackForm = () => {
  if (
    subsession.positive == '' ||
    subsession.negative == '' ||
    subsession.score == null
  )
    return false;
  subsession.status = 'Complete';
  emit('closeSubsessionFeedbackForm', props.index);
};
</script>

<template>
  <div class="modal fade" :id="'subsessionFeedbackModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            Feedback for '{{ subsession.title }}' by {{ subsession.name }}
          </h4>
        </div>
      </div>
      <button type="button" class="close" @click="closeModal">&times;</button>
    </div>
    <div class="modal-body">
      <form id="subsessionFeedbackForm" class="needs-validation" novalidate>
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Positive Comments:</span>
            </div>
            <textarea
              rows="8"
              v-model="subsession.positive"
              class="form-control"
              id="positiveComments"
              placeholder="Please provide some feedback about what you enjoyed about this session..."
              name="positive"
              autocomplete="off"
              required
            ></textarea>
            <div class="invalid-feedback">Please fill out this field.</div>
          </div>
          <br />
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Constructive Comments:</span>
            </div>
            <textarea
              rows="8"
              v-model="subsession.negative"
              class="form-control"
              id="negative"
              placeholder="Please provide some feedback about ways this session could be improved..."
              name="negative"
              autocomplete="off"
              required
            ></textarea>
            <div class="invalid-feedback">Please fill out this field.</div>
          </div>
          <br />
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"
                >Overall score (<span id="subsessionScore"></span>/100):</span
              >
            </div>
            <input
              type="range"
              v-model="subsession.score"
              style="width: 80%; margin: 10px"
              id="subsessionScoreRange"
              placeholder=""
              name="subsessionScoreRange"
              autocomplete="off"
              oninput="app.scoreChange(true)"
              onchange="app.scoreChange(true)"
            />
            <div class="invalid-feedback">
              Please indicate an overall score using the slider.
            </div>
          </div>
          <div class="input-group">
            <textarea
              rows="2"
              v-model="subsession.scoreText"
              class="form-control"
              id="subsessionScoreText"
              placeholder=""
              name="subsessionScoreText"
              autocomplete="off"
              readonly
            ></textarea>
          </div>
        </div>
      </form>
      <button
        class="btn btn-primary"
        id="submitSubsessionFeedbackForm"
        v-on:click.prevent="submitSubsessionFeedbackForm"
      >
        Give feedback
      </button>
      <button
        class="btn btn-secondary"
        id="skipSubsessionFeedbackForm"
        v-on:click.prevent="skipSubsessionFeedbackForm"
      >
        Skip this session
      </button>
      <a href="#" data-toggle="modal" data-target="#skipSubsessionFeedbackInfo"
        ><i class="fas fa-question-circle fa-2x"></i
      ></a>
    </div>
  </div>
</template>

<style scoped></style>
