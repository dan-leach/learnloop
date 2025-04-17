<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import router from "../router";
import { interactionSession } from "../data/interactionSession.js";
import { api } from "../data/api.js";
import Loading from "../components/Loading.vue";
import Swal from "sweetalert2";

interactionSession.isEdit = true;
interactionSession.id = useRouter().currentRoute.value.params.id;

// Load the edit view including fetching the details of the session to be edited
const loadEditView = async () => {
  // Fetch the existing details
  const fetchDetailsHostResult = await fetchDetailsHost();
  if (!fetchDetailsHostResult) return;

  // Remove any previous submissions bundled with the slides
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

// Fetch the details of the interaction session to be edited
const fetchDetailsHost = async () => {
  try {
    const res = await api("interaction/fetchDetailsHost", {
      id: interactionSession.id,
      pin: interactionSession.pin,
    });

    if (interactionSession.id != res.id) {
      throw new error("Response session ID does not match request session ID");
    }

    interactionSession.title = res.title;
    interactionSession.name = res.name;
    interactionSession.email = res.email;
    interactionSession.feedbackID = res.feedbackID;
    interactionSession.slides = res.slides;

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

onMounted(async () => {
  const { isConfirmed } = await Swal.fire({
    title: "Enter session ID and PIN",
    html:
      "<div class='overflow-hidden'>You will need your session ID and PIN which you can find in the email you received when your session was created. <br>" +
      '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input" value="' +
      interactionSession.id +
      '">' +
      '<input id="swalFormPin" placeholder="PIN" type="password" autocomplete="off" class="swal2-input"></div>',
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    preConfirm: () => {
      interactionSession.id = document
        .getElementById("swalFormId")
        .value.trim();
      interactionSession.pin = document
        .getElementById("swalFormPin")
        .value.trim();
      if (interactionSession.pin == "") {
        Swal.showValidationMessage("Please enter your PIN");
        return false;
      }
      if (interactionSession.id == "") {
        Swal.showValidationMessage("Please enter a session ID");
        return false;
      }
      return true;
    },
  });

  // If user confirmed the form, fetch the session details, else redirect to home
  if (isConfirmed) {
    await loadEditView();
    router.push("/interaction/edit/details/" + interactionSession.id);
  } else {
    router.push("/");
  }
});
</script>

<template>
  <Loading />
</template>
