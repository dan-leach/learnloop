<script setup>
import { interactSession } from '../data/interactSession.js';
</script>

<template>
  <h1 class="text-center display-4">Interact</h1>
  <p>Your session was created successfully.</p>
  <p class="display-4">Session ID: {{ interactSession.id }}</p>
  <p class="display-4">Session PIN: {{ interactSession.pin }}</p>
</template>

<style scoped></style>
