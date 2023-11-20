<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
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

const resetPin = (module, id) => {
  if (!id) id = ''
  let email = '';
  Swal.fire({
    title: 'Reset PIN',
    html:
      'You will need your session ID which you can find in emails relating to your session.<br>For example: learnloop.co.uk/?<mark>aBc123</mark>.<br>' +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="'+id+'">' +
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    preConfirm: () => {
      (id = document.getElementById('swalFormId').value);
      (email = document.getElementById('swalFormEmail').value);
      if (email == '') Swal.showValidationMessage('Please enter an email');
      if (id == '') Swal.showValidationMessage('Please enter a session ID');
    },
  }).then((result) => {
    if (result.isConfirmed) {
      api(module, 'resetPin', id, null, JSON.stringify(email)).then(
        function (res) {
          Swal.fire({
            icon: 'success',
            iconColor: '#17a2b8',
            text: res,
            confirmButtonColor: '#17a2b8',
          });
        },
        function (error) {
          Swal.fire({
            icon: 'error',
            iconColor: '#17a2b8',
            text: error,
            confirmButtonColor: '#17a2b8',
          });
        }
      );
    }
  });
};

const setNotificationPreference = (id) => {
  if (!id) id = ''
  let pin = '';
  let notifications = true;
  Swal.fire({
    title: 'Set notification preferences',
    html:
      'You will need your session ID and PIN which you can find in the email you received when your session was created.' +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input" value="'+id+'">' +
      '<input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input"><br><br>' +
      'Set notifications <select id="swalFormNotifications" type="select" class="swal2-input"><option value=true>On</option><option value=false>Off</option></select>',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    preConfirm: () => {
      (id = document.getElementById('swalFormId').value);
      (pin = document.getElementById('swalFormPin').value);
      if (pin == '') Swal.showValidationMessage('Please enter your PIN');
      if (id == '') Swal.showValidationMessage('Please enter a session ID');
      (notifications = document.getElementById('swalFormNotifications').value);
    },
  }).then((result) => {
    if (result.isConfirmed) {
      api('feedback', 'setNotificationPreference', id, pin, notifications).then(
        function (res) {
          Swal.fire({
            icon: 'success',
            iconColor: '#17a2b8',
            text: res,
            confirmButtonColor: '#17a2b8',
          });
        },
        function (error) {
          Swal.fire({
            icon: 'error',
            iconColor: '#17a2b8',
            text: error,
            confirmButtonColor: '#17a2b8',
          });
        }
      );
    }
  });
};

const findMySessions = (module) => {
  let email = '';
  Swal.fire({
    title: 'Find my sessions',
    html:
      "Enter your email below and we'll email you with a list of any sessions you've created previously." +
      '<input id="swalFormEmail" placeholder="Facilitator email" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    preConfirm: () => {
      email = document.getElementById('swalFormEmail').value;
      if (email == '') Swal.showValidationMessage('Please enter an email');
    },
  }).then((result) => {
    if (result.isConfirmed) {
      api(module, 'findMySessions', null, null, JSON.stringify(email)).then(
        function (res) {
          Swal.fire({
            icon: 'success',
            iconColor: '#17a2b8',
            text: res,
            confirmButtonColor: '#17a2b8',
          });
        },
        function (error) {
          Swal.fire({
            icon: 'error',
            iconColor: '#17a2b8',
            text: error,
            confirmButtonColor: '#17a2b8',
          });
        }
      );
    }
  });
};

const closeSession = (module) => {
  let id = '';
  let pin = '';
  Swal.fire({
    title: 'Close session',
    html:
      'You will need your session ID and PIN which you can find in the email you received when your session was created.<br><br>Please be aware that once closed a session cannot be reopend to further feedback.<br>' +
      '<input id="swalFormId" placeholder="Session ID" autocomplete="off" class="swal2-input">' +
      '<input id="swalFormPin" placeholder="Pin" type="password" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    preConfirm: () => {
      (id = document.getElementById('swalFormId').value);
      (pin = document.getElementById('swalFormPin').value);
      if (pin == '') Swal.showValidationMessage('Please enter a session PIN');
      if (id == '') Swal.showValidationMessage('Please enter a session ID');
    },
  }).then((result) => {
    if (result.isConfirmed) {
      api(module, 'closeSession', id, pin, null).then(
        function (res) {
          Swal.fire({
            icon: 'success',
            iconColor: '#17a2b8',
            text: res,
            confirmButtonColor: '#17a2b8',
          });
        },
        function (error) {
          Swal.fire({
            icon: 'error',
            iconColor: '#17a2b8',
            text: error,
            confirmButtonColor: '#17a2b8',
          });
        }
      );
    }
  });
};

onMounted(() => {
  let id = useRouter().currentRoute.value.params.id
  let routeName = useRouter().currentRoute.value.name;
  if (routeName == 'interact-resetPIN') resetPin('interact', id)
  if (routeName == 'feedback-resetPIN') resetPin('feedback', id)
  if (routeName == 'feedback-notifications') setNotificationPreference(id)
  if (routeName == 'home' && id) {
    if (id.charAt(0)=='i') router.push('/interact/'+id)
    else router.push('/feedback/'+id)
  }
})
</script>

<template>
  <main>
    <p class="text-center m-4">
      Welcome to LearnLoop. Please select from the options below.
    </p>
    <div class="d-flex justify-content-around flex-wrap">
      <div class="card p-2 m-2">
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
        <ul class="nav nav-pills justify-content-between m-2">
          <li class="nav-item mb-2">
            <button
              class="nav-link active"
              @click="router.push('/feedback/create')"
            >
              Create a new feedback session
            </button>
          </li>
          <li class="nav-item dropdown">
            <button
              class="nav-link active dropdown-toggle"
              data-bs-toggle="dropdown"
              href="#"
            >
              More options
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="router.push('/feedback/edit/')"
                  >Edit existing session</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="router.push('/feedback/view/')"
                  >View feedback</a
                >
              </li>
              <li>
                <a
                  class="dropdown-item"
                  @click="router.push('/feedback/attendance/')"
                  >View attendance</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="closeSession('feedback')"
                  >Close session</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="resetPin('feedback')"
                  >Reset PIN</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="findMySessions('feedback')"
                  >Find my sessions</a
                >
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="card p-2 m-2">
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
        <ul class="nav nav-pills justify-content-between m-2">
          <li class="nav-item mb-2">
            <button
              class="nav-link active"
              @click="router.push('/interact/create')"
            >
              Create a new interact session
            </button>
          </li>
          <li class="nav-item dropdown">
            <button
              class="nav-link active dropdown-toggle"
              data-bs-toggle="dropdown"
              href="#"
            >
              More options
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="router.push('/interact/edit/')"
                  >Edit existing session</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="router.push('/interact/host/?')"
                  >Host existing session</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="resetPin('interact')"
                  >Reset PIN</a
                >
              </li>
              <li>
                <a class="dropdown-item" @click="findMySessions('interact')"
                  >Find my sessions</a
                >
              </li>
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
.btn-primary {
  background-color: #17a2b8;
}
.nav-link.active {
  background-color: #17a2b8;
}
</style>
