<script setup>
import { ref, onBeforeUnmount } from 'vue';
import router from '../router';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Toast from '../assets/Toast.js';
import Swal from 'sweetalert2';

if (!interactSession.id || !interactSession.pin)
  router.push('/interact/create');

const link = ref({});
link.value.join = config.client.url + '/interact/' + interactSession.id;
link.value.host = config.client.url + '/interact/host/' + interactSession.id;
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
        iconColor: '#17a2b8',
        title: 'Copied',
      });
    },
    function (error) {
      Toast.fire({
        icon: 'error',
        iconColor: '#17a2b8',
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
        iconColor: '#17a2b8',
        title: 'Copied',
      });
    },
    function (error) {
      Toast.fire({
        icon: 'error',
        iconColor: '#17a2b8',
        title: 'Error copying to clipboard: ' + error,
      });
    }
  );
};

onBeforeUnmount(() => {
  interactSession.id = '';
  interactSession.pin = '';
  interactSession.name = '';
  interactSession.title = '';
  interactSession.interactions = [];
});
</script>

<template v-once>
  <h1 class="text-center display-4">Interact</h1>
  <p>Your session was created successfully.</p>
  <div class="d-flex flex-wrap flex-fill justify-content-around">
    <p
      @click="copyText(interactSession.id)"
      class="display-6 mx-4 p-4 shadow align-top"
    >
      ID: <strong>{{ interactSession.id }}</strong>
      <button v-if="clipboard" class="btn btn-outline-dark align-middle">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
    <p @click="copyText(interactSession.pin)" class="display-6 mx-4 p-4 shadow">
      PIN: <strong>{{ interactSession.pin }}</strong>
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

  <h2>How to direct attendees to the interact session</h2>
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
        alt="QR code to join interact session"
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
    and enter the session ID: <strong>{{ interactSession.id }}</strong> in the
    interact panel. When you launch the session your screen will also show a QR
    code for attendees to scan with their smartphones. You can use the QR code
    above if you prefer to embed this in a presentation.
  </p>

  <h2>How to launch your interact session</h2>
  <div class="d-flex text-center justify-content-around">
    <p @click="copyText(link.host)" class="display-6 p-4 shadow">
      {{ link.host.replace('https://', '') }}
      <button v-if="clipboard" class="btn btn-outline-dark">
        <font-awesome-icon :icon="['fas', 'copy']" />
      </button>
    </p>
  </div>
  <p>
    Go to the direct link above and enter your session PIN:
    <strong>{{ interactSession.pin }}</strong
    >.
  </p>
</template>

<style scoped></style>
