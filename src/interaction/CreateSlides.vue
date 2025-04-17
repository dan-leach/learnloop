<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Toast from "../assets/toast.js";
import { inject } from "vue";
const config = inject("config");
import Modal from "bootstrap/js/dist/modal";
import EditSlideForm from "./components/EditSlideForm.vue";
import Swal from "sweetalert2";

if (
  !interactionSession.isNew &&
  !interactionSession.useTemplate &&
  !interactionSession.isEdit
) {
  if (useRouter().currentRoute.value.path.includes("/edit/")) {
    router.push(
      "/interaction/edit/" + useRouter().currentRoute.value.params.id
    );
  } else {
    router.push("/interaction/create/type");
  }
}

const saveStatus = ref({
  saved: true,
  error: false,
});

const back = () => {
  router.push(
    `/interaction/${
      interactionSession.isEdit
        ? "edit/details/" + interactionSession.id
        : "create/login"
    }`
  );
};

let btnNext = ref({
  text: "Continue",
  wait: false,
});

// Update the session on the server
const updateSession = async () => {
  try {
    // Indicate to the user save in progress
    saveStatus.value.saved = false;
    saveStatus.value.error = false;
    btnNext.value.wait = true;

    // Perform the update
    const res = await api("interaction/updateSession", interactionSession);

    // Indicate to the user save is complete, after a short delay so the save process is visible
    setTimeout(() => {
      saveStatus.value.saved = true;
      btnNext.value.wait = false;
    }, 500);

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
const preview = async () => {
  try {
    const res = await api("interaction/fetchStatus", {
      id: interactionSession.id,
    });

    interactionSession.status = res;
    Swal.close();
    interactionSession.status.preview = true;
    Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Refresh connected devices",
      text: "If you previously connected any devices to this session please refresh the page on them now. This ensures the connected devices have your latest changes.",
      confirmButtonColor: "#17a2b8",
    });
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
const next = async () => {
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
    if (!interactionSession.isEdit) {
      router.push("/interaction/create/complete");
    } else {
      router.push("/");
      Toast.fire({
        icon: "success",
        title: updateMessage,
      });
    }
  }
};

const showWarning = ref(false);
// On finish editing check that at least 1 slide is added
const slidesFormIsValid = () => {
  if (!interactionSession.slides.length) {
    showWarning.value = true;
    return false;
  }
  return true;
};
</script>

<template>
  <div>
    <h1 class="text-center display-4">Interaction</h1>
    <div class="text-center">
      <p v-if="interactionSession.isEdit" class="form-label ms-2">
        Editing interaction session
        <span class="id-box">{{ interactionSession.id }}</span>
      </p>
      <p v-else>Add slides to your interaction session.</p>
    </div>

    <div class="alert alert-teal" alert-dismissible fade show role="alert">
      <div class="d-flex justify-content-between">
        <h4 class="alert-heading">Slides</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
      Create your interaction session by adding slides. Each slide can have
      static content and/or an interaction for attendees to respond to. Each
      time you add, remove or reorder a slide your session will auto-save. You
      can return to edit your slides at any point, including after using your
      interaction session, using your session ID and PIN.
    </div>

    <div class="p-2 mb-3">
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
                  <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
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
                {{ config.interaction.create.slides.types[slide.type].name }}
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
      <div
        class="text-danger"
        v-if="showWarning && !interactionSession.slides.length"
      >
        You must add at least one slide.
      </div>
    </div>
    <div class="d-flex flex-wrap justify-content-center mb-3">
      <button class="btn btn-secondary btn-lg m-2" id="btnBack" @click="back">
        Back
      </button>
      <button
        class="btn btn-lg btn-outline-success m-2"
        id="btnSaved"
        v-if="saveStatus.saved"
      >
        Saved <font-awesome-icon :icon="['fas', 'check']" />
      </button>
      <button
        class="btn btn-lg btn-outline-danger m-2"
        id="btnSaveError"
        v-else-if="saveStatus.error"
      >
        Not saved
        <font-awesome-icon :icon="['fas', 'triangle-exclamation']" />
      </button>
      <button class="btn btn-lg btn-outline-teal m-2" id="btnSaving" v-else>
        Saving <span class="spinner-border spinner-border-sm me-2"></span>
      </button>
      <button class="btn btn-lg btn-teal m-2" id="btnPreview" @click="preview">
        Preview
      </button>
      <button
        class="btn btn-lg btn-teal m-2"
        id="btnNext"
        @click="next"
        :disabled="btnNext.wait"
      >
        {{ btnNext.text }}
      </button>
    </div>
  </div>
</template>

<style>
.id-box {
  padding: 2px;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 10px;
  background-color: #17a2b8;
  color: white;
  letter-spacing: 3px;
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
