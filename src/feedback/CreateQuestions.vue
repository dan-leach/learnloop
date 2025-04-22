<script setup>
/**
 * @module feedback/CreateQuestions
 * @summary Step 4 of the session creation process.
 * @description This component allows users to add, edit, sort, and remove custom feedback questions
 *  for a feedback session. It supports session-based navigation and uses Bootstrap modals
 *  for editing questions.
 *
 * @requires vue
 * @requires sweetalert2
 * @requires bootstrap/js/dist/modal
 * @requires ../data/feedbackSession.js
 * @requires ../router/index.js
 */

import { onMounted, inject } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Swal from "sweetalert2";
import EditQuestionForm from "./components/EditQuestionForm.vue";
import Modal from "bootstrap/js/dist/modal";

const config = inject("config");

let editQuestionModal;

/**
 * Shows the modal form to add or edit a question.
 * @function showEditQuestionForm
 * @param {number} index - Index of the question to edit, or -1 to add new.
 */
const showEditQuestionForm = (index) => {
  editQuestionModal = new Modal(
    document.getElementById("editQuestionModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editQuestionModal.show();
};

/**
 * Hides the question modal and updates the question list.
 * @function hideEditQuestionModal
 * @param {number} [index] - Question index, or -1 for new, or undefined if cancelled.
 * @param {string} [question] - JSON stringified question object.
 */
const hideEditQuestionModal = (index, question) => {
  if (index) index = parseInt(index);
  if (index === undefined) {
    // Modal closed without submitting - do nothing.
  } else if (index === -1) {
    // Add new question
    feedbackSession.questions.push(JSON.parse(question));
  } else {
    // Edit existing question
    const { title, type, options, settings } = JSON.parse(question);
    Object.assign(feedbackSession.questions[index], {
      title,
      type,
      options,
      settings,
    });
  }
  editQuestionModal.hide();
};

/**
 * Reorders questions in the list.
 * @function sortQuestion
 * @param {number} index - Index of the question to move.
 * @param {number} x - Direction (-1 for up, 1 for down).
 */
const sortQuestion = (index, x) =>
  feedbackSession.questions.splice(
    index + x,
    0,
    feedbackSession.questions.splice(index, 1)[0]
  );

/**
 * Prompts user to confirm and removes a question.
 * @function removeQuestion
 * @param {number} index - Index of the question to remove.
 */
const removeQuestion = async (index) => {
  const { isConfirmed } = await Swal.fire({
    title: "Remove this question?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });

  if (isConfirmed) feedbackSession.questions.splice(index, 1);
};

/**
 * Navigates back to the session details or list view depending on session type.
 * @function back
 */
const back = () => {
  const base = `/feedback/${feedbackSession.isEdit ? "edit" : "create"}`;
  const path = feedbackSession.isSeries
    ? `${base}/sessions${
        feedbackSession.isEdit ? "/" + feedbackSession.id : ""
      }`
    : `${base}/details${
        feedbackSession.isEdit ? "/" + feedbackSession.id : ""
      }`;

  router.push(path);
};

/**
 * Navigates forward to the organisers step.
 * @function next
 */
const next = () => {
  router.push(
    `/feedback/${feedbackSession.isEdit ? "edit" : "create"}/organisers${
      feedbackSession.isEdit ? "/" + feedbackSession.id : ""
    }`
  );
};

// Redirect to type selection if session hasn't been initialized
onMounted(() => {
  if (
    !feedbackSession.isSeries &&
    !feedbackSession.isSingle &&
    !feedbackSession.useTemplate
  ) {
    router.push("/feedback/create/type");
  }
});
</script>

<template>
  <div class="container my-4">
    <h1 class="text-center display-4">Feedback</h1>
    <div class="text-center">
      <p v-if="feedbackSession.isEdit" class="form-label ms-2">
        Editing feedback session
        <span class="id-box">{{ feedbackSession.id }}</span>
      </p>
      <p v-else>Add custom questions (optional)</p>
    </div>
    <div class="p-2 mb-3">
      <!--custom questions-->
      <div class="alert alert-teal" alert-dismissible fade show role="alert">
        <div class="d-flex justify-content-between">
          <h4 class="alert-heading">Custom questions</h4>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
          ></button>
        </div>
        <span
          >The default feedback form asks attendees to provide:
          <ul>
            <li>free-text positive comments</li>
            <li>free-text constructive criticism</li>
            <li>an overall score (out of 100 using a slider)</li>
          </ul>
        </span>
        <span
          >You can add additional questions below if required (optional).</span
        ><br />
        <span v-if="feedbackSession.isSeries"
          >Custom questions only relate to the overall feedback and aren't asked
          for each session.</span
        >
      </div>
      <table class="table" id="questionsTable">
        <thead>
          <tr>
            <th class="bg-transparent p-0 ps-2"></th>
            <th class="bg-transparent p-0 ps-2">Question</th>
            <th class="bg-transparent p-0 ps-2">Type</th>
            <th class="bg-transparent p-0 ps-2">
              <button
                class="btn btn-teal btn-sm btn-right"
                id="btnAddQuestion"
                @click.prevent="showEditQuestionForm(-1)"
              >
                Add <i class="fas fa-plus"></i>
              </button>
            </th>
          </tr>
        </thead>
        <TransitionGroup name="list" tag="tbody">
          <template
            v-for="(question, index) in feedbackSession.questions"
            :key="question"
          >
            <tr>
              <td class="bg-transparent p-0 ps-2">
                <button
                  v-if="index != 0"
                  class="btn btn-default btn-sm p-0"
                  id="btnSortUp"
                  @click="sortQuestion(index, -1)"
                >
                  <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
                ><br />
                <button
                  v-if="index != feedbackSession.questions.length - 1"
                  class="btn btn-default btn-sm p-0"
                  id="btnSortDown"
                  @click="sortQuestion(index, 1)"
                >
                  <font-awesome-icon :icon="['fas', 'chevron-down']" />
                </button>
              </td>
              <td class="bg-transparent">{{ question.title }}</td>
              <td class="bg-transparent">
                {{ config.feedback.create.questions.types[question.type].name }}
              </td>
              <td class="bg-transparent">
                <button
                  class="btn btn-danger btn-sm btn-right ms-4"
                  id="btnRemoveQuestion"
                  @click="removeQuestion(index)"
                >
                  <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                <button
                  class="btn btn-teal btn-sm btn-right"
                  id="btnEditQuestion"
                  @click="showEditQuestionForm(index)"
                >
                  <font-awesome-icon :icon="['fas', 'edit']" />
                </button>
              </td>
            </tr>
          </template>
        </TransitionGroup>
      </table>
      <template v-for="(question, index) in feedbackSession.questions">
        <EditQuestionForm
          :index="index"
          @hideEditQuestionModal="hideEditQuestionModal"
        />
      </template>
      <EditQuestionForm
        index="-1"
        @hideEditQuestionModal="hideEditQuestionModal"
      />
    </div>
  </div>
  <!--back/next button-->
  <div class="d-flex justify-content-evenly mb-3">
    <button class="btn btn-secondary btn-lg me-2 mb-2" id="back" @click="back">
      Back
    </button>
    <button class="btn btn-teal btn-lg me-2 mb-2" id="next" @click="next">
      Continue
    </button>
  </div>
</template>

<style>
.container {
  max-width: 750px;
}
.btn-settings {
  min-width: 150px;
}
</style>
