<script setup>
import { onMounted } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";

const back = () => {
  router.push("/feedback/create-details");
};

const formIsValid = () => {
  document
    .getElementById("createSubsessionsForm")
    .classList.add("was-validated");
  if (!feedbackSession.title || !feedbackSession.name) return false;
  if (!feedbackSession.multipleDates && !feedbackSession.date) return false;
  return true;
};
const next = () => {
  if (!formIsValid()) return false;
  router.push("/feedback/create-options");
};

onMounted(async () => {
  if (!feedbackSession.isSeries && !feedbackSession.isSingle) {
    router.push("/feedback/create-type");
  }
});
</script>

<template>
  <form
    id="createSubsessionsForm"
    class="container needs-validation my-4"
    novalidate
  >
    <h1 class="text-center display-4">Feedback</h1>
    <p>
      Add details of your teaching event to start creating your feedback form
    </p>
    <!--Title-->
    <div class="form-floating mb-3">
      <input
        type="text"
        v-model="feedbackSession.title"
        class="form-control"
        id="title"
        placeholder=""
        name="title"
        autocomplete="off"
        v-focus-collapse="'titleHelp'"
        required
      />
      <label for="title">Event title</label>
      <div class="invalid-feedback">
        Please provide a title for your teaching event
      </div>
      <div class="collapse form-text mx-1" id="titleHelp">
        <ul>
          <li>
            Choose a title so attendees know what they're providing feedback for
          </li>
          <li>
            E.g. <i>'ST4-8 teaching day: Renal focus'</i> or
            <i>'How to manage paediatric DKA'</i>
          </li>
          <li>
            The title will appear on the certificate of attendance (if enabled)
          </li>
        </ul>
      </div>
    </div>

    <!--Date-->
    <div class="d-flex flex-wrap mb-3">
      <div class="form-floating flex-grow-1">
        <input
          type="date"
          placeholder=""
          v-model="feedbackSession.date"
          class="form-control"
          id="date"
          name="Date"
          autocomplete="off"
          v-focus-collapse="'dateHelp'"
          required
          :disabled="feedbackSession.multipleDates"
        />
        <label for="name">Date</label>
        <div class="invalid-feedback">
          Please provide a date, or select the multiple dates option
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-start">
        <div class="mx-2">or deliver on multiple dates</div>
        <div class="form-check form-switch">
          <input
            class="form-check-input"
            type="checkbox"
            id="toggleMultipleDates"
            v-model="feedbackSession.multipleDates"
            @click="feedbackSession.date = ''"
          />
        </div>
      </div>
      <div class="collapse form-text mx-1" id="dateHelp">
        <ul>
          <li>Provide a date on which your teaching event is taking place</li>
          <li>
            The date will appear on the certificate of attendance (if enabled)
          </li>
          <li>
            Select multiple dates to collate feedback for a session delivered
            multiple times with the same form
          </li>
        </ul>
      </div>
    </div>

    <!--Name-->
    <div class="form-floating mb-3">
      <input
        type="text"
        v-model="feedbackSession.name"
        class="form-control"
        id="name"
        placeholder=""
        name="name"
        autocomplete="off"
        v-focus-collapse="'nameHelp'"
        required
      />
      <label for="name">Facilitator name</label>
      <div class="invalid-feedback">
        Please provide the name of the facilitator for this teaching event
      </div>
      <div class="collapse form-text mx-1" id="nameHelp">
        <ul>
          <li>
            Provide the name of the person or team responsible for the teaching
          </li>
          <li>&nbsp;E.g. 'Dr Smith', or 'ST4-8 teaching reps'</li>
          <li>
            The facilitator name will appear on the feedback form and the
            certificate of attendance (if enabled)
          </li>
        </ul>
      </div>
    </div>
  </form>
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
</style>
