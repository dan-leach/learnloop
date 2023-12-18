<script setup>
import { onMounted } from 'vue';
import Modal from 'bootstrap/js/dist/modal';
import { feedbackSession } from '../../data/feedbackSession.js';
const emit = defineEmits(['hideDownloadFeedbackModal']);
</script>

<template>
  <div class="modal fade" id="downloadFeedbackModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Download feedback as PDF</h4>
          <button
            type="button"
            class="btn-close"
            @click.prevent="emit('hideDownloadFeedbackModal')"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            You can download a feedback report for each session individually, or
            all together with the overall feedback.
          </p>
          <form
            id="downloadFeedbackPostForm"
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
            <select
              id="selectSubID"
              name="subID"
              class="form-select mb-3"
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
            <div class="text-center">
              <button
                class="btn btn-teal text-center"
                id="submitSubsessionFeedbackForm"
              >
                Download
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
