<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { api } from "./data/api.js";
import router from "./router";
import Quote from "./components/Quote.vue";
import Swal from "sweetalert2";
import { feedbackSession } from "./data/feedbackSession.js";
import { interactionSession } from "./data/interactionSession.js";

import { inject } from "vue";
const config = inject("config");

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

const resetPin = (module, id) => {
  if (!id) id = "";
  let email = "";
  Swal.fire({
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
      id = document.getElementById("swalFormId").value;
      email = document.getElementById("swalFormEmail").value;
      if (email == "") {
        Swal.showValidationMessage("Please enter an email");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      await api(module + "/resetPin", { id, email }).then(
        function (res) {
          let html = res.message;
          if (res.sendMailFails.length) {
            html +=
              "<br><br>New pin emails to the following recepients failed:<br>";
            for (let fail of res.sendMailFails)
              html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
          Swal.fire({
            icon: "success",
            iconColor: "#17a2b8",
            html: html,
            confirmButtonColor: "#17a2b8",
          });
        },
        function (error) {
          if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
          Swal.fire({
            icon: "error",
            iconColor: "#17a2b8",
            text: error,
            confirmButtonColor: "#17a2b8",
          });
        }
      );
    },
  }).then((result) => {
    if (result.isConfirmed) {
      feedbackSession.reset();
      interactionSession.reset();
    }
  });
};

const setNotificationPreference = (id) => {
  if (!id) id = "";
  let pin = "";
  let notifications = true;
  Swal.fire({
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
      id = document.getElementById("swalFormId").value;
      pin = document.getElementById("swalFormPin").value;
      if (pin == "") {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      notifications = document.getElementById("swalFormNotifications").value;
      await api("feedback/updateNotificationPreferences", {
        id,
        pin,
        notifications: notifications === "true" ? true : false,
      }).then(
        function (res) {
          let html = res.message;
          if (res.sendMailFails.length) {
            html +=
              "<br><br>Notification preference emails to the following recepients failed:<br>";
            for (let fail of res.sendMailFails)
              html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
          Swal.fire({
            icon: "success",
            iconColor: "#17a2b8",
            html: html,
            confirmButtonColor: "#17a2b8",
          });
        },
        function (error) {
          if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
          Swal.fire({
            icon: "error",
            iconColor: "#17a2b8",
            text: error,
            confirmButtonColor: "#17a2b8",
          });
        }
      );
    },
  }).then((result) => {
    if (result.isConfirmed) {
      feedbackSession.reset();
    }
  });
};

const findMySessions = (module) => {
  let email = "";
  Swal.fire({
    title: "Find my sessions",
    html:
      "<div class='overflow-hidden'>Enter your email below and we'll email you with a list of any sessions you've created previously." +
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      email = document.getElementById("swalFormEmail").value;
      if (email == "") {
        Swal.showValidationMessage("Please enter an email");
        return false;
      }
      await api(
        module,
        "findMySessions",
        null,
        null,
        JSON.stringify(email)
      ).then(
        function (res) {
          Swal.fire({
            icon: "success",
            iconColor: "#17a2b8",
            text: res,
            confirmButtonColor: "#17a2b8",
          });
        },
        function (error) {
          Swal.fire({
            icon: "error",
            iconColor: "#17a2b8",
            text: error,
            confirmButtonColor: "#17a2b8",
          });
        }
      );
    },
  }).then((result) => {
    if (result.isConfirmed) {
      feedbackSession.reset();
      interactionSession.reset();
    }
  });
};

const closeFeedbackSession = () => {
  let id = "";
  let pin = "";
  Swal.fire({
    title: "Close session",
    html:
      "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created.<br><br>Please be aware that once closed a session cannot be reopend to further feedback.<br>" +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
      '<input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId").value;
      pin = document.getElementById("swalFormPin").value;
      if (pin == "") {
        Swal.showValidationMessage("Please enter a session PIN");
        return false;
      }
      if (id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      await api("feedback/closeSession", { id, pin }).then(
        function (res) {
          let html = res.message;
          if (res.sendMailFails.length) {
            html +=
              "<br><br>Session closure emails to the following recepients failed:<br>";
            for (let fail of res.sendMailFails)
              html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
          Swal.fire({
            icon: "success",
            iconColor: "#17a2b8",
            html: html,
            confirmButtonColor: "#17a2b8",
          });
        },
        function (error) {
          if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
          Swal.fire({
            icon: "error",
            iconColor: "#17a2b8",
            text: error,
            confirmButtonColor: "#17a2b8",
          });
        }
      );
    },
  }).then((result) => {
    if (result.isConfirmed) {
      feedbackSession.reset();
      interactionSession.reset();
    }
  });
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
    <p class="text-center fs-6 mt-2">
      Welcome to LearnLoop v{{ config.version }}. This version is under active
      development so it might behave in unpredictable ways. Please
      <a href="mailto:web@danleach.uk" target="_blank" style="color: black"
        >let me know</a
      >
      about any issues you find.
      <a
        href="https://github.com/dan-leach/learnloop/blob/v5/changelog.md"
        target="_blank"
        style="color: black"
        >View the changelog here</a
      >. If you prefer to wait until the update is formally released, please use
      <a href="https://learnloop.co.uk" style="color: black">LearnLoop.co.uk</a>
      instead.
    </p>
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
      </div>
      <div class="card bg-transparent shadow p-2 m-2">
        <h1 class="text-center">Interaction</h1>
        <p class="text-center">Engage with your audience interactively</p>
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
                <a class="dropdown-item" @click="findMySessions('interaction')"
                  >Find my sessions</a
                >
              </li>
            </ul>
          </li>
        </ul>
        <p class="text-center text-danger bg-dark p-1 mt-3">
          <strong>Interaction is in private beta by invitation only</strong>
        </p>
      </div>
    </div>
    <Quote />
  </main>
</template>

<style scoped>
.card {
  min-width: 300px;
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
}
</style>
