<script setup>
import { ref } from "vue";
import router from "../router";
import { interactSession } from "../data/interactSession.js";
import { api } from "../data/api.js";
import { config } from "../data/config.js";
import Modal from "bootstrap/js/dist/modal";
import EditInteractionForm from "./components/EditInteractionForm.vue";
import Swal from "sweetalert2";

let editInteractionModal;
const showEditInteractionForm = (index) => {
  editInteractionModal = new Modal(
    document.getElementById("editInteractionModal" + index),
    {
      backdrop: "static",
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
    title: "Remove this interaction?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) interactSession.interactions.splice(index, 1);
  });
};

const feedbackIdInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Feedback on your interact session (optional)",
    html:
      `
      <div class="text-start">
        <p>You can enter the session ID of a feedback request you previously created on LearnLoop (or <a href="` +
      config.client.url +
      `/feedback/create" target="_blank">click here to do this now in a new tab</a>) and attendees will be directed to the feedback form at the end of your interact session.<br><br> If you plan to use this interact session multiple times you can create a new feedback request each time and update the feedback session ID your attendees should be directed to, by using the edit interact session link in the email you'll receive once this session is created.</p>
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let btnSubmit = ref({
  text: "Create interact session",
  wait: false,
});
const formIsValid = () => {
  document
    .getElementById("createSessionSeriesForm")
    .classList.add("was-validated");
  if (!interactSession.interactions.length) {
    Swal.fire({
      title: "No interactions added",
      text: "You need to add at least 1 interaction to your session. Use the green 'Add' button.",
      icon: "error",
      iconColor: "#17a2b8",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (!interactSession.title || !interactSession.name || !interactSession.email)
    return false;
  return true;
};
const submit = () => {
  if (!formIsValid()) return false;
  btnSubmit.value.text = "Please wait...";
  btnSubmit.value.wait = true;
  api("interact", "insertSession", null, null, interactSession).then(
    function (res) {
      btnSubmit.value.text = "Create interact session";
      btnSubmit.value.wait = false;
      interactSession.id = res.id;
      interactSession.pin = res.pin;
      router.push("/interact/created");
    },
    function (error) {
      btnSubmit.value.text = "Retry creating interact session?";
      btnSubmit.value.wait = false;
      Swal.fire({
        title: "Error creating interact session",
        text: error,
        icon: "error",
        iconColor: "#17a2b8",
        confirmButtonColor: "#17a2b8",
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
    <div class="row">
      <div class="col-md mt-2">
        <div class="form-floating">
          <input
            type="text"
            v-model="interactSession.title"
            class="form-control"
            id="title"
            placeholder=""
            name="title"
            autocomplete="off"
            required
          />
          <label for="title">Session title</label>
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
      </div>
      <div class="col-md mt-2">
        <div class="input-group">
          <div class="form-floating">
            <input
              type="text"
              v-model="interactSession.feedbackID"
              class="form-control"
              placeholder=""
              id="feedbackID"
              name="title"
              autocomplete="off"
            />
            <label for="feedbackID">Feedback session ID (optional) </label>
          </div>
          <span class="input-group-text"
            ><font-awesome-icon
              :icon="['fas', 'question-circle']"
              size="lg"
              style="color: black"
              @click="feedbackIdInfo"
          /></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md mt-2">
        <div class="form-floating">
          <input
            type="text"
            v-model="interactSession.name"
            class="form-control"
            id="name"
            placeholder=""
            name="name"
            autocomplete="off"
            required
          />
          <label for="name">Facilitator name</label>
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
      </div>
      <div class="col-md mt-2">
        <div class="form-floating">
          <input
            type="email"
            v-model="interactSession.email"
            class="form-control"
            id="email"
            placeholder=""
            name="email"
            autocomplete="off"
            required
          />
          <label for="email">Facilitator email</label>
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
      </div>
    </div>
  </form>
  <div class="mt-4">
    <label for="interactionsTable" class="form-label">Interactions:</label>
    <table class="table" id="interactionsTable">
      <thead>
        <tr>
          <th></th>
          <th>Prompt</th>
          <th>Type</th>
          <th>
            <button
              class="btn btn-teal btn-sm btn-right"
              id="btnAddInteraction"
              @click.prevent="showEditInteractionForm(-1)"
            >
              Add <i class="fas fa-plus"></i>
            </button>
          </th>
        </tr>
      </thead>
      <TransitionGroup name="list" tag="tbody">
        <template
          v-for="(interaction, index) in interactSession.interactions"
          :key="interaction"
        >
          <tr>
            <td class="p-0 ps-2" v-if="interaction.type != 'waitingRoom'">
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
            <td v-if="interaction.type != 'waitingRoom'">
              {{ interaction.prompt }}
            </td>
            <td v-if="interaction.type != 'waitingRoom'">
              {{
                config.interact.create.interactions.types[interaction.type].name
              }}
            </td>
            <td v-if="interaction.type != 'waitingRoom'">
              <button
                class="btn btn-danger btn-sm btn-right ms-4"
                id="btnRemoveInteraction"
                @click="removeInteraction(index)"
              >
                <font-awesome-icon :icon="['fas', 'trash-can']" />
              </button>
              <button
                class="btn btn-teal btn-sm btn-right"
                id="btnEditInteraction"
                @click="showEditInteractionForm(index)"
              >
                <font-awesome-icon :icon="['fas', 'edit']" />
              </button>
            </td>
          </tr>
        </template>
      </TransitionGroup>
    </table>
    <template v-for="(interaction, index) in interactSession.interactions">
      <EditInteractionForm
        v-if="interaction.type != 'waitingRoom'"
        :index="index"
        @hideEditInteractionModal="hideEditInteractionModal"
      />
    </template>
    <EditInteractionForm
      index="-1"
      @hideEditInteractionModal="hideEditInteractionModal"
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
.form-label {
  font-size: 1.3rem;
}
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
