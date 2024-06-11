<script setup>
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
const emit = defineEmits(["toggleContent"]);
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
          class="slide-img img-fluid img-bg"
          :id="image.src"
          data-bs-toggle="modal"
          :data-bs-target="'#imgModal' + index"
          v-if="!image.hide"
        />
        <p class="text-center" v-if="!image.hide">
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
.slide-img:hover {
  border: 5px solid #17a2b8;
}
.img-bg {
  background-color: #f0f7f9;
}
.modal-content {
  background-color: #f0f7f9;
}
.modal-img {
  max-height: 100%;
}
</style>
