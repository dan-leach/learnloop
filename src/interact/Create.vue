<script setup>
import { ref } from 'vue';
import { interactSession } from '../data/interactSession.js';
import { api } from '../data/api.js';
import { config } from '../data/config.js';
import Modal from 'bootstrap/js/dist/modal';
import EditInteractionForm from './components/EditInteractionForm.vue';
import Swal from 'sweetalert2';

let editInteractionModal;
const showEditInteractionForm = (index) => {
  editInteractionModal = new Modal(
    document.getElementById('editInteractionModal' + index),
    {
      backdrop: 'static',
      keyboard: false,
      focus: true,
    }
  );
  editInteractionModal.show();
};
const hideEditInteractionModal = (index, interaction) => {
  if (!index) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    interactSession.interactions.push(JSON.parse(interaction));
  } else {
    interactSession.interactions[index] = JSON.parse(interaction);
  }
  editInteractionModal.hide();
};

const sortInteraction = (index, x) =>
  interactSession.interactions.splice(
    index + x,
    0,
    interactSession.interactions.splice(index, 1)[0]
  );
const removeInteraction = (index) => {
  Swal.fire({
    title: 'Remove this interaction?',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
  }).then((result) => {
    if (result.isConfirmed) interactSession.interactions.splice(index, 1);
  });
};

let btnSubmit = ref({
  text: 'Create interact session',
  wait: false,
});
const formIsValid = () => {
  document
    .getElementById('createSessionSeriesForm')
    .classList.add('was-validated');
  if (!interactSession.interactions.length) {
    Swal.fire({
      title: 'No interactions added',
      text: "You need to add at least 1 interaction to your session. Use the green 'Add' button.",
      icon: 'error',
      confirmButtonColor: '#17a2b8',
    });
    return false;
  }
  if (
    !interactSession.title ||
    !interactSession.date ||
    !interactSession.name ||
    !interactSession.email
  )
    return false;
  return true;
};
const submit = () => {
  if (!formIsValid()) return false;
  btnSubmit.value.text = 'Please wait...';
  btnSubmit.value.wait = true;
  api('interact', 'insertSession', null, null, interactSession).then(
    function (res) {
      btnSubmit.value.text = 'Create interact session';
      btnSubmit.value.wait = true;
      interactSession.id = res.id;
      interactSession.pin = res.pin;
      router.push('/interact/created');
    },
    function (error) {
      btnSubmit.value.text = 'Retry creating interact session?';
      btnSubmit.value.wait = false;
      Swal.fire({
        title: 'Error creating interact session',
        text: error,
        icon: 'error',
        confirmButtonColor: '#17a2b8',
      });
    }
  );
};
</script>

<template>
  <h1 class="text-center display-4">Interact</h1>
  <strong
    >Please provide details for the interact session you're creating.</strong
  >
  <form id="createSessionSeriesForm" class="needs-validation" novalidate>
    <div>
      <label for="title">Session series title:</label>
      <input
        type="text"
        v-model="interactSession.title"
        class="form-control"
        id="title"
        placeholder="Title for the interact session..."
        name="title"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <div class="mt-4">
      <label for="date">Session series date:</label>
      <input
        type="date"
        v-model="interactSession.date"
        class="form-control"
        id="date"
        placeholder="dd/mm/yyyy"
        name="date"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please select a date.</div>
    </div>
    <div class="mt-4">
      <label for="name">Facilitator name:</label>
      <input
        type="text"
        v-model="interactSession.name"
        class="form-control"
        id="name"
        placeholder="Facilitator of the interact session..."
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
        v-model="interactSession.email"
        class="form-control"
        id="email"
        placeholder="Email to send session details to..."
        name="email"
        autocomplete="off"
        required
      />
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
  </form>

  <h2 class="mt-4">Interactions</h2>
  <table class="table" id="interactionsTable">
    <thead>
      <tr>
        <th></th>
        <th>Prompt</th>
        <th>Type</th>
        <th>
          <button
            class="btn btn-success btn-sm btn-right"
            id="btnAddInteraction"
            @click.prevent="showEditInteractionForm(-1)"
          >
            Add <i class="fas fa-plus"></i>
          </button>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(interaction, index) in interactSession.interactions">
        <td class="p-0 ps-2">
          <button
            v-if="index != 0"
            class="btn btn-default btn-sm p-0"
            id="btnSortUp"
            @click="sortInteraction(index, -1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
          ><br />
          <button
            v-if="index != interactSession.interactions.length - 1"
            class="btn btn-default btn-sm p-0"
            id="btnSortDown"
            @click="sortInteraction(index, 1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-down']" />
          </button>
        </td>
        <td>{{ interaction.prompt }}</td>
        <td>
          {{ config.interact.create.interactions.types[interaction.type].name }}
        </td>
        <td>
          <button
            class="btn btn-danger btn-sm btn-right ms-4"
            id="btnRemoveInteraction"
            @click="removeInteraction(index)"
          >
            <font-awesome-icon :icon="['fas', 'trash-can']" />
          </button>
          <button
            class="btn btn-secondary btn-sm btn-right"
            id="btnEditInteraction"
            @click="showEditInteractionForm(index)"
          >
            <font-awesome-icon :icon="['fas', 'edit']" />
          </button>
        </td>
      </tr>
    </tbody>
  </table>
  <EditInteractionForm
    v-for="(interaction, index) in interactSession.interactions"
    :index="index"
    @hideEditInteractionModal="hideEditInteractionModal"
  />
  <EditInteractionForm
    index="-1"
    @hideEditInteractionModal="hideEditInteractionModal"
  />
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

<style></style>
