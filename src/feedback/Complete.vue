<script setup>
import { ref } from "vue";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import router from "../router";
import Swal from "sweetalert2";
import { inject } from "vue";
const config = inject("config");

if (!feedbackSession.id) router.push("/feedback/");

const orgChange = () => {
  if (
    feedbackSession.attendee.organisation == -1 ||
    feedbackSession.attendee.region == -1
  ) {
    feedbackSession.attendee.region = -1;
    feedbackSession.attendee.organisation = "";
  }
};

let lockForm = ref(false);
const submit = () => {
  document
    .getElementById("fetchCertificateForm")
    .classList.add("was-validated");
  if (!feedbackSession.attendee.name || !feedbackSession.attendee.organisation)
    return false;
  lockForm.value = true;
  console.log('TODO: set action of form to "api/"');
  document.getElementById("fetchCertificateForm").submit();
};
</script>

<template>
  <h1 class="text-center display-4">Feedback</h1>
  <p>
    Thank you, your anonymous feedback will be returned to the session
    facilitator.
  </p>
  <div v-if="feedbackSession.certificate">
    <p>
      The facilitator has provided a certificate for this session for you to
      download. Please fill in the details below to generate your certificate.
    </p>
    <form
      method="post"
      :action="config.api.url"
      id="fetchCertificateForm"
      class="needs-validation"
      novalidate
    >
      <div class="mb-4">
        <label for="attendeeName" class="form-label">Name</label>
        <input
          type="text"
          v-model="feedbackSession.attendee.name"
          class="form-control"
          id="attendeeName"
          placeholder="Your name as it should appear on the certificate...."
          name="attendeeName"
          autocomplete="off"
          :disabled="lockForm"
          required
        />
        <div class="invalid-feedback">
          Please enter your name as you would like it to appear on your
          certificate....
        </div>
      </div>
      <div class="mb-4" v-if="feedbackSession.attendee.region != -1">
        <label for="attendeeRegion" class="form-label">Region</label>
        <select
          v-model="feedbackSession.attendee.region"
          class="form-control"
          id="attendeeRegion"
          name="attendeeRegion"
          autocomplete="off"
          @change="orgChange"
          :disabled="lockForm"
          required
        >
          <option disabled value="">
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
        <div class="invalid-feedback">Please select a region.</div>
      </div>
      <div
        class="mb-4"
        v-if="
          feedbackSession.attendee.region !== '' &&
          feedbackSession.attendee.region != -1
        "
      >
        <label for="attendeeOrganisation" class="form-label"
          >Organisation:</label
        >
        <select
          v-model="feedbackSession.attendee.organisation"
          class="form-control"
          id="attendeeOrganisationSelect"
          name="attendeeOrganisationSelect"
          autocomplete="off"
          @change="orgChange"
          :disabled="lockForm"
          required
        >
          <option disabled value="">Please select your organisation...</option>
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
        <div class="invalid-feedback">Please select an organisation.</div>
      </div>
      <div class="mb=4" v-if="feedbackSession.attendee.region == -1">
        <label for="attendeeOrganisationText" class="form-label"
          >Organisation:</label
        >
        <input
          type="text"
          v-model="feedbackSession.attendee.organisation"
          class="form-control"
          id="attendeeOrganisationText"
          placeholder="Enter your organisation..."
          name="attendeeOrganisationText"
          autocomplete="off"
          required
          :disabled="lockForm"
        />
        <div class="invalid-feedback">Please enter your organisation....</div>
      </div>
      <input value="feedback" type="text" name="module" readonly hidden />
      <input
        value="fetchCertificate"
        type="text"
        name="route"
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
        v-model="feedbackSession.attendee.organisation"
        type="text"
        name="attendeeOrganisation"
        readonly
        hidden
      />
      <p>
        <small
          ><strong
            >These details are not linked to the feedback you have just
            submitted but may be recorded for tracking attendance
            trends.</strong
          ></small
        >
      </p>
      <div class="text-center mt-4">
        <button
          class="btn btn-lg btn-teal"
          id="downloadCertificate"
          @click.prevent="submit"
        >
          Download certificate
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.form-label {
  font-size: 1.3rem;
}
</style>
