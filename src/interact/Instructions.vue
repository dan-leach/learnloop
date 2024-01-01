<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { api } from "../data/api.js";
import { interactSession } from "../data/interactSession.js";
import { config } from "../data/config.js";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const link = ref({});
const loading = ref(true);

const fetchDetails = () => {
  api("interact", "fetchDetails", interactSession.id, null, null).then(
    function (res) {
      if (interactSession.id != res.id) {
        console.error(
          "interactSession.id != res.id",
          interactSession.id,
          res.id
        );
        return;
      }
      interactSession.title = res.title;
      interactSession.name = res.name;
      link.value.join = config.client.url + "/" + interactSession.id;
      link.value.host =
        config.client.url + "/interact/host/" + interactSession.id;
      link.value.qr =
        "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=" +
        link.value.join +
        "&choe=UTF-8&chld=h|1";
      loading.value = false;
    },
    function (error) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Unable to fetch interact session details",
        text: error,
        confirmButtonColor: "#17a2b8",
      });
      router.push("/");
    }
  );
};

onMounted(() => {
  interactSession.id = useRouter().currentRoute.value.params.id;
  if (!interactSession.id) {
    Swal.fire({
      title: "Enter session ID",
      html: '<div class="overflow-hidden"><input id="swalFormId" placeholder="ID" type="text" autocomplete="off" class="swal2-input"></div>',
      showCancelButton: true,
      confirmButtonColor: "#17a2b8",
      preConfirm: () => {
        interactSession.id = document.getElementById("swalFormId").value;
        if (interactSession.id == "")
          Swal.showValidationMessage("Please enter a session ID");
      },
    }).then((result) => {
      if (result.isConfirmed) {
        history.replaceState(
          {},
          "",
          "/interact/instructions/" + interactSession.id
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
      <h1 class="text-center display-4">Interact</h1>
      <p class="text-center display-6">
        How to join '{{ interactSession.title }}'
      </p>
      <div class="join-panel text-center m-2 p-2 d-flex justify-content-around">
        <div class="align-self-center me-4">
          <img
            :src="
              'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' +
              config.client.url +
              '/' +
              interactSession.id +
              '&choe=UTF-8&chld=q|1'
            "
          />
        </div>
        <div class="align-self-center">
          <p class="join-instructions">
            Scan the QR code, or go to<br />
            <strong>LearnLoop.co.uk/interact</strong><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="join-id px-4 py-1">{{ interactSession.id }}</span>
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
</style>
