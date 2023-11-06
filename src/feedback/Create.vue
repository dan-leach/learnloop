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

let hasQuestions = ref(false);

const toggleCertificate = () => {
  feedbackSession.certificate = !feedbackSession.certificate;
  feedbackSession.attendance = feedbackSession.certificate;
};

const toggleAttendance = () => {
  if (!feedbackSession.attendance && !feedbackSession.certificate) {
    Swal.fire({
      icon: 'error',
      text: "You must enable the 'Certificate of Attendance' option to be able to use the 'Register of Attendance' option.",
    });
  } else {
    feedbackSession.attendance = !feedbackSession.attendance;
  }
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
  <form id="createSessionForm" class="needs-validation m-2" novalidate>
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
  <span v-if="!hasQuestions">
    Do you want to ask additional questions to your attendees?
    <button
      class="btn btn-teal btn-sm"
      id="enableQuestions"
      v-on:click="hasQuestions = true"
    >
      Enable custom questions
    </button>
    <a href="#" data-toggle="modal" data-target="#enableQuestionsModal"
      ><i class="fas fa-question-circle fa-2x"></i
    ></a>
    <div class="modal" id="enableQuestionsModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Custom questions (Optional)</h4>
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            If the standard feedback form doesn't cover everything you want to
            ask, you can add additional questions.
          </div>
        </div>
      </div>
    </div>
    <br /><br />
  </span>
  <div v-if="hasQuestions">
    <h2>Custom questions</h2>
    <table class="table" id="questionsTable">
      <thead>
        <tr>
          <th>Question</th>
          <th>Type</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(question, index) in feedbackSession.questions">
          <td>{{ question.title }}</td>
          <td>{{ question.type }}</td>
          <td>
            <button
              style="float: right"
              class="btn btn-secondary btn-sm"
              id="btnEditQuestion"
              v-on:click="editQuestion(index)"
            >
              <i class="fas fa-edit"></i>
            </button>
          </td>
          <td>
            <button
              style="float: right"
              class="btn btn-danger btn-sm"
              id="btnRemoveQuestion"
              v-on:click="removeQuestion(index)"
            >
              <i class="fas fa-times"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <button
      style="float: right"
      class="btn btn-success btn-sm"
      id="btnAddQuestion"
      data-toggle="modal"
      data-target="#addQuestion"
      data-backdrop="static"
    >
      Add <i class="fas fa-plus"></i>
    </button>
    <div class="modal" id="addQuestion">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add a custom question</h4>
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <form id="createQuestionForm" class="needs-validation" novalidate>
              <div class="form-group">
                <label for="title">Question title:</label>
                <input
                  type="text"
                  v-model="question.title"
                  class="form-control"
                  id="questionTitle"
                  placeholder="Please enter your question..."
                  name="title"
                  autocomplete="off"
                  required
                />
                <div class="invalid-feedback">Please fill out this field.</div>
              </div>
              <div class="form-group">
                <label for="type"
                  >Question type:
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#questionTypeInfo"
                    ><i class="fas fa-question-circle fa-1x"></i></a
                ></label>
                <select
                  v-model="question.type"
                  id="questionType"
                  class="form-control"
                  required
                >
                  <option disabled value="">Please select question type</option>
                  <option value="text">Text</option>
                  <option value="checkbox">Checkboxes</option>
                  <option value="select">Drop-down select</option>
                </select>
                <div class="invalid-feedback">Please fill out this field.</div>
              </div>
              <div
                class="form-group"
                v-if="question.type == 'select' || question.type == 'checkbox'"
              >
                <label for="type"
                  >Question options:
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#questionOptionsInfo"
                    ><i class="fas fa-question-circle fa-1x"></i></a
                ></label>
                <input
                  type="text"
                  v-model="question.options"
                  class="form-control"
                  id="questionOptions"
                  placeholder="Please enter the question options..."
                  name="options"
                  autocomplete="off"
                  required
                />
                <div class="invalid-feedback">Please fill out this field.</div>
              </div>
            </form>
            <br /><br />
            <button
              class="btn btn-primary"
              id="submitCreateQuestion"
              v-on:click="createQuestion"
            >
              Add question
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal" id="questionTypeInfo">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Question type info</h4>
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            There are three question types:
            <ul>
              <li>
                Text - a free-text area for attendees to type into, similar to
                the positive or constructive comment inputs on the default form
              </li>
              <li>
                Checkboxes - a series of checkboxes which attendees can select
                from. Attendees can select as many options as apply, or none.
              </li>
              <li>
                Drop-down select - a series of options in a drop-down menu.
                Attendees must select only one option.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="modal" id="questionOptionsInfo">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Question options info</h4>
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            Please enter each option separated by a semicolon <code>;</code
            ><br />
            <br />
            e.g. <code>option 1;option 2; option 3</code>
          </div>
        </div>
      </div>
    </div>
    <br /><br />
  </div>

  <span v-if="feedbackSession.certificate">
    Attendees will be able to download a certificate for this session after
    providing feedback.
    <font-awesome-icon
      :icon="['fas', 'check']"
      size="2xl"
      style="color: green"
    />
  </span>
  <span v-if="!feedbackSession.certificate">
    Attendees will not be able to download a certificate for this session.
    <font-awesome-icon :icon="['fas', 'times']" size="2xl" style="color: red" />
  </span>
  <button
    class="btn btn-teal btn-sm mx-1"
    id="toggleCertificate"
    @click="toggleCertificate"
  >
    {{ feedbackSession.certificate ? 'Disable' : 'Enable' }} certificate
  </button>
  <a href="#" data-toggle="modal" data-target="#certificateOptionsModal"
    ><font-awesome-icon
      :icon="['fas', 'question-circle']"
      size="xl"
      style="color: black"
  /></a>
  <div class="modal" id="certificateOptionsModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Certificate of attendance (Optional)</h4>
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
        </div>
        <div class="modal-body">
          By default attendees of your session will be able to download a
          certificate of attendance after completing the feedback form. This is
          a good way of encouraging attendees to provide feedback.
          <br /><br />
          You can disable the certificate if you prefer. Attendees will still be
          able to provide feedback but will not be given the option to download
          a certificate afterwards.
        </div>
      </div>
    </div>
  </div>
  <br /><br />
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
    <font-awesome-icon :icon="['fas', 'times']" size="2xl" style="color: red" />
  </span>
  <button
    class="btn btn-teal btn-sm mx-1"
    id="toggleNotifications"
    @click="feedbackSession.notifications = !feedbackSession.notifications"
  >
    {{ feedbackSession.notifications ? 'Disable' : 'Enable' }} notifications
  </button>
  <a href="#" data-toggle="modal" data-target="#notificationsOptionsModal"
    ><font-awesome-icon
      :icon="['fas', 'question-circle']"
      size="xl"
      style="color: black"
  /></a>
  <div class="modal" id="notificationsOptionsModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            Email notifications when feedback submitted (Optional)
          </h4>
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
        </div>
        <div class="modal-body">
          By default you will receive an email when feedback for your session is
          submitted. To avoid overloading your inbox, no further notifications
          are sent within 2 hours.<br /><br />
          If you disable this you can still manually check using your session ID
          and PIN which are emailed to you once your session is created. You can
          also disable further notifications later, using a link in the
          notification email itself.
        </div>
      </div>
    </div>
  </div>
  <br /><br />
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
    <font-awesome-icon :icon="['fas', 'times']" size="2xl" style="color: red" />
  </span>
  <button
    class="btn btn-teal btn-sm mx-1"
    id="toggleAttendance"
    @click="toggleAttendance"
  >
    {{ feedbackSession.notifications ? 'Disable' : 'Enable' }} register of
    attendance
  </button>
  <a href="#" data-toggle="modal" data-target="#attendanceOptionsModal"
    ><font-awesome-icon
      :icon="['fas', 'question-circle']"
      size="xl"
      style="color: black"
  /></a>
  <div class="modal" id="attendanceOptionsModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Register of attendance (Optional)</h4>
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
        </div>
        <div class="modal-body">
          By default you will be able to generate an attendance report of people
          who have attended your session. The attendance report shows the name
          and organisation of each attendee who downloads a certificate of
          attendance. The attendee details are not linked to their feedback. To
          reduce the risk of attendees being linked to their feedback you will
          only be able to view a register of attendance once you have received
          at least 3 feedback submissions.<br /><br />The certificate option
          must be enabled for the attendance register to be available.
        </div>
      </div>
    </div>
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
