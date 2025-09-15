<script setup>
import { inject } from "vue";
const config = inject("config");

import { RouterLink } from "vue-router";
</script>

<template>
  <footer id="footer" class="footer mt-auto">
    <nav
      class="navbar bg-teal d-flex flex-column align-items-start justify-content-start"
    >
      <div class="mb-2 mx-4">
        <p class="footer-text">
          <span
            ><strong>LearnLoop&nbsp;</strong> Client
            <a :href="config.repo.client.changelog" target="_blank"
              >v{{ config.client.version }}</a
            >
            | API
            <a :href="config.repo.api.changelog" target="_blank"
              >v{{ config.api.version }}</a
            ></span
          >
        </p>
        <p class="footer-text">
          Created by
          <a :href="config.author.url" target="_blank">{{
            config.author.name
          }}</a>
          <span>
            | Contact
            <a :href="'mailto:' + config.author.email">{{
              config.author.email
            }}</a></span
          >
        </p>
        <p class="footer-text">
          <RouterLink to="/privacy-policy"><a>Privacy policy</a></RouterLink>
        </p>
      </div>
    </nav>
  </footer>
</template>

<style scoped>
.footer-text {
  color: black;
  text-decoration: none;
  margin: 0;
}
.left-col {
  font-weight: bold;
  float: right;
  margin-right: 25px;
}
a {
  color: black;
}
</style>
