<script setup>
/**
 * @module interaction/components/EditSlideForm
 * @summary Form to add or edit a slide as part of step 4 of the interaction session creation process.
 * @requires vue
 * @requires ../data/interactionSession.js
 * @requires sweetalert2
 * @requires ./Uploader.vue
 * @requires ./EditSlideFormHelp.js
 */

import { ref, watch, inject } from "vue";
import { interactionSession } from "../../data/interactionSession.js";
import Swal from "sweetalert2";
import Uploader from "./Uploader.vue";
import { questionTypeInfo, chartTypeInfo, optionsMinMaxInfo, preservePhrasesInfo, submissionLimitInfo, hideResponsesInfo } from "./EditSlideFormHelp.js";

const config = inject("config");

const props = defineProps(["index"]);
const slideIndex = parseInt(props.index);
const emit = defineEmits(["hideEditSlideModal"]);

const type = ref("");

/**
 * The current slide being edited or created.
 * Initialized as empty or populated based on props.index.
 */
const slide = ref({
  prompt: "",
  type: "",
  notes: "",
  hasContent: false,
  isInteractive: false,
  content: {
    images: [],
    textStrings: [],
    useBulletPoints: true,
    video: {
      youtubeIDs: [],
    }
  },
  interaction: {
    options: [],
    settings: {},
  },
  show: {
    content: {
      main: true,
      images: false,
      textStrings: false,
      video: false,
    },
    interaction: {
      main: true,
      options: true,
      settings: true,
    },
  },
});

if (slideIndex > -1) {
  slide.value = interactionSession.slides[slideIndex];
  type.value = slide.value.type;
}

/**
 * Watch for slide type changes and update slide properties accordingly.
 */
watch(type, (newType) => {
  slide.value.type = newType;
  const typeConfig = config.value.interaction.create.slides.types[newType];
  if (typeConfig) {
    slide.value.isInteractive = typeConfig.isInteractive;
    if (slide.value.isInteractive) {
      slide.value.interaction.settings = typeConfig.settings ?? {};
      slide.value.interaction.options = typeConfig.defaultOptions ?? [];
      if (slide.value.interaction.settings.selectedLimit) {
        slide.value.interaction.settings.selectedLimit.max =
          slide.value.interaction.options.length;
        keepSelectedLimitsWithinMinMax();
      }
    } else {
      slide.value.interaction.settings = {};
    }
  }
});

const newOption = ref("");

/**
 * Add a new interaction option to the slide.
 */
const addOption = () => {
  if (newOption.value.trim()) {
    slide.value.interaction.options.push({
      text: newOption.value.trim(),
      correct: false,
      incorrect: false
    });
    newOption.value = "";
    if (slide.value.interaction.settings?.selectedLimit?.max === slide.value.interaction.options.length - 1) {
      slide.value.interaction.settings.selectedLimit.max++;
    }
  } else {
    const el = document.getElementById("newOption");
    el.classList.add("is-invalid");
    setTimeout(() => el.classList.remove("is-invalid"), 3000);
  }
};

/**
 * Remove an option from the list by index.
 * @param {number} index
 */
const removeOption = (index) => {
  slide.value.interaction.options.splice(index, 1);
  keepSelectedLimitsWithinMinMax();
};

/**
 * Move an option up or down in the list.
 * @param {number} index
 * @param {number} offset - Direction to move: -1 or +1
 */
const sortOption = (index, offset) => {
  const opts = slide.value.interaction.options;
  opts.splice(index + offset, 0, opts.splice(index, 1)[0]);
};

const newVideo = ref("");

/**
 * Parse YouTube video ID from a URL.
 * @param {string} url
 * @returns {string|boolean}
 */
const youtube_parser = (url) => {
  const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
  const match = url.match(regExp);
  return match && match[7].length === 11 ? match[7] : false;
};

/**
 * Add a new YouTube video to the slide.
 */
const addVideo = () => {
  const ID = youtube_parser(newVideo.value);
  if (ID) {
    slide.value.content.video.youtubeIDs.push(ID);
    newVideo.value = "";
  } else {
    Swal.fire({
      title: "Invalid video URL",
      text: "The URL provided does not match the format expected for a YouTube video.",
      icon: "error",
      iconColor: "#17a2b8",
      confirmButtonColor: "#17a2b8",
    });
  }
};

/**
 * Remove a YouTube video by index.
 * @param {number} index
 */
const removeVideo = (index) => {
  slide.value.content.video.youtubeIDs.splice(index, 1);
};

const newTextString = ref("");

/**
 * Add a new text string to the slide.
 */
const addTextString = () => {
  if (newTextString.value.trim()) {
    slide.value.content.textStrings.push(newTextString.value.trim());
    newTextString.value = "";
  } else {
    const el = document.getElementById("newTextString");
    el.classList.add("is-invalid");
    setTimeout(() => el.classList.remove("is-invalid"), 3000);
  }
};

/**
 * Remove a text string from the list by index.
 * @param {number} index
 */
const removeTextString = (index) => {
  slide.value.content.textStrings.splice(index, 1);
};

/**
 * Move a text string up or down in the list.
 * @param {number} index
 * @param {number} offset
 */
const sortTextString = (index, offset) => {
  const strings = slide.value.content.textStrings;
  strings.splice(index + offset, 0, strings.splice(index, 1)[0]);
};

/**
 * Replace all images in the slide.
 * @param {Array} allMedia
 */
const imagesChange = (allMedia) => {
  if (allMedia.length) slide.value.content.images = allMedia;
};

/**
 * Enforce max submission limit based on config.
 */
const keepSubmissionLimitWithinMinMax = () => {
  const limit = slide.value.interaction.settings.submissionLimit;
  const max = config.value.interaction.create.slides.submissionLimitMax;
  if (limit < 1) slide.value.interaction.settings.submissionLimit = 1;
  else if (limit > max) slide.value.interaction.settings.submissionLimit = max;
};

/**
 * Enforce selected min/max limits based on number of options.
 */
const keepSelectedLimitsWithinMinMax = () => {
  const { selectedLimit } = slide.value.interaction.settings;
  const optLength = slide.value.interaction.options.length;
  if (!selectedLimit) return;
  selectedLimit.min = Math.max(1, Math.min(selectedLimit.min, optLength));
  selectedLimit.max = Math.max(selectedLimit.min, Math.min(selectedLimit.max, optLength));
};

/**
 * Submit and validate the slide form.
 * Emits the updated slide to parent.
 */
const submit = () => {
  slide.value.hasContent =
    slide.value.content.images.length ||
    slide.value.content.textStrings.length ||
    slide.value.content.video.youtubeIDs.length;

  if (slide.value.type === "static" && !slide.value.hasContent) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "No content added",
      text: "You must add some content for the slide type selected.",
      confirmButtonColor: "#17a2b8",
    });
    return;
  }

  newTextString.value = "";
  newOption.value = "";
  document
    .getElementById("editSlideModal" + slideIndex)
    .classList.add("was-validated");


  if (!slide.value.type || !slide.value.prompt) return;

  if (
    slide.value.interaction.settings.optionsLimit === 0 ||
    !slide.value.isInteractive
  ) {
    slide.value.interaction.options = [];
  } else {
    const minOptions = config.value.interaction.create.slides.minimumOptions;
    if (slide.value.interaction.options.length < minOptions) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Too few options added",
        text: `You need to add at least ${minOptions} options.`,
        confirmButtonColor: "#17a2b8",
      });
      return;
    }

    if (
      slide.value.interaction.options.length >
      slide.value.interaction.settings.optionsLimit
    ) {
      Swal.fire({
        icon: "error",
        iconColor: "#17a2b8",
        title: "Too many options added",
        text: `You can have up to ${slide.value.interaction.settings.optionsLimit} options.`,
        confirmButtonColor: "#17a2b8",
      });
      return;
    }
  }

  if (slide.value.interaction.settings.selectedLimit)
    keepSelectedLimitsWithinMinMax();

  if (
    slide.value.interaction.settings.charts &&
    !slide.value.interaction.settings.chart
  )
    return;

  emit("hideEditSlideModal", slideIndex, JSON.stringify(slide.value));

  if (slideIndex === -1) {
    // Reset form
    type.value = "";
    slide.value = {
      prompt: "",
      type: "",
      notes: "",
      hasContent: false,
      isInteractive: false,
      content: {
        images: [],
        textStrings: [],
        useBulletPoints: true,
        video: {
          youtubeIDs: [],
        }
      },
      interaction: {
        options: [],
        settings: {},
      },
      show: {
        content: {
          main: true,
          images: false,
          textStrings: false,
          video: true,
        },
        interaction: {
          main: true,
          options: true,
          settings: true,
        },
      },
    };
  }

  document
    .getElementById("editSlideModal" + slideIndex)
    .classList.remove("was-validated");
};
</script>


<template>
  <div class="modal fade" :id="'editSlideModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ index < 0 ? "Add a" : "Edit" }} slide</h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditSlideModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editSlideForm" class="needs-validation" novalidate>
            <!--type-->
            <div class="input-group mb-3">
              <div class="form-floating">
                <select
                  v-model="type"
                  class="form-control"
                  id="type"
                  name="type"
                  autocomplete="off"
                  required
                >
                  <option disabled value="">Please select a slide type</option>
                  <option
                    v-for="type in config.interaction.create.slides.types"
                    :value="type.id"
                  >
                    {{ type.name }} {{ type.isInteractive ? "&#8644;" : "" }}
                  </option>
                </select>
                <label for="type">Type</label>
              </div>
              <div class="input-group-text">
                <font-awesome-icon
                  :icon="['fas', 'question-circle']"
                  size="lg"
                  style="color: black"
                  @click="questionTypeInfo"
                />
              </div>
            </div>
            <div v-if="slide.type">
              <!--prompt-->
              <div class="form-floating mb-3">
                <input
                  type="text"
                  v-model="slide.prompt"
                  class="form-control"
                  id="prompt"
                  placeholder=""
                  name="prompt"
                  autocomplete="off"
                  v-focus-collapse="'promptHelp'"
                  required
                />
                <label for="prompt">{{
                  slide.isInteractive
                    ? "Prompt"
                    : "Slide heading"
                }}</label>
                <div class="collapse form-text mx-1" id="promptHelp">
                  <span v-if="slide.isInteractive">The prompt is usually a question or instruction to the attendee that indicates how they should respond.<br>E.g. <i>'Select the most appropriate treatment'</i> for a multiple choice interaction.<br> The prompt appears above the content or interaction on the presenter's screen and the attendee device.</span>
                  <span v-else>The slide heading appears above the content on the presenter's screen and the attendee device.</span>
                </div>
              </div>

              <!--notes-->
              <div class="form-floating mb-3">
                <textarea
                  v-model="slide.notes"
                  class="form-control"
                  id="notes"
                  placeholder=""
                  name="notes"
                  autocomplete="off"
                  v-focus-collapse="'notesHelp'"
                ></textarea>
                <label for="notes">Presenter notes</label>
                <div class="collapse form-text mx-1" id="notesHelp">
                  <span>If you have a secondary screen not visible to attendees you can use presenter view.</span><br><span>Notes added here are displayed along with a preview of the next slide.</span>
                </div>
              </div>
              <!--content-->
              <div class="card mb-3">
                <div class="card-header">
                  <button
                    id="btnShowContent"
                    class="btn"
                    @click="slide.show.content.main = !slide.show.content.main"
                  >
                    <label for="content" class="form-label me-2"
                      ><strong>Slide content</strong></label
                    ><font-awesome-icon
                      v-if="!slide.show.content.main"
                      :icon="['fas', 'chevron-down']"
                    />
                    <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                  </button>
                </div>
                <div class="card-body" v-if="slide.show.content.main">
                  <p class="ms-1 small">
                    Slide content can optionally be added to your slide. It is not interactive but can be used to
                    provide additional context for an interaction.
                  </p>
                  <!--images-->
                  <div class="card mb-3">
                    <div class="card-header">
                      <button
                        id="btnShowImage"
                        class="btn"
                        @click="
                          slide.show.content.images = !slide.show.content.images
                        "
                      >
                        <label for="imageUpload" class="form-label me-2"
                          >Images</label
                        ><font-awesome-icon
                          v-if="!slide.show.content.images"
                          :icon="['fas', 'chevron-down']"
                        />
                        <font-awesome-icon
                          v-else
                          :icon="['fas', 'chevron-up']"
                        />
                        <br />
                      </button>
                    </div>
                    <div class="card-body" v-if="slide.show.content.images">
                      <p class="ms-1 small">
                        Only upload images which are suitable for public release and which you hold the copyright for. Images uploaded to LearnLoop will be shown on attendee devices and are not guarranteed to be secure.
                      </p>
                      <uploader
                        @change="imagesChange"
                        :media="slide.content.images"
                        :max="config.interaction.create.slides.images.max"
                        :maxFilesize="config.interaction.create.slides.images.maxFilesize"
                        :config="config"
                      >
                      </uploader>
                      <p class="ms-1 small">
                        Note: if your slide contains multiple images captions will appear only when you click to enlarge the image.
                      </p>
                    </div>
                  </div>
                  <!--text strings-->
                  <div class="card mb-3">
                    <div class="card-header">
                      <button
                        id="btnShowTextStrings"
                        class="btn"
                        @click="
                          slide.show.content.textStrings =
                            !slide.show.content.textStrings
                        "
                      >
                        <label for="newTextStrings" class="form-label me-2"
                          >Text</label
                        ><font-awesome-icon
                          v-if="!slide.show.content.textStrings"
                          :icon="['fas', 'chevron-down']"
                        />
                        <font-awesome-icon
                          v-else
                          :icon="['fas', 'chevron-up']"
                        />
                      </button>
                    </div>
                    <div
                      class="card-body"
                      v-if="slide.show.content.textStrings"
                    >
                      <p class="ms-1 mb-0 small">
                      <div class="d-flex">
                        Bullet points
                        <div class="mx-3 form-check form-switch">
                          <input
                            v-model="slide.content.useBulletPoints"
                            class="form-check-input"
                            type="checkbox"
                            id="hideResponses"
                            name="hideResponses"
                          />
                        </div>
                      </div>
                    </p>

                      <table class="table" id="textStringsTable">
                        <TransitionGroup name="list" tag="tbody">
                          <tr
                            v-for="(textString, index) in slide.content
                              .textStrings"
                            :key="textString"
                          >
                            <td class="p-0 ps-2 sort-controls">
                              <button
                                v-if="index != 0"
                                class="btn btn-default btn-sm p-0"
                                id="btnSortUp"
                                @click.prevent="sortTextString(index, -1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-up']"
                                /></button
                              ><br />
                              <button
                                v-if="
                                  index != slide.content.textStrings.length - 1
                                "
                                class="btn btn-default btn-sm p-0"
                                id="btnSortDown"
                                @click.prevent="sortTextString(index, 1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-down']"
                                />
                              </button>
                            </td>
                            <td>
                              {{ textString }}
                            </td>
                            <td class="delete-control">
                              <button
                                style="float: right"
                                class="btn btn-danger btn-sm"
                                id="btnRemoveTextString"
                                @click.prevent="removeTextString(index)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'trash-can']"
                                />
                              </button>
                            </td>
                          </tr>
                        </TransitionGroup>
                      </table>
                      <div class="input-group">
                        <input
                          type="text"
                          @keyup.enter="addTextString"
                          class="form-control"
                          id="newTextString"
                          v-model="newTextString"
                          v-if="slide.content.useBulletPoints"
                          placeholder="Add a bullet point..."
                          name="newTextString"
                          autocomplete="off"
                        />
                        <textarea
                        v-else
                          rows="5"
                          @keyup.enter="addTextString"
                          class="form-control"
                          id="newTextString"
                          v-model="newTextString"
                          placeholder="Enter your text... add a new block to separate paragraphs"
                          name="newTextString"
                          autocomplete="off"
                        />
                        <button
                          class="btn btn-teal btn-sm"
                          @click.prevent="addTextString"
                        >
                          Add
                        </button>
                      </div>
                      <div class="invalid-feedback">
                        Please provide some text for this slide.
                      </div>
                    </div>
                  </div>
                  <!--video-->
                  <div class="card mb-3">
                    <div class="card-header">
                      <button
                        id="btnShowContent"
                        class="btn"
                        @click="slide.show.content.video = !slide.show.content.video"
                      >
                        <label for="content" class="form-label me-2"
                          >Video</label
                        ><font-awesome-icon
                          v-if="!slide.show.content.video"
                          :icon="['fas', 'chevron-down']"
                        />
                        <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                      </button>
                    </div>
                    <div class="card-body" v-if="slide.show.content.video">
                      <p class="ms-1 small">
                        Embed a video from YouTube with the video URL.
                      </p>
                      <div v-for="(id, index) in slide.content.video.youtubeIDs" class="text-center">
                        <iframe :src="'https://www.youtube.com/embed/'+id" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        <div class="text-center">
                        <button
                          class="btn btn-danger mb-2"
                          id="btnRemoveVideo"
                          @click.prevent="removeVideo(index)"
                        >
                          Remove video 
                          <font-awesome-icon
                            :icon="['fas', 'trash-can']"
                            class="ms-2"
                          />
                        </button>
                        </div>
                      </div>
                      <div class="input-group" v-if="slide.content.video.youtubeIDs.length < config.interaction.create.slides.videos.max">
                        <input
                          type="text"
                          @keyup.enter="addVideo"
                          class="form-control"
                          id="newVideo"
                          v-model="newVideo"
                          placeholder="Add a video..."
                          name="newVideo"
                          autocomplete="off"
                        />
                        <button
                          class="btn btn-teal btn-sm"
                          @click.prevent="addVideo"
                        >
                          Add
                        </button>
                      </div>
                      <div class="invalid-feedback">
                        Please provide a video for this slide.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--interaction-->
              <div class="card mb-3">
                <div class="card-header">
                  <button
                    id="btnShowInteraction"
                    class="btn"
                    @click="
                      slide.show.interaction.main = !slide.show.interaction.main
                    "
                  >
                    <label for="interaction" class="form-label me-2"
                      ><strong>Slide interaction</strong></label
                    ><font-awesome-icon
                      v-if="!slide.show.interaction.main"
                      :icon="['fas', 'chevron-down']"
                    />
                    <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                  </button>
                </div>
                <div class="card-body" v-if="slide.show.interaction.main">
                  <p class="ms-1 small" v-if="!slide.isInteractive">
                    This slide type does not have an interaction.
                  </p>
                  <div v-else>
                    <!--chart type-->
                    <div
                      v-if="slide.interaction.settings.charts"
                      class="input-group mb-3"
                    >
                      <div class="form-floating">
                        <select
                          v-model="slide.interaction.settings.chart"
                          class="form-control"
                          id="chartType"
                          name="chartType"
                          autocomplete="off"
                          required
                        >
                          <option disabled value="">
                            Please select a chart type
                          </option>
                          <option
                            v-for="chart in slide.interaction.settings.charts"
                            :value="chart"
                          >
                            {{ chart.charAt(0).toUpperCase() + chart.slice(1) }}
                          </option>
                        </select>
                        <label for="chartType">Chart type</label>
                      </div>
                      <div class="input-group-text">
                        <font-awesome-icon
                          :icon="['fas', 'question-circle']"
                          size="lg"
                          style="color: black"
                          @click="chartTypeInfo"
                        />
                      </div>
                    </div>
                    <!--options-->
                    <div
                      v-if="slide.interaction.settings.optionsLimit"
                      class="card mb-3"
                    >
                      <div class="card-header">
                        <button
                          id="btnShowOptions"
                          class="btn"
                          @click="
                            slide.show.interaction.options =
                              !slide.show.interaction.options
                          "
                        >
                          <label for="newOption" class="form-label me-2"
                            >Options</label
                          ><font-awesome-icon
                            v-if="!slide.show.interaction.options"
                            :icon="['fas', 'chevron-down']"
                          />
                          <font-awesome-icon
                            v-else
                            :icon="['fas', 'chevron-up']"
                          />
                        </button>
                      </div>
                      <div
                        class="card-body"
                        v-if="slide.show.interaction.options"
                      >
                        <p class="ms-1 mb-0 small">
                          Add different response options for your attendees to
                          choose. Optionally, you can indicate if each option is correct or incorrect to have this highlighted on the responses page.
                          <span v-if="slide.type == 'multipleChoice'">
                            <br />
                            Control how many options must be selected in the
                            settings panel below.</span
                          >
                        </p>
                        <table class="table" id="optionsTable">
                          <thead v-if="slide.interaction.options.length">
                            <td></td>
                            <td></td>
                            <td><font-awesome-icon :icon="['fas', 'check']" class="text-success" /></td>
                            <td><font-awesome-icon :icon="['fas', 'xmark']" class="text-danger" /></td>
                            <td></td>
                          </thead>
                          <TransitionGroup name="list" tag="tbody">
                            <tr
                              v-for="(option, index) in slide.interaction
                                .options"
                              :key="option"
                            >
                              <td class="p-0 ps-2 sort-controls">
                                <button
                                  v-if="index != 0"
                                  class="btn btn-default btn-sm p-0"
                                  id="btnSortUp"
                                  @click.prevent="sortOption(index, -1)"
                                >
                                  <font-awesome-icon
                                    :icon="['fas', 'chevron-up']"
                                  /></button
                                ><br />
                                <button
                                  v-if="
                                    index !=
                                    slide.interaction.options.length - 1
                                  "
                                  class="btn btn-default btn-sm p-0"
                                  id="btnSortDown"
                                  @click.prevent="sortOption(index, 1)"
                                >
                                  <font-awesome-icon
                                    :icon="['fas', 'chevron-down']"
                                  />
                                </button>
                              </td>
                              <td>
                                <input 
                                  type="text"
                                  class="form-control"
                                  v-model="option.text"
                                  placeholder=""
                                  autocomplete="off"
                                />
                              </td>
                              <td><input v-model="option.correct" class="form-check-input" type="checkbox" value="true" id="optionTrue" :disabled="option.incorrect"></td>
                              <td><input v-model="option.incorrect" class="form-check-input" type="checkbox" value="false" id="optionFalse" :disabled="option.correct"></td>
                              <td class="delete-control">
                                <button
                                  style="float: right"
                                  class="btn btn-danger btn-sm ms-4"
                                  id="btnRemoveOption"
                                  @click.prevent="removeOption(index)"
                                >
                                  <font-awesome-icon
                                    :icon="['fas', 'trash-can']"
                                  />
                                </button>
                              </td>
                            </tr>
                          </TransitionGroup>
                        </table>
                        <div class="input-group">
                          <input
                            type="text"
                            @keyup.enter="addOption"
                            class="form-control"
                            id="newOption"
                            v-model="newOption"
                            :placeholder="
                              slide.interaction.options.length >=
                              slide.interaction.settings.optionsLimit
                                ? 'Max options reached'
                                : 'Add an option...'
                            "
                            name="newOption"
                            autocomplete="off"
                            :required="!slide.interaction.options.length"
                            :disabled="
                              slide.interaction.options.length >=
                              slide.interaction.settings.optionsLimit
                            "
                          />
                          <button
                            class="btn btn-teal btn-sm"
                            @click.prevent="addOption"
                            :disabled="
                              slide.interaction.options.length >=
                              slide.interaction.settings.optionsLimit
                            "
                          >
                            Add
                          </button>
                        </div>
                        <div class="invalid-feedback">
                          Please provide some options for this slide.
                        </div>
                      </div>
                    </div>
                    <!--settings-->
                    <div class="card mb-3">
                      <div class="card-header">
                        <button
                          id="btnShowSettings"
                          class="btn"
                          @click="
                            slide.show.interaction.settings =
                              !slide.show.interaction.settings
                          "
                        >
                          <span class="form-label me-2">Settings</span
                          ><font-awesome-icon
                            v-if="!slide.show.interaction.settings"
                            :icon="['fas', 'chevron-down']"
                          />
                          <font-awesome-icon
                            v-else
                            :icon="['fas', 'chevron-up']"
                          />
                        </button>
                      </div>
                      <div
                        class="card-body"
                        v-if="slide.show.interaction.settings"
                      >
                        <div
                          v-if="slide.interaction.settings.selectedLimit"
                          class="row align-items-center mb-3"
                        >
                          <div class="col-md-3">
                            Number of options attendees must select
                          </div>
                          <div class="col-md-1 mt-2">
                            <font-awesome-icon
                              :icon="['fas', 'question-circle']"
                              size="lg"
                              style="color: black"
                              @click="optionsMinMaxInfo"
                            />
                          </div>
                          <div class="col-md-4 mt-2">
                            <div class="form-floating">
                              <input
                                type="number"
                                v-model="
                                  slide.interaction.settings.selectedLimit.min
                                "
                                @change="keepSelectedLimitsWithinMinMax"
                                min="1"
                                :max="
                                  slide.interaction.options.length
                                    ? slide.interaction.options.length
                                    : 1
                                "
                                class="form-control"
                                id="selectedLimitMin"
                                name="selectedLimit"
                                autocomplete="off"
                              />
                              <label for="selectedLimitMin">Minimum</label>
                            </div>
                          </div>
                          <div class="col-md-4 mt-2">
                            <div class="form-floating">
                              <input
                                type="number"
                                v-model="
                                  slide.interaction.settings.selectedLimit.max
                                "
                                @change="keepSelectedLimitsWithinMinMax"
                                min="1"
                                :max="
                                  slide.interaction.options.length
                                    ? slide.interaction.options.length
                                    : 1
                                "
                                class="form-control"
                                id="selectedLimitMax"
                                placeholder="1"
                                name="selectedLimit"
                                autocomplete="off"
                              />
                              <label for="selectedLimitMax">Maximum</label>
                            </div>
                          </div>
                        </div>

                        <div
                          v-if="type == 'wordCloud'"
                          class="row align-items-center mb-3"
                        >
                          <div class="col-md-3">
                            Preserve phrases
                          </div>
                          <div class="col-md-1">
                            <font-awesome-icon
                              :icon="['fas', 'question-circle']"
                              size="lg"
                              style="color: black"
                              @click="preservePhrasesInfo"
                            />
                          </div>
                          <div class="col-md-8 px-5 form-check form-switch">
                            <input
                              v-model="slide.interaction.settings.preservePhrases"
                              class="form-check-input"
                              type="checkbox"
                              id="preservePhrases"
                              name="preservePhrases"
                            />
                          </div>
                        </div>

                        <div
                          v-if="slide.interaction.settings.submissionLimit"
                          class="row align-items-center mb-3"
                        >
                          <div class="col-md-3">
                            Number of responses attendees can submit
                          </div>
                          <div class="col-md-1">
                            <font-awesome-icon
                              :icon="['fas', 'question-circle']"
                              size="lg"
                              style="color: black"
                              @click="submissionLimitInfo"
                            />
                          </div>
                          <div class="col-md-8">
                            <div class="form-floating">
                              <input
                                type="number"
                                v-model.lazy="
                                  slide.interaction.settings.submissionLimit
                                "
                                @change="keepSubmissionLimitWithinMinMax"
                                min="1"
                                :max="
                                  config.interaction.create.slides
                                    .submissionLimitMax
                                "
                                class="form-control"
                                id="submissionLimitMax"
                                name="submissionLimitMax"
                                autocomplete="off"
                                required
                              />
                              <label for="submissionLimitMax">Maximum</label>
                            </div>
                          </div>
                        </div>
                        <div
                          v-if="slide.interaction.settings.characterLimit"
                          class="row align-items-center mb-3"
                        >
                          <div class="col-md-3">
                            Character limit for each response
                          </div>
                          <div class="col-md-1"></div>
                          <div class="col-md-8">
                            <div class="form-floating">
                              <input
                                type="number"
                                placeholder=""
                                v-model.lazy="
                                  slide.interaction.settings.characterLimit.max
                                "
                                class="form-control"
                                id="characterLimit"
                                name="characterLimit"
                                autocomplete="off"
                                required
                              />
                              <label for="characterLimit">Maximum</label>
                            </div>
                          </div>
                        </div>
                        <div
                          v-if="
                            slide.interaction.settings.hideResponses !=
                            undefined
                          "
                          class="row align-items-center"
                        >
                          <div class="col-md-3">
                            Hide attendee responses until you reveal them
                          </div>
                          <div class="col-md-1">
                            <font-awesome-icon
                              :icon="['fas', 'question-circle']"
                              size="lg"
                              style="color: black"
                              @click="hideResponsesInfo"
                            />
                          </div>
                          <div class="col-md-8 px-5 form-check form-switch">
                            <input
                              v-model="slide.interaction.settings.hideResponses"
                              class="form-check-input"
                              type="checkbox"
                              id="hideResponses"
                              name="hideResponses"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditInteractionForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? "Add" : "Update" }} slide
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-check-input:checked {
  background-color: #17a2b8;
}
table {
  table-layout: fixed;
}
td {
  word-wrap: break-word;
}
.sort-controls,
.delete-control {
  width: 50px;
}
#notes {
  height: 100px
}
</style>
