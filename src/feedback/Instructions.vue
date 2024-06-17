<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { api } from "../data/api.js";
import { feedbackSession } from "../data/feedbackSession.js";
import { config } from "../data/config.js";
import Swal from "sweetalert2";
import Toast from "../assets/Toast.js";
import Loading from "../components/Loading.vue";

const link = ref({});
const loading = ref(true);

let clipboard = ref(false);
if (navigator.clipboard) clipboard.value = true;
const copyText = (string) => {
  if (!clipboard.value) return;
  navigator.clipboard.writeText(string).then(
    function () {
      Toast.fire({
        icon: "success",
        iconColor: "#17a2b8",
        iconColor: "#17a2b8",
        title: "Copied",
      });
    },
    function (error) {
      Toast.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Error copying to clipboard: " + error,
      });
    }
  );
};

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
        config.api.url + "shared/QRcode/?id=" + feedbackSession.id;
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
      html: '<div class="overflow-hidden"><input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input"></div>',
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
          <img :src="link.qr" />
        </div>
        <div class="align-self-center">
          <p class="give-instructions">
            Scan the QR code, or go to<br />
            <strong>{{ config.client.url.replace("https://", "") }}</strong
            ><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="give-id px-4 py-1">{{ feedbackSession.id }}</span>
          </p>
        </div>
      </div>
      <p class="text-center fs-4 m-4">
        Direct link:
        <a :href="link.give" target="_blank">{{
          link.give.replace("https://", "")
        }}</a>
        <font-awesome-icon
          :icon="['fas', 'copy']"
          size="lg"
          class="mx-2 btn-copy p-1 align-middle"
          style="color: black"
          @click="copyText(link.give)"
        />
      </p>
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
  letter-spacing: 10px;
}
.give-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
a {
  color: black;
}
.btn-copy {
  border: 5px solid transparent;
  border-radius: 5px;
}
.btn-copy:hover {
  border: 5px solid #17a2b8;
}
</style>
