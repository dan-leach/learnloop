<script setup>
/**
 * @module interaction/components/join/TextStringsWithImage
 * @summary Handles layout for a single image and text strings.
 *
 * @requires vue
 * @requires bootstrap
 */

import { ref, computed, inject } from "vue";
import { Modal } from "bootstrap";
import TextStrings from "./TextStrings.vue";

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
 * Extracts the single image (with `src` and `caption`) from slide content.
 * @returns {{ src: string, caption: string } | null}
 */
const image = computed(() => {
  const img = props.slide?.content?.images?.[0];
  if (!img) return null;

  return {
    src: `${config.value.api.fetchImageUrl}?id=${props.id}&filename=${img.filename}`,
    caption: img.caption || "",
  };
});
</script>

<template>
  <div class="container-fluid p-3">
    <div v-if="image" class="text-center">
      <img
        :src="image.src"
        :alt="image.caption || 'Image'"
        class="img-fluid img-thumbnail"
        style="cursor: pointer"
        @click="openModal(image)"
      />
      <div v-if="image.caption" class="small text-muted">
        {{ image.caption }}
      </div>
    </div>
  </div>

  <div>
    <TextStrings :slide="slide" />
  </div>
</template>

<style scoped></style>
