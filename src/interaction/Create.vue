<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Toast from "../assets/toast.js";
import { inject } from "vue";
const config = inject("config");
import Loading from "../components/Loading.vue";
import Modal from "bootstrap/js/dist/modal";
import EditSlideForm from "./components/EditSlideForm.vue";
import Swal from "sweetalert2";

let loading = ref(true);
const saveStatus = ref({
  saved: true,
  error: false,
});

console.log("TEMP SET ACTIVE");
//interactionSession.id = "faW5Vk";
//interactionSession.pin = "554107";
interactionSession.title = "Demo";
interactionSession.name = "Dan";
interactionSession.email = "web@danleach.uk";

// Load the create page, or get the session id and pin if editing then trigger fetch details
onMounted(async () => {
  // Check if user is editing an existing session
  interactionSession.editMode =
    useRouter().currentRoute.value.name == "interaction-edit" ? true : false;

  if (interactionSession.editMode) {
    // If URL parameter is set, use it as the session ID
    interactionSession.id = useRouter().currentRoute.value.params.id;

    // If the id and pin is already set user is likely returning to create view from preview so no need to fetch existing details
    if (interactionSession.id && interactionSession.pin) {
      loading.value = false;
      return;
    }

    // If no pin is set, prompt user to enter it (id prefilled if set in URL parameter)
    const { isConfirmed } = await Swal.fire({
      title: "Enter session ID and PIN",
      html:
        "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
        interactionSession.id +
        '">' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        interactionSession.id = document
          .getElementById("swalFormId")
          .value.trim();
        interactionSession.pin = document
          .getElementById("swalFormPin")
          .value.trim();
        if (interactionSession.pin == "") {
          Swal.showValidationMessage("Please enter your PIN");
          return false;
        }
        if (interactionSession.id == "") {
          Swal.showValidationMessage("Please enter a session ID");
          return false;
        }
        return true;
      },
    });

    // If user confirmed the form, fetch the session details, else redirect to home
    if (isConfirmed) {
      history.replaceState({}, "", interactionSession.id);
      loadEditView();
    } else {
      router.push("/");
    }
  } else {
    // Creating a new session
    loading.value = false;
    if (!interactionSession.slides.length && !interactionSession.title) {
      const { isConfirmed } = await Swal.fire({
        title: "Private Beta",
        text: "LearnLoop Interaction is in private beta and can only be used by invitation. Unless you have joined the beta-testing group and had your email approved, you will not be able to create an interaction session.",
        showCancelButton: true,
        confirmButtonText: "I'm a beta-tester",
        confirmButtonColor: "#dc3545",
      });

      if (!isConfirmed) router.push("/");
    }
  }
});

const createSessionFromTemplate = async () => {
  // Get the id and pin for the session to be used as template
  const { isConfirmed } = await Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "<div class='overflow-hidden'>You will need the session ID and PIN for the session you want to use as the template. You can find these details in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      interactionSession.id = document
        .getElementById("swalFormId")
        .value.trim();
      interactionSession.pin = document
        .getElementById("swalFormPin")
        .value.trim();
      if (interactionSession.pin == "") {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }
      if (interactionSession.id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      return true;
    },
  });

  // If form not confirmed, cancel the create from template process
  if (!isConfirmed) {
    return;
  }
  // Fetch the existing details
  const fetchDetailsHostResult = await fetchDetailsHost();

  if (!fetchDetailsHostResult) {
    interactionSession.id = "";
    interactionSession.pin = "";
    router.push("/interaction/create");
    return;
  }

  // Remove the template session id, pin and feedbackID, and set the title to 'copy of' to prompt user to make an update
  interactionSession.id = "";
  interactionSession.pin = "";
  interactionSession.feedbackID = "";
  interactionSession.title = "Copy of " + interactionSession.title;

  // Create the new session using the template details
  const insertSessionResult = await insertSession();
  if (!insertSessionResult) return;

  // Update the session, adding the template slides
  await updateSession();
};

// Show info about the feedback ID field
const feedbackIdInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Feedback on your interaction session (optional)",
    html:
      `
      <div class="text-start">
        <p>You can enter the session ID of a feedback request you previously created on LearnLoop (or <a href="` +
      config.client.url +
      `/feedback/create" target="_blank">click here to do this now in a new tab</a>) and attendees will be directed to the feedback form at the end of your interaction session.<br><br> If you plan to use this interaction session multiple times you can create a new feedback request each time and update the feedback session ID your attendees should be directed to, by using the edit interaction session link in the email you'll receive once this session is created.</p>
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

// Load the edit view including fetching the details of the session to be edited
const loadEditView = async () => {
  // Fetch the existing details
  const fetchDetailsHostResult = await fetchDetailsHost();
  if (!fetchDetailsHostResult) return;

  // Remove any previous submissions bundled with the slides
  for (let slide of interactionSession.slides) {
    slide.submissions = [];
    slide.submissionsCount = 0;
  }

  loading.value = false;

  Swal.fire({
    icon: "warning",
    iconColor: "#17a2b8",
    title: "Previous responses will be deleted",
    text: "If you make changes to your interaction session, any previous responses by attendees will be deleted.",
    confirmButtonColor: "#17a2b8",
  });
};

// Fetch the details of the interaction session to be edited
const fetchDetailsHost = async () => {
  try {
    const res = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id != res.id) {
      throw new error("Response session ID does not match request session ID");
    }

    interactionSession.title = res.title;
    interactionSession.name = res.name;
    interactionSession.email = res.email;
    interactionSession.feedbackID = res.feedbackID;
    interactionSession.slides = res.slides;

    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to fetch interaction session details",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
    return false;
  }
};

let btnInsertSession = ref({
  text: "Create interaction session",
  wait: false,
});
// Insert the session on the server and get a session ID and PIN
const insertSession = async () => {
  try {
    // Check the session details are valid
    if (!sessionFormIsValid()) return false;

    // Indicate to the user insert in progress
    btnInsertSession.value.text = "Please wait...";
    btnInsertSession.value.wait = true;

    // Perform the insert
    const res = await api("interaction/insertSession", {
      title: interactionSession.title,
      feedbackId: interactionSession.feedbackID,
      name: interactionSession.name,
      email: interactionSession.email,
    });

    // Indicate to the user insert is complete
    btnInsertSession.value.text = "Create interaction session";
    btnInsertSession.value.wait = false;

    // Locally record the response
    interactionSession.id = res.id;
    interactionSession.pin = res.pin;
    interactionSession.emailOutcome = res.emailOutcome;

    // If the email was not sent, inform the user
    if (!interactionSession.emailOutcome.sendSuccess) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Confirmation email failed",
        html: `Please ensure you save your session ID and PIN shown on the next page as the confirmation email could not be sent. <br><br><span class="text-danger">Error: ${res.emailOutcome.error}</span>`,
        confirmButtonColor: "#17a2b8",
      });
    }

    return true;
  } catch (error) {
    //Indicate to the user insert failed
    btnInsertSession.value.text = "Retry creating interaction session?";
    btnInsertSession.value.wait = false;
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to create interaction session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
};

// Check the session details are valid
const sessionFormIsValid = () => {
  document
    .getElementById("createSessionSeriesForm")
    .classList.add("was-validated");
  if (
    !interactionSession.title ||
    !interactionSession.name ||
    !interactionSession.email
  )
    return false;
  return true;
};

// Update the session when any of the session details fields are changed after the session has been inserted
const sessionDetailsChangeEvent = () => {
  if (interactionSession.id) updateSession();
};

// Update the session on the server
const updateSession = async () => {
  try {
    // Indicate to the user save in progress
    saveStatus.value.saved = false;
    saveStatus.value.error = false;

    // Perform the update
    const res = await api("interaction/updateSession", interactionSession);

    // Indicate to the user save is complete, after a short delay so the save process is visible
    setTimeout(() => (saveStatus.value.saved = true), 500);

    return res.message;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Toast.fire({
      icon: "error",
      title: `Error saving session: ${error}`,
    });
    saveStatus.value.error = true;
    return false;
  }
};

let editSlideModal;
// Show the edit slide form
const showEditSlideForm = (index) => {
  editSlideModal = new Modal(
    document.getElementById("editSlideModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editSlideModal.show();
};

// Hide the edit slide form
const hideEditSlideModal = (index, slide) => {
  editSlideModal.hide();
  if (index === undefined) return; //user did not submit the form, closed using the X. Do nothing except hide the modal

  if (index == -1) {
    interactionSession.slides.push(JSON.parse(slide));
  } else {
    //otherwise use Object assign to avoid row visually jumping around as array mutated
    Object.assign(interactionSession.slides[index], JSON.parse(slide));
  }

  // Auto save the change on the server
  updateSession();
};

// Sort a given slide up or down
const sortSlide = (index, x) => {
  interactionSession.slides.splice(
    index + x,
    0,
    interactionSession.slides.splice(index, 1)[0]
  );
  updateSession();
};

// Remove the slide at the given index
const removeSlide = async (index) => {
  const { isConfirmed } = await Swal.fire({
    title: "Remove this slide?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  });

  if (isConfirmed) {
    interactionSession.slides.splice(index, 1);
    updateSession();
  }
};

// Show the host view in preview mode
const previewSession = async () => {
  try {
    const res = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });

    interactionSession.status = res;
    Swal.close();
    interactionSession.status.preview = true;
    router.push("/interaction/host/preview");
  } catch (error) {
    console.error("previewSession error:", error);
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to preview interaction session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
  }
};

// Finish editing the session by performing a final save and deactivating any preview submissions
const finishEditing = async () => {
  if (!slidesFormIsValid()) return false;

  // Perform a final save
  const updateMessage = await updateSession();

  // Deactivate any preview submissions
  api("interaction/deactivateSubmissions", {
    id: interactionSession.id,
    pin: interactionSession.pin,
    isPreview: true,
  });

  if (updateMessage) {
    // Do nothing if the save failed
    if (!interactionSession.editMode) {
      router.push("/interaction/created");
    } else {
      router.push("/");
      Toast.fire({
        icon: "success",
        title: updateMessage,
      });
    }
  }
};

// On finish editing check that the session details are valid, at least 1 slide is added
const slidesFormIsValid = () => {
  document
    .getElementById("createSessionSeriesForm")
    .classList.add("was-validated");
  if (!interactionSession.slides.length) {
    Swal.fire({
      title: "No slides added",
      text: "You need to add at least 1 slide to your session. Use the green 'Add' button.",
      icon: "error",
      iconColor: "#17a2b8",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (
    !interactionSession.title ||
    !interactionSession.name ||
    !interactionSession.email
  )
    return false;
  return true;
};
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interaction</h1>
      <div class="text-center mb-3" v-if="!interactionSession.id">
        <button
          class="btn btn-sm btn-teal"
          id="btnCreateSessionFromTemplate"
          @click="createSessionFromTemplate"
        >
          Create from template
        </button>
      </div>
      <form
        id="createSessionSeriesForm"
        class="card bg-transparent shadow p-2 mb-3 needs-validation"
        novalidate
      >
        <label for="sessionDetails" class="form-label">Session details</label>
        <div class="row">
          <div class="col-md mb-3">
            <div class="form-floating">
              <input
                type="text"
                v-model="interactionSession.title"
                class="form-control"
                id="title"
                placeholder=""
                name="title"
                autocomplete="off"
                @change="sessionDetailsChangeEvent"
                required
              />
              <label for="title">Session title</label>
              <div class="invalid-feedback">Please fill out this field.</div>
            </div>
          </div>
          <div class="col-md mb-3">
            <div class="input-group">
              <div class="form-floating">
                <input
                  type="text"
                  v-model="interactionSession.feedbackID"
                  class="form-control"
                  placeholder=""
                  id="feedbackID"
                  name="title"
                  autocomplete="off"
                  @change="sessionDetailsChangeEvent"
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
          <div class="col-md mb-3">
            <div class="form-floating">
              <input
                type="text"
                v-model="interactionSession.name"
                class="form-control"
                id="name"
                placeholder=""
                name="name"
                autocomplete="off"
                @change="sessionDetailsChangeEvent"
                required
              />
              <label for="name">Facilitator name</label>
              <div class="invalid-feedback">Please fill out this field.</div>
            </div>
          </div>
          <div class="col-md mb-3">
            <div class="form-floating">
              <input
                type="email"
                v-model="interactionSession.email"
                class="form-control"
                id="email"
                placeholder=""
                name="email"
                autocomplete="off"
                @change="sessionDetailsChangeEvent"
                required
              />
              <label for="email">Facilitator email</label>
              <div class="invalid-feedback">Please fill out this field.</div>
            </div>
          </div>
        </div>
      </form>
      <div class="text-center mb-3" v-if="!interactionSession.id">
        <button
          class="btn btn-lg btn-teal"
          id="btnInsertSession"
          @click="insertSession"
          :disabled="btnInsertSession.wait"
        >
          <span
            v-if="btnInsertSession.wait"
            class="spinner-border spinner-border-sm me-2"
          ></span
          >{{ btnInsertSession.text }}
        </button>
      </div>
      <div v-else>
        <div class="card bg-transparent shadow p-2 mb-3">
          <label for="slidesTable" class="form-label">Slides</label>
          <table class="table" id="slidesTable">
            <thead>
              <tr>
                <th class="bg-transparent p-0 ps-2"></th>
                <th class="bg-transparent p-0 ps-2">Question/heading</th>
                <th class="bg-transparent p-0 ps-2">Slide type</th>
                <th class="bg-transparent p-0 ps-2">
                  <button
                    class="btn btn-teal btn-sm btn-right"
                    id="btnAddSlide"
                    @click.prevent="showEditSlideForm(-1)"
                  >
                    Add <i class="fas fa-plus"></i>
                  </button>
                </th>
              </tr>
            </thead>
            <TransitionGroup name="list" tag="tbody">
              <template
                v-for="(slide, index) in interactionSession.slides"
                :key="slide"
              >
                <tr>
                  <td
                    class="bg-transparent p-0 ps-2"
                    v-if="slide.type != 'waitingRoom'"
                  >
                    <button
                      v-if="index != 0"
                      class="btn btn-default btn-sm p-0"
                      id="btnSortUp"
                      @click="sortSlide(index, -1)"
                    >
                      <font-awesome-icon
                        :icon="['fas', 'chevron-up']"
                      /></button
                    ><br />
                    <button
                      v-if="index != interactionSession.slides.length - 1"
                      class="btn btn-default btn-sm p-0"
                      id="btnSortDown"
                      @click="sortSlide(index, 1)"
                    >
                      <font-awesome-icon :icon="['fas', 'chevron-down']" />
                    </button>
                  </td>
                  <td
                    class="bg-transparent prompt-cell"
                    v-if="slide.type != 'waitingRoom'"
                  >
                    {{ slide.prompt }}
                  </td>
                  <td class="bg-transparent" v-if="slide.type != 'waitingRoom'">
                    {{
                      config.interaction.create.slides.types[slide.type].name
                    }}
                  </td>
                  <td class="bg-transparent" v-if="slide.type != 'waitingRoom'">
                    <button
                      class="btn btn-danger btn-sm btn-right ms-4 mb-2"
                      id="btnRemoveSlide"
                      @click="removeSlide(index)"
                    >
                      <font-awesome-icon :icon="['fas', 'trash-can']" />
                    </button>
                    <button
                      class="btn btn-teal btn-sm btn-right"
                      id="btnEditSlide"
                      @click="showEditSlideForm(index)"
                    >
                      <font-awesome-icon :icon="['fas', 'edit']" />
                    </button>
                  </td>
                </tr>
              </template>
            </TransitionGroup>
          </table>
          <template v-for="(slide, index) in interactionSession.slides">
            <EditSlideForm
              :index="index"
              @hideEditSlideModal="hideEditSlideModal"
            />
          </template>
          <EditSlideForm index="-1" @hideEditSlideModal="hideEditSlideModal" />
        </div>
        <div class="d-flex flex-wrap justify-content-center mb-3">
          <button
            class="btn btn-lg btn btn-outline-success m-2"
            id="saved"
            v-if="saveStatus.saved"
          >
            Saved <font-awesome-icon :icon="['fas', 'check']" />
          </button>
          <button
            class="btn btn-lg btn btn-outline-danger m-2"
            id="saveError"
            v-else-if="saveStatus.error"
          >
            Not saved
            <font-awesome-icon :icon="['fas', 'triangle-exclamation']" />
          </button>
          <button
            class="btn btn-lg btn btn-outline-teal m-2"
            id="saving"
            v-else
          >
            Saving <span class="spinner-border spinner-border-sm me-2"></span>
          </button>
          <button
            class="btn btn-lg btn-teal m-2"
            id="previewSession"
            @click="previewSession"
          >
            Preview
          </button>
          <button
            class="btn btn-lg btn-teal m-2"
            id="btnFinishEditing"
            @click="finishEditing"
          >
            Finish
          </button>
        </div>
      </div>
    </div>
  </Transition>
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
.prompt-cell {
  max-width: 50vw;
}
</style>
