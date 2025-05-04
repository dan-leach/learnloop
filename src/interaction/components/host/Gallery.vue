<script setup>
/**
 * @module interaction/components/host/Gallery
 * @summary Renders an image grid with modal view functionality.
 * @description
 * This component displays a responsive gallery of images with click-to-enlarge functionality.
 * Images open in a fullscreen Bootstrap modal and support caption display and navigation.
 * The layout adjusts based on image count, and slide data is passed in via props.
 *
 * @requires vue
 * @requires bootstrap
 */

import { ref, computed, inject } from "vue";
import { Modal } from "bootstrap";

const config = inject("config");

const props = defineProps({
  slide: {
    type: Object,
    required: true,
  },
});

const currentImage = ref({});
const currentIndex = ref(0);
const modalElement = ref(null);
let modalInstance = null;

/**
 * Opens the modal with the selected image.
 * @param {Object} image - The image object (with src and caption).
 * @param {number} index - The index of the selected image.
 */
const openModal = (image, index) => {
  currentImage.value = image;
  currentIndex.value = index;

  if (!modalInstance) {
    modalInstance = new Modal(modalElement.value);
  }

  modalInstance.show();
};

/**
 * Advances to the next image in the modal.
 */
const nextImage = () => {
  if (currentIndex.value < imageList.value.length - 1) {
    currentImage.value = imageList.value[++currentIndex.value];
  }
};

/**
 * Goes back to the previous image in the modal.
 */
const prevImage = () => {
  if (currentIndex.value > 0) {
    currentImage.value = imageList.value[--currentIndex.value];
  }
};

/**
 * Closes the image modal.
 */
const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

/**
 * Computes the list of image objects (src, caption) from slide content.
 * @returns {Array<{src: string, caption: string}>}
 */
const imageList = computed(() => {
  const images = props.slide?.content?.images || [];
  return images.map((img) => ({
    src: `${config.value.api.fetchImageUrl}?folder=${img.folder}&filename=${img.filename}`,
    caption: img.caption || "",
  }));
});

/**
 * Returns the appropriate Bootstrap column class based on image count.
 * @param {number} count - Number of images.
 * @returns {string} Bootstrap column class.
 */
const getColClass = (count) => {
  if (count === 1) return "col-12";
  if (count === 2 || count === 4) return "col-6";
  if ([3, 5, 6, 9].includes(count)) return "col-4";
  if (count >= 7) return "col-3";
  return "col-6 col-sm-4 col-md-3 col-lg-2";
};

/**
 * Returns a row class name based on image count for responsive sizing.
 * @param {number} count - Number of images.
 * @returns {string} Row height class.
 */
const getRowHeight = (count) => {
  if (count < 3) return "row-single";
  if (count < 9) return "row-double";
  return "row-triple";
};
</script>

<template>
  <div class="container-fluid p-3">
    <div
      class="row g-3 justify-content-center"
      :class="getRowHeight(imageList.length)"
    >
      <div
        v-for="(image, index) in imageList"
        :key="index"
        :class="getColClass(imageList.length)"
      >
        <div class="text-center">
          <img
            :src="image.src"
            :alt="image.caption || 'Image'"
            class="img-fluid img-thumbnail"
            style="cursor: pointer"
            @click="openModal(image, index)"
          />
          <div v-if="image.caption" class="small text-muted">
            {{ image.caption }}
          </div>
        </div>
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
            <div class="d-flex align-items-center">
              <button
                v-if="imageList.length > 1"
                class="btn btn-teal me-2"
                @click="prevImage"
                aria-label="Previous Image"
              >
                <font-awesome-icon :icon="['fas', 'circle-chevron-left']" />
              </button>
              <button
                v-if="imageList.length > 1"
                class="btn btn-teal ms-2"
                @click="nextImage"
                aria-label="Next Image"
              >
                <font-awesome-icon :icon="['fas', 'circle-chevron-right']" />
              </button>
            </div>

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
            <div v-if="currentImage.caption" class="mt-2 small text-muted">
              {{ currentImage.caption }}
            </div>
          </div>
        </div>
      </div>
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
.row-single img {
  height: 60vh;
  width: auto;
  object-fit: contain;
}
.row-double img {
  height: 30vh;
  width: auto;
  object-fit: contain;
}
.row-triple img {
  height: 20vh;
  width: auto;
  object-fit: contain;
}
</style>
