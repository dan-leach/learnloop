<script setup>
import { ref, watch, onMounted } from "vue";
import router from "./router";
import { RouterLink, RouterView } from "vue-router";
import { Tooltip } from "bootstrap";
new Tooltip(document.body, {
  selector: "[data-bs-toggle='tooltip']",
  trigger: "hover",
});

let goID = ref("");
const go = () => {
  if (goID.value) {
    document.getElementById("inputGo").classList.remove("is-invalid");
    if (goID.value.charAt(0) == "i") {
      router.push("/interaction/" + goID.value.trim());
    } else {
      router.push("/feedback/" + goID.value.trim());
    }
  } else {
    document.getElementById("inputGo").classList.add("is-invalid");
  }
  goID.value = "";
};

let showGo = ref(true);
router.afterEach((to, from) => {
  if (to.name == "home") {
    showGo.value = true;
  } else {
    showGo.value = false;
  }
});

import { inject } from "vue";
const config = inject("config");

//manage loading state while config data fetched
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
let load = ref({
  pending: true,
  failed: "",
});

//fetching the config data
import { fetchConfig } from "./data/fetchConfig";
const loadConfigData = async () => {
  //show the loading spinner
  load.value.pending = true;

  //clear the failed message in case of retry
  load.value.failed = "";

  try {
    const config = await fetchConfig();

    if (config.api.showConsole) {
      console.log("Config fetched successfully:", config);
    }

    load.value.pending = false;
  } catch (error) {
    if (Array.isArray(error)) error = error.map((e) => e.msg).join(" ");
    load.value.failed = error.toString() || "Failed to load configuration data";
    load.value.pending = false;
  }
};

onMounted(() => {
  loadConfigData();
});
</script>

<template>
  <div v-if="load.failed" class="container-fluid m-3">
    <h4>Sorry, something went wrong...</h4>
    <p>
      If this keeps happening please contact
      <a href="mailto:mail@learnloop.co.uk">mail@learnloop.co.uk</a>
      including the error message show below:<br />
      <code>{{ load.failed }}</code>
    </p>
    <button type="button" @click="loadConfigData" class="btn btn-primary mb-4">
      Retry
    </button>
  </div>

  <loading v-else-if="load.pending" v-model:active="load.pending" />
  <div v-else class="d-flex flex-column vh-100">
    <nav
      id="header"
      v-if="!config.client.isFocusView"
      class="navbar bg-teal d-flex justify-content-between"
    >
      <RouterLink to="/" class="navbar-brand m-0"
        ><img
          alt="LearnLoop logo"
          class="logo"
          src="https://learnloop.co.uk/logo.png"
      /></RouterLink>
      <div class="input-group input-go me-2" v-if="showGo">
        <input
          id="inputGo"
          type="text"
          placeholder="Enter a code"
          autocomplete="off"
          class="form-control"
          v-model="goID"
          @keyup.enter="go"
        />
        <button type="button" id="btnGo" class="btn btn-go" @click="go">
          Go
        </button>
      </div>
    </nav>
    <div id="app-view" :class="{ container: !config.client.isFocusView }">
      <RouterView />
    </div>
    <footer
      id="footer"
      v-if="!config.client.isFocusView"
      class="footer mt-auto"
    >
      <nav class="navbar bg-teal justify-content-center">
        <ul class="navbar-nav nav-pills flex-row">
          <li class="nav-item">
            <a class="nav-link p-2 mx-2" :href="config.repo" target="_blank"
              >LearnLoop v{{ config.version }}</a
            >
          </li>
          <li class="nav-item">
            <a class="nav-link p-2 mx-2" :href="config.web" target="_blank"
              >Created by {{ config.author }}</a
            >
          </li>
        </ul>
        <ul class="navbar-nav nav-pills flex-row">
          <li class="nav-item">
            <a
              class="nav-link p-2 mx-2"
              :href="'mailto:' + config.email"
              target="_blank"
              >Contact: {{ config.email }}</a
            >
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link p-2 mx-2" to="/privacy-policy"
              >Privacy policy</RouterLink
            >
          </li>
        </ul>
      </nav>
    </footer>
  </div>
</template>

<style scoped>
.logo {
  width: 20vw;
  min-width: 150px;
  max-width: 180px;
}
.footer-spacer {
  min-height: 70px;
}

.nav-link.active {
  background-color: #17a2b8;
  color: black;
}
.nav-link:hover {
  background-color: #00606e;
  color: white;
}
.input-go {
  max-width: 200px;
  border-width: 4px;
  border-radius: 10px;
  border-color: #03cceb;
  border-style: solid;
}
.btn-go {
  background-color: #17a2b8;
  border-radius: 8px;
}
.btn-go:hover {
  background-color: #00606e;
  color: white;
}
</style>
