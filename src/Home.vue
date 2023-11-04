<script setup>
import { onMounted, ref } from 'vue';
import { api } from './data/api.js';
import router from './router';
import Quote from './components/Quote.vue';
import Swal from 'sweetalert2';
import { feedbackSession } from './data/feedbackSession.js';
import { interactSession } from './data/interactSession.js';

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

const resetPin = (module) => {
  let id = ''
  let email = ''
  Swal.fire({
    title: 'Reset PIN',
    html:
      'You will need your session ID which you can find in emails relating to your session, or in the link to your feedback form.<br>For example: learnloop.co.uk/?<mark>aBc123</mark>.<br>'+
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' + 
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#007bff',
    preConfirm: () => {
      id = document.getElementById('swalFormId').value,
      email = document.getElementById('swalFormEmail').value
    }
  }).then((result) => {
    if (result.isConfirmed) {
      if (!id || !email) {
        Swal.fire({
          icon: 'error',
          text: 'Please enter a session ID and facilitator email.'
        })
        return
      }
      api(module, 'resetPin', id, null, JSON.stringify(email)).then(
        function(res) {
          Swal.fire({
            icon: 'success',
            text: res
          })
        },
        function(error) {
          Swal.fire({
            icon: 'error',
            text: error
          })
        }
      )
    }
  })
}

const findMySessions = (module) => {
  let email = ''
  Swal.fire({
    title: 'Find my sessions',
    html:
      'Enter your email below and we\'ll email you with a list of any sessions you\'ve created previously.'+
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#007bff',
    preConfirm: () => {
      email = document.getElementById('swalFormEmail').value
    }
  }).then((result) => {
    if (result.isConfirmed) {
      if (!email) {
        Swal.fire({
          icon: 'error',
          text: 'Please enter a facilitator email.'
        })
        return
      }
      api(module, 'findMySessions', null, null, JSON.stringify(email)).then(
        function(res) {
          Swal.fire({
            icon: 'success',
            text: res
          })
        },
        function(error) {
          Swal.fire({
            icon: 'error',
            text: error
          })
        }
      )
    }
  })
}
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
            @keyup.enter="giveFeedback"
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
        <ul class="nav nav-pills justify-content-center">
          <li class="nav-item m-2">
            <button
              class="nav-link active"
              @click="router.push('/feedback/create')"
            >
              Create a new feedback session
            </button>
          </li>
          <li class="nav-item dropdown m-2">
            <button
              class="nav-link active dropdown-toggle"
              data-bs-toggle="dropdown"
              href="#"
            >
              More options
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="#">Edit existing session</a>
              </li>
              <li><a class="dropdown-item" href="#">View feedback</a></li>
              <li><a class="dropdown-item" href="#">View attendance</a></li>
              <li><a class="dropdown-item" href="#">Close session</a></li>
              <li><a class="dropdown-item" href="#">Reset PIN</a></li>
              <li><a class="dropdown-item" href="#">Find my sessions</a></li>
            </ul>
          </li>
        </ul>
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
            @keyup.enter="joinInteract"
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
        <ul class="nav nav-pills justify-content-center">
          <li class="nav-item m-2">
            <button
              class="nav-link active"
              @click="router.push('/interact/create')"
            >
              Create a new interact session
            </button>
          </li>
          <li class="nav-item dropdown m-2">
            <button
              class="nav-link active dropdown-toggle"
              data-bs-toggle="dropdown"
              href="#"
            >
              More options
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="router.push('/interact/edit/')">Edit existing session</a>
              </li>
              <li>
                <a class="dropdown-item" @click="router.push('/interact/host/')"
                  >Host existing session</a
                >
              </li>
              <li><a class="dropdown-item" href="#">View attendance</a></li>
              <li><a class="dropdown-item" @click="resetPin('interact')">Reset PIN</a></li>
              <li><a class="dropdown-item" @click="findMySessions('interact')">Find my sessions</a></li>
            </ul>
          </li>
        </ul>
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
