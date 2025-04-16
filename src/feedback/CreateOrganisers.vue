<script setup>
import { onMounted, ref } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Swal from "sweetalert2";
import Modal from "bootstrap/js/dist/modal";
import { api } from "../data/api.js";
import EditOrganiserForm from "./components/EditOrganiserForm.vue";

let editOrganiserModal;
const showEditOrganiserForm = (index) => {
  editOrganiserModal = new Modal(
    document.getElementById("editOrganiserModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editOrganiserModal.show();
};
const hideEditOrganiserModal = (index, organiser) => {
  if (index === undefined) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.organisers.push(JSON.parse(organiser));
  } else {
    const { name, email, isLead, canEdit } = JSON.parse(organiser);
    Object.assign(feedbackSession.organisers[index], {
      name,
      email,
      isLead,
      canEdit,
    });
  }
  editOrganiserModal.hide();
};
const sortOrganiser = (index, x) =>
  feedbackSession.organisers.splice(
    index + x,
    0,
    feedbackSession.organisers.splice(index, 1)[0]
  );
const removeOrganiser = async (index) => {
  const { isConfirmed } = await Swal.fire({
    title: "Remove this organiser?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });

  if (isConfirmed) feedbackSession.organisers.splice(index, 1);
};

const back = () => {
  router.push("/feedback/create-questions");
};

const showWarning = ref(false);
const formIsValid = () => {
  if (!feedbackSession.organisers.length) {
    showWarning.value = true;
    return false;
  }
  return true;
};

const btnSubmit = ref({
  text: "Create feedback session",
  wait: false,
});
const submit = async () => {
  if (!formIsValid()) return false;
  try {
    btnSubmit.value.text = "Please wait...";
    btnSubmit.value.wait = true;
    const response = await api("feedback/insertSession", feedbackSession);
    btnSubmit.value.text = "Create feedback session";
    btnSubmit.value.wait = false;
    feedbackSession.id = response.id;
    feedbackSession.pin = response.leadPin;
    feedbackSession.sendMailFails = response.sendMailFails;
    router.push("/feedback/created");
  } catch (error) {
    btnSubmit.value.text = "Retry creating feedback session?";
    btnSubmit.value.wait = false;
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      title: "Error creating feedback session",
      text: error,
      icon: "error",
      iconColor: "#17a2b8",
      confirmButtonColor: "#17a2b8",
    });
  }
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
      <p>Add organisers who need to view feedback or edit the session</p>
    </div>
    <!--organisers-->
    <div class="alert alert-teal" alert-dismissible fade show role="alert">
      <div class="d-flex justify-content-between">
        <h4 class="alert-heading">Organisers</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
      <span
        >Adding an organiser allows them to view the feedback for this
        session.</span
      ><br />
      <span>You can optionally grant access to edit the feedback request.</span
      ><br />
      <span>You should add your own details as the lead organsier.</span><br />
      <span
        >The lead organiser cannot be changed once the session is created.</span
      >
    </div>

    <p class="d-flex align-items-center">
      <font-awesome-icon
        :icon="['fas', 'triangle-exclamation']"
        size="2xl"
        style="color: #17a2b8"
        class="me-3"
      />
      Only add organisers who should be able to view the feedback for all
      sessions.
    </p>

    <table class="table" id="organisersTable">
      <thead>
        <tr>
          <th class="bg-transparent p-0 ps-2"></th>
          <th class="bg-transparent p-0 ps-2">Name</th>
          <th class="bg-transparent p-0 ps-2">Email</th>
          <th class="bg-transparent p-0 ps-2">Lead</th>
          <th class="bg-transparent p-0 ps-2">Can edit?</th>
          <th class="bg-transparent p-0 ps-2">
            <button
              class="btn btn-teal btn-sm btn-right"
              id="btnAddOrganiser"
              @click.prevent="showEditOrganiserForm(-1)"
            >
              Add <i class="fas fa-plus"></i>
            </button>
          </th>
        </tr>
      </thead>
      <TransitionGroup name="list" tag="tbody">
        <template
          v-for="(organiser, index) in feedbackSession.organisers"
          :key="organiser"
        >
          <tr>
            <td class="bg-transparent p-0 ps-2">
              <button
                v-if="index != 0"
                class="btn btn-default btn-sm p-0"
                id="btnSortUp"
                @click="sortOrganiser(index, -1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
              ><br />
              <button
                v-if="index != feedbackSession.organisers.length - 1"
                class="btn btn-default btn-sm p-0"
                id="btnSortDown"
                @click="sortOrganiser(index, 1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-down']" />
              </button>
            </td>
            <td class="bg-transparent">{{ organiser.name }}</td>
            <td class="bg-transparent">{{ organiser.email }}</td>
            <td class="bg-transparent">
              <font-awesome-icon
                v-if="organiser.isLead"
                :icon="['fas', 'check']"
                size="2xl"
                style="color: green"
              />
              <font-awesome-icon
                v-else
                :icon="['fas', 'times']"
                size="2xl"
                style="color: red"
              />
            </td>
            <td class="bg-transparent">
              <font-awesome-icon
                v-if="organiser.canEdit"
                :icon="['fas', 'check']"
                size="2xl"
                style="color: green"
              />
              <font-awesome-icon
                v-else
                :icon="['fas', 'times']"
                size="2xl"
                style="color: red"
              />
            </td>
            <td class="bg-transparent">
              <button
                class="btn btn-danger btn-sm btn-right ms-4"
                id="btnRemoveOrganiser"
                @click="removeOrganiser(index)"
              >
                <font-awesome-icon :icon="['fas', 'trash-can']" />
              </button>
              <button
                class="btn btn-teal btn-sm btn-right"
                id="btnEditOrganiser"
                @click="showEditOrganiserForm(index)"
              >
                <font-awesome-icon :icon="['fas', 'edit']" />
              </button>
            </td>
          </tr>
        </template>
      </TransitionGroup>
    </table>
    <template v-for="(organisers, index) in feedbackSession.organisers">
      <EditOrganiserForm
        :index="index"
        :isEdit="false"
        @hideEditOrganiserModal="hideEditOrganiserModal"
      />
    </template>
    <EditOrganiserForm
      index="-1"
      :isEdit="false"
      @hideEditOrganiserModal="hideEditOrganiserModal"
    />
    <div
      class="text-danger"
      v-if="showWarning && !feedbackSession.organisers.length"
    >
      You must add at least one organiser
    </div>
  </div>
  <!--back/next button-->
  <div class="d-flex justify-content-evenly mb-3">
    <button class="btn btn-secondary btn-lg me-2 mb-2" id="back" @click="back">
      Back
    </button>
    <button
      class="btn btn-teal btn-lg me-2 mb-2"
      id="submit"
      @click="submit"
      :disabled="btnSubmit.wait"
    >
      <span
        v-if="btnSubmit.wait"
        class="spinner-border spinner-border-sm me-2"
      ></span
      >{{ btnSubmit.text }}
    </button>
  </div>
</template>

<style>
.container {
  max-width: 750px;
}
</style>
