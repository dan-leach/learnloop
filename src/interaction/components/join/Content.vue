<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
</script>

<template>
  <div class="d-flex flex-column align-items-center" v-if="slide.content">
    <div
      class="image-container m-2"
      v-for="(image, index) in slide.content.images"
    >
      <!-- Modal -->
      <div
        class="modal fade"
        :id="'imgModal' + index"
        tabindex="-1"
        :aria-labelledby="'imgModalLabel' + index"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-fullscreen">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" :id="'imgModalLabel' + index">
                {{ image.caption }}
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div
              class="modal-body d-flex justify-content-center align-items-center"
            >
              <img
                :src="config.api.imagesUrl + image.src"
                class="img-fluid modal-img"
                :id="image.src + '-onModal'"
                data-bs-dismiss="modal"
              />
            </div>
          </div>
        </div>
      </div>
      <img
        :src="config.api.imagesUrl + image.src"
        class="img-fluid"
        data-bs-toggle="modal"
        :data-bs-target="'#imgModal' + index"
      />
      <p class="text-center">{{ image.caption }}</p>
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
