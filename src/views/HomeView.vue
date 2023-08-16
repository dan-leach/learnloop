<script setup>
import { onMounted, ref } from 'vue';
import router from '../router';
import Quote from '../components/Quote.vue';
import { feedbackSession } from '../data/feedbackSession.js';
import { interactSession } from '../data/interactSession.js';
import { cookies } from '../data/cookies.js';

let giveFeedback = () => {
  if (feedbackSession.id) {
    router.push('/feedback/' + feedbackSession.id);
  } else {
    document.getElementById('feedbackID').classList.add('is-invalid');
  }
};
let createFeedbackSession = () => router.push('/feedback/createSession');
let moreFeedbackOptions = () => router.push('/feedback');

let joinInteract = () => {
  if (interactSession.id) {
    router.push('/interact/' + interactSession.id);
  } else {
    document.getElementById('interactID').classList.add('is-invalid');
  }
};
let createInteractSession = () => router.push('/interact/createSession');
let moreInteractOptions = () => router.push('/interact');

onMounted(() => {
  if (document.cookie) {
    try {
      let raw = document.cookie;
      let split = raw.split('; ');
      let sliced = [];
      for (let sp of split) sliced.push(sp.slice(7));
      for (let sl of sliced) cookies.push(JSON.parse(sl));
    } catch (e) {
      console.log(e);
    }
  }
  console.log('HomeView mounted');
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
        <button class="btn btn-primary m-2" @click="createFeedbackSession">
          Create a new feedback session
        </button>
        <button class="btn btn-primary m-2" @click="moreFeedbackOptions">
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
        <button class="btn btn-primary m-2" @click="createInteractSession">
          Create a new interact session
        </button>
        <button class="btn btn-primary m-2" @click="moreInteractOptions">
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
