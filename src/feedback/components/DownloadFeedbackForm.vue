<script setup>
import { ref } from "vue";
import { feedbackSession } from "../../data/feedbackSession.js";
const emit = defineEmits(["hideDownloadFeedbackModal", "fetchFeedbackPDF"]);
const downloadId = ref("");
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
          <form id="downloadFeedbackForm">
            <select
              id="selectDownloadId"
              name="selectDownloadId"
              class="form-select mb-3"
              v-if="feedbackSession.subsessions.length"
              v-model="downloadId"
            >
              <option value="">Overall feedback and all sessions</option>
              <option
                v-for="subsession in feedbackSession.subsessions"
                :value="subsession.id"
                name="downloadId"
              >
                Just '{{ subsession.title }}'
              </option>
            </select>
            <div class="text-center">
              <button
                class="btn btn-teal text-center"
                id="submitDownloadFeedbackForm"
                @click.prevent="emit('fetchFeedbackPDF', downloadId)"
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
