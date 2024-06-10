<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
const emit = defineEmits(["toggleContent"]);

const toggleExpandImage = (image) => {
  let expand = image.expand ? false : true;
  for (let img of props.slide.content.images) {
    img.expand = false;
    img.hide = expand ? true : false;
  }
  image.hide = false;
  image.expand = expand;
};
</script>

<template>
  <div
    class="d-flex flex-wrap flex-column align-items-center"
    v-if="slide.content"
  >
    <div
      class="d-flex flex-wrap align-items-center justify-content-around"
      v-if="slide.content.images.length"
    >
      <div class="image-container m-2" v-for="image in slide.content.images">
        <img
          :src="config.api.imagesUrl + image.src"
          class="img-fluid img-bg"
          :class="
            image.expand
              ? 'position-absolute top-50 start-50 translate-middle'
              : ''
          "
          :id="image.src"
          @click="toggleExpandImage(image)"
          v-if="!image.hide"
        />
        <p
          class="text-center"
          :class="
            image.expand
              ? 'position-absolute bottom-0 start-50 translate-middle-x'
              : ''
          "
          v-if="!image.hide"
        >
          {{ image.caption }}
        </p>
      </div>
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
.image-container {
  max-width: 50%;
  max-height: 50%;
}
.img-fluid:hover {
  border: 5px solid #17a2b8;
}
.img-bg {
  background-color: #f0f7f9;
}
</style>
