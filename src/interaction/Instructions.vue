<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import { config } from "../data/config.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const link = ref({});
const loading = ref(true);

const fetchDetails = () => {
  api("interaction", "fetchDetails", interactionSession.id, null, null).then(
    function (res) {
      if (interactionSession.id != res.id) {
        console.error(
          "interactionSession.id != res.id",
          interactionSession.id,
          res.id
        );
        return;
      }
      interactionSession.title = res.title;
      interactionSession.name = res.name;
      link.value.join = config.client.url + "/" + interactionSession.id;
      link.value.host =
        config.client.url + "/interaction/host/" + interactionSession.id;
      link.value.qr =
        config.api.url + "shared/QRcode/?id=" + interactionSession.id;
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to fetch interaction session details",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  interactionSession.id = useRouter().currentRoute.value.params.id;
  if (!interactionSession.id) {
    Swal.fire({
      title: "Enter session ID",
      html: '<div class="overflow-hidden"><input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        interactionSession.id = document.getElementById("swalFormId").value;
        if (interactionSession.id == "")
          Swal.showValidationMessage("Please enter a session ID");
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState(
          {},
          "",
          "/interaction/instructions/" + interactionSession.id
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
      <h1 class="text-center display-4">Interaction</h1>
      <p class="text-center display-6">
        How to join '{{ interactionSession.title }}'
      </p>
      <div class="join-panel text-center m-2 p-2 d-flex justify-content-around">
        <div class="align-self-center me-4">
          <img class="qr-code" :src="link.qr" />
        </div>
        <div class="align-self-center">
          <p class="join-instructions">
            Scan the QR code, or go to<br />
            <strong>LearnLoop.co.uk/interaction</strong><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="join-id px-4 py-1">{{ interactionSession.id }}</span>
          </p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style>
.join-instructions {
  margin: 0;
  font-size: 2rem;
  font-weight: 300;
}
.join-id {
  font-size: 3rem;
  font-family: serif;
  border: 2px solid #17a2b8;
  border-radius: 15px;
  color: #17a2b8;
}
.join-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
.qr-code {
  width: 200px;
}
</style>
