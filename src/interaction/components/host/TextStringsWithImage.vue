<script setup>
/**
 * @module interaction/components/host/TextStringsWithImage
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
});

const modalElement = ref(null);
let modalInstance = null;

const currentImage = ref({});

/**
 * Opens the modal showing the full-size image.
 * @param {Object} image - The image object with `src` and optional `caption`.
 */
const openModal = (image) => {
  currentImage.value = image;

  if (!modalInstance) {
    modalInstance = new Modal(modalElement.value);
  }

  modalInstance.show();
};

/**
 * Closes the modal.
 */
const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

/**
 * Extracts the single image (with `src` and `caption`) from slide content.
 * @returns {{ src: string, caption: string } | null}
 */
const image = computed(() => {
  const img = props.slide?.content?.images?.[0];
  if (!img) return null;

  return {
    src: `${config.value.api.fetchImageUrl}?folder=${img.folder}&filename=${img.filename}`,
    caption: img.caption || "",
  };
});
</script>

<template>
  <div class="row">
    <div class="col-6 container-fluid p-3">
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

      <!-- Bootstrap Modal -->
      <div
        class="modal fade"
        id="imageModal"
        tabindex="-1"
        aria-labelledby="imageModalLabel"
        ref="modalElement"
      >
        <div class="modal-dialog modal-fullscreen">
          <div class="modal-content bg-dark text-white">
            <div
              class="modal-header border-0 position-relative justify-content-between align-items-center"
            >
              <h5
                class="modal-title position-absolute top-50 start-50 translate-middle text-center w-100"
                style="pointer-events: none"
              >
                {{ currentImage.caption }}
              </h5>

              <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div
              class="modal-body d-flex justify-content-center align-items-center"
              @click="closeModal"
            >
              <img
                :src="currentImage.src"
                class="img-fluid modal-image"
                :alt="currentImage.caption || 'Full Image'"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6">
      <TextStrings :slide="slide" />
    </div>
  </div>
</template>

<style scoped>
.modal-image {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
}
.img-thumbnail:hover {
  background-color: #17a2b8;
}
</style>
