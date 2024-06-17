<script setup>
import { onMounted, onUpdated } from "vue";
import { config } from "../../../data/config.js";
const props = defineProps(["slide"]);
const emit = defineEmits(["toggleContent"]);

const setImageContainerWidth = () => {
  let imageContainerIsFullWidth = props.slide.content.textStrings.length
    ? false
    : true;
  let imageCount = props.slide.content.images.length;
  let className = imageContainerIsFullWidth
    ? "image-container-" + imageCount
    : "image-container-half-" + imageCount;
  for (let i = 0; i < imageCount; i++)
    document
      .getElementById("imageContainer" + i)
      .setAttribute("class", className);
};

onMounted(() => {
  if (props.slide.content.images.length) setImageContainerWidth();
});
onUpdated(() => {
  if (props.slide.content.images.length) setImageContainerWidth();
});
</script>

<template>
  <div
    class="d-flex align-items-center align-content-stretch justify-content-around"
    v-if="slide.content"
  >
    <div
      class="images-container d-flex flex-wrap align-items-center align-content-stretch justify-content-around"
      v-if="slide.content.images.length"
    >
      <div
        :id="'imageContainer' + index"
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
          <div
            class="modal-dialog modal-fullscreen"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <div class="modal-content">
              <div class="modal-header d-flex justify-content-around">
                <h5
                  class="modal-title display-5 flex-grow-1 text-center"
                  :id="'imgModalLabel' + index"
                >
                  {{ image.caption }}
                </h5>
                <button type="button" class="btn-close"></button>
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
        />
        <p class="text-center" v-if="slide.content.images.length == 1">
          {{ image.caption }}
        </p>
      </div>
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
          width="560"
          height="315"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
      </div>
    </div>
    <div
      class="paragraph-container d-flex flex-column align-items-center justify m-2"
      v-if="slide.content.textStrings.length && !slide.content.useBulletPoints"
    >
      <p v-for="textString in slide.content.textStrings">{{ textString }}</p>
    </div>
    <div
      class="bullets-container d-flex flex-column align-items-center justify m-2"
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
.images-container {
  font-size: 1.5em;
  flex: 1 1 0;
}

.image-container-1 {
  max-width: 90%;
  max-height: 90%;
}
.image-container-2 {
  max-width: 45%;
  max-height: 90%;
}
.image-container-3 {
  max-width: 30%;
  max-height: 90%;
}
.image-container-4 {
  max-width: 45%;
  max-height: 45%;
}
.image-container-5 {
  max-width: 30%;
  max-height: 45%;
}
.image-container-6 {
  max-width: 30%;
  max-height: 45%;
}
.image-container-7 {
  max-width: 30%;
  max-height: 30%;
}
.image-container-8 {
  max-width: 30%;
  max-height: 30%;
}
.image-container-1 {
  max-width: 30%;
  max-height: 30%;
}
.image-container-10 {
  max-width: 22%;
  max-height: 30%;
}

.image-container-half-1 {
  max-width: 90%;
  max-height: 90%;
}
.image-container-half-2 {
  max-width: 90%;
  max-height: 45%;
}
.image-container-half-3 {
  max-width: 90%;
  max-height: 30%;
}
.image-container-half-4 {
  max-width: 90%;
  max-height: 22%;
}
.image-container-half-5 {
  max-width: 45%;
  max-height: 30%;
}
.image-container-half-6 {
  max-width: 45%;
  max-height: 30%;
}
.image-container-half-7 {
  max-width: 45%;
  max-height: 22%;
}
.image-container-half-8 {
  max-width: 45%;
  max-height: 22%;
}
.image-container-half-9 {
  max-width: 30%;
  max-height: 30%;
}
.image-container-half-10 {
  max-width: 30%;
  max-height: 22%;
}

.slide-img {
  max-height: 20%;
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
