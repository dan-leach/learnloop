<script setup>
import Modal from 'bootstrap/js/dist/modal';
const props = defineProps(['index', 'subsession']);

const emit = defineEmits(['hideSubsessionFeedbackModal']);

let submitSubsessionFeedbackForm = () => {
  document
    .getElementById('subsessionFeedbackModal' + props.index)
    .classList.add('was-validated');
  if (
    props.subsession.positive == '' ||
    props.subsession.negative == '' ||
    props.subsession.score == null
  )
    return false;
  props.subsession.status = 'Complete';
  emit('hideSubsessionFeedbackModal', props.index);
};

let scoreChange = () => {
  let x = props.subsession.score;
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

  props.subsession.scoreText = y;
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
          <button
            type="button"
            class="btn-close"
            @click.prevent="emit('hideSubsessionFeedbackModal', props.index)"
          ></button>
        </div>
        <div class="modal-body">
          <form id="subsessionFeedbackForm" class="needs-validation" novalidate>
            <div>
              <label for="positiveComments" class="form-label"
                >Positive Comments:</label
              >
              <textarea
                rows="5"
                v-model="subsession.positive"
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
                >Constructive Comments:</label
              >
              <textarea
                rows="5"
                v-model="subsession.negative"
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
            <div class="mt-4">
              <label for="score" class="form-label"
                >Score: {{ subsession.score }}/100</label
              >
              <input
                type="range"
                v-model="subsession.score"
                id="scoreRange"
                placeholder=""
                class="form-range mx-2"
                name="scoreRange"
                autocomplete="off"
                @input="scoreChange"
                @change="scoreChange"
                required
              />
              <p class="text-center">
                {{ subsession.scoreText }}
              </p>
              <input
                type="text"
                v-model="subsession.score"
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
          <button
            class="btn btn-primary text-center"
            id="submitSubsessionFeedbackForm"
            v-on:click.prevent="submitSubsessionFeedbackForm"
          >
            Give feedback
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
