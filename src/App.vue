<script setup>
import { ref, watch } from 'vue';
import router from './router';
import { RouterLink, RouterView } from 'vue-router';
import { config } from './data/config.js';
import { Tooltip } from 'bootstrap';
new Tooltip(document.body, {
  selector: "[data-bs-toggle='tooltip']",
  trigger: 'hover',
});

let goID = ref('');
const go = () => {
  if (goID.value) {
    document.getElementById('inputGo').classList.remove('is-invalid');
    if (goID.value.charAt(0) == 'i') {
      router.push('/interaction/' + goID.value.trim());
    } else {
      router.push('/feedback/' + goID.value.trim());
    }
  } else {
    document.getElementById('inputGo').classList.add('is-invalid');
  }
  goID.value = '';
};

let showGo = ref(true);
router.afterEach((to, from) => {
  if (to.name == 'home') {
    showGo.value = true;
  } else {
    showGo.value = false;
  }
});
</script>

<template>
  <div class="d-flex flex-column vh-100">
    <nav
      id="header"
      v-if="!config.client.isFocusView"
      class="navbar bg-teal d-flex justify-content-between"
    >
      <RouterLink to="/" class="navbar-brand m-0"
        ><img alt="LearnLoop logo" class="logo" src="@/assets/logo.png"
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
