<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  
  <nav>
    <RouterLink to="/">
      <img alt="LearnLoop logo" class="logo" src="@/assets/logo.png" width="225" />
    </RouterLink>

    <RouterLink to="/feedback">
      <span class="nav-item">Feedback</span>
    </RouterLink>

    <RouterLink to="/interact">
      <span class="nav-item">Interact</span>
    </RouterLink>
  </nav>

  <RouterView />

</template>

<style scoped>
nav {
  background-color: #17a2b8;
}
.nav-item {
  color: black;
  background-color: white;
  border-radius: .25rem;
  padding: .5rem 1rem;
  box-sizing: border-box;
}
.nav-item a {

}
</style>
