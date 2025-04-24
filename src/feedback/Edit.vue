<script setup>
/**
 * @module feedback/edit
 * @summary Manages the loading and editing of a feedback session.
 * @description This module allows users to load and edit an existing feedback session by providing their session ID and PIN.
 * It validates the session ID and PIN, fetches the session data from the server, and handles various types of questions and settings.
 * Additionally, it ensures error handling and user feedback in case of failure.
 * @requires vue
 * @requires vue-router
 * @requires swal2
 * @requires feedbackSession
 * @requires api
 * @requires Loading
 * @requires promptSessionDetails
 */

import { onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";
import { promptSessionDetails } from "../assets/promptSessionDetails";

// Set the current session to editing mode and set the session ID from the route params
feedbackSession.isEdit = true;
feedbackSession.id = useRouter().currentRoute.value.params.id;

/**
 * Loads the details of the feedback session for editing.
 *
 * This function retrieves session data via the API, processes the data,
 * and updates the session object accordingly. It handles errors and
 * provides feedback if something goes wrong during the load process.
 *
 * @returns {Promise<void>} - Returns a promise that resolves when the session details are loaded successfully.
 */
const loadUpdateDetails = async () => {
  try {
    // Fetch session details from the API
    const response = await api("feedback/loadUpdateSession", {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
      isTemplate: false,
    });

    // Ensure the session ID matches the response
    if (feedbackSession.id != response.id) {
      console.error(
        "feedbackSession.id != response.id",
        feedbackSession.id,
        response.id
      );
      return;
    }

    // Update feedback session object with response data
    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.multipleDates = response.multipleDates ? true : false;
    feedbackSession.name = response.name;

    feedbackSession.certificate = response.certificate;
    feedbackSession.attendance = response.attendance;

    // Handle subsessions if available
    if (response.subsessions.length) {
      feedbackSession.subsessions = response.subsessions;
      feedbackSession.isSeries = true;
    } else {
      feedbackSession.isSingle = true;
    }

    // Handle questions and their settings
    if (response.questions.length) {
      feedbackSession.questions = response.questions;
      for (let question of feedbackSession.questions) {
        if (!question.settings) {
          // For pre-v5 custom questions, add default settings
          question.settings = {
            selectedLimit: {
              min: 1,
              max: 100,
            },
            characterLimit: 500,
          };
        }
        if (question.settings.required == undefined) {
          // Set 'required' for text and select questions, but not for checkboxes
          if (question.type == "text" || question.type == "select")
            question.settings.required = true;
        }
      }
    }

    // Set organiser details
    feedbackSession.organisers = response.organisers;
    for (let organiser of feedbackSession.organisers) organiser.existing = true;
  } catch (error) {
    // If an error occurs, show an error message and redirect
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Unable to load feedback session",
      text: error,
      confirmButtonColor: "#17a2b8",
    });
    feedbackSession.reset();
    router.push("/");
  }
};

/**
 * Prompts the user to enter the session ID and PIN for editing the session.
 *
 * This function displays a confirmation dialog with input fields for
 * the session ID and PIN. If confirmed, it loads the session details for
 * further editing. If canceled, it redirects the user to the home page.
 *
 * @returns {Promise<void>} - Returns a promise that resolves when the session details are either loaded or the user is redirected.
 */
onMounted(async () => {
  const { isConfirmed, id, pin } = await promptSessionDetails(
    feedbackSession.id
  );

  if (!isConfirmed) {
    router.push("/");
    return;
  }

  feedbackSession.id = id;
  feedbackSession.pin = pin;
  if (await loadUpdateDetails()) {
    router.push("/feedback/edit/details/" + feedbackSession.id);
  }
});
</script>

<template>
  <Loading />
</template>
