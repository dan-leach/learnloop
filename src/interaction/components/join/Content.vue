<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
</script>

<template>
  <div class="d-flex flex-column align-items-center" v-if="slide.content">
    <div class="image-container m-2" v-for="image in slide.content.images">
      <img :src="config.api.imagesUrl + image.src" class="img-fluid" />
    </div>
    <div
      class="paragraph-container d-flex flex-column justify m-2"
      v-if="slide.content.paragraphs.length"
    >
      <p v-for="paragraph in slide.content.paragraphs">{{ paragraph }}</p>
    </div>
    <div
      class="bullets-container d-flex flex-column justify m-2"
      v-if="slide.content.bullets.length"
    >
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
