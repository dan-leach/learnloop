<script setup>
import { RouterLink, RouterView } from "vue-router";
import { config } from "./data/config.js";
</script>

<template>
  <nav id="header" v-if="!config.client.isFocusView" class="navbar bg-teal">
    <div class="container-fluid">
      <RouterLink to="/" class="navbar-brand"
        ><img alt="LearnLoop logo" class="logo" src="@/assets/logo.png"
      /></RouterLink>
      <p class="fs-3 text-danger">
        This version of LearnLoop is under development
      </p>
    </div>
  </nav>
  <div id="app-view" :class="{ container: !config.client.isFocusView }">
    <RouterView />
  </div>
  <div
    id="footer"
    v-if="!config.client.isFocusView"
    class="footer-spacer d-none d-md-block"
  >
    <nav class="navbar bg-teal fixed-bottom justify-content-center">
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
  </div>
  <div
    id="footer"
    v-if="!config.client.isFocusView"
    class="footer-spacer d-block d-md-none mt-2"
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
@media only screen and (max-width: 700px) {
  .fixed-bottom {
    position: static;
  }
}
</style>
