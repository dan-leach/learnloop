<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";
import { inject } from "vue";
const config = inject("config");

let loading = ref(true);

const viewAttendance = () => {
  api("feedback/viewAttendance", {
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
      feedbackSession.name = res.name;
      feedbackSession.attendance = res.attendance;
      loading.value = false;
    },
    function (error) {
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
  );
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;
  Swal.fire({
    title: "Enter session ID and PIN",
    html:
      '<div class="overflow-hidden">You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      feedbackSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
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
      viewAttendance();
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
      <p>
        Viewing attendance report for
        <strong>'{{ feedbackSession.title }}'</strong> by
        <strong>{{ feedbackSession.name }}</strong> on
        <strong>{{ feedbackSession.date }}</strong
        >.
      </p>
      <form method="post" :action="config.api.url">
        <input type="text" name="module" value="feedback" readonly hidden />
        <input
          type="text"
          name="route"
          value="fetchAttendancePDF"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.id"
          type="text"
          name="id"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.pin"
          type="text"
          name="pin"
          readonly
          hidden
        />

        <div class="text-center">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"
                >Download attendance report as PDF</span
              >
            </div>
            <div class="input-group-append">
              <button class="btn btn-primary" id="btnFetchAttendancePDF">
                Go
              </button>
            </div>
          </div>
        </div>
      </form>
      <form method="post" :action="config.api.url">
        <input type="text" name="module" value="feedback" readonly hidden />
        <input
          type="text"
          name="route"
          value="fetchAttendanceCSV"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.id"
          type="text"
          name="id"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.pin"
          type="text"
          name="pin"
          readonly
          hidden
        />

        <div class="text-center">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"
                >Download attendance report as CSV</span
              >
            </div>
            <div class="input-group-append">
              <button class="btn btn-primary" id="btnFetchAttendanceCSV">
                Go
              </button>
            </div>
          </div>
        </div>
      </form>
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
