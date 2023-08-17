<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { interactSession } from '../../data/interactSession.js';
import { api } from '../../data/api.js';
import Interaction from './components/Interaction.vue';

let currentIndex = ref(0);

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
  <p class="text-center">
    You have joined {{ interactSession.title }} organised by
    {{ interactSession.name }}.
  </p>
  <div class="container">
    <Interaction
      v-for="(interaction, index) in interactSession.interactions"
      :index="index"
      :currentIndex="currentIndex"
      :interaction="interaction"
    />
  </div>
  <div class="text-center">
    <button
      v-if="currentIndex > 0"
      @click="currentIndex--"
      class="btn btn-teal me-2"
    >
      Previous
    </button>
    <button
      v-if="currentIndex < interactSession.interactions.length - 1"
      @click="currentIndex++"
      class="btn btn-teal mw-2"
    >
      Next
    </button>
    <br />Interaction: {{ currentIndex + 1 }} of
    {{ interactSession.interactions.length }}
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
}
</style>
