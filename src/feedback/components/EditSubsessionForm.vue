<script setup>
import { ref } from "vue";
import { feedbackSession } from "../../data/feedbackSession.js";
import Swal from "sweetalert2";
import { inject } from "vue";
const config = inject("config");

const props = defineProps(["index", "isEdit"]);
const emit = defineEmits(["hideEditSubsessionModal"]);

let title = ref("");
let name = ref("");
let email = ref("");
let emailLocked = ref(false);

if (props.index > -1) {
  const subsession = feedbackSession.subsessions[props.index];
  title.value = subsession.title;
  name.value = subsession.name;
  email.value = subsession.email || "";
  if (props.isEdit && email.value.length && subsession.id)
    emailLocked.value = true;
}

let btnSubmit = ref({
  text: "Add to series",
  wait: false,
});

let submit = () => {
  document
    .getElementById("editSubsessionModal" + props.index)
    .classList.add("was-validated");
  if (!title.value || !name.value) return false;
  createSubsession();
};

const createSubsession = () => {
  emit(
    "hideEditSubsessionModal",
    props.index,
    JSON.stringify({
      title: title.value,
      name: name.value,
      email: email.value,
    })
  );
  if (props.index == -1) {
    title.value = "";
    name.value = "";
    email.value = "";
  }
  document
    .getElementById("editSubsessionModal" + props.index)
    .classList.remove("was-validated");
};

const subsessionFacilitatorEmailInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Provide an email for session facilitators (Optional)",
    html: "<div class=\"text-start\">If you provide an email address for the facilitator of this session we'll let them know that a feedback request has been set up for them. They will receive notifications when feedback is submitted (they can disable this feature if they prefer) and will be able to view feedback that is submitted for their session (but not other sessions or overall feedback).<br><br>If you don't have their email address and permission to use it for this purpose leave this field blank. As the organiser, you will still be able to view feedback for their session and share it with them manually.</div>",
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};
</script>

<template>
  <div class="modal fade" :id="'editSubsessionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? "Add a" : "Edit" }} session
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
            <div class="row">
              <div class="col mb-3">
                <div class="form-floating">
                  <input
                    type="text"
                    v-model="title"
                    class="form-control"
                    id="prompt"
                    placeholder=""
                    name="prompt"
                    autocomplete="off"
                    required
                  />
                  <label for="title">Session title</label>
                  <div class="invalid-feedback">
                    Please provide a title for this session.
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md mb-3">
                <div class="form-floating">
                  <input
                    type="text"
                    v-model="name"
                    class="form-control"
                    id="name"
                    placeholder=""
                    name="name"
                    autocomplete="off"
                    required
                  />
                  <label for="Name">Facilitator name</label>
                  <div class="invalid-feedback">
                    Please provide a facilitator name for this session.
                  </div>
                </div>
              </div>
              <div class="col-md mb-3">
                <div class="input-group">
                  <div class="form-floating">
                    <input
                      type="text"
                      v-model="email"
                      class="form-control"
                      id="email"
                      placeholder=""
                      name="email"
                      autocomplete="off"
                      :disabled="emailLocked"
                    />
                    <label for="Email">Facilitator email</label>
                  </div>
                  <div class="input-group-text">
                    <font-awesome-icon
                      :icon="['fas', 'question-circle']"
                      size="lg"
                      style="color: black"
                      @click="subsessionFacilitatorEmailInfo"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditSubsessionForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? "Add" : "Update" }} session
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
