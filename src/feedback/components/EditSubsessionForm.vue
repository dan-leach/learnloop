<script setup>
/**
 * @module feedback/components/EditSubsessionForm
 * @summary Modal for adding or editing a feedback subsession
 * @description Allows a user to add or edit title, facilitator name, and optionally facilitator email.
 * Uses data binding and validation. Emits updated data back to the parent component.
 */

import { ref } from "vue";
import { feedbackSession } from "../../data/feedbackSession.js";

const props = defineProps(["index", "isEdit"]);
const emit = defineEmits(["hideEditSubsessionModal"]);

/**
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Session title input value
 */
let title = ref("");

/**
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Facilitator name input value
 */
let name = ref("");

/**
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Facilitator email input value
 */
let email = ref("");

/**
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Whether the email input is locked from editing (e.g. for existing sessions)
 */
let emailLocked = ref(false);

// Pre-fill form fields when editing an existing subsession
if (props.index > -1) {
  const subsession = feedbackSession.subsessions[props.index];
  title.value = subsession.title;
  name.value = subsession.name;
  email.value = subsession.email || "";

  if (props.isEdit && email.value.length && subsession.id) {
    emailLocked.value = true;
  }
}

/**
 * @function submit
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Validates input and emits new or updated subsession data
 * @returns {boolean} Returns false if form validation fails
 */
let submit = () => {
  document
    .getElementById("editSubsessionModal" + props.index)
    .classList.add("was-validated");

  if (!title.value || !name.value) return false;

  createSubsession();
};

/**
 * @function createSubsession
 * @memberof module:feedback/components/EditSubsessionForm
 * @description Emits form data and resets the form if creating a new subsession
 */
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

  // Reset input fields if adding a new subsession
  if (props.index == -1) {
    title.value = "";
    name.value = "";
    email.value = "";
  }

  document
    .getElementById("editSubsessionModal" + props.index)
    .classList.remove("was-validated");
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
            <!--title-->
            <div class="form-floating mb-3">
              <input
                type="text"
                v-model="title"
                class="form-control"
                id="prompt"
                placeholder=""
                name="prompt"
                autocomplete="off"
                v-focus-collapse="'subsessionTitleHelp'"
                required
              />
              <label for="title">Session title</label>
              <div class="invalid-feedback">
                Please provide a title for this session.
              </div>
              <div class="collapse form-text mx-1" id="subsessionTitleHelp">
                <span
                  >Choose a title so attendees know what they're providing
                  feedback for.</span
                ><br />
                <span>
                  A list of session titles will appear on the certificate of
                  attendance (if enabled).
                </span>
              </div>
            </div>

            <!--name-->
            <div class="form-floating mb-3">
              <input
                type="text"
                v-model="name"
                class="form-control"
                id="name"
                placeholder=""
                name="name"
                autocomplete="off"
                v-focus-collapse="'subsessionNameHelp'"
                required
              />
              <label for="Name">Facilitator name</label>
              <div class="invalid-feedback">
                Please provide a facilitator name for this session.
              </div>
              <div class="collapse form-text mx-1" id="subsessionNameHelp">
                <span
                  >Provide the name of the person or team responsible for the
                  session.</span
                ><br />
                <span>
                  The facilitator name will appear on the feedback form but not
                  on the certificate of attendance.
                </span>
              </div>
            </div>

            <!--email-->
            <div class="form-floating mb-3">
              <input
                type="text"
                v-model="email"
                class="form-control"
                id="email"
                placeholder=""
                name="email"
                autocomplete="off"
                v-focus-collapse="'subsessionEmailHelp'"
                :disabled="emailLocked"
              />
              <label for="Email">Facilitator email</label>
              <div class="collapse form-text mx-1" id="subsessionEmailHelp">
                <span
                  >Provide an email address for the facilitator of this session
                  (optional).</span
                ><br />
                <span
                  >They will be able to view feedback for their session (but not
                  other sessions or overall feedback).</span
                ><br />
                <span
                  >If you don't have their email address and permission to use
                  it for this purpose leave this field blank.</span
                ><br /><span
                  >As the organiser, you will still be able to view feedback for
                  their session and share it with them manually.</span
                ><br />
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
