<script setup>
import { onMounted, inject, ref } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Swal from "sweetalert2";
import EditSubsessionForm from "./components/EditSubsessionForm.vue";
import Modal from "bootstrap/js/dist/modal";
const config = inject("config");

let editSubsessionModal;
const showEditSubsessionForm = (index) => {
  editSubsessionModal = new Modal(
    document.getElementById("editSubsessionModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editSubsessionModal.show();
};

/**
 * Validates an email address.
 *
 * This function checks if the provided email follows the standard email format.
 *
 * @param {string} email - The email address to validate.
 * @returns {boolean} - Returns true if the email is valid, otherwise false.
 */
function emailIsValid(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

const hideEditSubsessionModal = async (index, subsession) => {
  //hide the subsession form
  editSubsessionModal.hide();

  if (index === undefined) return; //user did not submit the form, closed using the X. Do nothing except hide the modal

  //get the data from the form output
  const { name, title, email } = JSON.parse(subsession);

  if (index == -1) {
    //if index -1, is a new subsession so just push directly
    feedbackSession.subsessions.push({ name, title, email });
  } else {
    //otherwise use Object assign to avoid row visually jumping around as array mutated
    Object.assign(feedbackSession.subsessions[index], { name, title, email });
  }
  if (!email && config.value.client.subsessionEmailPrompt) {
    config.value.client.subsessionEmailPrompt = false; //only prompt once
    const { isConfirmed } = await Swal.fire({
      title: "Are you sure you don't want to provide a facilitator email?",
      text: "If you don't provide an email for the facilitator of this session they won't be able to view their feedback directly. As the organiser, you will still be able to view feedback for their session and share it with them manually if you wish. Click 'Cancel' if you want to return and enter a faciltator email.",
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
    });

    if (!isConfirmed) {
      //if user opts to return to add an email, show the form again. If the index was -1 the subsession will be at the last index position
      showEditSubsessionForm(
        index >= 0 ? index : feedbackSession.subsessions.length - 1
      );
    }
  } else if (email && !emailIsValid(email)) {
    await Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      text: "Email is not valid",
      confirmButtonColor: "#17a2b8",
    });
    //show the form again. If the index was -1 the subsession will be at the last index position
    showEditSubsessionForm(
      index >= 0 ? index : feedbackSession.subsessions.length - 1
    );
  }
};
const sortSubsession = (index, x) =>
  feedbackSession.subsessions.splice(
    index + x,
    0,
    feedbackSession.subsessions.splice(index, 1)[0]
  );
const removeSubsession = async (index) => {
  const { isConfirmed } = await Swal.fire({
    title: "Remove this subsession?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });

  if (isConfirmed) feedbackSession.subsessions.splice(index, 1);
};

const back = () => {
  router.push(
    `/feedback/${feedbackSession.isEdit ? "edit" : "create"}/details${
      feedbackSession.isEdit ? "/" + feedbackSession.id : ""
    }`
  );
};

const showWarning = ref(false);
const formIsValid = () => {
  if (!feedbackSession.subsessions.length) {
    showWarning.value = true;
    return false;
  }
  return true;
};
const next = () => {
  if (!formIsValid()) return false;
  router.push(
    `/feedback/${feedbackSession.isEdit ? "edit" : "create"}/questions${
      feedbackSession.isEdit ? "/" + feedbackSession.id : ""
    }`
  );
};

onMounted(async () => {
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
      <p v-else>Add sessions to your teaching event</p>
    </div>
    <div class="alert alert-teal" alert-dismissible fade show role="alert">
      <div class="d-flex justify-content-between">
        <h4 class="alert-heading">Sessions</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
      <span
        >Attendees will be asked to provide feedback on each of the sessions you
        add below.</span
      ><br />
      <span
        >Session facilitators can view their feedback if your provide their
        email.</span
      >
    </div>
    <!--subsessions-->
    <table class="table" id="subsessionsTable">
      <thead>
        <tr>
          <th class="bg-transparent p-0 ps-2"></th>
          <th class="bg-transparent p-0 ps-2">Title</th>
          <th class="bg-transparent p-0 ps-2">Facilitator</th>
          <th class="bg-transparent p-0 ps-2">Email</th>
          <th class="bg-transparent p-0 ps-2">
            <button
              class="btn btn-teal btn-sm btn-right"
              id="btnAddSubsession"
              @click.prevent="showEditSubsessionForm(-1)"
            >
              Add <i class="fas fa-plus"></i>
            </button>
          </th>
        </tr>
      </thead>
      <TransitionGroup name="list" tag="tbody">
        <template
          v-for="(subsession, index) in feedbackSession.subsessions"
          :key="subsession"
        >
          <tr>
            <td class="bg-transparent p-0 ps-2">
              <button
                v-if="index != 0"
                class="btn btn-default btn-sm p-0"
                id="btnSortUp"
                @click="sortSubsession(index, -1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
              ><br />
              <button
                v-if="index != feedbackSession.subsessions.length - 1"
                class="btn btn-default btn-sm p-0"
                id="btnSortDown"
                @click="sortSubsession(index, 1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-down']" />
              </button>
            </td>
            <td class="bg-transparent">{{ subsession.title }}</td>
            <td class="bg-transparent">{{ subsession.name }}</td>
            <td class="bg-transparent">{{ subsession.email }}</td>
            <td class="bg-transparent">
              <button
                class="btn btn-danger btn-sm btn-right ms-4"
                id="btnRemoveSubsession"
                @click="removeSubsession(index)"
              >
                <font-awesome-icon :icon="['fas', 'trash-can']" />
              </button>
              <button
                class="btn btn-teal btn-sm btn-right"
                id="btnEditSubsession"
                @click="showEditSubsessionForm(index)"
              >
                <font-awesome-icon :icon="['fas', 'edit']" />
              </button>
            </td>
          </tr>
        </template>
      </TransitionGroup>
    </table>
    <template v-for="(subsession, index) in feedbackSession.subsessions">
      <EditSubsessionForm
        :index="index"
        :isEdit="feedbackSession.isEdit"
        @hideEditSubsessionModal="hideEditSubsessionModal"
      />
    </template>
    <EditSubsessionForm
      index="-1"
      :isEdit="feedbackSession.isEdit"
      @hideEditSubsessionModal="hideEditSubsessionModal"
    />
    <div
      class="text-danger"
      v-if="showWarning && !feedbackSession.subsessions.length"
    >
      You must add at least one session
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
</style>
