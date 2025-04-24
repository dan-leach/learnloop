<script setup>
/**
 * @module interaction/Edit
 * @summary Fetches an interaction session ready for editing.
 * @description
 * This component is responsible for allowing a facilitator to edit a previously created interaction session.
 * It prompts for the session ID and PIN, fetches the session details, resets existing submissions, and navigates to the session detail editing screen.
 *
 * @requires vue
 * @requires vue-router
 * @requires ../data/interactionSession
 * @requires ../data/api
 * @requires SweetAlert2
 * @requires ../assets/promptSessionDetails
 */

import { onMounted } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Swal from "sweetalert2";
import { promptSessionDetails } from "../assets/promptSessionDetails";

// Initialize session as being edited and grab the session ID from the route
interactionSession.isEdit = true;
interactionSession.id = useRouter().currentRoute.value.params.id;

/**
 * Loads and prepares the edit view for the session.
 * Resets submissions and alerts the user that responses will be deleted.
 * @async
 */
const loadEditView = async () => {
  const fetchDetailsHostResult = await fetchDetailsHost();
  if (!fetchDetailsHostResult) return false;

  for (let slide of interactionSession.slides) {
    slide.submissions = [];
    slide.submissionsCount = 0;
  }

  Swal.fire({
    icon: "warning",
    iconColor: "#17a2b8",
    title: "Previous responses will be deleted",
    text: "If you make changes to your interaction session, any previous responses by attendees will be deleted.",
    confirmButtonColor: "#17a2b8",
  });
};

/**
 * Fetches the interaction session's host-level details from the backend.
 * Updates the session object with the response data.
 * @returns {Promise<boolean>} Whether the fetch succeeded.
 * @async
 */
const fetchDetailsHost = async () => {
  try {
    const response = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id !== response.id) {
      throw new Error("Response session ID does not match request session ID");
    }

    interactionSession.title = response.title;
    interactionSession.name = response.name;
    interactionSession.email = response.email;
    interactionSession.feedbackID = response.feedbackID;
    interactionSession.slides = response.slides;

    return true;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");

    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to fetch interaction session details",
      text: error,
      confirmButtonColor: "#17a2b8",
    });

    interactionSession.reset();
    router.push("/");
    return false;
  }
};

// Mount logic to prompt for ID + PIN and proceed to edit screen
onMounted(async () => {
  const { isConfirmed, id, pin } = await promptSessionDetails(
    interactionSession.id
  );

  if (!isConfirmed) {
    router.push("/");
    return;
  }

  interactionSession.id = id;
  interactionSession.pin = pin;
  if (await loadEditView()) {
    router.push("/interaction/edit/details/" + interactionSession.id);
  }
});
</script>

<template>
  <Loading />
</template>
