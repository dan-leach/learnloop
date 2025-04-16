<script setup>
import { onMounted } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import router from "../router/index.js";
import Swal from "sweetalert2";

const toggleCertificate = () => {
  feedbackSession.certificate = !feedbackSession.certificate;
  feedbackSession.attendance = feedbackSession.certificate;
};
const toggleAttendance = () => {
  if (!feedbackSession.attendance && !feedbackSession.certificate) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      text: "You must enable the 'Certificate of Attendance' option to be able to use the 'Register of Attendance' option.",
      confirmButtonColor: "#17a2b8",
    });
  } else {
    feedbackSession.attendance = !feedbackSession.attendance;
  }
};

const back = () => {
  router.push("/feedback/create/type");
};

const formIsValid = () => {
  document.getElementById("createDetailsForm").classList.add("was-validated");
  if (!feedbackSession.title || !feedbackSession.name) return false;
  if (!feedbackSession.multipleDates && !feedbackSession.date) return false;
  return true;
};
const next = () => {
  if (!formIsValid()) return false;
  if (feedbackSession.isSeries) {
    router.push(
      `/feedback/${feedbackSession.isEdit ? "edit" : "create"}/sessions${
        feedbackSession.isEdit ? "/" + feedbackSession.id : ""
      }`
    );
  } else {
    router.push(
      `/feedback/${feedbackSession.isEdit ? "edit" : "create"}/questions${
        feedbackSession.isEdit ? "/" + feedbackSession.id : ""
      }`
    );
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
  <form
    id="createDetailsForm"
    class="container needs-validation my-4"
    novalidate
  >
    <h1 class="text-center display-4">Feedback</h1>
    <div class="text-center">
      <p v-if="feedbackSession.isEdit" class="form-label ms-2">
        Editing feedback session
        <span class="id-box">{{ feedbackSession.id }}</span>
      </p>
      <p v-else>Add some details about your teaching event</p>
    </div>
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
        Please provide a title for your teaching event.
      </div>
      <div class="collapse form-text mx-1" id="titleHelp">
        <span
          >Choose a title so attendees know what they're providing feedback
          for.</span
        ><br />
        <span>
          E.g. <i>'ST4-8 teaching day: Renal focus'</i> or
          <i>'How to manage paediatric DKA'</i>.</span
        ><br />
        <span>
          The title will appear on the certificate of attendance (if enabled).
        </span>
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
          Please provide a date, or select the multiple dates option.
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
        <span>Provide a date on which your teaching event is taking place.</span
        ><br />
        <span>
          The date will appear on the certificate of attendance (if
          enabled).</span
        ><br />
        <span>
          Select multiple dates to collate feedback for a session delivered
          multiple times with the same form.
        </span>
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
        Please provide the name of the facilitator for this teaching event.
      </div>
      <div class="collapse form-text mx-1" id="nameHelp">
        <span>
          Provide the name of the person or team responsible for the
          teaching.</span
        ><br />
        <span>E.g. 'Dr Smith', or 'ST4-8 teaching reps'.</span><br />
        <span>
          The facilitator name will appear on the feedback form and the
          certificate of attendance (if enabled).
        </span>
      </div>
    </div>

    <!--Certificate-->
    <div class="mb-3">
      <div class="d-flex flex-wrap align-items-center justify-content-start">
        <div class="d-flex align-items-center justify-content-start">
          <button
            class="btn btn-settings btn-teal btn-sm"
            id="toggleCertificate"
            @click.prevent="toggleCertificate"
          >
            {{ feedbackSession.certificate ? "Disable" : "Enable" }}
            certificate
          </button>
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            size="xl"
            class="mx-2"
            style="color: black"
            v-focus-collapse="'certificateHelp'"
          />
        </div>
        <div>
          <span v-if="feedbackSession.certificate">
            Attendees receive certificate after giving feedback
            <font-awesome-icon
              :icon="['fas', 'check']"
              size="2xl"
              style="color: green"
            />
          </span>
          <span v-else>
            Attendees will not receive a certificate
            <font-awesome-icon
              :icon="['fas', 'times']"
              size="2xl"
              style="color: red"
            />
          </span>
        </div>
      </div>
      <div class="collapse form-text mx-1" id="certificateHelp">
        <span
          >Providing a certificate of attendance encourages attendees to provide
          feedback.</span
        >
      </div>
    </div>

    <!--Attendance-->
    <div class="mb-3">
      <div class="d-flex flex-wrap align-items-center justify-content-start">
        <div class="d-flex align-items-center justify-content-start">
          <button
            class="btn btn-settings btn-teal btn-sm"
            id="toggleAttendance"
            @click.prevent="toggleAttendance"
          >
            {{ feedbackSession.attendance ? "Disable" : "Enable" }}
            register
          </button>
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            size="xl"
            class="mx-2"
            style="color: black"
            v-focus-collapse="'attendanceHelp'"
          />
        </div>
        <div>
          <span v-if="feedbackSession.attendance">
            Register of attendance will be kept
            <font-awesome-icon
              :icon="['fas', 'check']"
              size="2xl"
              style="color: green"
            />
          </span>
          <span v-if="!feedbackSession.attendance">
            Register of attendance won't be kept
            <font-awesome-icon
              :icon="['fas', 'times']"
              size="2xl"
              style="color: red"
            />
          </span>
        </div>
      </div>
      <div class="collapse form-text mx-1" id="attendanceHelp">
        <span
          >The attendance report shows the name and organisation of each
          attendee who downloads a certificate of attendance.</span
        ><br />
        <span>The attendee details are not linked to their feedback.</span>
      </div>
    </div>
  </form>
  <!--back/next button-->
  <div class="d-flex justify-content-evenly mb-3">
    <button
      class="btn btn-secondary btn-lg me-2 mb-2"
      id="back"
      @click="back"
      v-if="!feedbackSession.isEdit"
    >
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
.id-box {
  padding: 2px;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 10px;
  background-color: #17a2b8;
  color: white;
  letter-spacing: 3px;
}
</style>
