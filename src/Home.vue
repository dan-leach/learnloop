<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { api } from "./data/api.js";
import router from "./router";
import Quote from "./components/Quote.vue";
import Swal from "sweetalert2";
import { feedbackSession } from "./data/feedbackSession.js";
import { interactionSession } from "./data/interactionSession.js";

import { inject } from "vue";
const config = inject("config");

const deployVersion = false;
const interactionInterestEmail = ref("");
const interactionInterest = async () => {
  if (interactionInterestEmail.value) {
    document
      .getElementById("interactionInterestEmail")
      .classList.remove("is-invalid");

    try {
      const response = await api("interaction/interest", {
        email: interactionInterestEmail.value,
      });
      Swal.fire({
        icon: "success",
        iconColor: "#17a2b8",
        text: response.message,
        confirmButtonColor: "#17a2b8",
      });
      interactionInterestEmail.value = "";
    } catch (error) {
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
    }
  } else {
    document
      .getElementById("interactionInterestEmail")
      .classList.add("is-invalid");
  }
};

const viewFeedback = () => {
  if (feedbackSession.id) {
    document.getElementById("feedbackID").classList.remove("is-invalid");
    router.push("/feedback/view/" + feedbackSession.id.trim());
  } else {
    document.getElementById("feedbackID").classList.add("is-invalid");
  }
};

const hostInteraction = () => {
  if (interactionSession.id) {
    document.getElementById("interactionID").classList.remove("is-invalid");
    router.push("/interaction/host/" + interactionSession.id.trim());
  } else {
    document.getElementById("interactionID").classList.add("is-invalid");
  }
};

const resetPin = async (module, id) => {
  if (!id) id = "";
  let email = "";
  router.push("/");
  const { isConfirmed } = await Swal.fire({
    title: "Reset PIN",
    html:
      "<div class='overflow-hidden'>You will need your session ID which you can find in emails relating to your session.<br>For example: " +
      config.value.client.url.replace("https://", "") +
      "/<mark>aBc123</mark>.<br>" +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="' +
      id +
      '">' +
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId").value.trim();
      email = document.getElementById("swalFormEmail").value.trim();
      if (email == "") {
        Swal.showValidationMessage("Please enter an email");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }

      try {
        const response = await api(module + "/resetPin", { id, email });

        let html = response.message;
        if (response.sendMailFails.length) {
          html +=
            "<br><br>New pin emails to the following recepients failed:<br>";
          for (let fail of response.sendMailFails)
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
        }
        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html: html,
          confirmButtonColor: "#17a2b8",
        });
      } catch (error) {
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          icon: "error",
          iconColor: "#17a2b8",
          text: error,
          confirmButtonColor: "#17a2b8",
        });
      }
    },
  });

  if (isConfirmed) {
    feedbackSession.reset();
    interactionSession.reset();
  }
};

const setNotificationPreference = async (id) => {
  if (!id) id = "";
  let pin = "";
  router.push("/");
  let notifications = true;
  const { isConfirmed } = await Swal.fire({
    title: "Set notification preferences",
    html:
      "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created." +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="' +
      id +
      '">' +
      '<input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input"><br><br>' +
      'Set notifications <select id="swalFormNotifications" type="select" class="swal2-input"><option value=true>On</option><option value=false>Off</option></select></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId").value.trim();
      pin = document.getElementById("swalFormPin").value.trim();
      if (pin == "") {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      notifications = document.getElementById("swalFormNotifications").value;

      try {
        const response = await api("feedback/updateNotificationPreferences", {
          id,
          pin,
          notifications: notifications === "true" ? true : false,
        });

        let html = response.message;
        if (response.sendMailFails.length) {
          html +=
            "<br><br>Notification preference emails to the following recepients failed:<br>";
          for (let fail of response.sendMailFails)
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
        }
        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html: html,
          confirmButtonColor: "#17a2b8",
        });
      } catch (error) {
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          icon: "error",
          iconColor: "#17a2b8",
          text: error,
          confirmButtonColor: "#17a2b8",
        });
      }
    },
  });

  if (isConfirmed) {
    feedbackSession.reset();
  }
};

const findMySessions = async (module) => {
  let email = "";
  const { isConfirmed } = await Swal.fire({
    title: "Find my sessions",
    html:
      "<div class='overflow-hidden'>Enter your email below and we'll email you with a list of sessions for which you're listed as an organiser or facilitator.<br>" +
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      email = document.getElementById("swalFormEmail").value.trim();
      if (email == "") {
        Swal.showValidationMessage("Please enter an email");
        return false;
      }

      try {
        const response = await api(module + "/findMySessions", {
          email,
        });

        let html = response.message;
        if (response.sendMailFails.length) {
          html +=
            "Session history emails to the following recepients failed:<br>";
          for (let fail of response.sendMailFails)
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
        }
        Swal.fire({
          icon: response.sendMailFails.length ? "error" : "success",
          iconColor: "#17a2b8",
          html: html,
          confirmButtonColor: "#17a2b8",
        });
      } catch (error) {
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          icon: "error",
          iconColor: "#17a2b8",
          text: error,
          confirmButtonColor: "#17a2b8",
        });
      }
    },
  });

  if (isConfirmed) {
    feedbackSession.reset();
    interactionSession.reset();
  }
};

const closeFeedbackSession = async () => {
  let id = "";
  let pin = "";
  const { isConfirmed } = await Swal.fire({
    title: "Close session",
    html:
      "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created.<br><br>Please be aware that once closed a session cannot be reopend to further feedback.<br>" +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
      '<input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId").value.trim();
      pin = document.getElementById("swalFormPin").value.trim();
      if (pin == "") {
        Swal.showValidationMessage("Please enter a session PIN");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }

      try {
        const response = await api("feedback/closeSession", { id, pin });

        let html = response.message;
        if (response.sendMailFails.length) {
          html +=
            "<br><br>Session closure emails to the following recepients failed:<br>";
          for (let fail of response.sendMailFails)
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
        }
        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html: html,
          confirmButtonColor: "#17a2b8",
        });
      } catch (error) {
        if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
        Swal.fire({
          icon: "error",
          iconColor: "#17a2b8",
          text: error,
          confirmButtonColor: "#17a2b8",
        });
      }
    },
  });

  if (isConfirmed) {
    feedbackSession.reset();
    interactionSession.reset();
  }
};

onMounted(() => {
  feedbackSession.reset();
  interactionSession.reset();
  let id = useRouter().currentRoute.value.params.id;
  if (window.location.toString().includes("?")) {
    //v4 query
    let v4Param = window.location.toString().split("?")[1];
    console.log("v4Param", v4Param);
    if (v4Param.includes("view=")) {
      v4Param = v4Param.replace("view=", "");
      router.push("/feedback/view/" + v4Param);
    } else if (v4Param.includes("notifications=")) {
      v4Param = v4Param.replace("notifications=", "");
      setNotificationPreference(v4Param);
    } else if (v4Param.includes("edit=")) {
      v4Param = v4Param.replace("edit=", "");
      router.push("/feedback/edit/" + v4Param);
    } else if (v4Param.includes("resetpin=")) {
      v4Param = v4Param.replace("resetpin=", "");
      resetPin("feedback", v4Param);
    } else if (v4Param.includes("attendance=")) {
      v4Param = v4Param.replace("attendance=", "");
      router.push("/feedback/attendance/" + v4Param);
    } else {
      router.push("/feedback/" + v4Param);
    }
    return;
  }
  let routeName = useRouter().currentRoute.value.name;
  if (routeName == "interaction-resetPIN") resetPin("interaction", id);
  else if (routeName == "feedback-resetPIN") resetPin("feedback", id);
  else if (routeName == "feedback-notifications") setNotificationPreference(id);
  else if (routeName == "home" && id) {
    if (id.charAt(0) == "i") router.push("/interaction/" + id);
    else router.push("/feedback/" + id);
  } else {
    router.push("/");
  }
});
</script>

<template>
  <main>
    <p class="text-center fs-6 mt-2" v-if="!deployVersion">
      Welcome to LearnLoop v{{ config.version }}. This version is under active
      development so it might behave in unpredictable ways. Please
      <a href="mailto:web@danleach.uk" target="_blank" style="color: black"
        >let me know</a
      >
      about any issues you find.
      <a
        href="https://github.com/dan-leach/learnloop/blob/v6/changelog.md"
        target="_blank"
        style="color: black"
        >View the changelog here</a
      >. If you prefer to wait until the update is formally released, please use
      <a href="https://learnloop.co.uk" style="color: black">LearnLoop.co.uk</a>
      instead.
    </p>
    <div v-else class="mt-3"></div>
    <div class="d-flex justify-content-around">
      <!--remove hidden attribute to restore update info card-->
      <div class="card bg-transparent shadow mb-3 update-info-card" hidden>
        <div class="card-body">
          <h5 class="card-title">What's new in the January update?</h5>
          <p class="card-text">
            Create feedback requests using existing sessions as a template, add
            additional organisers, easier custom questions and more.
            <a href="#" data-bs-toggle="collapse" data-bs-target="#changes"
              >Read more...</a
            >
          </p>
          <div class="collapse my-2" id="changes">
            <ul>
              <li class="mb-2">
                You can now use a previous session as a template. The session
                details including the organiser details, custom questions, and
                any sessions will be automatically populated from your template
                session. You can edit the details before submitting. The session
                which is used as a template is not changed. Click
                <strong>Use a previous session as a template</strong> on the
                <strong>Create a new feedback session</strong> to try it out.
              </li>
              <li class="mb-2">
                You can add multiple organisers to your feedback sessions. You
                decide which organisers can edit the session or just view the
                feedback and attendance data.
              </li>
              <li class="mb-2">
                Custom questions now have more options, such as specifying how
                many checkboxes must be selected (e.g. between 2 and 4), a
                character limit for free-text questions, and all questions can
                be required or optional.
              </li>
              <li class="mb-2">
                You now have the option to select
                <strong>deliver on multiple dates</strong> if you want to gather
                feedback in one place for a session you are delivering more than
                once.
              </li>
              <li>
                <strong>Coming soon:</strong> Interaction will allow you to
                share questions and polls with your audience via their
                smartphone or other devices during teaching sessions. Register
                your interest below to be the first to know when it's available.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-around flex-wrap mt-2">
      <div class="card bg-transparent shadow p-2 m-2">
        <h1 class="text-center">Feedback</h1>
        <p class="text-center">Quickly gather anonymous feedback on teaching</p>
        <div class="input-group m-2">
          <input
            id="feedbackID"
            type="text"
            placeholder="Session ID"
            autocomplete="off"
            class="form-control"
            v-model="feedbackSession.id"
            @keyup.enter="viewFeedback"
          />
          <button
            type="button"
            id="viewFeedback"
            class="btn btn-teal me-3"
            @click="viewFeedback"
          >
            View feedback
          </button>
        </div>
        <ul class="nav nav-pills justify-content-between m-2 d-flex gap-2">
          <li class="nav-item mb-2 flex-grow-1 d-flex">
            <button
              class="nav-link active flex-grow-1"
              @click="router.push('/feedback/create')"
            >
              Create a new feedback session
            </button>
          </li>
          <li class="nav-item dropdown flex-grow-1 d-flex more-options">
            <button
              class="nav-link active dropdown-toggle flex-grow-1"
              data-bs-toggle="dropdown"
              href="#"
            >
              More options
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="router.push('/feedback/edit/')"
                  >Edit existing session</a
                >
              </li>
              <li>
                <a
                  class="dropdown-item"
                  @click="router.push('/feedback/attendance/')"
                  >View attendance</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="closeFeedbackSession()"
                  >Close session</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="resetPin('feedback')"
                  >Reset PIN</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="setNotificationPreference()"
                  >Set notification preferences</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="findMySessions('feedback')"
                  >Find my sessions</a
                >
              </li>
            </ul>
          </li>
        </ul>
        <small class="text-center">
          {{ config.feedback.count }} responses
        </small>
      </div>
      <div class="card bg-transparent shadow p-2 m-2 interaction-card">
        <!--remove interaction-card class once back to full layout not just expression of interest-->
        <h1 class="text-center">Interaction</h1>
        <p class="text-center">Engage with your audience during teaching</p>
        <div v-if="deployVersion">
          <h4 class="text-center">Coming soon</h4>
          <div class="input-group m-2">
            <input
              id="interactionInterestEmail"
              type="email"
              placeholder="Email"
              autocomplete="off"
              class="form-control"
              v-model="interactionInterestEmail"
            />
            <button
              type="button"
              id="btnInteractionInterest"
              class="btn btn-teal me-3"
              @click="interactionInterest"
            >
              Register interest
            </button>
          </div>
        </div>
        <div v-else>
          <div class="input-group m-2">
            <input
              id="interactionID"
              type="text"
              placeholder="Session ID"
              autocomplete="off"
              class="form-control"
              v-model="interactionSession.id"
              @keyup.enter="hostInteraction"
            />
            <button
              type="button"
              id="hostInteraction"
              class="btn btn-teal me-3"
              @click="hostInteraction"
            >
              Host session
            </button>
          </div>
          <ul class="nav nav-pills justify-content-between m-2 d-flex gap-2">
            <li class="nav-item mb-2 flex-grow-1 d-flex">
              <button
                class="nav-link active flex-grow-1"
                @click="router.push('/interaction/create')"
              >
                Create a new interaction session
              </button>
            </li>
            <li class="nav-item dropdown flex-grow-1 d-flex more-options">
              <button
                class="nav-link active dropdown-toggle flex-grow-1"
                data-bs-toggle="dropdown"
                href="#"
              >
                More options
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a
                    class="dropdown-item"
                    @click="router.push('/interaction/edit/')"
                    >Edit existing session</a
                  >
                </li>
                <li>
                  <a class="dropdown-item" @click="resetPin('interaction')"
                    >Reset PIN</a
                  >
                </li>
                <li>
                  <a
                    class="dropdown-item"
                    @click="findMySessions('interaction')"
                    >Find my sessions</a
                  >
                </li>
              </ul>
            </li>
          </ul>
          <div class="text-center">
            <small>{{ config.interaction.count }} interactions</small>
          </div>
          <p class="text-center text-danger bg-dark p-1 mt-3">
            <strong>Interaction is in private beta by invitation only</strong>
          </p>
        </div>
      </div>
    </div>
    <Quote />
  </main>
</template>

<style scoped>
.card {
  min-width: 300px;
}
.update-info-card {
  width: 1070px;
}
.nav-link.active {
  background-color: #17a2b8;
  color: black;
}
.nav-link:hover {
  background-color: #00606e;
  color: white;
}
.nav-item,
.nav-link {
  height: 40px;
}
@media only screen and (min-width: 1200px) {
  .more-options {
    margin-left: 15px;
  }
  .interaction-card {
    min-width: 450px;
  }
}
</style>
