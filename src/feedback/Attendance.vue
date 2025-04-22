<script setup>
/**
 * @module feedback/Attendance
 * @summary Attendance viewer for feedback sessions.
 * @description Handles PIN-protected access to session attendance, and provides option to download a report as PDF.
 * @requires vue
 * @requires vue-router
 * @requires ../data/feedbackSession.js
 * @requires ../data/api.js
 * @requires ../router
 * @requires ../components/Loading.vue
 * @requires sweetalert2
 * @requires ../assets/Toast.js
 */

import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";

let loading = ref(true);

/**
 * Fetches attendance data from the backend.
 * Validates session match before updating local state.
 * @memberof module:feedback/Attendance
 * @returns {Promise<void>}
 */
const viewAttendance = async () => {
  try {
    const response = await api("feedback/viewAttendance", {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
    });

    if (feedbackSession.id !== response.id) {
      console.error(
        "feedbackSession.id mismatch",
        feedbackSession.id,
        response.id
      );
      return;
    }

    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.name = response.name;
    feedbackSession.attendance = response.attendance;
    loading.value = false;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load attendance report",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    router.push("/");
  }
};

/**
 * Downloads the attendance report as a PDF.
 * Uses browser blob handling and fires a success toast on completion.
 * @memberof module:feedback/Attendance
 * @returns {Promise<void>}
 */
const fetchAttendancePDF = async () => {
  try {
    const response = await api(
      "feedback/fetchAttendancePDF",
      {
        id: feedbackSession.id,
        pin: feedbackSession.pin,
      },
      "blob"
    );

    const a = document.createElement("a");
    a.href = response;
    a.download = `${feedbackSession.title} attendance.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(response);

    Toast.fire({
      icon: "success",
      iconColor: "#17a2b8",
      title: "The attendance report should now be downloading.",
    });
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Error",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
  }
};

/**
 * Mounted lifecycle logic to prompt user for session ID and PIN,
 * then fetch attendance data if valid.
 * @memberof module:feedback/Attendance
 */
onMounted(async () => {
  // Try to prefill session ID from route
  feedbackSession.id = useRouter().currentRoute.value.params.id;

  const { isConfirmed } = await Swal.fire({
    title: "Enter session ID and PIN",
    html:
      '<div class="overflow-hidden">You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
      `<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="${feedbackSession.id}">` +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value.trim();
      feedbackSession.pin = document.getElementById("swalFormPin").value.trim();
      if (!feedbackSession.pin)
        Swal.showValidationMessage("Please enter your PIN");
      if (!feedbackSession.id)
        Swal.showValidationMessage("Please enter a session ID");
    },
  });

  if (isConfirmed) {
    history.replaceState({}, "", feedbackSession.id);
    viewAttendance();
  } else {
    router.push("/");
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
      <p>
        Viewing attendance report for
        <strong>'{{ feedbackSession.title }}'</strong> by
        <strong>{{ feedbackSession.name }}</strong> on
        <strong>{{ feedbackSession.date }}</strong
        >.
      </p>
      <button
        class="btn btn-teal mb-3"
        id="btnDownloadPDF"
        @click="fetchAttendancePDF"
      >
        Download attendance report as PDF
      </button>
      <br />
      <h4>
        Total attendees
        <span class="badge rounded-pill bg-teal">{{
          feedbackSession.attendance.count
        }}</span>
      </h4>
      <br />
      <div v-for="region in feedbackSession.attendance.regions" class="mb-4">
        <button
          type="button"
          class="btn ps-0 position-relative"
          v-if="feedbackSession.attendance.regions.length > 1"
        >
          <h5 class="mb-0">
            {{ region.name }}
            <span class="badge rounded-pill bg-teal">
              {{ region.count }}
            </span>
          </h5>
        </button>
        <div v-for="organisation in region.organisations" class="mb-3">
          <h6 class="mb-0">
            {{ organisation.name }}
            <span class="badge rounded-pill bg-teal">{{
              organisation.count
            }}</span>
          </h6>
          <span v-for="(name, index) in organisation.attendees"
            >{{ name
            }}<span v-if="index != organisation.attendees.length - 1"
              >,
            </span></span
          >
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped></style>
