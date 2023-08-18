<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../../data/api.js';
import { interactSession } from '../../data/interactSession.js';
import Loading from '../components/Loading.vue';
import Interaction from './components/Interaction.vue';
import CatchUp from '../../assets/CatchUp.js';

let currentIndex = ref(0);
let facilitatorIndex = 0;

let loading = ref(true);
let showInteraction = ref(true);

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.path.replace(
    '/interact/',
    ''
  );
  api('interact', 'fetchDetails', interactSession.id, null, null).then(
    function (res) {
      if (interactSession.id != res.id) {
        console.error(
          'interactSession.id != response.id',
          interactSession.id,
          response.id
        );
        return;
      }
      interactSession.title = res.title;
      interactSession.name = res.name;
      interactSession.interactions = res.interactions;
      facilitatorIndex = res.facilitatorIndex;
      currentIndex.value = facilitatorIndex;
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: 'error',
        title: 'Unable to join interact session',
        text: error,
      });
      router.push('/');
    }
  );
});

let checkFacilitatorIndexInterval = () => {
  //api call to check facilitatorIndex
  facilitatorIndex = 2;
  //CatchUp.fire({});
});

let goNext = () => {
  showInteraction.value = false;
  currentIndex.value++;
  setTimeout(() => {
    showInteraction.value = true;
  }, 250);
};
let goBack = () => {
  showInteraction.value = false;
  currentIndex.value--;
  setTimeout(() => {
    showInteraction.value = true;
  }, 250);
};
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <p class="text-center">
        You have joined {{ interactSession.title }} organised by
        {{ interactSession.name }}.
      </p>
      <Transition name="slide-up">
        <div class="container card" v-if="showInteraction">
          <Interaction :currentIndex="currentIndex" />
        </div>
      </Transition>
      <div class="container developer-panel card mt-5">
        <!--remove this developer panel for deployment-->
        <div class="text-center m-2">
          <span>Developer Panel</span><br />
          <button
            v-if="currentIndex > 0"
            @click="goBack"
            class="btn btn-teal me-2"
          >
            Previous
          </button>
          <button
            v-if="currentIndex < interactSession.interactions.length - 1"
            @click="goNext"
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
.container {
  max-width: 600px;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.25s ease-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}
</style>
