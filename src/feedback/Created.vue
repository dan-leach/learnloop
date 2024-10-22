<script setup>
import { ref } from "vue";
import router from "../router";
import { feedbackSession } from "../data/feedbackSession.js";
import Toast from "../assets/Toast.js";
import { inject } from "vue";
const config = inject("config");

if (!feedbackSession.id || !feedbackSession.pin) {
  router.push("/feedback/create");
}

const leadOrganiserIndex = ref(0);
if (feedbackSession.organisers.length > 1) {
  leadOrganiserIndex.value = feedbackSession.organisers.findIndex(
    (organiser) => organiser.isLead
  );
}

const link = ref({});
link.value.give = config.value.client.url + "/" + feedbackSession.id;
link.value.view =
  config.value.client.url + "/feedback/view/" + feedbackSession.id;
link.value.qr = config.value.api.url + "qrcode/?id=" + feedbackSession.id;
let clipboard = ref(false);
if (navigator.clipboard) clipboard.value = true;
const copyText = (string) => {
  if (!clipboard.value) return;
  navigator.clipboard.writeText(string).then(
    function () {
      Toast.fire({
        icon: "success",
        iconColor: "#17a2b8",
        iconColor: "#17a2b8",
        title: "Copied",
      });
    },
    function (error) {
      Toast.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Error copying to clipboard: " + error,
      });
    }
  );
};
const copyImg = async (src) => {
  if (!clipboard.value) return;
  const response = await fetch(src);
  const blob = await response.blob();
  navigator.clipboard.write([new ClipboardItem({ "image/png": blob })]).then(
    function () {
      Toast.fire({
        icon: "success",
        iconColor: "#17a2b8",
        title: "Copied",
      });
    },
    function (error) {
      Toast.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Error copying to clipboard: " + error,
      });
    }
  );
};
</script>

<template v-once>
  <h1 class="text-center display-4">Feedback</h1>
  <p>Your session was created successfully.</p>
  <p v-if="feedbackSession.sendMailFails.length" class="text-danger">Session creation emails to the following recepients failed:
    <ul>
      <li v-for="fail in feedbackSession.sendMailFails">{{ fail.name }} ({{ fail.email }}): <i>{{ fail.error }}</i></li>
    </ul>
  </p>
  <div class="d-flex flex-wrap flex-fill justify-content-around">
    <p
      @click="copyText(feedbackSession.id)"
      class="display-6 mx-4 p-4 shadow align-top"
    >
      ID: <strong>{{ feedbackSession.id }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle ms-2">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
    <p @click="copyText(feedbackSession.pin)" class="display-6 mx-4 p-4 shadow">
      PIN: <strong>{{ feedbackSession.pin }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle ms-2">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
  </div>
  <p v-if="feedbackSession.organisers.length > 1">
    The PIN shown above is for
    {{ feedbackSession.organisers[leadOrganiserIndex].name }}, the lead
    organiser. The PIN for other organisers will be sent to them by email.
  </p>
  <p>
    <strong
      >All details on this page are included in your confirmation email.</strong
    ><br />
    Please check your inbox (or your junk mail) to ensure you received it. If it
    didn't arrive be sure to make a note of these details. Add
    {{ config.noreplyemail }} your safe senders list for next time.
  </p>
  <div class="accordion" id="accordionCreated">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingOne">
        <button
          class="accordion-button collapsed"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseOne"
          aria-expanded="true"
          aria-controls="collapseOne"
        >
          <h4>How to direct attendees to the feedback form</h4>
        </button>
      </h2>
      <div
        id="collapseOne"
        class="accordion-collapse collapse"
        aria-labelledby="headingOne"
        data-bs-parent="#accordionCreated"
      >
        <div class="accordion-body">
          <div
            class="d-flex justify-content-around align-items-center flex-wrap"
          >
            <div>
              <div class="text-center p-4 mb-4 shadow">
                <p @click="copyText(link.give)" class="display-6">
                  {{ link.give.replace("https://", "") }}
                  <button v-if="clipboard" class="btn btn-outline-dark">
                    <font-awesome-icon :icon="['fas', 'copy']" />
                  </button>
                </p>
              </div>
            </div>
            <div
              @click="copyImg(link.qr)"
              class="d-flex align-items-center p-4 mb-4 shadow"
            >
              <img
                :src="link.qr"
                class="me-2"
                alt="QR code to the feedback form"
                height="150"
              />
              <div>
                <button v-if="clipboard" class="btn btn-outline-dark">
                  <font-awesome-icon :icon="['fas', 'copy']" />
                </button>
              </div>
            </div>
          </div>
          <p>
            Provide the direct link above or ask them to go to
            <a :href="config.client.url">{{
              config.client.url.replace("https://", "")
            }}</a>
            and enter the session ID:
            <strong>{{ feedbackSession.id }}</strong> in the "Enter a code" box,
            or you can use the QR code above and embed this in a presentation.
          </p>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTwo">
        <button
          class="accordion-button collapsed"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseTwo"
          aria-expanded="true"
          aria-controls="collapseTwo"
        >
          <h4>How to view your feedback</h4>
        </button>
      </h2>
      <div
        id="collapseTwo"
        class="accordion-collapse collapse"
        aria-labelledby="headingTwo"
        data-bs-parent="#accordionCreated"
      >
        <div class="accordion-body">
          <div class="d-flex text-center justify-content-around">
            <p @click="copyText(link.view)" class="display-6 p-4 shadow">
              {{ link.view.replace("https://", "") }}
              <button v-if="clipboard" class="btn btn-outline-dark">
                <font-awesome-icon :icon="['fas', 'copy']" />
              </button>
            </p>
          </div>
          <p>
            Go to the direct link above and enter your session PIN:
            <strong>{{ feedbackSession.pin }}</strong
            >.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media only screen and (max-width: 600px) {
  .display-6 {
    font-size: 1.4rem;
  }
}
.pin-info {
  font-size: medium;
}
</style>
