<script setup>
const props = defineProps(['slide']);
</script>

<template>
  <p class="text-center">
    <strong>{{ slide.prompt }}</strong>
  </p>
  <p v-if="slide.settings.hostScreenOnly" class="text-center">
    This slide is only shown on the host's screen
  </p>
  <div v-else class="bullets-container d-flex flex-column justify">
    <ul>
      <li v-for="bullet in slide.content.bullets">{{ bullet }}</li>
    </ul>
  </div>
</template>

<style scoped>
.text-center {
  width: 100%;
}
.btnSubmitBelowText {
  font-size: small;
  font-weight: lighter;
}
</style>
