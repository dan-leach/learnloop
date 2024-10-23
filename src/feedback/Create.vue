<script setup>
import { onMounted, ref } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Modal from "bootstrap/js/dist/modal";
import EditSubsessionForm from "./components/EditSubsessionForm.vue";
import EditOrganiserForm from "./components/EditOrganiserForm.vue";
import EditQuestionForm from "./components/EditQuestionForm.vue";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";
import { inject } from "vue";
const config = inject("config");

let isSeries = ref(false);
const toggleSingleSeries = () => {
  if (isSeries.value) {
    if (feedbackSession.subsessions.length) {
      Swal.fire({
        title: "Lose sessions",
        text: "You have added sessions to this feedback request. If you switch back to requesting feedback for a single session you will lose this progress. Continue?",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
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
const seriesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Collect feedback for multiple sessions (Optional)",
    html: '<div class="text-start">You are currently requesting feedback for a single session. Alternatively, you can create a session series where attendees can provide feedback for multiple sessions (for example, a teaching day with different presenters) using a single link.<br><br>As the organiser you will be able to view all feedback collected, individual presenters can view the feedback for just their session.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};
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
  if (index === undefined) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.subsessions.push(JSON.parse(subsession));
  } else {
    const { name, title, email } = JSON.parse(subsession);
    Object.assign(feedbackSession.subsessions[index], { name, title, email });
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
  if (index === undefined) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.questions.push(JSON.parse(question));
  } else {
    const { title, type, options, settings } = JSON.parse(question);
    Object.assign(feedbackSession.questions[index], {
      title,
      type,
      options,
      settings,
    });
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

const organisersInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Organisers",
    html: '<div class="text-start">Adding an organiser allows them to view the feedback for this session and, optionally, edit the feedback request. You should add your own details as the lead organsier. The lead organiser cannot be changed once the session is created and will receive an email notification if any other organisers with editing rights make changes to the feedback request.<br><br>For a session series, adding an organiser here will allow them to view the feedback for all sessions. To allow facilitators to view the feedback for just their session, add their email to the session table above.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};
let editOrganiserModal;
const showEditOrganiserForm = (index) => {
  editOrganiserModal = new Modal(
    document.getElementById("editOrganiserModal" + index),
    {
      backdrop: "static",
      keyboard: false,
      focus: true,
    }
  );
  editOrganiserModal.show();
};
const hideEditOrganiserModal = (index, organiser) => {
  if (index === undefined) {
    //user did not submit the form, closed using the X. Do nothing except hide the modal
  } else if (index == -1) {
    feedbackSession.organisers.push(JSON.parse(organiser));
  } else {
    const { name, email, isLead, canEdit } = JSON.parse(organiser);
    Object.assign(feedbackSession.organisers[index], {
      name,
      email,
      isLead,
      canEdit,
    });
  }
  editOrganiserModal.hide();
};
const sortOrganiser = (index, x) =>
  feedbackSession.organisers.splice(
    index + x,
    0,
    feedbackSession.organisers.splice(index, 1)[0]
  );
const removeOrganiser = (index) => {
  Swal.fire({
    title: "Remove this organiser?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) feedbackSession.organisers.splice(index, 1);
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
const multipleDatesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Multiple dates (Optional)",
    html: '<div class="text-start">By default you must provide a date for your session. The certificate (if enabled) will show this date.<br><br> If you are running the same session on multiple dates you can use the "Deliver on multiple dates" option. When this option is used the certificate will instead show the date when they submitted feedback. Your feedback report will be organised by the month the feedback was submitted.</div>',
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let isEdit =
  useRouter().currentRoute.value.name == "feedback-edit" ? true : false;
let loading = ref(isEdit ? true : false);
let btnSubmit = ref({
  text: isEdit ? "Update feedback session" : "Create feedback session",
  wait: false,
});
const loadUpdateDetails = (isTemplate = false) => {
  api("feedback/loadUpdateSession", {
    id: feedbackSession.id,
    pin: feedbackSession.pin,
  }).then(
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
      feedbackSession.multipleDates = res.multipleDates;
      feedbackSession.name = res.name;

      feedbackSession.certificate = res.certificate;
      feedbackSession.attendance = res.attendance;

      if (res.subsessions.length) {
        feedbackSession.subsessions = res.subsessions;
        isSeries.value = true;
      }
      if (res.questions.length) {
        feedbackSession.questions = res.questions;
        hasQuestions.value = true;
        for (let question of feedbackSession.questions) {
          if (!question.settings) {
            //for pre-v5 custom questions
            question.settings = {
              selectedLimit: {
                min: 1,
                max: 100,
              },
              characterLimit: 500,
            };
          }
          if (question.settings.required == undefined) {
            //for older sessions with undefined 'required' paramenter default to required for text and select but not for checkboxes
            if (question.type == "text" || question.type == "select")
              question.settings.required = true;
          }
        }
      }

      feedbackSession.organisers = res.organisers;
      for (let organiser of feedbackSession.organisers)
        organiser.existing = true;

      if (isTemplate) {
        //remove the id and pin so that a new session is created rather than overwriting the template
        //save id in templateId to show template message at top
        feedbackSession.templateId = feedbackSession.id;
        feedbackSession.id = "";
        feedbackSession.pin = "";

        //remove the ids from subsessions so new subsessions are created for a template based create
        for (let subsession of feedbackSession.subsessions) {
          subsession.id = "";
        }
      }

      loading.value = false;
    },

    function (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to load feedback session",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};
const loadTemplate = () => {
  Swal.fire({
    title: "Enter ID and PIN for the session you want to use as a template",
    html:
      "You will need your session ID and PIN which you can find in the email you received when the session you want to use as a template was created. <br>" +
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
      loadUpdateDetails(true);
    }
  });
};

const formIsValid = () => {
  document.getElementById("createSessionForm").classList.add("was-validated");
  if (!feedbackSession.title || !feedbackSession.name) return false;
  if (!feedbackSession.multipleDates && !feedbackSession.date) return false;
  if (isSeries.value && !feedbackSession.subsessions.length) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "No sessions added to your session series",
      text: "You need to add at least one session to your session series, or switch back to creating a feedback request for a single session.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (!feedbackSession.organisers.length) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "No organisers added to your session",
      text: "You need to add at least one organiser to your session.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  let leadOrganiserCount = 0;
  for (let organiser of feedbackSession.organisers) {
    if (organiser.isLead) leadOrganiserCount++;
  }
  if (leadOrganiserCount != 1) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "A lead organiser must be designated",
      text: "You need to assign the lead organiser role. Use the edit icon by the appropriate person. Only one person can have the lead organiser role.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  return true;
};
const submit = () => {
  if (!formIsValid()) return false;
  btnSubmit.value.text = "Please wait...";
  btnSubmit.value.wait = true;
  if (isEdit) {
    api("feedback/updateSession", feedbackSession).then(
      function (res) {
        btnSubmit.value.text = "Update feedback session";
        btnSubmit.value.wait = false;
        let text = res.message;
        if (res.sendMailFails.length) {
          text +=
            "<br><br>Session update emails to the following recepients failed:<br>";
          for (let fail of res.sendMailFails)
            text += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
        }

        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html: text,
          confirmButtonColor: "#17a2b8",
        });
        router.push("/");
      },
      function (error) {
        btnSubmit.value.text = "Retry updating feedback session?";
        btnSubmit.value.wait = false;
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          title: "Error updating feedback session",
          text: error,
          icon: "error",
          iconColor: "#17a2b8",
          confirmButtonColor: "#17a2b8",
        });
      }
    );
  } else {
    api("feedback/insertSession", feedbackSession).then(
      function (res) {
        btnSubmit.value.text = "Create feedback session";
        btnSubmit.value.wait = false;
        feedbackSession.id = res.id;
        feedbackSession.pin = res.leadPin;
        feedbackSession.sendMailFails = res.sendMailFails;
        router.push("/feedback/created");
      },
      function (error) {
        btnSubmit.value.text = "Retry creating feedback session?";
        btnSubmit.value.wait = false;
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          title: "Error creating feedback session",
          text: error,
          icon: "error",
          iconColor: "#17a2b8",
          confirmButtonColor: "#17a2b8",
        });
      }
    );
  }
};

onMounted(() => {
  if (isEdit) {
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
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Feedback</h1>
      <p v-if="feedbackSession.templateId">
        Using session
        <span class="id-box">{{ feedbackSession.templateId }}</span> as template
        for new session. The original session will not be altered.
        <button
          class="btn btn-sm btn-sessions btn-teal me-2"
          id="btnLoadTemplate"
          @click="feedbackSession.reset()"
        >
          Create session from scratch instead
        </button>
      </p>
      <p v-else-if="isEdit" class="form-label ms-2">
        Editing feedback session
        <span class="id-box">{{ feedbackSession.id }}</span>
      </p>
      <div class="d-flex align-items-center justify-content-start mb-2" v-else>
        <button
          class="btn btn-sm btn-sessions btn-teal me-2"
          id="btnLoadTemplate"
          @click="loadTemplate"
        >
          Use a previous session as a template
        </button>
      </div>
      <div class="d-flex align-items-stretch justify-content-between flex-wrap">
        <!--Session details-->
        <form
          id="createSessionForm"
          class="card bg-transparent shadow p-2 mb-3 flex-grow-1 needs-validation details-card"
          novalidate
        >
          <label for="sessionDetails" class="form-label"
            >Session {{ isSeries ? "series " : "" }}details</label
          >
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
              required
            />
            <label for="title"
              >Session {{ isSeries ? "series " : "" }}title</label
            >
            <div class="invalid-feedback">Please fill out this field.</div>
          </div>
          <!--Date-->
          <div class="d-flex flex-wrap">
            <div class="form-floating mb-3 flex-grow-1">
              <input
                type="date"
                placeholder=""
                v-model="feedbackSession.date"
                class="form-control"
                id="date"
                name="Date"
                autocomplete="off"
                required
                :disabled="feedbackSession.multipleDates"
              />
              <label for="name">Date</label>
              <div class="invalid-feedback">Please fill out this field.</div>
            </div>
            <div class="d-flex align-items-center justify-content-start mb-3">
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
              <font-awesome-icon
                :icon="['fas', 'question-circle']"
                size="xl"
                class="mx-2"
                style="color: black"
                @click="multipleDatesInfo"
              />
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
              required
            />
            <label for="name">
              {{ isSeries ? "Organiser" : "Facilitator" }} name</label
            >
            <div class="invalid-feedback">Please fill out this field.</div>
          </div>
        </form>
        <!--Options-->
        <div class="card bg-transparent shadow p-2 mb-3 settings-card">
          <label for="furtherOptions" class="form-label">Options</label>
          <!--Certificate-->
          <div class="d-flex align-items-center justify-content-start">
            <div class="d-flex align-items-center justify-content-start mb-3">
              <button
                class="btn btn-settings btn-teal btn-sm"
                id="toggleCertificate"
                @click="toggleCertificate"
              >
                {{ feedbackSession.certificate ? "Disable" : "Enable" }}
                certificate
              </button>
              <font-awesome-icon
                :icon="['fas', 'question-circle']"
                size="xl"
                class="mx-2"
                style="color: black"
                @click="certificateInfo"
              />
            </div>
            <div class="mb-3">
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
          <!--Attendance-->
          <div class="d-flex align-items-center justify-content-start">
            <div class="d-flex align-items-center justify-content-start mb-3">
              <button
                class="btn btn-settings btn-teal btn-sm"
                id="toggleAttendance"
                @click="toggleAttendance"
              >
                {{ feedbackSession.attendance ? "Disable" : "Enable" }}
                register
              </button>
              <font-awesome-icon
                :icon="['fas', 'question-circle']"
                size="xl"
                class="mx-2"
                style="color: black"
                @click="attendanceInfo"
              />
            </div>
            <div class="mb-3">
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
        </div>
      </div>
      <!--subsessions-->
      <div class="card bg-transparent shadow p-2 mb-3">
        <label for="subsessionsTable" class="form-label"
          >Sessions
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            class="mx-2"
            style="color: black"
            @click="seriesInfo"
        /></label>
        <div class="d-flex flex-wrap align-items-center justify-content-start">
          <div class="d-flex align-items-center justify-content-start">
            <button
              class="btn btn-sm btn-sessions btn-teal me-2"
              id="enableSubsessions"
              @click="toggleSingleSeries"
            >
              Switch {{ isSeries ? "to single session" : "to session series" }}
            </button>
          </div>
          <div>
            You are currently requesting feedback for a
            {{ isSeries ? "session series" : "single session" }}
          </div>
        </div>
        <table v-if="isSeries" class="table" id="subsessionsTable">
          <thead>
            <tr>
              <th class="bg-transparent p-0 ps-2"></th>
              <th class="bg-transparent p-0 ps-2">Title</th>
              <th class="bg-transparent p-0 ps-2">Facilitator</th>
              <th class="bg-transparent p-0 ps-2">Email</th>
              <th class="bg-transparent p-0 ps-2">
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
                <td class="bg-transparent p-0 ps-2">
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
                <td class="bg-transparent">{{ subsession.title }}</td>
                <td class="bg-transparent">{{ subsession.name }}</td>
                <td class="bg-transparent">{{ subsession.email }}</td>
                <td class="bg-transparent">
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
            :isEdit="isEdit"
            @hideEditSubsessionModal="hideEditSubsessionModal"
          />
        </template>
        <EditSubsessionForm
          index="-1"
          :isEdit="isEdit"
          @hideEditSubsessionModal="hideEditSubsessionModal"
        />
      </div>
      <!--custom questions-->
      <div class="card bg-transparent shadow p-2 mb-3">
        <label for="questionsTable" class="form-label"
          >Custom questions
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            class="mx-2"
            style="color: black"
            @click="questionsInfo"
        /></label>
        <div
          class="d-flex flex-wrap align-items-center justify-content-start"
          v-if="!hasQuestions"
        >
          <div class="d-flex align-items-center justify-content-start">
            <button
              class="btn btn-teal btn-sm btn-questions me-2"
              id="enableQuestions"
              v-on:click="hasQuestions = true"
            >
              Enable custom questions
            </button>
          </div>
          <div>
            Attendees will only be asked to complete the default feedback
            questions
          </div>
        </div>
        <div v-if="hasQuestions">
          <table class="table" id="questionsTable">
            <thead>
              <tr>
                <th class="bg-transparent p-0 ps-2"></th>
                <th class="bg-transparent p-0 ps-2">Question</th>
                <th class="bg-transparent p-0 ps-2">Type</th>
                <th class="bg-transparent p-0 ps-2">
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
                  <td class="bg-transparent p-0 ps-2">
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
                  <td class="bg-transparent">{{ question.title }}</td>
                  <td class="bg-transparent">
                    {{
                      config.feedback.create.questions.types[question.type].name
                    }}
                  </td>
                  <td class="bg-transparent">
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
      <!--organisers-->
      <div class="card bg-transparent shadow p-2 mb-3">
        <label for="organisersTable" class="form-label"
          >Organisers
          <font-awesome-icon
            :icon="['fas', 'question-circle']"
            class="mx-2"
            style="color: black"
            @click="organisersInfo"
        /></label>
        <div>
          <span v-if="isSeries"
            >Only add organisers who should be able to view the feedback for all
            sessions. Subsession facilitators will be able to view the feedback
            for their session providing you add their email in the sessions
            table above.</span
          >
        </div>
        <table class="table" id="organisersTable">
          <thead>
            <tr>
              <th class="bg-transparent p-0 ps-2"></th>
              <th class="bg-transparent p-0 ps-2">Name</th>
              <th class="bg-transparent p-0 ps-2">Email</th>
              <th class="bg-transparent p-0 ps-2">Lead</th>
              <th class="bg-transparent p-0 ps-2">Can edit?</th>
              <th class="bg-transparent p-0 ps-2">
                <button
                  class="btn btn-teal btn-sm btn-right"
                  id="btnAddOrganiser"
                  @click.prevent="showEditOrganiserForm(-1)"
                >
                  Add <i class="fas fa-plus"></i>
                </button>
              </th>
            </tr>
          </thead>
          <TransitionGroup name="list" tag="tbody">
            <template
              v-for="(organiser, index) in feedbackSession.organisers"
              :key="organiser"
            >
              <tr>
                <td class="bg-transparent p-0 ps-2">
                  <button
                    v-if="index != 0"
                    class="btn btn-default btn-sm p-0"
                    id="btnSortUp"
                    @click="sortOrganiser(index, -1)"
                  >
                    <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
                  ><br />
                  <button
                    v-if="index != feedbackSession.organisers.length - 1"
                    class="btn btn-default btn-sm p-0"
                    id="btnSortDown"
                    @click="sortOrganiser(index, 1)"
                  >
                    <font-awesome-icon :icon="['fas', 'chevron-down']" />
                  </button>
                </td>
                <td class="bg-transparent">{{ organiser.name }}</td>
                <td class="bg-transparent">{{ organiser.email }}</td>
                <td class="bg-transparent">
                  <font-awesome-icon
                    v-if="organiser.isLead"
                    :icon="['fas', 'check']"
                    size="2xl"
                    style="color: green"
                  />
                  <font-awesome-icon
                    v-else
                    :icon="['fas', 'times']"
                    size="2xl"
                    style="color: red"
                  />
                </td>
                <td class="bg-transparent">
                  <font-awesome-icon
                    v-if="organiser.canEdit"
                    :icon="['fas', 'check']"
                    size="2xl"
                    style="color: green"
                  />
                  <font-awesome-icon
                    v-else
                    :icon="['fas', 'times']"
                    size="2xl"
                    style="color: red"
                  />
                </td>
                <td class="bg-transparent">
                  <button
                    class="btn btn-danger btn-sm btn-right ms-4"
                    v-if="!isEdit || !organiser.isLead"
                    id="btnRemoveOrganiser"
                    @click="removeOrganiser(index)"
                  >
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                  </button>
                  <button
                    class="btn btn-teal btn-sm btn-right"
                    v-if="!isEdit || !organiser.isLead"
                    id="btnEditOrganiser"
                    @click="showEditOrganiserForm(index)"
                  >
                    <font-awesome-icon :icon="['fas', 'edit']" />
                  </button>
                </td>
              </tr>
            </template>
          </TransitionGroup>
        </table>
        <template v-for="(organisers, index) in feedbackSession.organisers">
          <EditOrganiserForm
            :index="index"
            :isEdit="isEdit"
            @hideEditOrganiserModal="hideEditOrganiserModal"
          />
        </template>
        <EditOrganiserForm
          index="-1"
          :isEdit="isEdit"
          @hideEditOrganiserModal="hideEditOrganiserModal"
        />
      </div>

      <div class="text-center mb-3">
        <button
          class="btn btn-teal btn-lg"
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
@media only screen and (min-width: 1200px) {
  .details-card {
    min-width: 400px;
  }
  .settings-card {
    max-width: 500px;
    margin-left: 15px;
  }
}
@media only screen and (max-width: 1199px) {
  .settings-card {
    width: 100%;
  }
}
.btn-settings {
  width: 95px;
  min-height: 55px;
}
.btn-sessions,
.btn-questions {
  min-width: 180px;
}
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
.table-info {
  font-size: 0.5em;
  font-weight: 100;
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
