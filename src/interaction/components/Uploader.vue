<script setup>
import { config } from '../../data/config.js';
</script>

<template>
  <div>
    <div class="mu-container" :class="isInvalid ? 'mu-red-border' : ''">
      <table class="table" id="imageTable">
        <TransitionGroup name="list" tag="tbody">
          <!--IMAGES PREVIEW-->
          <tr v-for="(image, index) in images" :key="index">
            <td class="bg-transparent p-0 ps-2">
              <button
                v-if="index != 0"
                class="btn btn-default btn-sm p-0"
                id="btnSortUp"
                @click="sortImage(index, -1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-up']" /></button
              ><br />
              <button
                v-if="index != images.length - 1"
                class="btn btn-default btn-sm p-0"
                id="btnSortDown"
                @click="sortImage(index, 1)"
              >
                <font-awesome-icon :icon="['fas', 'chevron-down']" />
              </button>
            </td>
            <td class="bg-transparent">
              <div class="mu-image-container">
                <img
                  :src="config.api.imagesUrl + image.src"
                  alt=""
                  class="mu-images-preview"
                />
              </div>
            </td>
            <td class="bg-transparent">
              <div class="form-floating flex-grow-1">
                <input
                  type="text"
                  v-model="image.caption"
                  class="form-control"
                  id="caption"
                  placeholder=""
                  name="caption"
                  autocomplete="off"
                />
                <label for="caption">Caption </label>
              </div>
            </td>
            <td class="bg-transparent">
              <button
                class="btn btn-danger btn-sm btn-right ms-4 mb-2"
                id="btnRemoveImage"
                @click="removeImage(index)"
              >
                <font-awesome-icon :icon="['fas', 'trash-can']" />
              </button>
            </td>
          </tr>
          <!--UPLOAD BUTTON-->
          <tr key="add">
            <td></td>
            <td>
              <div class="mu-plusbox-container" v-if="images.length < max">
                <label
                  class="mu-plusbox d-flex align-items-center justify-content-center"
                >
                  <div v-if="isLoading" class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <svg
                    v-else
                    class="mu-plus-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    width="1em"
                    height="1em"
                    preserveAspectRatio="xMidYMid meet"
                    viewBox="0 0 24 24"
                  >
                    <g fill="none">
                      <path
                        d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3z"
                        :fill="
                          images.length < max && !isLoading
                            ? 'currentColor'
                            : 'grey'
                        "
                      />
                    </g>
                  </svg>
                  <input
                    @change="fileChange"
                    type="file"
                    accept="image/*"
                    multiple
                    hidden
                    :disabled="images.length >= max || isLoading"
                  />
                </label>
              </div>
            </td>
            <td>
              <p v-if="images.length < max">
                Size limit per image: {{ maxFilesize / 1000000 }}MB<br />
              </p>
              <p v-else>You cannot add more than {{ max }} images per slide</p>
            </td>
            <td></td>
          </tr>
        </TransitionGroup>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { config } from '../../data/config.js';

export default {
  props: {
    server: {
      type: String,
      default: config.api.imageUrl,
    },
    isInvalid: {
      type: Boolean,
      default: false,
    },
    media: {
      type: Array,
      default: [],
    },
    location: {
      type: String,
      default: '',
    },
    max: {
      type: Number,
      default: null,
    },
    maxFilesize: {
      type: Number,
      default: 4,
    },
    warnings: {
      type: Boolean,
      default: true,
    },
    headers: {
      type: Object,
      default: null,
    },
  },
  mounted() {
    this.init();
  },
  data() {
    return {
      images: [],
      config: {
        headers: null,
      },

      isLoading: true,
    };
  },
  methods: {
    init() {
      this.images = this.media;
      this.config.headers = this.headers;
      setTimeout(() => (this.isLoading = false), 1000);
    },
    async fileChange(event) {
      this.isLoading = true;
      let files = event.target.files;

      for (var i = 0; i < files.length; i++) {
        if (!this.max || this.images.length < this.max) {
          if (files[i].size <= this.maxFilesize * 1000000) {
            let formData = new FormData();
            let url = URL.createObjectURL(files[i]);
            formData.set('image', files[i]);
            const { data } = await axios.post(
              config.api.url + '/interaction/uploads/index.php',
              formData,
              this.config
            );
            if (data.error) {
              this.isLoading = false;
              Swal.fire({
                icon: 'error',
                iconColor: '#17a2b8',
                title: 'Error uploading image',
                text: data.msg,
                confirmButtonColor: '#17a2b8',
              });
              return;
            }
            let addedImage = {
              url: url,
              src: data.src,
              size: files[i].size,
              type: files[i].type,
            };
            this.images.push(addedImage);
          } else {
            if (this.warnings) {
              alert(
                'The file you are trying to upload is too big. \nMaximum Filesize: ' +
                  this.maxFilesize +
                  'MB'
              );
            }
            break;
          }
        } else {
          if (this.warnings) {
            alert(
              'You have reached the maximum number of files that you can upload. \nMaximum Files: ' +
                this.max
            );
          }
          break;
        }
      }
      event.target.value = null;
      this.isLoading = false;
    },
    removeImage(index) {
      this.images.splice(index, 1);
    },
    sortImage(index, x) {
      this.images.splice(index + x, 0, this.images.splice(index, 1)[0]);
    },
  },
};
</script>

<style scoped>
.mu-container {
  box-sizing: border-box !important;
  width: 100% !important;
  height: auto !important;
}

/* ----elements-wrapper--- */

.mu-elements-wraper {
  display: flex !important;
  flex-wrap: wrap !important;
}

/* ----plusbox--- */

.mu-plusbox-container {
  display: inline-flex !important;
  height: 90px !important;
  width: 140px !important;
  margin: 0.25rem !important;
}
.mu-plusbox {
  background-color: #ffffff !important;
  border: 1px dashed #818181 !important;
  border-radius: 5px !important;
  cursor: pointer !important;
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
  width: 140px !important;
  height: 90px !important;
}
.mu-plusbox:hover {
  background-color: #f1f1f1 !important;
}
.mu-plusbox:hover > .mu-plus-icon {
  color: #028296 !important;
}
.mu-plus-icon {
  color: #00afca !important;
  font-size: 3rem !important;
  flex: 1;
}

/* ----media-preview---- */

.mu-image-container {
  width: 140px !important;
  height: 90px !important;
  margin: 0.25rem !important;

  position: relative;
}
.mu-images-preview {
  border-radius: 5px !important;
  border: 1px solid #818181 !important;
  width: 140px !important;
  height: 90px !important;
  transition: filter 0.1s linear;
  object-fit: cover;
  object-position: center;
}
.mu-images-preview:hover {
  filter: brightness(90%);
}

.mu-close-btn {
  background: none !important;
  color: white !important;
  border: none !important;
  padding: 0px !important;
  margin: 0px !important;
  cursor: pointer !important;

  position: absolute !important;
  top: 0px;
  right: 0px;
}
.mu-times-icon {
  font-size: 3rem !important;
  filter: drop-shadow(0px 0px 1px black);
}
.mu-close-btn:hover {
  color: red !important;
}

/* -------------------- */

.mu-red-border {
  border: 1px solid #dc3545 !important;
}

.mu-mt-1 {
  margin-top: 0.25rem !important;
}

/* -------------------- */

img {
  -webkit-user-drag: none;
  -khtml-user-drag: none;
  -moz-user-drag: none;
  -o-user-drag: none;
}
</style>
