<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
</script>

<template>
  <div class="d-flex flex-column">
    <div class="image-container m-2" v-if="slide.content.image.src">
      <img
        :src="config.api.imagesUrl + slide.content.image.src"
        class="img-fluid"
      />
    </div>
    <div class="paragraph-container d-flex flex-column justify m-2">
      <p v-for="paragraph in slide.content.paragraphs">{{ paragraph }}</p>
    </div>
    <div class="bullets-container d-flex flex-column justify m-2">
      <ul>
        <li v-for="bullet in slide.content.bullets">{{ bullet }}</li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.bullets-container,
.paragraph-container,
.image-container {
  font-size: 1.5em;
  flex: 1 1 0;
}
</style>
