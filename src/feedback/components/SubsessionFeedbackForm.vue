<script setup>
/**
 * @module feedback/components/SubsessionFeedbackForm
 * @summary Provides a feedback modal for a session's subsession.
 * @description This module allows users to provide feedback for a specific subsession. It includes a form for providing positive and constructive comments, as well as a score for the session. It validates the form and emits events when feedback is successfully submitted.
 * @requires vue
 */

const props = defineProps(["index", "subsession"]);

const emit = defineEmits(["hideSubsessionFeedbackModal"]);

/**
 * Handles the submission of the feedback form.
 * Validates the form inputs and marks the subsession as complete upon successful submission.
 * Emits an event to hide the modal after feedback submission.
 */
let submitSubsessionFeedbackForm = () => {
  document
    .getElementById("subsessionFeedbackModal" + props.index)
    .classList.add("was-validated");
  if (
    props.subsession.positive == "" ||
    props.subsession.negative == "" ||
    props.subsession.score == null
  )
    return false;
  props.subsession.status = "Complete";
  emit("hideSubsessionFeedbackModal", props.index);
};

/**
 * Updates the score text based on the user's score input.
 * This method categorizes the score and generates a text description.
 */
let scoreChange = () => {
  let x = props.subsession.score;
  let y = "slider error";
  if (x > 95) {
    y = "an overwhelmingly excellent session, couldn't be improved";
  } else if (x > 80) {
    y = "an excellent session, minimal grounds for improvement";
  } else if (x > 70) {
    y = "a very good session, minor points for improvement";
  } else if (x > 60) {
    y = "a fairly good session, could be improved further";
  } else if (x > 40) {
    y = "basically sound, but needs further development";
  } else if (x >= 20) {
    y = "not adequate in its current state";
  } else if (x < 20) {
    y = "an extremely poor session";
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
                >Positive comments
                <sup
                  ><font-awesome-icon
                    :icon="['fas', 'asterisk']"
                    size="2xs"
                    style="color: #ff0000"
                    class="float-right" /></sup
              ></label>
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
                >Constructive comments
                <sup
                  ><font-awesome-icon
                    :icon="['fas', 'asterisk']"
                    size="2xs"
                    style="color: #ff0000"
                    class="float-right" /></sup
              ></label>
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
                >Score
                <sup
                  ><font-awesome-icon
                    :icon="['fas', 'asterisk']"
                    size="2xs"
                    style="color: #ff0000"
                    class="float-right" /></sup
                >&nbsp;&nbsp;&nbsp;&nbsp;{{ subsession.score }}/100</label
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
                class="form-control-range form-range"
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
          <div class="text-center">
            <button
              class="btn btn-teal text-center"
              id="submitSubsessionFeedbackForm"
              v-on:click.prevent="submitSubsessionFeedbackForm"
            >
              Give feedback
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-label {
  font-size: 1.3rem;
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
</style>
