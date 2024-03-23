<script setup>
import Content from "./Content.vue";
const props = defineProps(["slide"]);
</script>

<template>
  <Content :slide="slide" />
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.bullets-container {
  font-size: 1.5em;
}
</style>
