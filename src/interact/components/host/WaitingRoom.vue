<script setup>
import { interactSession } from '../../../data/interactSession.js';
import { api } from '../../../data/api.js';
import Swal from 'sweetalert2';
import Toast from '../../../assets/Toast.js';

const deleteSubmissions = () => {
  Swal.fire({
    title: 'Delete submissions?',
    text: "Once previous submissions have been deleted they can't be restored.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    confirmButtonText: 'Delete',
  }).then((result) => {
    if (result.isConfirmed)
      api(
        'interact',
        'deleteSubmissions',
        interactSession.id,
        interactSession.pin,
        null
      ).then(
        function () {
          Toast.fire({
            icon: 'success',
            title: 'Submissions have been cleared',
          });
          interactSession.submissionCount = 0;
        },
        function (error) {
          Swal.fire({
            icon: 'error',
            title: 'Unable to delete previous submissions',
            text: error,
          });
        }
      );
  });
};
</script>

<template>
  <div class="text-center m-4">
    <p>
      <font-awesome-icon
        :icon="['fas', 'hourglass-half']"
        class="display-1 m-4"
      />
    </p>
    <p class="display-6">Click the right arrow to start your session.</p>
    <div v-if="interactSession.submissionCount" class="m-5">
      <p>
        This interact session already has
        {{ interactSession.submissionCount }} submission{{
          interactSession.submissionCount == 1 ? '' : 's'
        }}.
      </p>
      <button class="btn btn-teal" @click="deleteSubmissions">
        Clear submissions
      </button>
    </div>
  </div>
</template>

<style scoped></style>
