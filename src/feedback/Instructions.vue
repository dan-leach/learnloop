<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { api } from "../data/api.js";
import { feedbackSession } from "../data/feedbackSession.js";
import { config } from "../data/config.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const link = ref({});
const loading = ref(true);

const fetchDetails = () => {
  api("feedback", "fetchDetails", feedbackSession.id, null, null).then(
    function (res) {
      if (feedbackSession.id != res.id) {
        console.error(
          "feedbackSession.id != res.id",
          feedbackSession.id,
          res.id
        );
        return;
      }
      feedbackSession.title = res.title;
      feedbackSession.name = res.name;
      link.value.give = config.client.url + "/" + feedbackSession.id;
      link.value.qr =
        "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=" +
        link.value.give +
        "&choe=UTF-8&chld=h|1";
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to fetch feedback session details",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  feedbackSession.id = useRouter().currentRoute.value.params.id;
  if (!feedbackSession.id) {
    Swal.fire({
      title: "Enter session ID",
      html: '<input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input">',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        feedbackSession.id = document.getElementById("swalFormId").value;
        if (feedbackSession.id == "")
          Swal.showValidationMessage("Please enter a session ID");
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState(
          {},
          "",
          "/feedback/instructions/" + feedbackSession.id
        );
        fetchDetails();
      } else {
        router.push("/");
      }
    });
  } else {
    fetchDetails();
  }
});
</script>

<template v-once>
  <Transition name="fade" mode="out-in">
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else>
      <h1 class="text-center display-4">Feedback</h1>
      <p class="text-center display-6">
        How to give feedback for '{{ feedbackSession.title }}'
      </p>
      <div class="give-panel text-center m-2 p-2 d-flex justify-content-around">
        <div class="align-self-center me-4">
          <img
            :src="
              'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' +
              config.client.url +
              '/' +
              feedbackSession.id +
              '&choe=UTF-8&chld=q|1'
            "
          />
        </div>
        <div class="align-self-center">
          <p class="give-instructions">
            Scan the QR code, or go to<br />
            <strong>LearnLoop.co.uk/feedback</strong><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="give-id px-4 py-1">{{ feedbackSession.id }}</span>
          </p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style>
.give-instructions {
  margin: 0;
  font-size: 2rem;
  font-weight: 300;
}
.give-id {
  font-size: 3rem;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 15px;
  color: #17a2b8;
}
.give-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
</style>
