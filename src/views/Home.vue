<script setup>
import { onMounted, ref } from 'vue';
import router from '../router';
import Quote from './components/Quote.vue';
import { feedbackSession } from '../data/feedbackSession.js';
import { interactSession } from '../data/interactSession.js';
import { cookies } from '../data/cookies.js';

const giveFeedback = () => {
  if (feedbackSession.id) {
    document.getElementById('feedbackID').classList.remove('is-invalid');
    router.push('/feedback/' + feedbackSession.id);
  } else {
    document.getElementById('feedbackID').classList.add('is-invalid');
  }
};

const joinInteract = () => {
  if (interactSession.id) {
    document.getElementById('interactID').classList.remove('is-invalid');
    router.push('/interact/' + interactSession.id);
  } else {
    document.getElementById('interactID').classList.add('is-invalid');
  }
};

onMounted(() => {
  if (document.cookie) {
    try {
      const raw = document.cookie;
      const splits = raw.split('; ');
      const sliced = [];
      for (let split of splits) {
        const index = split.indexOf('=');
        sliced.push(split.slice(index + 1));
      }
      for (let slice of sliced) cookies.push(JSON.parse(slice));
    } catch (e) {
      console.log(e);
    }
  }
});
</script>

<template>
  <main>
    <p class="text-center m-4">
      Welcome to LearnLoop. Please select from the options below.
    </p>
    <div class="d-flex justify-content-around flex-wrap">
      <div class="card p-2 m-2 bg-teal">
        <h1 class="text-center">Feedback</h1>
        <p class="text-center">
          Quickly and easily gather anonymous feedback on teaching.
        </p>
        <div class="input-group m-2">
          <input
            id="feedbackID"
            type="text"
            placeholder="Session ID"
            autocomplete="off"
            class="form-control"
            v-model="feedbackSession.id"
          />
          <button
            type="button"
            id="giveFeedback"
            class="btn btn-primary me-3"
            @click="giveFeedback"
          >
            Give feedback
          </button>
        </div>
        <button
          class="btn btn-primary m-2"
          @click="router.push('/feedback/create')"
        >
          Create a new feedback session
        </button>
        <button class="btn btn-primary m-2" @click="router.push('/feedback')">
          More feedback options
        </button>
      </div>
      <div class="card p-2 m-2 bg-teal">
        <h1 class="text-center">Interact</h1>
        <p class="text-center">Engage your audience with live interactions.</p>
        <div class="input-group m-2">
          <input
            id="interactID"
            type="text"
            placeholder="Session ID"
            autocomplete="off"
            class="form-control"
            v-model="interactSession.id"
          />
          <button
            type="button"
            id="joinInteract"
            class="btn btn-primary me-3"
            @click="joinInteract"
          >
            Join session
          </button>
        </div>
        <button
          class="btn btn-primary m-2"
          @click="router.push('/interact/create')"
        >
          Create a new interact session
        </button>
        <button class="btn btn-primary m-2" @click="router.push('/interact')">
          More interact options
        </button>
      </div>
    </div>
    <Quote />
  </main>
</template>

<style scoped>
.card {
  width: 40%;
  min-width: 300px;
}
</style>
