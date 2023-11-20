<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import router from '../router';
import { api } from '../data/api.js';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Swal from 'sweetalert2';
import Loading from '../components/Loading.vue';
import JoinInteraction from './components/JoinInteraction.vue';

const loading = ref(true);
const showInteraction = ref(true);
const currentIndex = ref(0);
const facilitatorIndex = ref(0);

const goToInteraction = (index) => {
  showInteraction.value = false;
  currentIndex.value = index;
  setTimeout(() => {
    showInteraction.value = true;
  }, 250);
};

const fetchFacilitatorIndex = () => {
  api('interact', 'fetchFacilitatorIndex', interactSession.id, null, null).then(
    function (res) {
      facilitatorIndex.value = res;
      console.log(
        currentIndex.value,
        interactSession.interactions[currentIndex.value].response,
        interactSession.interactions[currentIndex.value].closed
      );
      if (
        currentIndex.value == 0 ||
        interactSession.interactions[currentIndex.value].response === '' ||
        interactSession.interactions[currentIndex.value].response ===
          undefined ||
        interactSession.interactions[currentIndex.value].closed
      ) {
        if (currentIndex.value != facilitatorIndex.value)
          goToInteraction(facilitatorIndex.value);
      }
    },
    function (error) {
      console.log('fetchFacilitatorIndex failed', error);
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
      interactSession.interactions = res.interactions;
      for (let interaction of interactSession.interactions) interaction.submissionCount = 0
      facilitatorIndex.value = res.facilitatorIndex;
      currentIndex.value = res.facilitatorIndex;
      loading.value = false;
      setInterval(
        fetchFacilitatorIndex,
        config.interact.join.currentIndexPollInterval
      );
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        iconColor: '#17a2b8',
        title: 'Unable to join interact session',
        text: error,
        confirmButtonColor: '#17a2b8',
      });
      router.push('/');
    }
  );
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.params.id
  if (!interactSession.id) {
    Swal.fire({
      title: 'Enter session ID',
      html:
        'You will need a session ID provided by your facilitator. <br>' +
        '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      preConfirm: () => {
        interactSession.id = document.getElementById('swalFormId').value;
        if (interactSession.id == '')
          Swal.showValidationMessage('Please enter a session ID');
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
    fetchDetails();
  }
});
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interact</h1>
      <p class="text-center">
        {{ interactSession.title }} | {{ interactSession.name }}
      </p>
      <Transition name="slide-up">
        <div
          class="d-flex justify-content-around flex-wrap"
          v-if="showInteraction"
        >
          <div class="side-content"></div>
          <!--side-content on left too to ensure Interaction panel remains center-->
          <div class="container card m-2">
            <JoinInteraction :currentIndex="currentIndex" />
          </div>
          <div class="side-content align-self-center">
            <Transition name="fade" appear>
              <div
                class="card bg-teal text-center p-2"
                v-show="currentIndex != facilitatorIndex"
                @click="goToInteraction(facilitatorIndex)"
              >
                <font-awesome-icon
                  :icon="['fas', 'circle-chevron-right']"
                  class="display-5"
                />
                Catch up to facilitator
              </div>
            </Transition>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<style scoped>
.side-content {
  width: 150px;
}
.container {
  max-width: 600px;
}
</style>
