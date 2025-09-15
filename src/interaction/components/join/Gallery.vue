<script setup>
/**
 * @module interaction/components/join/Gallery
 * @summary Renders an image grid.
 *
 * @requires vue
 * @requires bootstrap
 */

import { computed, inject } from "vue";

const config = inject("config");

const props = defineProps({
  slide: {
    type: Object,
    required: true,
  },
  id: {
    type: String,
    default: "",
  },
});

/**
 * Computes the list of image objects (src, caption) from slide content.
 * @returns {Array<{src: string, caption: string}>}
 */
const imageList = computed(() => {
  const images = props.slide?.content?.images || [];
  return images.map((img) => ({
    src: `${config.value.api.fetchImageUrl}?id=${props.id}&filename=${img.filename}`,
    caption: img.caption || "",
  }));
});
</script>

<template>
  <div class="container-fluid p-3">
    <div v-for="(image, index) in imageList" :key="index" class="mb-2">
      <div class="text-center">
        <img
          :src="image.src"
          :alt="image.caption || 'Image'"
          class="img-fluid img-thumbnail"
        />
        <div v-if="image.caption" class="small text-muted">
          {{ image.caption }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
