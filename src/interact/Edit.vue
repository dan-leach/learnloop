<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactSession } from "../data/interactSession.js";
import { api } from "../data/api.js";
import { config } from "../data/config.js";
import Loading from "../components/Loading.vue";
import Modal from "bootstrap/js/dist/modal";
import EditInteractionForm from "./components/EditInteractionForm.vue";
import Swal from "sweetalert2";

const loading = ref(true);

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
    title: "Feedback on your interact session",
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
  text: "Update interact session",
  wait: false,
});
const formIsValid = () => {
  document
    .getElementById("editSessionSeriesForm")
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
  api(
    "interact",
    "updateSession",
    interactSession.id,
    interactSession.pin,
    interactSession
  ).then(
    function (res) {
      btnSubmit.value.text = "Update interact session";
      btnSubmit.value.wait = false;
      Swal.fire({
        title: "Interact session updated",
        icon: "success",
        iconColor: "#17a2b8",
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    },
    function (error) {
      btnSubmit.value.text = "Retry updating interact session?";
      btnSubmit.value.wait = false;
      Swal.fire({
        title: "Error updating interact session",
        text: error,
        icon: "error",
        iconColor: "#17a2b8",
        confirmButtonColor: "#17a2b8",
      });
    }
  );
};

const fetchDetailsHost = () => {
  api(
    "interact",
    "fetchDetailsHost",
    interactSession.id,
    interactSession.pin,
    null
  ).then(
    function (res) {
      if (interactSession.id != res.id) {
        console.error(
          "interactSession.id != res.id",
          interactSession.id,
          response.id
        );
        return;
      }
      interactSession.title = res.title;
      interactSession.name = res.name;
      interactSession.email = res.email;
      interactSession.feedbackID = res.feedbackID;
      interactSession.interactions = res.interactions;
      for (let interaction of interactSession.interactions) {
        interaction.submissions = [];
        interaction.submissionsCount = 0;
      }
      loading.value = false;
      Swal.fire({
        icon: "warning",
        iconColor: "#17a2b8",
        title: "Previous responses will be deleted",
        text: "If you make changes to your interact session, any previous responses by attendees will be deleted.",
        confirmButtonColor: "#17a2b8",
      });
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to edit interact session",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.params.id;
  Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      interactSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      interactSession.id = document.getElementById("swalFormId").value;
      interactSession.pin = document.getElementById("swalFormPin").value;
      if (interactSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (interactSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  }).then((result) => {
    if (result.isConfirmed) {
      history.replaceState({}, "", interactSession.id);
      fetchDetailsHost();
    } else {
      router.push("/");
    }
  });
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interact</h1>
      <strong>Please make changes to your interact session below.</strong>
      <form id="editSessionSeriesForm" class="needs-validation" novalidate>
        <div>
          <label for="title" class="form-label">Session title:</label>
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
          <label for="name" class="form-label">Facilitator name:</label>
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
          <label for="email" class="form-label">Facilitator email:</label>
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
        <div class="mt-4">
          <label for="feedbackID" class="form-label"
            >Feedback session ID: (optional)
            <font-awesome-icon
              :icon="['fas', 'question-circle']"
              size="xl"
              style="color: black"
              @click="feedbackIdInfo"
          /></label>
          <input
            type="text"
            v-model="interactSession.feedbackID"
            class="form-control"
            id="feedbackID"
            placeholder="Session ID for a feedback request you've already created..."
            name="title"
            autocomplete="off"
          />
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
                  class="btn btn-success btn-sm btn-right"
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
                    config.interact.create.interactions.types[interaction.type]
                      .name
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
                    class="btn btn-secondary btn-sm btn-right"
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
          id="submitUpdateSession"
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
    </div>
  </Transition>
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
