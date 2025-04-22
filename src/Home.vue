<script setup>
/**
 * @module Home
 * @summary Main home page logic for LearnLoop
 * @description
 * This module handles homepage routing, email interest registration, session routing,
 * feedback/interaction session management (reset PIN, notification preferences, session closure, etc),
 * and legacy support for query parameters from LearnLoop v4.
 *
 * @requires vue
 * @requires vue-router
 * @requires ./data/api.js
 * @requires ./router
 * @requires ./components/Quote.vue
 * @requires sweetalert2
 * @requires ./data/feedbackSession.js
 * @requires ./data/interactionSession.js
 */

import { onMounted, ref, inject } from "vue";
import { useRouter } from "vue-router";
import router from "./router";
import Swal from "sweetalert2";
import { api } from "./data/api.js";
import { feedbackSession } from "./data/feedbackSession.js";
import { interactionSession } from "./data/interactionSession.js";
import Quote from "./components/Quote.vue";

const config = inject("config");
const deployVersion = false;

const interactionInterestEmail = ref("");

/**
 * Submits an email to register interest in the interaction module.
 * Shows success/error message based on server response.
 */
const interactionInterest = async () => {
  if (!interactionInterestEmail.value) {
    document
      .getElementById("interactionInterestEmail")
      ?.classList.add("is-invalid");
    return;
  }

  document
    .getElementById("interactionInterestEmail")
    ?.classList.remove("is-invalid");

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
};

/**
 * Navigates to the feedback view page if session ID exists.
 */
const viewFeedback = () => {
  const id = feedbackSession.id?.trim();
  if (!id) {
    document.getElementById("feedbackID")?.classList.add("is-invalid");
    return;
  }

  document.getElementById("feedbackID")?.classList.remove("is-invalid");
  router.push("/feedback/view/" + id);
};

/**
 * Navigates to the interaction host page if session ID exists.
 */
const hostInteraction = () => {
  const id = interactionSession.id?.trim();
  if (!id) {
    document.getElementById("interactionID")?.classList.add("is-invalid");
    return;
  }

  document.getElementById("interactionID")?.classList.remove("is-invalid");
  router.push("/interaction/host/" + id);
};

/**
 * Resets the facilitator PIN for a session.
 * @param {string} module - 'feedback' or 'interaction'
 * @param {string} id - Optional session ID to prefill
 */
const resetPin = async (module, id = "") => {
  let email = "";
  router.push("/");

  const { isConfirmed } = await Swal.fire({
    title: "Reset PIN",
    html: `
      <div class='overflow-hidden'>
        You will need your session ID from the session email.<br>
        For example: ${config.value.client.url.replace(
          "https://",
          ""
        )}/<mark>aBc123</mark>.<br>
        <input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="${id}">
        <input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId")?.value.trim();
      email = document.getElementById("swalFormEmail")?.value.trim();

      if (!email) return Swal.showValidationMessage("Please enter an email");
      if (!id) return Swal.showValidationMessage("Please enter a session ID");

      try {
        const response = await api(`${module}/resetPin`, { id, email });
        let html = response.message;

        if (response.sendMailFails.length) {
          html +=
            "<br><br>New pin emails to the following recipients failed:<br>";
          for (const fail of response.sendMailFails) {
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
        }

        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html,
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

/**
 * Allows facilitators to update session notification preferences.
 * @param {string} id - Optional session ID to prefill
 */
const setNotificationPreference = async (id = "") => {
  let pin = "";
  router.push("/");

  const { isConfirmed } = await Swal.fire({
    title: "Set notification preferences",
    html: `
      <div class='overflow-hidden'>
        You will need your session ID and PIN from the session email.
        <input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="${id}">
        <input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input"><br><br>
        Set notifications <select id="swalFormNotifications" class="swal2-input">
          <option value=true>On</option>
          <option value=false>Off</option>
        </select>
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId")?.value.trim();
      pin = document.getElementById("swalFormPin")?.value.trim();
      const notifications =
        document.getElementById("swalFormNotifications")?.value === "true";

      if (!pin) return Swal.showValidationMessage("Please enter your PIN");
      if (!id) return Swal.showValidationMessage("Please enter a session ID");

      try {
        const response = await api("feedback/updateNotificationPreferences", {
          id,
          pin,
          notifications,
        });
        let html = response.message;

        if (response.sendMailFails.length) {
          html += "<br><br>Notification emails failed for:<br>";
          for (const fail of response.sendMailFails) {
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
        }

        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html,
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

  if (isConfirmed) feedbackSession.reset();
};

/**
 * Facilitators can request an email listing all their sessions.
 * @param {string} module - 'feedback' or 'interaction'
 */
const findMySessions = async (module) => {
  let email = "";

  const { isConfirmed } = await Swal.fire({
    title: "Find my sessions",
    html: `
      <div class='overflow-hidden'>
        Enter your email and we’ll email you a list of your sessions.<br>
        <input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      email = document.getElementById("swalFormEmail")?.value.trim();
      if (!email) return Swal.showValidationMessage("Please enter an email");

      try {
        const response = await api(`${module}/findMySessions`, { email });
        let html = response.message;

        if (response.sendMailFails.length) {
          html += "<br>Session history emails failed for:<br>";
          for (const fail of response.sendMailFails) {
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
        }

        Swal.fire({
          icon: response.sendMailFails.length ? "error" : "success",
          iconColor: "#17a2b8",
          html,
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

/**
 * Allows a facilitator to permanently close a feedback session.
 */
const closeFeedbackSession = async () => {
  let id = "";
  let pin = "";

  const { isConfirmed } = await Swal.fire({
    title: "Close session",
    html: `
      <div class='overflow-hidden'>
        You will need your session ID and PIN from your confirmation email.<br><br>
        Once closed, a session cannot be reopened.
        <input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">
        <input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input">
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: async () => {
      id = document.getElementById("swalFormId")?.value.trim();
      pin = document.getElementById("swalFormPin")?.value.trim();

      if (!pin) return Swal.showValidationMessage("Please enter a session PIN");
      if (!id) return Swal.showValidationMessage("Please enter a session ID");

      try {
        const response = await api("feedback/closeSession", { id, pin });
        let html = response.message;

        if (response.sendMailFails.length) {
          html += "<br><br>Session closure emails failed for:<br>";
          for (const fail of response.sendMailFails) {
            html += `${fail.name} (${fail.email}): <span class='text-danger'><i>${fail.error}</i></span><br>`;
          }
        }

        Swal.fire({
          icon: "success",
          iconColor: "#17a2b8",
          html,
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

/**
 * Runs on page load. Handles legacy URL support and router redirection.
 */
onMounted(() => {
  feedbackSession.reset();
  interactionSession.reset();

  const route = useRouter().currentRoute.value;
  const id = route.params.id;
  const routeName = route.name;

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

  if (routeName === "interaction-resetPIN") resetPin("interaction", id);
  else if (routeName === "feedback-resetPIN") resetPin("feedback", id);
  else if (routeName === "feedback-notifications")
    setNotificationPreference(id);
  else if (routeName === "home" && id) {
    id.charAt(0) === "i"
      ? router.push("/interaction/" + id)
      : router.push("/feedback/" + id);
  } else {
    router.push("/");
  }
});
</script>

<template>
  <!--dev alert-->
  <div class="alert alert-danger mt-3" show role="alert" v-if="!deployVersion">
    <div class="d-flex justify-content-between">
      <h4 class="alert-heading">Development version</h4>
    </div>
    Welcome to LearnLoop v{{ config.version }}.
    <a
      href="https://github.com/dan-leach/learnloop/blob/v6/changelog.md"
      target="_blank"
      style="color: black"
      >View the changelog here</a
    >.<br />This version is under active development so it might behave in
    unpredictable ways. Please
    <a href="mailto:web@danleach.uk" target="_blank" style="color: black"
      >let me know</a
    >
    about any issues you find.<br />If you prefer to wait until the update is
    formally released, please use
    <a href="https://learnloop.co.uk" style="color: black">LearnLoop.co.uk</a>
    instead.
  </div>

  <!--update card-->
  <div class="d-flex justify-content-around mt-3">
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
              details including the organiser details, custom questions, and any
              sessions will be automatically populated from your template
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
              character limit for free-text questions, and all questions can be
              required or optional.
            </li>
            <li class="mb-2">
              You now have the option to select
              <strong>deliver on multiple dates</strong> if you want to gather
              feedback in one place for a session you are delivering more than
              once.
            </li>
            <li>
              <strong>Coming soon:</strong> Interaction will allow you to share
              questions and polls with your audience via their smartphone or
              other devices during teaching sessions. Register your interest
              below to be the first to know when it's available.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!--main cards-->
  <div class="row justify-content-center align-items-stretch mt-2">
    <div class="col-12 col-md-8 col-lg-6 mb-3">
      <div class="card bg-transparent shadow p-2 mx-auto h-100">
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
    </div>
    <div class="col-12 col-md-8 col-lg-6 mb-3">
      <div class="card bg-transparent shadow p-2 mx-auto h-100">
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
  </div>

  <!--quote-->
  <Quote />
</template>

<style scoped>
.card {
  max-width: 500px;
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
