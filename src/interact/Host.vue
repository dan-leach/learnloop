<script setup>
/*
Todo:
add to goToInteraction an api call to update the facilitatorIndex to then be polled by attendees
*/

import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../router';
import { api } from '../data/api.js';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Swal from 'sweetalert2';
import Loading from '../components/Loading.vue';
import HostInteraction from './components/HostInteraction.vue';

const loading = ref(true);
const showInteraction = ref(true);
const currentIndex = ref(0);

const goToInteraction = (index) => {
  showInteraction.value = false;
  currentIndex.value = index;
  setTimeout(() => {
    showInteraction.value = true;
  }, 250);
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
    JSON.stringify({
      interactionIndex: currentIndex.value,
      lastSubmissionId: lastSubmissionId,
    })
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
      for (let submission of res.newSubmissions) submissions.push(submission);
    },
    function (error) {
      console.log('fetchNewSubmissions failed', error);
    }
  );
};

const fetchDetails = () => {
  api(
    'interact',
    'fetchDetails',
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
  //dev only
  interactSession.id = 'abc';
  interactSession.pin = '123';
  fetchDetails();
  return;
  //
  if (interactSession.id == '/interact/host' || interactSession.id == '') {
    Swal.fire({
      title: 'Enter session ID and PIN',
      html:
        'You will need your session ID and PIN which you can find in the email you received when your session was created. <br>' +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">' +
        '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#007bff',
      preConfirm: () => {
        interactSession.id = document.getElementById('swalFormId').value;
        interactSession.pin = document.getElementById('swalFormPin').value;
        if (interactSession.id == '')
          Swal.showValidationMessage('Please enter a session ID');
        if (interactSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState({}, '', interactSession.id);
        fetchDetails();
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
      confirmButtonColor: '#007bff',
      preConfirm: () => {
        interactSession.pin = document.getElementById('swalFormPin').value;
        if (interactSession.pin == '')
          Swal.showValidationMessage('Please enter your PIN');
      },
    }).then((result) => {
      if (result.isConfirmed) {
        fetchDetails();
      } else {
        router.push('/');
      }
    });
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else id="host-main">
      <h1 class="text-center display-4">Interact</h1>
      <p class="text-center">
        {{ interactSession.title }} | {{ interactSession.name }}
      </p>

      <Transition name="slide-up">
        <HostInteraction
          v-if="showInteraction"
          :currentIndex="currentIndex"
          class="container card m-2"
        />
      </Transition>

      <div class="developer-panel card m-2 mt-5">
        <!--remove this developer panel for deployment-->
        <div class="text-center m-2">
          <span>Developer Panel</span><br />
          <button
            v-if="currentIndex > 0"
            @click="goToInteraction(currentIndex - 1)"
            class="btn btn-teal me-2"
          >
            Previous
          </button>
          <button
            v-if="currentIndex < interactSession.interactions.length - 1"
            @click="goToInteraction(currentIndex + 1)"
            class="btn btn-teal mw-2"
          >
            Next
          </button>
          <br />Interaction: {{ currentIndex + 1 }} of
          {{ interactSession.interactions.length }}
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.developer-panel {
  background-color: lightgrey;
}
</style>
