<script setup>
import { ref, watch } from 'vue';
import { interactionSession } from '../../data/interactionSession.js';
import { config } from '../../data/config.js';
import Swal from 'sweetalert2';
import Dropzone from 'dropzone';

const props = defineProps(['index']);
const emit = defineEmits(['hideEditSlideModal']);

let prompt = ref('');
let type = ref('');
let isInteractive = ref(true);
let content = ref({});
let options = ref([]);
let settings = ref({});
let chart = ref('');
let charts = ref('');

if (props.index > -1) {
  const slide = interactionSession.slides[props.index];
  console.log(slide);
  prompt.value = slide.prompt;
  type.value = slide.type;
  content.value = slide.content;
  options.value = slide.options;
  settings.value = slide.settings;
}

watch(type, (newType, oldType) => {
  if (type.value) {
    settings.value =
      config.interaction.create.slides.types[type.value].settings;
    charts.value = config.interaction.create.slides.types[type.value].charts;
    isInteractive.value =
      config.interaction.create.slides.types[type.value].isInteractive;
    content.value = config.interaction.create.slides.types[type.value].content;
  }
  if (settings.value.selectedLimit) {
    settings.value.selectedLimit.max = options.value.length;
    keepSelectedLimitsWithinMinMax();
  }
});

const questionTypeInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Question types',
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
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

const chartTypeInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Chart types',
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
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

const optionsMinMaxInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Minimum and maximum number of options',
    html: `
      <div class="text-start">
        <p>By default attendees must select between one and all of the available options. You can change the minimum and maximum if required. If the attendee attempts to submit a response with fewer than the minimum number of options selected they will receive an alert like this one:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-selection-min.png" class="img-fluid mx-auto d-block">
        <p>Or, if they select more options than the maximum, they will receive an alert like this one:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-selection-max.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

const submissionLimitInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Number of responses',
    html: `
      <div class="text-start">
        <p>By default attendees can respond only once to interactions (10 times for free text interactions). You can change this number if required. Once they have responded the maximum number of times the interaction will be disabled on their device:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-submission-limit.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

const hideResponsesInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Hide responses until you reveal them',
    html: `
      <div class="text-start">
        <p>If you want to prevent attendees from seeing what others are responding until you reveal the answer you can select this option. Your screen will display this view until your click to reveal the responses:</p>
        <img src="https://dev.learnloop.co.uk/img/slide-hide-responses.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

const hostScreenOnlyInfo = () => {
  Swal.fire({
    icon: 'info',
    iconColor: '#17a2b8',
    title: 'Show slide content on host screen only',
    html: `
      <div class="text-start">
        <p>By default the content shown on non-interactive slides is not show on attendees devices. If you want them to be able to view the slide on their device you can change this option.</p>
      </div>`,
    width: '60%',
    confirmButtonColor: '#17a2b8',
  });
};

let newOption = ref('');
const addOption = () => {
  if (newOption.value) {
    options.value.push(newOption.value);
    newOption.value = '';
    if (settings.value.selectedLimit)
      if (settings.value.selectedLimit.max == options.value.length - 1)
        settings.value.selectedLimit.max++;
  } else {
    document.getElementById('newOption').classList.add('is-invalid');
    setTimeout(
      () => document.getElementById('newOption').classList.remove('is-invalid'),
      3000
    );
  }
};
const removeOption = (index) => {
  options.value.splice(index, 1);
  keepSelectedLimitsWithinMinMax();
};
const sortOption = (index, x) =>
  options.value.splice(index + x, 0, options.value.splice(index, 1)[0]);

let newBullet = ref('');
const addBullet = () => {
  if (newBullet.value) {
    content.value.bullets.push(newBullet.value);
    newBullet.value = '';
  } else {
    document.getElementById('newBullet').classList.add('is-invalid');
    setTimeout(
      () => document.getElementById('newBullet').classList.remove('is-invalid'),
      3000
    );
  }
};
const removeBullet = (index) => content.value.bullets.splice(index, 1);
const sortBullet = (index, x) =>
  content.value.bullets.splice(
    index + x,
    0,
    content.value.bullets.splice(index, 1)[0]
  );

let showSettings = ref(false);

const keepSubmissionLimitWithinMinMax = () => {
  const submissionLimit = settings.value.submissionLimit;
  if (submissionLimit < 1) settings.value.submissionLimit = 1;
  else if (
    submissionLimit > config.interaction.create.slides.submissionLimitMax
  )
    settings.value.submissionLimit =
      config.interaction.create.slides.submissionLimitMax;
  else return true;
};
const keepSelectedLimitsWithinMinMax = () => {
  if (settings.value.selectedLimit.min > options.value.length)
    settings.value.selectedLimit.min = options.value.length;
  if (settings.value.selectedLimit.min < 1)
    settings.value.selectedLimit.min = 1;
  if (settings.value.selectedLimit.max > options.value.length)
    settings.value.selectedLimit.max = options.value.length;
  if (settings.value.selectedLimit.max < 1)
    settings.value.selectedLimit.max = 1;
  if (settings.value.selectedLimit.min > settings.value.selectedLimit.max)
    settings.value.selectedLimit.max = settings.value.selectedLimit.min;
};

let submit = () => {
  newOption.value = '';
  newBullet.value = '';
  document
    .getElementById('editSlideModal' + props.index)
    .classList.add('was-validated');
  if (!type.value) return false;
  if (content.value.bullets && !content.value.bullets.length) {
    Swal.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title: 'Too few bullet points added',
      text: 'You need to add at least 1 bullet point.',
      confirmButtonColor: '#17a2b8',
    });
    return false;
  }
  if (settings.value.optionsLimit == 0) {
    options.value = [];
  } else if (
    options.value.length < config.interaction.create.slides.minimumOptions
  ) {
    Swal.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title: 'Too few options added',
      text:
        'You need to add at least ' +
        config.interaction.create.slides.minimumOptions +
        ' options.',
      confirmButtonColor: '#17a2b8',
    });
    return false;
  }
  if (options.value.length > settings.value.optionsLimit) {
    Swal.fire({
      icon: 'error',
      iconColor: '#17a2b8',
      title: 'Too many options added',
      text:
        'You can have up to ' +
        settings.value.optionsLimit +
        ' options for the slide type selected.',
      confirmButtonColor: '#17a2b8',
    });
    return false;
  }
  if (!prompt.value) return false;
  if (settings.value.selectedLimit) keepSelectedLimitsWithinMinMax();
  if (charts.value && !chart.value) return false;
  emit(
    'hideEditSlideModal',
    props.index,
    JSON.stringify({
      prompt: prompt.value,
      type: type.value,
      isInteractive: isInteractive.value,
      chart: chart.value,
      content: content.value,
      options: options.value,
      settings: settings.value,
    })
  );
  if (props.index == -1) {
    prompt.value = '';
    type.value = '';
    chart.value = '';
    charts.value = [];
    content.value = {};
    options.value = [];
    settings.value = {};
  }
  document
    .getElementById('editSlideModal' + props.index)
    .classList.remove('was-validated');
};
</script>

<template>
  <div class="modal fade" :id="'editSlideModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ index < 0 ? 'Add a' : 'Edit' }} slide</h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditSlideModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editSlideForm" class="needs-validation" novalidate>
            <div class="form-floating mb-3">
              <input
                type="text"
                v-model="prompt"
                class="form-control"
                id="prompt"
                placeholder=""
                name="prompt"
                autocomplete="off"
                required
              />
              <label for="prompt">{{
                isInteractive
                  ? 'Question or instruction to attendees'
                  : 'Slide heading'
              }}</label>
            </div>
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
                    {{ type.name }} {{ type.isInteractive ? '&#8644;' : '' }}
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
            <div v-if="type">
              <div v-if="charts" class="input-group mb-3">
                <div class="form-floating">
                  <select
                    v-model="chart"
                    class="form-control"
                    id="chart"
                    name="chart"
                    autocomplete="off"
                    required
                  >
                    <option disabled value="">
                      Please select a chart type
                    </option>
                    <option v-for="chart in charts" :value="chart">
                      {{ chart.charAt(0).toUpperCase() + chart.slice(1) }}
                    </option>
                  </select>
                  <label for="type">Chart type</label>
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
              <div v-if="settings.optionsLimit" class="card mb-3">
                <div class="card-header">
                  <label for="newOption" class="px-2 form-label">Options</label>
                </div>
                <div class="card-body">
                  <table class="table" id="optionsTable">
                    <TransitionGroup name="list" tag="tbody">
                      <tr v-for="(option, index) in options" :key="option">
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
                            v-if="index != options.length - 1"
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
                            <font-awesome-icon :icon="['fas', 'trash-can']" />
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
                        options.length >= settings.optionsLimit
                          ? 'Max options reached'
                          : 'Add an option...'
                      "
                      name="newOption"
                      autocomplete="off"
                      :required="!options.length"
                      :disabled="options.length >= settings.optionsLimit"
                    />
                    <button
                      class="btn btn-teal btn-sm"
                      @click.prevent="addOption"
                      :disabled="options.length >= settings.optionsLimit"
                    >
                      Add
                    </button>
                  </div>
                  <div class="invalid-feedback">
                    Please provide some options for this slide.
                  </div>
                </div>
              </div>
              <div v-if="content">
                <div v-if="content.bullets" class="card mb-3">
                  <div class="card-header">
                    <label for="newBullets" class="px-2 form-label"
                      >Bullet points</label
                    >
                  </div>
                  <div class="card-body">
                    <table class="table" id="bulletsTable">
                      <TransitionGroup name="list" tag="tbody">
                        <tr
                          v-for="(bullet, index) in content.bullets"
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
                              v-if="index != content.bullets.length - 1"
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
                              <font-awesome-icon :icon="['fas', 'trash-can']" />
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
                        :required="!content.bullets.length"
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
                <div v-if="content.image" class="card mb-3">
                  <div class="card-header">
                    <label for="imageUpload" class="px-2 form-label"
                      >Image</label
                    >
                  </div>
                  <div class="card-body">
                    <p>
                      <span v-if="!content.image.required"> Optional:</span>
                      Select an image to display on this slide
                    </p>
                    <input class="form-control" type="file" id="formFile" />
                  </div>
                </div>
              </div>
              <div class="card mb-3">
                <div class="card-header">
                  <button
                    id="btnExtraSettings"
                    class="btn"
                    @click="showSettings = !showSettings"
                  >
                    <span class="form-label me-2">Settings</span
                    ><font-awesome-icon
                      v-if="!showSettings"
                      :icon="['fas', 'chevron-down']"
                    />
                    <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                  </button>
                </div>
                <div v-if="showSettings">
                  <div class="card-body">
                    <div
                      v-if="settings.selectedLimit"
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
                            v-model="settings.selectedLimit.min"
                            @change="keepSelectedLimitsWithinMinMax"
                            min="1"
                            :max="options.length ? options.length : 1"
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
                            v-model="settings.selectedLimit.max"
                            @change="keepSelectedLimitsWithinMinMax"
                            min="1"
                            :max="options.length ? options.length : 1"
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
                      v-if="settings.submissionLimit"
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
                            v-model.lazy="settings.submissionLimit"
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
                      v-if="settings.characterLimit"
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
                            v-model.lazy="settings.characterLimit.max"
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
                      v-if="settings.hideResponses != undefined"
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
                          v-model="settings.hideResponses"
                          class="form-check-input"
                          type="checkbox"
                          id="hideResponses"
                          name="hideResponses"
                        />
                      </div>
                    </div>
                    <div
                      v-if="
                        !config.interaction.create.slides.types[type]
                          .isInteractive
                      "
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
                          v-model="settings.hostScreenOnly"
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
          <div class="text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditInteractionForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? 'Add' : 'Update' }} slide
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
