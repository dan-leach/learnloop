<script setup>
import { RouterLink, RouterView } from "vue-router";
import { config } from "./data/config.js";

import Toast from "./assets/Toast.js";
</script>

<template>
  <nav id="header" v-if="!config.client.isFullscreen" class="navbar bg-teal">
    <div class="container-fluid">
      <RouterLink to="/" class="navbar-brand"
        ><img alt="LearnLoop logo" class="logo" src="@/assets/logo.png"
      /></RouterLink>
      <p class="fs-3 text-danger">
        This is the development version of LearnLoop
      </p>
    </div>
  </nav>
  <div id="app-view" :class="{ container: !config.client.isFullscreen }">
    <RouterView />
  </div>
  <div
    id="footer"
    v-if="!config.client.isFullscreen"
    class="footer-spacer d-none d-md-block"
  >
    <nav class="navbar bg-teal fixed-bottom justify-content-center">
      <ul class="navbar-nav nav-pills flex-row">
        <li class="nav-item">
          <a
            class="nav-link p-2 mx-2"
            href="https://github.com/dan-leach/learnloop"
            target="_blank"
            >LearnLoop v5.0</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link p-2 mx-2"
            href="https://danleach.uk"
            target="_blank"
            >Created by Dan Leach</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link p-2 mx-2"
            href="mailto:mail@learnloop.co.uk"
            target="_blank"
            >Contact: mail@learnloop.co.uk</a
          >
        </li>
        <li class="nav-item">
          <RouterLink class="nav-link p-2 mx-2" to="/privacy-policy"
            >Privacy policy</RouterLink
          >
        </li>
      </ul>
    </nav>
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
</style>
