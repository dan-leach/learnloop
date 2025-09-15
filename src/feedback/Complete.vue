<script setup>
/**
 * @module feedback/Complete
 * @summary Certificate download form for feedback session attendees.
 * @description Allows users to submit their details and download a certificate of attendance for a completed feedback session.
 * @requires vue
 * @requires ../data/feedbackSession.js
 * @requires ../data/api.js
 * @requires ../router
 * @requires sweetalert2
 * @requires ../assets/Toast.js
 */

import { ref, inject } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import router from "../router";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";

// Inject config provided globally (e.g. via app.provide)
const config = inject("config");

// Redirect if accessed without an active feedback session
if (!feedbackSession.id) router.push("/feedback/");

/**
 * Handles logic for selecting a custom organisation.
 * @memberof module:feedback/Complete
 */
const orgChange = () => {
  if (
    feedbackSession.attendee.organisation === -1 ||
    feedbackSession.attendee.region === -1
  ) {
    feedbackSession.attendee.region = -1;
    feedbackSession.attendee.organisation = "";
  }
};

let lockForm = ref(false);

/**
 * Validates and submits the certificate request form.
 * On success, triggers download of certificate PDF.
 * @memberof module:feedback/Complete
 * @returns {Promise<void>}
 */
const submit = async () => {
  document
    .getElementById("fetchCertificateForm")
    .classList.add("was-validated");

  const attendee = feedbackSession.attendee;

  // Basic validation
  if (
    !attendee.name ||
    !Number.isInteger(attendee.region) ||
    !attendee.organisation
  ) {
    return false;
  }

  lockForm.value = true;

  // Convert region ID to name, or "Other"
  const region =
    attendee.region === -1
      ? "Other"
      : config.value.client.regions[attendee.region].name;

  try {
    // Request certificate as blob
    const response = await api(
      "feedback/fetchCertificate",
      {
        id: feedbackSession.id,
        attendee: {
          name: attendee.name,
          region,
          organisation: attendee.organisation,
        },
      },
      "blob"
    );

    // Trigger browser download
    const a = document.createElement("a");
    a.href = response;
    a.download = `${feedbackSession.title} certificate.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(response);

    Toast.fire({
      icon: "success",
      iconColor: "#17a2b8",
      title: "Your certificate should now be downloading.",
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
</script>

<template>
  <div id="downloadCertificateForm" class="container my-4">
    <h1 class="text-center display-4">Feedback</h1>
    <div class="text-center">
      <p>
        Thank you, your anonymous feedback will be returned to the session
        facilitator.
      </p>
    </div>
    <div v-if="feedbackSession.certificate">
      <form
        method="post"
        :action="config.api.url + 'feedback/fetchCertificate'"
        id="fetchCertificateForm"
        class="needs-validation"
        novalidate
      >
        <div class="alert alert-teal" alert-dismissible fade show role="alert">
          <div class="d-flex justify-content-between">
            <h4 class="alert-heading">Certificate of Attendance</h4>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="alert"
              aria-label="Close"
            ></button>
          </div>
          <span
            >The facilitator has provided a certificate for this session for you
            to download.</span
          ><br /><span
            >Please fill in the details below to generate your
            certificate.</span
          ><br />
          <span
            >The details you provide below are not linked to your feedback{{
              feedbackSession.attendance
                ? " but is recorded for tracking attendance"
                : ""
            }}.</span
          >
        </div>

        <!--name-->
        <div class="form-floating mb-3">
          <input
            type="text"
            v-model="feedbackSession.attendee.name"
            class="form-control"
            id="attendeeName"
            placeholder=""
            name="attendeeName"
            autocomplete="off"
            :disabled="lockForm"
            v-focus-collapse="'nameHelp'"
            required
          />
          <label for="attendeeName" class="form-label">Name</label>
          <div class="invalid-feedback">
            Please enter your name as you would like it to appear on your
            certificate....
          </div>
          <div class="collapse form-text mx-1" id="nameHelp">
            <span
              >Enter your name as you would like it to appear on your
              certificate.</span
            >
          </div>
        </div>

        <!--region-->
        <div
          class="form-floating mb-3"
          v-if="feedbackSession.attendee.region != -1"
        >
          <select
            v-model="feedbackSession.attendee.region"
            class="form-select"
            id="attendeeRegion"
            name="attendeeRegion"
            autocomplete="off"
            @change="orgChange"
            :disabled="lockForm"
            required
          >
            <option selected disabled value="">
              Please select the region for your organisation...
            </option>
            <option
              v-for="(region, index) in config.client.regions"
              :value="index"
            >
              {{ region.name }}
            </option>
            <option value="-1">Other...</option>
          </select>
          <label for="attendeeRegion" class="form-label">Region</label>
          <div class="invalid-feedback">Please select a region.</div>
        </div>

        <!--organisation select-->
        <div
          class="form-floating mb-3"
          v-if="
            feedbackSession.attendee.region !== '' &&
            feedbackSession.attendee.region != -1
          "
        >
          <select
            v-model="feedbackSession.attendee.organisation"
            class="form-select"
            id="attendeeOrganisationSelect"
            name="attendeeOrganisationSelect"
            autocomplete="off"
            @change="orgChange"
            :disabled="lockForm"
            required
          >
            <option disabled value="">
              Please select your organisation...
            </option>
            <option
              v-for="organisation in config.client.regions[
                feedbackSession.attendee.region
              ].organisations"
              :value="organisation"
            >
              {{ organisation }}
            </option>
            <option value="-1">Other...</option>
          </select>
          <label for="attendeeOrganisation" class="form-label"
            >Organisation</label
          >
          <div class="invalid-feedback">Please select an organisation.</div>
        </div>

        <!--organisation free-text-->
        <div
          class="form-floating mb-3"
          v-if="feedbackSession.attendee.region == -1"
        >
          <input
            type="text"
            v-model="feedbackSession.attendee.organisation"
            class="form-control"
            id="attendeeOrganisationText"
            placeholder=""
            name="attendeeOrganisationText"
            autocomplete="off"
            required
            :disabled="lockForm"
          />
          <label for="attendeeOrganisationText" class="form-label"
            >Organisation</label
          >
          <div class="invalid-feedback">Please enter your organisation....</div>
        </div>
        <input
          v-model="feedbackSession.id"
          type="text"
          name="id"
          readonly
          hidden
        />
        <input
          v-model="feedbackSession.attendee.organisation"
          type="text"
          name="attendeeOrganisation"
          readonly
          hidden
        />
        <div class="text-center mt-4">
          <button
            class="btn btn-lg btn-teal"
            id="fetchCertificate"
            @click.prevent="submit"
          >
            Download certificate
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 750px;
}
.pdf-container {
  height: 95vh;
}
</style>
