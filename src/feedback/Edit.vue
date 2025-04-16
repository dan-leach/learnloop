<script setup>
import { onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { feedbackSession } from "../data/feedbackSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";

feedbackSession.isEdit = true;
feedbackSession.id = useRouter().currentRoute.value.params.id;

const loadUpdateDetails = async () => {
  try {
    const response = await api("feedback/loadUpdateSession", {
      id: feedbackSession.id,
      pin: feedbackSession.pin,
      isTemplate: false,
    });

    if (feedbackSession.id != response.id) {
      console.error(
        "feedbackSession.id != response.id",
        feedbackSession.id,
        response.id
      );
      return;
    }

    feedbackSession.title = response.title;
    feedbackSession.date = response.date;
    feedbackSession.multipleDates = response.multipleDates ? true : false;
    feedbackSession.name = response.name;

    feedbackSession.certificate = response.certificate;
    feedbackSession.attendance = response.attendance;

    if (response.subsessions.length) {
      feedbackSession.subsessions = response.subsessions;
      feedbackSession.isSeries = true;
    } else {
      feedbackSession.isSingle = true;
    }

    if (response.questions.length) {
      feedbackSession.questions = response.questions;
      for (let question of feedbackSession.questions) {
        if (!question.settings) {
          //for pre-v5 custom questions
          question.settings = {
            selectedLimit: {
              min: 1,
              max: 100,
            },
            characterLimit: 500,
          };
        }
        if (question.settings.required == undefined) {
          //for older sessions with undefined 'required' paramenter default to required for text and select but not for checkboxes
          if (question.type == "text" || question.type == "select")
            question.settings.required = true;
        }
      }
    }

    feedbackSession.organisers = response.organisers;
    for (let organiser of feedbackSession.organisers) organiser.existing = true;
  } catch (error) {
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

onMounted(async () => {
  const { isConfirmed } = await Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      feedbackSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input">',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      feedbackSession.id = document.getElementById("swalFormId").value.trim();
      feedbackSession.pin = document.getElementById("swalFormPin").value.trim();
      if (feedbackSession.pin == "")
        Swal.showValidationMessage("Please enter your PIN");
      if (feedbackSession.id == "")
        Swal.showValidationMessage("Please enter a session ID");
    },
  });

  if (isConfirmed) {
    await loadUpdateDetails();
    router.push("/feedback/edit/details/" + feedbackSession.id);
  } else {
    router.push("/");
  }
});
</script>

<template>
  <Loading />
</template>
