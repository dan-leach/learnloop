<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import { config } from "../data/config.js";
import Modal from "bootstrap/js/dist/modal";
import Loading from "../components/Loading.vue";
import EditSubsessionForm from "./components/EditSubsessionForm.vue";
import EditQuestionForm from "./components/EditQuestionForm.vue";
import Swal from "sweetalert2";

let isSeries = ref(false);

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
    title: "Remove this subsession?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) feedbackSession.subsessions.splice(index, 1);
  });
};

let hasQuestions = ref(false);
const questionsInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Custom questions (Optional)",
    html: "<div class=\"text-start\">The default feedback form asks attendees to provide some free-text positive feedback, free-text constructive criticism and an overall score (using a slider, out of 100). If that doesn't cover everything you want to ask, you can add additional questions.<br><br>If you're creating a feedback form for a session series the custom questions only relate to the overall feedback and aren't asked for all the subsessions.</div>",
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let editQuestionModal;
const showEditQuestionForm = (index) => {
  editQuestionModal = new Modal(
    document.getElementById("editQuestionModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editQuestionModal.show();
};
const hideEditQuestionModal = (index, question) => {
  if (!index) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.questions.push(JSON.parse(question));
  } else {
    feedbackSession.questions[index] = JSON.parse(question);
  }
  editQuestionModal.hide();
};

const sortQuestion = (index, x) =>
  feedbackSession.questions.splice(
    index + x,
    0,
    feedbackSession.questions.splice(index, 1)[0]
  );
const removeQuestion = (index) => {
  Swal.fire({
    title: "Remove this question?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) feedbackSession.questions.splice(index, 1);
  });
};

const toggleCertificate = () => {
  feedbackSession.certificate = !feedbackSession.certificate;
  feedbackSession.attendance = feedbackSession.certificate;
};
const certificateInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Certificate of attendance (Optional)",
    html: '<div class="text-start">By default attendees of your session will be able to download a certificate of attendance after completing the feedback form. This is a good way of encouraging attendees to provide feedback.<br><br> You can disable the certificate if you prefer. Attendees will still be able to provide feedback but will not be given the option to download a certificate afterwards.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const notificationsInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Email notifications when feedback submitted (Optional)",
    html: '<div class="text-start">By default you will receive an email when feedback for your session is submitted. To avoid overloading your inbox, no further notifications are sent within 2 hours.<br><br>If you disable this you can still manually check using your session ID and PIN which are emailed to you once your session is created. You can also disable further notifications later, using a link in the notification email itself.<br><br>If you provide an email for them, facilitators of each session in this series will receive an email notifying them that the feedback request has been set up. They will also receive email notifications when feedback for their session is submitted, but they can disable these if preferred using a link in the notification email itself.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
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
const attendanceInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Register of attendance (Optional)",
    html: '<div class="text-start">By default you will be able to generate an attendance report of people who have attended your session. The attendance report shows the name and organisation of each attendee who downloads a certificate of attendance. The attendee details are not linked to their feedback. To reduce the risk of attendees being linked to their feedback you will only be able to view a register of attendance once you have received at least 3 feedback submissions.<br><br>The certificate option must be enabled for the attendance register to be available.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let btnSubmit = ref({
  text: "Update feedback session",
  wait: false,
});
const formIsValid = () => {
  document.getElementById("createSessionForm").classList.add("was-validated");
  if (!feedbackSession.title || !feedbackSession.name || !feedbackSession.email)
    return false;
  if (isSeries.value && !feedbackSession.subsessions.length)
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "No sessions added to your session series",
      text: "You need to add at least one session to your session series, or switch back to creating a feedback request for a single session.",
      confirmButtonColor: "#17a2b8",
    });
  return true;
};
const submit = () => {
  if (!formIsValid()) return false;
  btnSubmit.value.text = "Please wait...";
  btnSubmit.value.wait = true;
  api(
    "feedback",
    "updateDetails",
    feedbackSession.id,
    feedbackSession.pin,
    feedbackSession
  ).then(
    function (res) {
      btnSubmit.value.text = "Update feedback session";
      btnSubmit.value.wait = false;
      Swal.fire({
        icon: "success",
        iconColor: "#17a2b8",
        text: res,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    },
    function (error) {
      btnSubmit.value.text = "Retry updating feedback session?";
      btnSubmit.value.wait = false;
      Swal.fire({
        title: "Error updating feedback session",
        text: error,
        icon: "error",
        iconColor: "#17a2b8",
        confirmButtonColor: "#17a2b8",
      });
    }
  );
};

let loading = ref(true);
const loadUpdateDetails = () => {
  api(
    "feedback",
    "loadUpdateDetails",
    feedbackSession.id,
    feedbackSession.pin,
    null
  ).then(
    function (res) {
      if (feedbackSession.id != res.id) {
        console.error(
          "feedbackSession.id != res.id",
          feedbackSession.id,
          res.id
        );
        return;
      }
      feedbackSession.title = res.title;
      feedbackSession.date = res.date;
      feedbackSession.name = res.name;
      feedbackSession.email = res.email;
      if (res.subsessions.length) {
        feedbackSession.subsessions = res.subsessions;
        isSeries.value = true;
      }
      if (res.questions.length) {
        feedbackSession.questions = res.questions;
        hasQuestions.value = true;
      }
      feedbackSession.certificate = res.certificate;
      feedbackSession.notifications = res.notifications;
      feedbackSession.attendance = res.attendance;
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to load feedback report",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;
  Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      feedbackSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value;
      feedbackSession.pin = document.getElementById("swalFormPin").value;
      if (feedbackSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (feedbackSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  }).then((result) => {
    if (result.isConfirmed) {
      history.replaceState({}, "", feedbackSession.id);
      loadUpdateDetails();
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
      <h1 class="text-center display-4">Feedback</h1>
      <strong>Feedback session details</strong>
      <form id="createSessionForm" class="needs-validation my-2" novalidate>
        <div>
          <label for="title" class="form-label"
            >Session {{ isSeries ? "series " : "" }}title:</label
          >
          <input
            type="text"
            v-model="feedbackSession.title"
            class="form-control"
            id="title"
            :placeholder="
              'Title of the session' + (isSeries ? ' series' : '') + '...'
            "
            name="title"
            autocomplete="off"
            required
          />
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="mt-4">
          <label for="name" class="form-label">Date:</label>
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
          <label for="name" class="form-label"
            >{{ isSeries ? "Organiser" : "Facilitator" }} name:</label
          >
          <input
            type="text"
            v-model="feedbackSession.name"
            class="form-control"
            id="name"
            :placeholder="
              (isSeries ? 'Organiser' : 'Facilitator') +
              ' of the session' +
              (isSeries ? ' series' : '') +
              '...'
            "
            name="name"
            autocomplete="off"
            required
          />
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="mt-4">
          <label for="email" class="form-label"
            >{{ isSeries ? "Organiser" : "Facilitator" }} email:</label
          >
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
        <label for="subsessionsTable" class="form-label">Sessions:</label>
        <table class="table" id="subsessionsTable">
          <thead>
            <tr>
              <th></th>
              <th>Title</th>
              <th>Facilitator</th>
              <th>Email</th>
              <th>
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
            @hideEditSubsessionModal="hideEditSubsessionModal"
          />
        </template>
        <EditSubsessionForm
          index="-1"
          @hideEditSubsessionModal="hideEditSubsessionModal"
        />
      </div>
      <div class="my-4">
        <label for="questionsTable" class="form-label">Custom questions:</label
        ><br />
        <span v-if="!hasQuestions">
          <button
            class="btn btn-teal btn-sm mx-1"
            id="enableQuestions"
            v-on:click="hasQuestions = true"
          >
            Enable custom questions
          </button>
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            size="xl"
            style="color: black"
            @click="questionsInfo"
          />
          <span v-if="!hasQuestions">
            Attendees will only be asked to complete the default feedback
            questions.
          </span>
        </span>
        <div v-if="hasQuestions">
          <h2 class="mt-4">Questions</h2>
          <table class="table" id="questionsTable">
            <thead>
              <tr>
                <th></th>
                <th>Question</th>
                <th>Type</th>
                <th>
                  <button
                    class="btn btn-teal btn-sm btn-right"
                    id="btnAddQuestion"
                    @click.prevent="showEditQuestionForm(-1)"
                  >
                    Add <i class="fas fa-plus"></i>
                  </button>
                </th>
              </tr>
            </thead>
            <TransitionGroup name="list" tag="tbody">
              <template
                v-for="(question, index) in feedbackSession.questions"
                :key="question"
              >
                <tr>
                  <td class="p-0 ps-2">
                    <button
                      v-if="index != 0"
                      class="btn btn-default btn-sm p-0"
                      id="btnSortUp"
                      @click="sortQuestion(index, -1)"
                    >
                      <font-awesome-icon
                        :icon="['fas', 'chevron-up']"
                      /></button
                    ><br />
                    <button
                      v-if="index != feedbackSession.questions.length - 1"
                      class="btn btn-default btn-sm p-0"
                      id="btnSortDown"
                      @click="sortQuestion(index, 1)"
                    >
                      <font-awesome-icon :icon="['fas', 'chevron-down']" />
                    </button>
                  </td>
                  <td>{{ question.title }}</td>
                  <td>
                    {{
                      config.feedback.create.questions.types[question.type].name
                    }}
                  </td>
                  <td>
                    <button
                      class="btn btn-danger btn-sm btn-right ms-4"
                      id="btnRemoveQuestion"
                      @click="removeQuestion(index)"
                    >
                      <font-awesome-icon :icon="['fas', 'trash-can']" />
                    </button>
                    <button
                      class="btn btn-teal btn-sm btn-right"
                      id="btnEditQuestion"
                      @click="showEditQuestionForm(index)"
                    >
                      <font-awesome-icon :icon="['fas', 'edit']" />
                    </button>
                  </td>
                </tr>
              </template>
            </TransitionGroup>
          </table>
          <template v-for="(question, index) in feedbackSession.questions">
            <EditQuestionForm
              :index="index"
              @hideEditQuestionModal="hideEditQuestionModal"
            />
          </template>
          <EditQuestionForm
            index="-1"
            @hideEditQuestionModal="hideEditQuestionModal"
          />
        </div>
      </div>
      <div class="my-4">
        <label for="furtherOptions" class="form-label">Options:</label><br />
        <button
          class="btn btn-teal btn-sm mx-1"
          id="toggleCertificate"
          @click="toggleCertificate"
        >
          {{ feedbackSession.certificate ? "Disable" : "Enable" }} certificate
        </button>
        <font-awesome-icon
          :icon="['fas', 'question-circle']"
          size="xl"
          style="color: black"
          @click="certificateInfo"
        />
        <span v-if="feedbackSession.certificate">
          Attendees will be able to download a certificate for this session
          after providing feedback.
          <font-awesome-icon
            :icon="['fas', 'check']"
            size="2xl"
            style="color: green"
          />
        </span>
        <span v-if="!feedbackSession.certificate">
          Attendees will not be able to download a certificate for this session.
          <font-awesome-icon
            :icon="['fas', 'times']"
            size="2xl"
            style="color: red"
          />
        </span>
      </div>
      <div class="my-4">
        <button
          class="btn btn-teal btn-sm mx-1"
          id="toggleNotifications"
          @click="
            feedbackSession.notifications = !feedbackSession.notifications
          "
        >
          {{ feedbackSession.notifications ? "Disable" : "Enable" }}
          notifications
        </button>
        <font-awesome-icon
          :icon="['fas', 'question-circle']"
          size="xl"
          style="color: black"
          @click="notificationsInfo"
        />
        <span v-if="feedbackSession.notifications">
          You will receive an email each time feedback is submitted.
          <font-awesome-icon
            :icon="['fas', 'check']"
            size="2xl"
            style="color: green"
          />
        </span>
        <span v-if="!feedbackSession.notifications">
          You won't receive an email each time feedback is submitted.
          <font-awesome-icon
            :icon="['fas', 'times']"
            size="2xl"
            style="color: red"
          />
        </span>
      </div>
      <div class="my-4">
        <button
          class="btn btn-teal btn-sm mx-1"
          id="toggleAttendance"
          @click="toggleAttendance"
        >
          {{ feedbackSession.notifications ? "Disable" : "Enable" }} register of
          attendance
        </button>
        <font-awesome-icon
          :icon="['fas', 'question-circle']"
          size="xl"
          style="color: black"
          @click="attendanceInfo"
        />
        <span v-if="feedbackSession.attendance">
          Register of attendance will be kept.
          <font-awesome-icon
            :icon="['fas', 'check']"
            size="2xl"
            style="color: green"
          />
        </span>
        <span v-if="!feedbackSession.attendance">
          Register of attendance won't be kept.
          <font-awesome-icon
            :icon="['fas', 'times']"
            size="2xl"
            style="color: red"
          />
        </span>
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
</style>
