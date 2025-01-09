<script setup>
import { inject } from "vue";
const config = inject("config");
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
                :src="
                  config.api.fetchImageUrl +
                  '?folder=' +
                  image.folder +
                  '&filename=' +
                  image.filename
                "
                class="img-fluid modal-img"
                :id="image.src + '-onModal'"
                data-bs-dismiss="modal"
              />
            </div>
          </div>
        </div>
      </div>
      <img
        :src="
          config.api.fetchImageUrl +
          '?folder=' +
          image.folder +
          '&filename=' +
          image.filename
        "
        class="img-fluid"
        data-bs-toggle="modal"
        :data-bs-target="'#imgModal' + index"
      />
      <p class="text-center">{{ image.caption }}</p>
    </div>
    <div
      class="video-container d-flex flex-wrap align-items-center align-content-stretch justify-content-around"
      v-if="slide.content.video.youtubeIDs.length"
    >
      <div
        :id="'videoContainer' + index"
        class="video-container m-2"
        v-for="(id, index) in slide.content.video.youtubeIDs"
      >
        <iframe
          :src="'https://www.youtube.com/embed/' + id"
          width="100%"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
      </div>
    </div>
    <div
      class="paragraph-container d-flex flex-column justify m-2"
      v-if="slide.content.textStrings.length && !slide.content.useBulletPoints"
    >
      <p v-for="textString in slide.content.textStrings">{{ textString }}</p>
    </div>
    <div
      class="bullets-container d-flex flex-column justify m-2"
      v-if="slide.content.textStrings.length && slide.content.useBulletPoints"
    >
      <ul>
        <li v-for="textString in slide.content.textStrings">
          {{ textString }}
        </li>
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
