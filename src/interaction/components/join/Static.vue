<script setup>
import Content from "./Content.vue";
const props = defineProps(["slide"]);
</script>

<template>
  <p class="text-center">
    <strong>{{ slide.prompt }}</strong>
  </p>
  <Content :slide="slide" />
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
