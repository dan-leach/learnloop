<script setup>
/**
 * @module interaction/components/join/Video
 * @summary Handles layout for video content.
 *
 */

const props = defineProps({
  slide: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div
    class="d-flex align-items-center align-content-stretch justify-content-around"
  >
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
  </div>
</template>

<style scoped></style>
