<script setup>
import { ref, watch } from "vue";
import { interactionSession } from "../../data/interactionSession.js";
import { config } from "../../data/config.js";
import Swal from "sweetalert2";
import Uploader from "./Uploader.vue";
import { faScaleUnbalanced } from "@fortawesome/free-solid-svg-icons";

const props = defineProps(["index"]);
const emit = defineEmits(["hideEditSlideModal"]);

const type = ref("");

const slide = ref({
  prompt: "",
  type: "",
  hasContent: false,
  isInteractive: false,
  content: {
    images: [],
    paragraphs: [],
    bullets: [],
  },
  interaction: {
    options: [],
    settings: {},
  },
  show: {
    content: {
      main: true,
      images: false,
      paragraphs: false,
      bullets: false,
    },
    interaction: {
      main: true,
      options: true,
      settings: true,
    },
  },
});

if (props.index > -1) slide.value = interactionSession.slides[props.index];

watch(type, (newType, oldType) => {
  slide.value.type = type;
  if (slide.value.type) {
    slide.value.isInteractive =
      config.interaction.create.slides.types[slide.value.type].isInteractive;
    if (slide.value.isInteractive) {
      slide.value.interaction.settings =
        config.interaction.create.slides.types[slide.value.type].settings;
      if (slide.value.interaction.settings.selectedLimit) {
        slide.value.interaction.settings.selectedLimit.max =
          slide.value.interaction.options.length;
        keepSelectedLimitsWithinMinMax();
      }
    }
  }
});

const questionTypeInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Question types",
    html: `
      <div class="text-start">
        <p>There are several different slide types available. Interactive slide types are denoted by &#8644;.<br> Click below to see examples and further details about each.</p>
        <div class="accordion" id="accordionTypes">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Single choice &#8644;
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/slide-type-example-single-choice.png" class="img-fluid mx-auto d-block">
                <p>Single choice interactions allow you to provide a selection of options from which attendees can choose only one. By default attendees will be able to respond only once but you can allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Multiple choice &#8644;
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/slide-type-example-multiple-choice.png" class="img-fluid mx-auto d-block">
                <p>Multiple choice interactions allow you to provide a selection of options from which attendees can choose. By default attendees must chose at least 1 option and can select as many as they wish, but optionally you can set a minimum and maximum number of options per response. By default attendees will be able to respond only once but you can optionally allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Free text &#8644;
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/slide-type-example-free-text.png" class="img-fluid mx-auto d-block">
                <p>Free text interactions allow attendees to provide typed responses which appear on the host screen. By default attendees will be able to respond 10 times but you can set a different number of allowed responses per person. To keep responses short the default character limit is 200, but you can set this to a different value if required. You can set the responses not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
          <h2 class="accordion-header" id="headingFour">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
              Bullet points
            </button>
          </h2>
          <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFour">
            <div class="accordion-body">
              <img src="https://dev.learnloop.co.uk/img/slide-type-example-bullet-points.png" class="img-fluid mx-auto d-block">
              <p>Bullet points is a non-interactive slide type which allows you to show text in bullet point format. By default the content on this slide will show on the host's screen only, not the attendees devices, but this can be changed in settings if preferred.</p>
            </div>
          </div>
        </div>
        </div>
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const chartTypeInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Chart types",
    html: `
      <div class="text-start">
        <p>There are two different chart types available for showing single choice or multiple choice interaction results. Click below to see examples and further details:</p>
        <div class="accordion" id="accordionTypes">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Bar chart
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/slide-chart-example-bar.png" class="img-fluid mx-auto d-block">
                <p>You can hover over any of the bars to show the exact number of responses that option has received.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Doughnut chart
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/slide-chart-example-doughnut.png" class="img-fluid mx-auto d-block">
                <p>You can hover over any of the segments to show the exact number of responses that option has received.</p>
              </div>
            </div>
          </div>
        </div>
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const optionsMinMaxInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Minimum and maximum number of options",
    html: `
      <div class="text-start">
        <p>By default attendees must select between one and all of the available options. You can change the minimum and maximum if required. If the attendee attempts to submit a response with fewer than the minimum number of options selected they will receive an alert like this one:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-selection-min.png" class="img-fluid mx-auto d-block">
        <p>Or, if they select more options than the maximum, they will receive an alert like this one:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-selection-max.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const submissionLimitInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Number of responses",
    html: `
      <div class="text-start">
        <p>By default attendees can respond only once to interactions (10 times for free text interactions). You can change this number if required. Once they have responded the maximum number of times the interaction will be disabled on their device:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-submission-limit.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const hideResponsesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Hide responses until you reveal them",
    html: `
      <div class="text-start">
        <p>If you want to prevent attendees from seeing what others are responding until you reveal the answer you can select this option. Your screen will display this view until your click to reveal the responses:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-hide-responses.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const hostScreenOnlyInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Show slide content on host screen only",
    html: `
      <div class="text-start">
        <p>By default the content shown on non-interactive slides is not show on attendees devices. If you want them to be able to view the slide on their device you can change this option.</p>
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let newOption = ref("");
const addOption = () => {
  if (newOption.value) {
    slide.value.interaction.options.push(newOption.value);
    newOption.value = "";
    if (slide.value.interaction.settings.selectedLimit)
      if (
        slide.value.interaction.settings.selectedLimit.max ==
        slide.value.interaction.options.length - 1
      )
        slide.value.interaction.settings.selectedLimit.max++;
  } else {
    document.getElementById("newOption").classList.add("is-invalid");
    setTimeout(
      () => document.getElementById("newOption").classList.remove("is-invalid"),
      3000
    );
  }
};
const removeOption = (index) => {
  slide.value.interaction.options.splice(index, 1);
  keepSelectedLimitsWithinMinMax();
};
const sortOption = (index, x) =>
  slide.value.interaction.options.splice(
    index + x,
    0,
    slide.value.interaction.options.splice(index, 1)[0]
  );

let newParagraph = ref("");
const addParagraph = () => {
  if (newParagraph.value) {
    slide.value.content.paragraphs.push(newParagraph.value);
    newParagraph.value = "";
  } else {
    document.getElementById("newParagraph").classList.add("is-invalid");
    setTimeout(
      () =>
        document.getElementById("newParagraph").classList.remove("is-invalid"),
      3000
    );
  }
};
const removeParagraph = (index) =>
  slide.value.content.paragraphs.splice(index, 1);
const sortParagraph = (index, x) =>
  slide.value.content.paragraphs.splice(
    index + x,
    0,
    slide.value.content.paragraphs.splice(index, 1)[0]
  );

let newBullet = ref("");
const addBullet = () => {
  if (newBullet.value) {
    slide.value.content.bullets.push(newBullet.value);
    newBullet.value = "";
  } else {
    document.getElementById("newBullet").classList.add("is-invalid");
    setTimeout(
      () => document.getElementById("newBullet").classList.remove("is-invalid"),
      3000
    );
  }
};
const removeBullet = (index) => slide.value.content.bullets.splice(index, 1);
const sortBullet = (index, x) =>
  slide.value.content.bullets.splice(
    index + x,
    0,
    slide.value.content.bullets.splice(index, 1)[0]
  );

const imagesChange = (allMedia) => {
  if (allMedia.length) slide.value.content.images = allMedia;
};

const keepSubmissionLimitWithinMinMax = () => {
  const submissionLimit = slide.value.interaction.settings.submissionLimit;
  if (submissionLimit < 1) slide.value.interaction.settings.submissionLimit = 1;
  else if (
    submissionLimit > config.interaction.create.slides.submissionLimitMax
  )
    slide.value.interaction.settings.submissionLimit =
      config.interaction.create.slides.submissionLimitMax;
  else return true;
};
const keepSelectedLimitsWithinMinMax = () => {
  if (
    slide.value.interaction.settings.selectedLimit.min >
    slide.value.interaction.options.length
  )
    slide.value.interaction.settings.selectedLimit.min =
      slide.value.interaction.options.length;
  if (slide.value.interaction.settings.selectedLimit.min < 1)
    slide.value.interaction.settings.selectedLimit.min = 1;
  if (
    slide.value.interaction.settings.selectedLimit.max >
    slide.value.interaction.options.length
  )
    slide.value.interaction.settings.selectedLimit.max =
      slide.value.interaction.options.length;
  if (slide.value.interaction.settings.selectedLimit.max < 1)
    slide.value.interaction.settings.selectedLimit.max = 1;
  if (
    slide.value.interaction.settings.selectedLimit.min >
    slide.value.interaction.settings.selectedLimit.max
  )
    slide.value.interaction.settings.selectedLimit.max =
      slide.value.interaction.settings.selectedLimit.min;
};

let submit = () => {
  console.log(slide);
  slide.value.hasContent =
    slide.value.content.images.length ||
    slide.value.content.bullets.length ||
    slide.value.content.paragraphs.length
      ? true
      : false;
  newBullet.value = "";
  newParagraph.value = "";
  newOption.value = "";
  document
    .getElementById("editSlideModal" + props.index)
    .classList.add("was-validated");
  if (!slide.value.type) return false;
  if (
    slide.value.interaction.settings.optionsLimit == 0 ||
    !slide.value.isInteractive
  ) {
    slide.value.interaction.options = [];
  } else if (
    slide.value.interaction.options.length <
    config.interaction.create.slides.minimumOptions
  ) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Too few options added",
      text:
        "You need to add at least " +
        config.interaction.create.slides.minimumOptions +
        " options.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (
    slide.value.interaction.options.length >
    slide.value.interaction.settings.optionsLimit
  ) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Too many options added",
      text:
        "You can have up to " +
        slide.value.interaction.settings.optionsLimit +
        " options for the slide type selected.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  //  if (!checkImageValid(image.value)) return false;
  if (!slide.value.prompt) return false;
  if (slide.value.interaction.settings.selectedLimit)
    keepSelectedLimitsWithinMinMax();
  if (
    slide.value.interaction.settings.charts &&
    !slide.value.interaction.settings.chart
  )
    return false;
  emit("hideEditSlideModal", props.index, JSON.stringify(slide.value));
  if (props.index == -1) {
    type.value = "";
    slide.value = {
      prompt: "",
      type: "",
      hasContent: false,
      isInteractive: false,
      content: {
        images: [],
        paragraphs: [],
        bullets: [],
      },
      interaction: {
        options: [],
        settings: {},
      },
      show: {
        content: {
          main: true,
          images: false,
          paragraphs: false,
          bullets: false,
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
    .getElementById("editSlideModal" + props.index)
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
                  required
                />
                <label for="prompt">{{
                  slide.isInteractive
                    ? "Question or instruction to attendees"
                    : "Slide heading"
                }}</label>
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
                    Slide content is not itself interactive but can be used to
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
                        Optional: add any images you would like to appear on
                        your slide.
                      </p>
                      <uploader
                        @change="imagesChange"
                        :media="slide.content.images"
                        :max="config.interaction.create.slides.images.max"
                      >
                      </uploader>
                    </div>
                  </div>
                  <!--paragraphs-->
                  <div class="card mb-3">
                    <div class="card-header">
                      <button
                        id="btnShowParagraphs"
                        class="btn"
                        @click="
                          slide.show.content.paragraphs =
                            !slide.show.content.paragraphs
                        "
                      >
                        <label for="text" class="form-label me-2"
                          >Text paragraphs</label
                        ><font-awesome-icon
                          v-if="!slide.show.content.paragraphs"
                          :icon="['fas', 'chevron-down']"
                        />
                        <font-awesome-icon
                          v-else
                          :icon="['fas', 'chevron-up']"
                        />
                      </button>
                    </div>
                    <div class="card-body" v-if="slide.show.content.paragraphs">
                      <p class="ms-1 mb-0 small">
                        Optional: add paragraphs of text to appear on your
                        slide.
                      </p>
                      <table class="table" id="paragraphsTable">
                        <TransitionGroup name="list" tag="tbody">
                          <tr
                            v-for="(paragraph, index) in slide.content
                              .paragraphs"
                            :key="paragraph"
                          >
                            <td class="p-0 ps-2 sort-controls">
                              <button
                                v-if="index != 0"
                                class="btn btn-default btn-sm p-0"
                                id="btnSortUp"
                                @click.prevent="sortParagraph(index, -1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-up']"
                                /></button
                              ><br />
                              <button
                                v-if="
                                  index != slide.content.paragraphs.length - 1
                                "
                                class="btn btn-default btn-sm p-0"
                                id="btnSortDown"
                                @click.prevent="sortParagraph(index, 1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-down']"
                                />
                              </button>
                            </td>
                            <td>
                              {{ paragraph }}
                            </td>
                            <td class="delete-control">
                              <button
                                style="float: right"
                                class="btn btn-danger btn-sm"
                                id="btnRemoveParagraphs"
                                @click.prevent="removeParagraph(index)"
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
                          @keyup.enter="addParagraph"
                          class="form-control"
                          id="newParagraph"
                          v-model="newParagraph"
                          placeholder="Add a paragraph..."
                          name="newParagraph"
                          autocomplete="off"
                        />
                        <button
                          class="btn btn-teal btn-sm"
                          @click.prevent="addParagraph"
                        >
                          Add
                        </button>
                      </div>
                      <div class="invalid-feedback">
                        Please some paragraphs for this slide.
                      </div>
                    </div>
                  </div>
                  <!--bullets-->
                  <div class="card mb-3">
                    <div class="card-header">
                      <button
                        id="btnShowBullets"
                        class="btn"
                        @click="
                          slide.show.content.bullets =
                            !slide.show.content.bullets
                        "
                      >
                        <label for="newBullets" class="form-label me-2"
                          >Bullet points</label
                        ><font-awesome-icon
                          v-if="!slide.show.content.bullets"
                          :icon="['fas', 'chevron-down']"
                        />
                        <font-awesome-icon
                          v-else
                          :icon="['fas', 'chevron-up']"
                        />
                      </button>
                    </div>
                    <div class="card-body" v-if="slide.show.content.bullets">
                      <p class="ms-1 mb-0 small">
                        Optional: add bullet points to appear on your slide.
                      </p>
                      <table class="table" id="bulletsTable">
                        <TransitionGroup name="list" tag="tbody">
                          <tr
                            v-for="(bullet, index) in slide.content.bullets"
                            :key="bullet"
                          >
                            <td class="p-0 ps-2 sort-controls">
                              <button
                                v-if="index != 0"
                                class="btn btn-default btn-sm p-0"
                                id="btnSortUp"
                                @click.prevent="sortBullet(index, -1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-up']"
                                /></button
                              ><br />
                              <button
                                v-if="index != slide.content.bullets.length - 1"
                                class="btn btn-default btn-sm p-0"
                                id="btnSortDown"
                                @click.prevent="sortBullet(index, 1)"
                              >
                                <font-awesome-icon
                                  :icon="['fas', 'chevron-down']"
                                />
                              </button>
                            </td>
                            <td>
                              {{ bullet }}
                            </td>
                            <td class="delete-control">
                              <button
                                style="float: right"
                                class="btn btn-danger btn-sm"
                                id="btnRemoveBullet"
                                @click.prevent="removeBullet(index)"
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
                          @keyup.enter="addBullet"
                          class="form-control"
                          id="newBullet"
                          v-model="newBullet"
                          placeholder="Add a bullet point..."
                          name="newBullet"
                          autocomplete="off"
                        />
                        <button
                          class="btn btn-teal btn-sm"
                          @click.prevent="addBullet"
                        >
                          Add
                        </button>
                      </div>
                      <div class="invalid-feedback">
                        Please provide some bullet points for this slide.
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
                          choose.
                          <span v-if="slide.type == 'multipleChoice'">
                            <br />
                            Control how many options must be selected in the
                            settings panel below.</span
                          >
                        </p>
                        <table class="table" id="optionsTable">
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
                              <td>{{ option }}</td>
                              <td class="delete-control">
                                <button
                                  style="float: right"
                                  class="btn btn-danger btn-sm"
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
                        <div
                          v-if="!slide.isInteractive"
                          class="row align-items-center"
                        >
                          <div class="col-md-3">
                            Show slide content on host screen only
                          </div>
                          <div class="col-md-1">
                            <font-awesome-icon
                              :icon="['fas', 'question-circle']"
                              size="lg"
                              style="color: black"
                              @click="hostScreenOnlyInfo"
                            />
                          </div>
                          <div class="col-md-8 px-5 form-check form-switch">
                            <input
                              v-model="
                                slide.interaction.settings.hostScreenOnly
                              "
                              class="form-check-input"
                              type="checkbox"
                              id="hostScreenOnly"
                              name="hostScreenOnly"
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
</style>
