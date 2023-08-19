<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../data/api.js';
import { interactSession } from '../data/interactSession.js';
import { config } from '../data/config.js';
import Loading from '../components/Loading.vue';
import JoinInteraction from './components/JoinInteraction.vue';

const loading = ref(true);
const showInteraction = ref(true);
const currentIndex = ref(0);
const facilitatorIndex = ref(0);

const checkCurrentIndex = () => {
  api('interact', 'checkCurrentIndex', interactSession.id, null, null).then(
    function (res) {
      facilitatorIndex.value = res.facilitatorIndex;
    },
    function (error) {
      console.log('checkCurrentIndex failed', error);
    }
  );
};

const goToInteraction = (index) => {
  showInteraction.value = false;
  currentIndex.value = index;
  setTimeout(() => {
    showInteraction.value = true;
  }, 250);
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.path.replace(
    '/interact/',
    ''
  );
  api('interact', 'fetchDetails', interactSession.id, null, null).then(
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
      facilitatorIndex.value = res.facilitatorIndex;
      currentIndex.value = res.facilitatorIndex;
      loading.value = false;
      setInterval(
        checkCurrentIndex,
        config.interact.join.currentIndexPollInterval
      );
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
</script>

<template>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Interact</h1>
      <p class="text-center">
        You have joined {{ interactSession.title }} organised by
        {{ interactSession.name }}.
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
                Go to current
                {{ currentIndex }}/{{ facilitatorIndex }}
              </div>
            </Transition>
          </div>
        </div>
      </Transition>

      <div class="container developer-panel card mt-5">
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
          <br />Facilitator: {{ facilitatorIndex + 1 }}
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.side-content {
  width: 10vw;
}
.developer-panel {
  background-color: lightgrey;
}
.container {
  max-width: 600px;
}
</style>
