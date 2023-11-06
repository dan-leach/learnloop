<script setup>
import { ref } from 'vue';
import router from '../router';
import { feedbackSession } from '../data/feedbackSession.js';
import { api } from '../data/api.js';
import { config } from '../data/config.js';
import Modal from 'bootstrap/js/dist/modal';
import EditSubsessionForm from './components/EditSubsessionForm.vue';
import Swal from 'sweetalert2';

let isSeries = ref(false);
const toggleSingleSeries = () => {
  if (isSeries.value) {
    if (feedbackSession.subsessions.length) {
      Swal.fire({
        title: 'Lose sessions',
        text: 'You have added sessions to this feedback request. If you switch back to requesting feedback for a single session you will lose this progress. Continue?',
        showCancelButton: true,
      }).then((result) => {
        if (result.isConfirmed) {
          feedbackSession.subsessions = [];
          isSeries.value = !isSeries.value;
        } else {
          return;
        }
      });
    } else {
      isSeries.value = !isSeries.value;
    }
  } else {
    isSeries.value = !isSeries.value;
  }
};

let editSubsessionModal;
const showEditSubsessionForm = (index) => {
  editSubsessionModal = new Modal(
    document.getElementById('editSubsessionModal' + index),
    {
      backdrop: 'static',
      keyboard: false,
      focus: true,
    }
  );
  editSubsessionModal.show();
};
const hideEditSubsessionModal = (index, subsession) => {
  console.log(subsession);
  if (!index) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.subsessions.push(JSON.parse(subsession));
  } else {
    feedbackSession.subsessions[index] = JSON.parse(subsession);
  }
  editSubsessionModal.hide();
};

const sortSubsession = (index, x) =>
  feedbackSession.subsessions.splice(
    index + x,
    0,
    feedbackSession.subsessions.splice(index, 1)[0]
  );
const removeSubsession = (index) => {
  Swal.fire({
    title: 'Remove this subsession?',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
  }).then((result) => {
    if (result.isConfirmed) feedbackSession.subsessions.splice(index, 1);
  });
};

let btnSubmit = ref({
  text: 'Create feedback session',
  wait: false,
});
const formIsValid = () => {
  document.getElementById('createSessionForm').classList.add('was-validated');
  if (!feedbackSession.title || !feedbackSession.name || !feedbackSession.email)
    return false;
  if (isSeries.value && !feedbackSession.subsessions.length)
    Swal.fire({
      icon: 'error',
      title: 'No sessions added to your session series',
      text: 'You need to add at least one session to your session series, or switch back to creating a feedback request for a single session.',
    });
  return true;
};
const submit = () => {
  if (!formIsValid()) return false;
  btnSubmit.value.text = 'Please wait...';
  btnSubmit.value.wait = true;
  api('feedback', 'insertSession', null, null, feedbackSession).then(
    function (res) {
      btnSubmit.value.text = 'Create feedback session';
      btnSubmit.value.wait = false;
      interactSession.id = res.id;
      interactSession.pin = res.pin;
      router.push('/feedback/created');
    },
    function (error) {
      btnSubmit.value.text = 'Retry creating feedback session?';
      btnSubmit.value.wait = false;
      Swal.fire({
        title: 'Error creating feedback session',
        text: error,
        icon: 'error',
        confirmButtonColor: '#17a2b8',
      });
    }
  );
};
</script>

<template>
  <h1 class="text-center display-4">Feedback</h1>
  <div v-if="!isSeries" class="mt-4">
    <strong>You are currently requesting feedback for a single session.</strong>
    <button
      class="btn btn-sm btn-teal m-2"
      id="enableSubsessions"
      @click="toggleSingleSeries"
    >
      Switch to session series
    </button>
  </div>
  <div v-else class="mt-4">
    <strong>You are currently requesting feedback for a session series.</strong>
    <button
      class="btn btn-sm btn-teal m-2"
      id="enableSubsessions"
      @click="toggleSingleSeries"
    >
      Switch back to single session
    </button>
  </div>
  <strong
    >Please provide details for the feedback session you're creating.</strong
  >
  <form id="createSessionForm" class="needs-validation" novalidate>
    <div>
      <label for="title">Session title:</label>
      <input
        type="text"
        v-model="feedbackSession.title"
        class="form-control"
        id="title"
        placeholder="Title of the session..."
        name="title"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <div class="mt-4">
      <label for="name">Session date:</label>
      <input
        type="date"
        v-model="feedbackSession.date"
        class="form-control"
        id="date"
        name="Date"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <div class="mt-4">
      <label for="name">Facilitator name:</label>
      <input
        type="text"
        v-model="feedbackSession.name"
        class="form-control"
        id="name"
        placeholder="Facilitator of the session..."
        name="name"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <div class="mt-4">
      <label for="email">Facilitator email:</label>
      <input
        type="email"
        v-model="feedbackSession.email"
        class="form-control"
        id="email"
        placeholder="Email to send details to..."
        name="email"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
  </form>
  <div v-if="isSeries">
    <h2 class="mt-4">Sessions</h2>
    <table class="table" id="subsessionsTable">
      <thead>
        <tr>
          <th></th>
          <th>Title</th>
          <th>Facilitator</th>
          <th>Email</th>
          <th>
            <button
              class="btn btn-success btn-sm btn-right"
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
            <td class="p-0 ps-2">
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
            <td>{{ subsession.title }}</td>
            <td>{{ subsession.name }}</td>
            <td>{{ subsession.email }}</td>
            <td>
              <button
                class="btn btn-danger btn-sm btn-right ms-4"
                id="btnRemoveSubsession"
                @click="removeSubsession(index)"
              >
                <font-awesome-icon :icon="['fas', 'trash-can']" />
              </button>
              <button
                class="btn btn-secondary btn-sm btn-right"
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
        @hideEditSubsessionModal="hideEditSubsessionModal"
      />
    </template>
    <EditSubsessionForm
      index="-1"
      @hideEditSubsessionModal="hideEditSubsessionModal"
    />
  </div>
  <div class="text-center mt-4">
    <button
      class="btn btn-teal"
      id="submitCreateSession"
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
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
