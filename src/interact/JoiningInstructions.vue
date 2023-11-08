<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import router from '../router';
import { useRouter } from 'vue-router';
import { api } from '../data/api.js';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Swal from 'sweetalert2';
import Toast from '../assets/Toast.js';
import Loading from '../components/Loading.vue';

const link = ref({});
const loading = ref(true);

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

const fetchDetails = () => {
  api('interact', 'fetchDetails', interactSession.id, null, null).then(
    function (res) {
      if (interactSession.id != res.id) {
        console.error(
          'interactSession.id != res.id',
          interactSession.id,
          res.id
        );
        return;
      }
      interactSession.title = res.title;
      interactSession.name = res.name;
      link.value.join = config.client.url + '/interact/' + interactSession.id;
      link.value.host =
        config.client.url + '/interact/host/' + interactSession.id;
      link.value.qr =
        'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=' +
        link.value.join +
        '&choe=UTF-8&chld=h|1';
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        iconColor: '#17a2b8',
        title: 'Unable to fetch interact session details',
        text: error,
        confirmButtonColor: '#17a2b8',
      });
      router.push('/');
    }
  );
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.path.replace(
    '/interact/joining-instructions/',
    ''
  );
  if (
    interactSession.id == '/interact/joining-instructions/' ||
    interactSession.id == '/interact/joining-instructions' ||
    interactSession.id == ''
  ) {
    Swal.fire({
      title: 'Enter session ID',
      html: '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        interactSession.id = document.getElementById('swalFormId').value;
        if (interactSession.id == '')
          Swal.showValidationMessage('Please enter a session ID');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState(
          {},
          '',
          '/interact/joining-instructions/' + interactSession.id
        );
        fetchDetails();
      } else {
        router.push('/');
      }
    });
  } else {
    fetchDetails();
  }
});

onBeforeUnmount(() => {
  interactSession.id = '';
  interactSession.pin = '';
  interactSession.name = '';
  interactSession.title = '';
  interactSession.interactions = [];
});
</script>

<template v-once>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interact</h1>
      <p class="text-center display-6">
        Joining instructions for {{ interactSession.title }}
      </p>
      <div
        @click="copyImg(link.qr)"
        class="d-flex justify-content-center align-items-center p-4 mb-4"
      >
        <img
          :src="link.qr"
          class="me-2"
          alt="QR code to join interact session"
          height="300"
        />
        <div>
          <button v-if="clipboard" class="btn btn-outline-dark">
            <font-awesome-icon :icon="['fas', 'copy']" />
          </button>
        </div>
      </div>
      <div class="text-center p-4 mb-4">
        <p @click="copyText(link.join)" class="display-6">
          {{ link.join.replace('https://', '') }}
          <button v-if="clipboard" class="btn btn-outline-dark">
            <font-awesome-icon :icon="['fas', 'copy']" />
          </button>
        </p>
        <p>
          Or, go to
          <a :href="config.client.url + '/interact'">{{
            config.client.url.replace('https://', '') + '/interact'
          }}</a>
          and enter the session ID: <strong>{{ interactSession.id }}</strong>
        </p>
      </div>
    </div>
  </Transition>
</template>

<style scoped></style>
