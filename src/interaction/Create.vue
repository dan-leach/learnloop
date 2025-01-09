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

const sessionDetailsChangeEvent = () => {
  if (interactionSession.id) updateSession();
};
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

let editSlideModal;
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
const hideEditSlideModal = (index, slide) => {
  editSlideModal.hide();
  if (index === undefined) return; //user did not submit the form, closed using the X. Do nothing except hide the modal

  if (index == -1) {
    interactionSession.slides.push(JSON.parse(slide));
  } else {
    interactionSession.slides[index] = JSON.parse(slide);
  }

  updateSession();
};

const sortSlide = (index, x) => {
  interactionSession.slides.splice(
    index + x,
    0,
    interactionSession.slides.splice(index, 1)[0]
  );
  updateSession();
};
const removeSlide = (index) => {
  Swal.fire({
    title: "Remove this slide?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) {
      interactionSession.slides.splice(index, 1);
      updateSession();
    }
  });
};

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

let btnInsertSession = ref({
  text: "Create interaction session",
  wait: false,
});
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
const insertSession = () => {
  if (!sessionFormIsValid()) return false;
  btnInsertSession.value.text = "Please wait...";
  btnInsertSession.value.wait = true;
  api("interaction/insertSession", {
    title: interactionSession.title,
    feedbackId: interactionSession.feedbackID,
    name: interactionSession.name,
    email: interactionSession.email,
  }).then(
    function (res) {
      btnInsertSession.value.text = "Create interaction session";
      btnInsertSession.value.wait = false;
      interactionSession.id = res.id;
      interactionSession.pin = res.pin;
      interactionSession.emailOutcome = res.emailOutcome;
      if (!interactionSession.emailOutcome.sendSuccess) {
        Swal.fire({
          icon: "error",
          iconColor: "#17a2b8",
          title: "Confirmation email failed",
          html: `Please ensure you save your session ID and PIN shown on the next page as the confirmation email could not be sent. <br><br><span class="text-danger">Error: ${res.emailOutcome.error}</span>`,
          confirmButtonColor: "#17a2b8",
        });
      }
    },
    function (error) {
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
    }
  );
};

interactionSession.editMode =
  useRouter().currentRoute.value.name == "interaction-edit" ? true : false;
let loading = ref(interactionSession.editMode ? true : false);
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
const updateSession = async () => {
  saveStatus.value.saved = false;
  saveStatus.value.error = false;
  return api("interaction/updateSession", interactionSession).then(
    function (res) {
      saveStatus.value.saved = true;
      return res.message;
    },
    function (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Toast.fire({
        icon: "error",
        title: `Error saving session: ${error}`,
      });
      saveStatus.value.error = true;
      return false;
    }
  );
};
const finishEditing = async () => {
  if (!slidesFormIsValid()) return false;
  const updateMessage = await updateSession();
  if (updateMessage) {
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

const previewSession = () => {
  router.push("/interaction/host/preview");
};

const fetchDetailsHost = () => {
  api("interaction/fetchDetailsHost", {
    id: interactionSession.id,
    pin: interactionSession.pin,
  }).then(
    function (res) {
      if (interactionSession.id != res.id) {
        console.error(
          "interactionSession.id != res.id",
          interactionSession.id,
          res.id
        );
        return;
      }
      interactionSession.title = res.title;
      interactionSession.name = res.name;
      interactionSession.email = res.email;
      interactionSession.feedbackID = res.feedbackID;
      interactionSession.slides = res.slides;
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
    },
    function (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to edit interaction session",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  if (interactionSession.editMode) {
    interactionSession.id = useRouter().currentRoute.value.params.id;
    if (interactionSession.pin) {
      loading.value = false;
    } else {
      Swal.fire({
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
          interactionSession.id = document.getElementById("swalFormId").value;
          interactionSession.pin = document.getElementById("swalFormPin").value;
          if (interactionSession.pin == "")
            Swal.showValidationMessage("Please enter your PIN");
          if (interactionSession.id == "")
            Swal.showValidationMessage("Please enter a session ID");
        },
      }).then((result) => {
        if (result.isConfirmed) {
          history.replaceState({}, "", interactionSession.id);
          fetchDetailsHost();
        } else {
          router.push("/");
        }
      });
    }
  } else if (!interactionSession.slides.length && !interactionSession.title) {
    Swal.fire({
      title: "Private Beta",
      text: "LearnLoop Interaction is in private beta and can only be used by invitation. Unless you have joined the beta-testing group and had your email approved, you will not be able to create an interaction session.",
      showCancelButton: true,
      confirmButtonText: "I'm a beta-tester",
      confirmButtonColor: "#dc3545",
    }).then((result) => {
      if (!result.isConfirmed) router.push("/");
    });
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interaction</h1>
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
