<script setup>

import { RouterLink } from 'vue-router'

</script>

<template>
  <div class="center">
    <h1 class="display-4">Can't seem to find that...</h1><br>
    <p>We can't find the page you're looking for. Perhaps <RouterLink to="/">start over</RouterLink>?</p>
  </div>
</template>
  
<style>
.center {
  display: flex;
  flex-flow: column wrap;
  justify-content: center;
  align-items: center;
  height: 60vh;
}
</style>
  