<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../router';
import { api } from '../data/api.js';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Swal from 'sweetalert2';
import Loading from '../components/Loading.vue';
import HostInteraction from './components/HostInteraction.vue';
import Toast from '../assets/Toast.js';

const loading = ref(true);
const currentIndex = ref(0);

const updateFacilitatorIndex = () => {
  api(
    'interact',
    'updateFacilitatorIndex',
    interactSession.id,
    interactSession.pin,
    currentIndex.value
  ).then(
    function () {},
    function (error) {
      console.log('updateFacilitatorIndex failed', error);
    }
  );
};

const fetchSubmissionCount = () => {
  api(
    'interact',
    'fetchSubmissionCount',
    interactSession.id,
    interactSession.pin,
    null
  ).then(
    function (res) {
      interactSession.submissionCount = res;
    },
    function (error) {
      console.log('fetchSubmissionCount failed', error);
    }
  );
};

const goToInteraction = (index) => {
  if (currentIndex.value == 0 && index == 1) {
    config.client.isFullscreen = true
    Toast.fire({
      icon: 'info',
      title: 'Press F11 to toggle fullscreen.',
    });
  } else if (index == 0) {
    config.client.isFullscreen = false
  }
  currentIndex.value = index;
  updateFacilitatorIndex();
  if (index == 0) fetchSubmissionCount();
  interactSession.interactions[currentIndex.value].submissions = [];
};

const fetchNewSubmissions = () => {
  const submissions =
    interactSession.interactions[currentIndex.value].submissions;
  const lastSubmissionId = submissions.length
    ? submissions[submissions.length - 1].id
    : 0;
  api(
    'interact',
    'fetchNewSubmissions',
    interactSession.id,
    interactSession.pin,
    {
      interactionIndex: currentIndex.value,
      lastSubmissionId: lastSubmissionId,
    }
  ).then(
    function (res) {
      for (let submission of res) submissions.push(submission);
    },
    function (error) {
      console.log('fetchNewSubmissions failed', error);
    }
  );
};

const fetchDetailsHost = () => {
  api(
    'interact',
    'fetchDetailsHost',
    interactSession.id,
    interactSession.pin,
    null
  ).then(
    function (res) {
      if (interactSession.id != res.id) {
        console.error(
          'interactSession.id != res.id',
          interactSession.id,
          response.id
        );
        return;
      }
      interactSession.title = res.title;
      interactSession.name = res.name;
      interactSession.interactions = res.interactions;
      for (let interaction of interactSession.interactions) {
        interaction.submissions = [];
        interaction.submissionsCount = 0;
      }
      loading.value = false;
      fetchSubmissionCount();
      updateFacilitatorIndex();
      fetchNewSubmissions();
      setInterval(
        fetchNewSubmissions,
        config.interact.host.newSubmissionsPollInterval
      );
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable to launch interact session hosting',
        text: error,
      });
      router.push('/');
    }
  );
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.path.replace(
    '/interact/host/',
    ''
  );
  if (
    interactSession.id == '/interact/host' ||
    interactSession.id == '/interact/host/' ||
    interactSession.id == ''
  ) {
    Swal.fire({
      title: 'Enter session ID and PIN',
      html:
        'You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        interactSession.id = document.getElementById('swalFormId').value;
        interactSession.pin = document.getElementById('swalFormPin').value;
        if (interactSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
        if (interactSession.id == '')
          Swal.showValidationMessage('Please enter a session ID');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState({}, '', interactSession.id);
        fetchDetailsHost();
      } else {
        router.push('/');
      }
    });
  } else {
    Swal.fire({
      title: 'Enter session PIN',
      html:
        'You will need your session PIN which you can find in the email you received when your session was created. <br>' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        interactSession.pin = document.getElementById('swalFormPin').value;
        if (interactSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        fetchDetailsHost();
        updateFacilitatorIndex();
      } else {
        router.push('/');
      }
    });
  }
});

onBeforeUnmount(() => {
  api(
    'interact',
    'updateFacilitatorIndex',
    interactSession.id,
    interactSession.pin,
    0
  ).then(
    function () {},
    function (error) {
      console.log('updateFacilitatorIndex failed', error);
    }
  );
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 v-if="!config.client.isFullscreen" class="text-center display-4">
        Interact
      </h1>
      <p v-if="!config.client.isFullscreen" class="text-center">
        {{ interactSession.title }} | {{ interactSession.name }}
      </p>
      <HostInteraction
        :currentIndex="currentIndex"
        class="m-2"
        :class="{ container: !config.client.isFullscreen }"
        @goForward="goToInteraction(currentIndex + 1)"
        @goBack="goToInteraction(currentIndex - 1)"
      />
    </div>
  </Transition>
</template>

<style scoped></style>
