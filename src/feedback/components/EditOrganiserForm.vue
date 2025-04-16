<script setup>
import { ref } from "vue";
import { feedbackSession } from "../../data/feedbackSession.js";
import Swal from "sweetalert2";

const props = defineProps(["index", "isEdit"]);
const emit = defineEmits(["hideEditOrganiserModal"]);

let name = ref("");
let email = ref("");
let isLead = ref(true);
let canEdit = ref(true);
let existing = ref(false);

if (props.isEdit) isLead.value = false;

if (props.index > -1) {
  const organiser = feedbackSession.organisers[props.index];
  name.value = organiser.name;
  email.value = organiser.email;
  isLead.value = organiser.isLead;
  canEdit.value = organiser.canEdit;
  if (props.isEdit) existing.value = organiser.existing;
}

/**
 * Validates an email address.
 *
 * This function checks if the provided email follows the standard email format.
 *
 * @param {string} email - The email address to validate.
 * @returns {boolean} - Returns true if the email is valid, otherwise false.
 */
function emailIsValid(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

let btnSubmit = ref({
  text: "Add organiser",
  wait: false,
});

let submit = () => {
  document
    .getElementById("editOrganiserModal" + props.index)
    .classList.add("was-validated");
  if (!name.value || !email.value) return false;
  if (isLead.value && otherOrganiserIsLead()) {
    Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Lead organiser already exists",
      html: '<div class="text-start">Another organiser is already designated as the lead. You cannot have more than one lead organiser.</div>',
      width: "60%",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (isLead.value && !canEdit.value) {
    Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Lead organiser must have edit access",
      html: '<div class="text-start">The lead organsier must have editing rights.</div>',
      width: "60%",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (organiserEmailDuplicate()) {
    Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Organiser email already used",
      html: '<div class="text-start">Another organiser already has this email address.</div>',
      width: "60%",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  btnSubmit.text = "Please wait";
  btnSubmit.wait = true;
  if (emailIsValid(email.value)) {
    addOrganiser();
  } else {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      text: "Email address is not valid",
      confirmButtonColor: "#17a2b8",
    });
    btnSubmit.text = "Retry add organiser?";
    btnSubmit.wait = false;
  }
};

const addOrganiser = () => {
  emit(
    "hideEditOrganiserModal",
    props.index,
    JSON.stringify({
      name: name.value,
      email: email.value,
      isLead: isLead.value,
      canEdit: canEdit.value,
    })
  );
  if (props.index == -1) {
    name.value = "";
    email.value = "";
    isLead.value = isLead.value || otherOrganiserIsLead() ? false : true;
    canEdit.value = true;
  }
  document
    .getElementById("editOrganiserModal" + props.index)
    .classList.remove("was-validated");
};

const organiserEmailDuplicate = () => {
  for (let i in feedbackSession.organisers) {
    //return true another organiser already has this email
    if (feedbackSession.organisers[i].email === email.value && i != props.index)
      return true;
  }
  return false;
};

const otherOrganiserIsLead = () => {
  for (let i in feedbackSession.organisers) {
    //return true if one of the organisers isLead, excluding the current organiser index
    if (feedbackSession.organisers[i].isLead && i != props.index) return true;
  }
  return false;
};
const toggleIsLead = () => {
  if (isLead.value && otherOrganiserIsLead()) {
    Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Lead organiser already exists",
      html: '<div class="text-start">Another organiser is already designated as the lead. You cannot have more than one lead organiser.</div>',
      width: "60%",
      confirmButtonColor: "#17a2b8",
    });
    isLead.value = false;
  }
  if (isLead.value) canEdit.value = true;
};

const toggleCanEdit = async () => {
  if (isLead.value && !canEdit.value) {
    await Swal.fire({
      icon: "info",
      iconColor: "#17a2b8",
      title: "Lead organiser must have edit access",
      html: '<div class="text-start">The lead organsier must have editing rights.</div>',
      width: "60%",
      confirmButtonColor: "#17a2b8",
    });

    canEdit.value = true;
  }
};
</script>

<template>
  <div class="modal fade" :id="'editOrganiserModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? "Add a" : "Edit" }} organiser
          </h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditOrganiserModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editOrganiserForm" class="needs-validation" novalidate>
            <div>
              <div class="form-floating">
                <input
                  type="text"
                  v-model="name"
                  class="form-control"
                  id="name"
                  placeholder=""
                  name="name"
                  autocomplete="off"
                  v-focus-collapse="'organiserNameHelp'"
                  required
                />
                <label for="Name">Organiser name</label>
                <div class="collapse form-text mx-1" id="organiserNameHelp">
                  <span>
                    This does not appear on the feedback form or certificate of
                    attendance.
                  </span>
                </div>
              </div>
              <div class="my-3">
                <div class="form-floating">
                  <input
                    type="text"
                    v-model="email"
                    class="form-control"
                    id="email"
                    placeholder=""
                    name="email"
                    autocomplete="off"
                    required
                    v-focus-collapse="'organiserEmailHelp'"
                    :disabled="existing"
                  />
                  <label for="Email">Organiser email</label>
                  <div class="collapse form-text mx-1" id="organiserEmailHelp">
                    <span>
                      The organiser will receive the the session ID and PIN to
                      be able to view the feedback by email.
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <!--lead-->
            <div class="d-flex align-items-center justify-content-start">
              <div class="d-flex align-items-center justify-content-start mt-3">
                <div class="form-check form-switch">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="toggleIsLead"
                    v-model="isLead"
                    @change="toggleIsLead"
                    :disabled="isEdit"
                  />
                </div>
                <font-awesome-icon
                  :icon="['fas', 'question-circle']"
                  size="xl"
                  class="mx-2"
                  style="color: black"
                  v-focus-collapse="'leadOrganiserHelp'"
                />
              </div>
              <div class="mt-3">
                <span v-if="isLead">
                  This is the lead organiser. They will always have edit access
                  and their email cannot be changed once the session has been
                  created.
                </span>
                <span v-else> Not the lead organiser </span>
              </div>
            </div>
            <div class="collapse form-text mx-1" id="leadOrganiserHelp">
              <span
                >The email address for the lead organiser cannot be changed
                later and will always have edit access.</span
              ><br />
              <span
                >They will receive an email notification if the session is
                updated by any of the other organsiers with editing
                rights.</span
              ><br />
              <span
                >The login details provided on the next page are for the lead
                organiser.</span
              >
            </div>

            <!--can edit-->
            <div class="d-flex align-items-center justify-content-start">
              <div class="d-flex align-items-center justify-content-start mt-3">
                <div class="form-check form-switch">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="toggleCanEdit"
                    v-model="canEdit"
                    @change="toggleCanEdit"
                  />
                </div>
                <font-awesome-icon
                  :icon="['fas', 'question-circle']"
                  size="xl"
                  class="mx-2"
                  style="color: black"
                  v-focus-collapse="'editOrganiserHelp'"
                />
              </div>
              <div class="mt-3">
                <span v-if="canEdit">
                  This organiser can edit this feedback session
                </span>
                <span v-else>
                  This organiser cannot edit this feedback session
                </span>
              </div>
            </div>
            <div class="collapse form-text mx-1" id="editOrganiserHelp">
              <span
                >If you grant an organiser editing rights they can make changes
                to this feedback request (until feedback has been
                submitted).</span
              ><br />
              <span
                >Otherwise they will only be able to view the feedback and
                attendance reports.</span
              ><br />
              <span>At least one organiser must have editing rights.</span>
            </div>
          </div>
          <div class="text-center mt-3">
            <button
              class="btn btn-teal text-center"
              id="submitEditOrganiserForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? "Add" : "Update" }} organiser
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
