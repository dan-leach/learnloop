<script setup>
import { onMounted } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Swal from "sweetalert2";
import EditQuestionForm from "./components/EditQuestionForm.vue";
import Modal from "bootstrap/js/dist/modal";

let editQuestionModal;
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
const hideEditQuestionModal = (index, question) => {
  if (index === undefined) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.questions.push(JSON.parse(question));
  } else {
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
const sortQuestion = (index, x) =>
  feedbackSession.questions.splice(
    index + x,
    0,
    feedbackSession.questions.splice(index, 1)[0]
  );
const removeQuestion = async (index) => {
  const { isConfirmed } = await Swal.fire({
    title: "Remove this question?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });

  if (isConfirmed) feedbackSession.questions.splice(index, 1);
};

const back = () => {
  if (feedbackSession.isSeries) {
    router.push("/feedback/create-subsessions");
  } else {
    router.push("/feedback/create-details");
  }
};

const next = () => {
  router.push("/feedback/create-organisers");
};

onMounted(async () => {
  if (
    !feedbackSession.isSeries &&
    !feedbackSession.isSingle &&
    !feedbackSession.useTemplate
  ) {
    router.push("/feedback/create-type");
  }
});
</script>

<template>
  <div class="container my-4">
    <h1 class="text-center display-4">Feedback</h1>
    <div class="text-center">
      <p>Add custom questions (optional)</p>
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
