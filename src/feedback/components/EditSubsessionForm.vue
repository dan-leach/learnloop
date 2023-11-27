<script setup>
import { ref, watch } from 'vue';
import { feedbackSession } from '../../data/feedbackSession.js';
import { config } from '../../data/config.js';
import Swal from 'sweetalert2';
import { api } from '../../data/api.js';

const props = defineProps(['index']);
const emit = defineEmits(['hideEditSubsessionModal']);

let title = ref('');
let name = ref('');
let email = ref('');

if (props.index > -1) {
  const subsession = feedbackSession.subsessions[props.index];
  title.value = subsession.title;
  name.value = subsession.name;
  email.value = subsession.email;
}

let btnSubmit = ref({
  text: 'Add to series',
  wait: false,
});

let submit = () => {
  document
    .getElementById('editSubsessionModal' + props.index)
    .classList.add('was-validated');
  if (!title.value || !name.value) return false;
  if (email.value == '') {
    console.log(config.client.subsessionEmailPrompt);
    if (config.client.subsessionEmailPrompt) {
      config.client.subsessionEmailPrompt = false;
      Swal.fire({
        title: "Are you sure you don't want to provide a facilitator email?",
        text: "If you don't provide an email for the facilitator of this session they won't be able to view their feedback directly. As the organiser, you will still be able to view feedback for their session and share it with them manually if you wish. Click 'Cancel' if you want to return and enter a faciltator email.",
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
      }).then((result) => {
        if (result.isConfirmed) {
          createSubsession();
        }
      });
      btnSubmit.text = 'Retry add to series?';
      btnSubmit.wait = false;
    } else {
      createSubsession();
    }
  } else {
    btnSubmit.text = 'Please wait';
    btnSubmit.wait = true;
    api(
      'feedback',
      'checkEmailIsValid',
      null,
      null,
      JSON.stringify(email.value)
    ).then(
      function () {
        createSubsession();
      },
      function (error) {
        Swal.fire({
          icon: 'error',
          iconColor: '#17a2b8',
          text: error,
          confirmButtonColor: '#17a2b8',
        });
        btnSubmit.text = 'Retry add to series?';
        btnSubmit.wait = false;
      }
    );
  }
};

const createSubsession = () => {
  console.log(email.value);
  emit(
    'hideEditSubsessionModal',
    props.index,
    JSON.stringify({
      title: title.value,
      name: name.value,
      email: email.value,
    })
  );
  if (props.index == -1) {
    title.value = '';
    name.value = '';
    email.value = '';
  }
  document
    .getElementById('editSubsessionModal' + props.index)
    .classList.remove('was-validated');
};

const subsessionFacilitatorEmailInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Provide an email for session facilitators (Optional)',
    html: '<div class="text-start">If you provide an email address for the facilitator of this session we\'ll let them know that a feedback request has been set up for them. They will receive notifications when feedback is submitted (they can disable this feature if they prefer) and will be able to view feedback that is submitted for their session (but not other sessions or overall feedback).<br><br>If you don\'t have their email address and permission to use it for this purpose leave this field blank. As the organiser, you will still be able to view feedback for their session and share it with them manually.</div>',
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};
</script>

<template>
  <div class="modal fade" :id="'editSubsessionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? 'Add a' : 'Edit' }} session
          </h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditSubsessionModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editSubsessionForm" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="title" class="form-label">Session title:</label>
              <input
                type="text"
                v-model="title"
                class="form-control"
                id="prompt"
                placeholder="Title for the session..."
                name="prompt"
                autocomplete="off"
                required
              />
              <div class="invalid-feedback">
                Please provide a title for this session.
              </div>
            </div>
            <div class="mb-4">
              <label for="Name" class="form-label">Facilitator name:</label>
              <input
                type="text"
                v-model="name"
                class="form-control"
                id="name"
                placeholder="Name of the session facilitator..."
                name="name"
                autocomplete="off"
                required
              />
              <div class="invalid-feedback">
                Please provide a facilitator name for this session.
              </div>
            </div>
            <div class="mb-4">
              <label for="Email" class="form-label"
                >Facilitator email:
                <font-awesome-icon
                  :icon="['fas', 'question-circle']"
                  size="l"
                  style="color: black"
                  @click="subsessionFacilitatorEmailInfo"
              /></label>
              <input
                type="text"
                v-model="email"
                class="form-control"
                id="email"
                placeholder="Email of the session facilitator..."
                name="email"
                autocomplete="off"
              />
            </div>
          </div>
          <div class="mt-4 text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditSubsessionForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? 'Add' : 'Update' }} session
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-check-input:checked {
  background-color: #17a2b8;
}
</style>
