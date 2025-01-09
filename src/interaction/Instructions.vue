<script setup>
import { ref, onMounted } from "vue";
import router from "../router";
import { useRouter } from "vue-router";
import { api } from "../data/api.js";
import { interactionSession } from "../data/interactionSession.js";
import { inject } from "vue";
const config = inject("config");
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
  api("interaction/fetchDetailsJoin", {
    id: interactionSession.id,
  }).then(
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
      if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
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
            <strong>{{ config.client.url.replace("https://", "") }}</strong
            ><br />
            and enter this code:
          </p>
          <p class="m-2">
            <span class="join-id px-4 py-1">{{ interactionSession.id }}</span>
          </p>
        </div>
      </div>
      <p class="text-center fs-4 m-4">
        Direct link:
        <a :href="link.join" target="_blank">{{
          link.join.replace("https://", "")
        }}</a>
        <font-awesome-icon
          :icon="['fas', 'copy']"
          size="lg"
          class="mx-2 btn-copy p-1 align-middle"
          style="color: black"
          @click="copyText(link.join)"
        />
      </p>
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
  letter-spacing: 10px;
}
.join-panel {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
}
.qr-code {
  width: 200px;
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
