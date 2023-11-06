<script setup>
import { ref, onBeforeUnmount } from 'vue';
import router from '../router';
import { feedbackSession } from '../data/feedbackSession.js';
import { config } from '../data/config.js';
import Toast from '../assets/Toast.js';
import Swal from 'sweetalert2';

if (!feedbackSession.id || !feedbackSession.pin)
  router.push('/feedback/create');

const link = ref({});
link.value.give = config.client.url + '/feedback/' + feedbackSession.id;
link.value.view = config.client.url + '/feedback/host/' + feedbackSession.id;
link.value.qr =
  'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=' +
  link.value.join +
  '&choe=UTF-8&chld=h|1';
let clipboard = ref(false);
if (navigator.clipboard) clipboard.value = true;
const copyText = (string) => {
  if (!clipboard.value) return;
  navigator.clipboard.writeText(string).then(
    function () {
      Toast.fire({
        icon: 'success',
        title: 'Copied',
      });
    },
    function (error) {
      Toast.fire({
        icon: 'error',
        title: 'Error copying to clipboard: ' + error,
      });
    }
  );
};
const copyImg = async (src) => {
  if (!clipboard.value) return;
  const response = await fetch(src);
  const blob = await response.blob();
  navigator.clipboard.write([new ClipboardItem({ 'image/png': blob })]).then(
    function () {
      Toast.fire({
        icon: 'success',
        title: 'Copied',
      });
    },
    function (error) {
      Toast.fire({
        icon: 'error',
        title: 'Error copying to clipboard: ' + error,
      });
    }
  );
};

onBeforeUnmount(() => {
  feedbackSession.id = '';
  feedbackSession.pin = '';
  feedbackSession.name = '';
  feedbackSession.title = '';
  feedbackSession.date = '';
  feedbackSession.email = '';
});
</script>

<template v-once>
  <h1 class="text-center display-4">Feedback</h1>
  <p>Your session was created successfully.</p>
  <div class="d-flex flex-wrap flex-fill justify-content-around">
    <p
      @click="copyText(feedbackSession.id)"
      class="display-6 mx-4 p-4 shadow align-top"
    >
      ID: <strong>{{ feedbackSession.id }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
    <p @click="copyText(feedbackSession.pin)" class="display-6 mx-4 p-4 shadow">
      PIN: <strong>{{ feedbackSession.pin }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
  </div>
  <p>
    Please check your inbox to ensure you received the confirmation email. If it
    didn't arrive, take a look in your junk mail, or be sure to make a note of
    these details. Add mail@learnloop.co.uk your safe senders list for next
    time.
  </p>

  <h2>How to direct attendees to the feedback form</h2>
  <div class="d-flex justify-content-around align-items-center flex-wrap">
    <div>
      <div class="text-center p-4 mb-4 shadow">
        <p @click="copyText(link.join)" class="display-6">
          {{ link.join.replace('https://', '') }}
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
      config.client.url.replace('https://', '')
    }}</a>
    and enter the session ID: <strong>{{ feedbackSession.id }}</strong> in the
    feedback panel, or you can use the QR code above if and embed this in a
    presentation.
  </p>

  <h2>How to view your feedback</h2>
  <div class="d-flex text-center justify-content-around">
    <p @click="copyText(link.view)" class="display-6 p-4 shadow">
      {{ link.host.replace('https://', '') }}
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
</template>

<style scoped></style>
