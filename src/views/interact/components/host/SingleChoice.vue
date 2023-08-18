<script setup>
import Toast from '../../../../assets/Toast.js';

const props = defineProps(['interaction']);
</script>

<template>
  <p class="text-center">{{ interaction.title }}</p>
</template>

<style scoped></style>
