<script setup>
import { ref, watch } from "vue";
import { interactSession } from "../../data/interactSession.js";
import { config } from "../../data/config.js";
import Swal from "sweetalert2";

const props = defineProps(["index"]);
const emit = defineEmits(["hideEditInteractionModal"]);

let prompt = ref("");
let type = ref("");
let options = ref([]);
let settings = ref({});
let chart = ref("");
let charts = ref("");

if (props.index > -1) {
  const interaction = interactSession.interactions[props.index];
  prompt.value = interaction.prompt;
  type.value = interaction.type;
  options.value = interaction.options;
  settings.value = interaction.settings;
}

watch(type, (newType, oldType) => {
  if (type.value) {
    settings.value =
      config.interact.create.interactions.types[type.value].settings;
    charts.value = config.interact.create.interactions.types[type.value].charts;
  }
  if (settings.value.selectedLimit) {
    settings.value.selectedLimit.max = options.value.length;
    keepSelectedLimitsWithinMinMax();
  }
});

const questionTypeInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Question types",
    html: `
      <div class="text-start">
        <p>There are several different interaction types available. Click below to see examples and further details about each:</p>
        <div class="accordion" id="accordionTypes">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Single choice
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/interaction-type-example-single-choice.png" class="img-fluid mx-auto d-block">
                <p>Single choice interactions allow you to provide a selection of options from which attendees can choose only one. By default attendees will be able to respond only once but you can allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the interaction.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Multiple choice
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/interaction-type-example-multiple-choice.png" class="img-fluid mx-auto d-block">
                <p>Multiple choice interactions allow you to provide a selection of options from which attendees can choose. By default attendees must chose at least 1 option and can select as many as they wish, but optionally you can set a minimum and maximum number of options per response. By default attendees will be able to respond only once but you can optionally allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the interaction.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Short text
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/interaction-type-example-short-text.png" class="img-fluid mx-auto d-block">
                <p>Short text interactions allow attendees to provide short free text responses which appear on the host screen. By default attendees will be able to respond 10 times but you can set a different number of allowed responses per person. To keep responses short the default character limit is 200, but you can set this to a different value if required. You can set the responses not to appear on the host view until after you reveal them by activating this option in settings when creating the interaction.</p>
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
                <img src="https://dev.learnloop.co.uk/img/interaction-chart-example-bar.png" class="img-fluid mx-auto d-block">
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
                <img src="https://dev.learnloop.co.uk/img/interaction-chart-example-doughnut.png" class="img-fluid mx-auto d-block">
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
        <img src="https://dev.learnloop.co.uk/img/interaction-selection-min.png" class="img-fluid mx-auto d-block">
        <p>Or, if they select more options than the maximum, they will receive an alert like this one:</p>
        <img src="https://dev.learnloop.co.uk/img/interaction-selection-max.png" class="img-fluid mx-auto d-block">
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
        <p>By default attendees can respond only once to interactions (10 times for text interactions). You can change this number if required. Once they have responded the maximum number of times the interaction will be disabled on their device:</p>
        <img src="https://dev.learnloop.co.uk/img/interaction-submission-limit.png" class="img-fluid mx-auto d-block">
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
        <img src="https://dev.learnloop.co.uk/img/interaction-hide-responses.png" class="img-fluid mx-auto d-block">
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let newOption = ref("");
const addOption = () => {
  if (newOption.value) {
    options.value.push(newOption.value);
    newOption.value = "";
    if (settings.value.selectedLimit)
      if (settings.value.selectedLimit.max == options.value.length - 1)
        settings.value.selectedLimit.max++;
  } else {
    document.getElementById("newOption").classList.add("is-invalid");
    setTimeout(
      () => document.getElementById("newOption").classList.remove("is-invalid"),
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

let showSettings = ref(false);

const keepSubmissionLimitWithinMinMax = () => {
  const submissionLimit = settings.value.submissionLimit;
  if (submissionLimit < 1) settings.value.submissionLimit = 1;
  else if (
    submissionLimit > config.interact.create.interactions.submissionLimitMax
  )
    settings.value.submissionLimit =
      config.interact.create.interactions.submissionLimitMax;
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
  newOption.value = "";
  document
    .getElementById("editInteractionModal" + props.index)
    .classList.add("was-validated");
  if (!type.value) return false;
  if (settings.value.optionsLimit == 0) {
    options.value = [];
  } else if (
    options.value.length < config.interact.create.interactions.minimumOptions
  ) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Too few options added",
      text:
        "You need to add at least " +
        config.interact.create.interactions.minimumOptions +
        " options.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (options.value.length > settings.value.optionsLimit) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Too many options added",
      text:
        "You can have up to " +
        settings.value.optionsLimit +
        " options for the interaction type selected.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (!prompt.value) return false;
  if (settings.value.selectedLimit) keepSelectedLimitsWithinMinMax();
  emit(
    "hideEditInteractionModal",
    props.index,
    JSON.stringify({
      prompt: prompt.value,
      type: type.value,
      chart: chart.value,
      options: options.value,
      settings: settings.value,
    })
  );
  if (props.index == -1) {
    prompt.value = "";
    type.value = "";
    chart.value = "";
    charts.value = [];
    options.value = [];
    settings.value = {};
  }
  document
    .getElementById("editInteractionModal" + props.index)
    .classList.remove("was-validated");
};
</script>

<template>
  <div class="modal fade" :id="'editInteractionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? "Add an" : "Edit" }} interaction
          </h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditInteractionModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editInteractionForm" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="prompt" class="form-label">Prompt:</label>
              <input
                type="text"
                v-model="prompt"
                class="form-control"
                id="prompt"
                placeholder="Question or instruction to attendees..."
                name="prompt"
                autocomplete="off"
                required
              />
              <div class="invalid-feedback">
                Please provide a prompt for this interaction.
              </div>
            </div>
            <div class="mb-4">
              <label for="type" class="form-label"
                >Type:
                <font-awesome-icon
                  :icon="['fas', 'question-circle']"
                  size="l"
                  style="color: black"
                  @click="questionTypeInfo"
              /></label>
              <select
                v-model="type"
                class="form-control"
                id="type"
                name="type"
                autocomplete="off"
                required
              >
                <option disabled value="">
                  Please select an interaction type
                </option>
                <option
                  v-for="type in config.interact.create.interactions.types"
                  :value="type.id"
                >
                  {{ type.name }}
                </option>
              </select>
              <div class="invalid-feedback">
                Please select an interaction type.
              </div>
            </div>
            <div v-if="type">
              <div v-if="charts" class="mb-4">
                <label for="type" class="form-label"
                  >Chart type:
                  <font-awesome-icon
                    :icon="['fas', 'question-circle']"
                    size="l"
                    style="color: black"
                    @click="chartTypeInfo"
                /></label>
                <select
                  v-model="chart"
                  class="form-control"
                  id="chart"
                  name="chart"
                  autocomplete="off"
                  required
                >
                  <option disabled value="">Please select a chart type</option>
                  <option v-for="chart in charts" :value="chart">
                    {{ chart.charAt(0).toUpperCase() + chart.slice(1) }}
                  </option>
                </select>
                <div class="invalid-feedback">Please select a chart type.</div>
              </div>
              <div v-if="settings.optionsLimit" class="mb-4">
                <label for="newOption" class="form-label">Options:</label>
                <table class="table" id="optionsTable">
                  <TransitionGroup name="list" tag="tbody">
                    <tr v-for="(option, index) in options" :key="option">
                      <td class="p-0 ps-2">
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
                          <font-awesome-icon :icon="['fas', 'chevron-down']" />
                        </button>
                      </td>
                      <td>{{ option }}</td>
                      <td>
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
                <div class="input-group mb-3">
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
                    class="btn btn-success btn-sm"
                    @click.prevent="addOption"
                    :disabled="options.length >= settings.optionsLimit"
                  >
                    Add
                  </button>
                </div>
                <div class="invalid-feedback">
                  Please provide some options for this interaction.
                </div>
              </div>
              <div class="card">
                <div class="card-header">
                  <button
                    id="btnExtraSettings"
                    class="btn"
                    @click="showSettings = !showSettings"
                  >
                    <span class="me-2">Settings</span
                    ><font-awesome-icon
                      v-if="!showSettings"
                      :icon="['fas', 'chevron-down']"
                    />
                    <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                  </button>
                </div>
                <div v-if="showSettings">
                  <div class="card-body">
                    <div v-if="settings.selectedLimit" class="mb-4">
                      <label for="selectedLimit" class="form-label"
                        >Number of options attendees must select:
                        <font-awesome-icon
                          :icon="['fas', 'question-circle']"
                          size="l"
                          style="color: black"
                          @click="optionsMinMaxInfo"
                      /></label>
                      <div class="input-group" id="selectedLimit">
                        <span class="input-group-text">Minimum:</span>
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
                        <span class="input-group-text ms-2">Maximum:</span>
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
                      </div>
                      <div class="invalid-feedback">
                        Please enter appropriate values.
                      </div>
                    </div>
                    <div v-if="settings.submissionLimit" class="mb-4">
                      <label for="submissionLimit" class="form-label"
                        >Number of times attendees can submit a response to this
                        interaction:
                        <font-awesome-icon
                          :icon="['fas', 'question-circle']"
                          size="l"
                          style="color: black"
                          @click="submissionLimitInfo"
                      /></label>
                      <div class="input-group" id="submissionLimit">
                        <span class="input-group-text">Maximum:</span>
                        <input
                          type="number"
                          v-model.lazy="settings.submissionLimit"
                          @change="keepSubmissionLimitWithinMinMax"
                          min="1"
                          :max="
                            config.interact.create.interactions
                              .submissionLimitMax
                          "
                          class="form-control"
                          id="selectedLimitMin"
                          name="selectedLimit"
                          autocomplete="off"
                          required
                        />
                      </div>
                    </div>
                    <div v-if="settings.characterLimit" class="mb-4">
                      <label for="characterLimit" class="form-label"
                        >Character limit for each response:</label
                      >
                      <div class="input-group" id="characterLimit">
                        <span class="input-group-text">Maximum:</span>
                        <input
                          type="number"
                          v-model.lazy="settings.characterLimit.max"
                          class="form-control"
                          id="characterLimit"
                          name="characterLimit"
                          autocomplete="off"
                          required
                        />
                      </div>
                    </div>
                    <div
                      v-if="settings.hideResponses != undefined"
                      class="mb-4"
                    >
                      <div class="form-check form-switch">
                        <input
                          v-model="settings.hideResponses"
                          class="form-check-input"
                          type="checkbox"
                          id="hideResponses"
                          name="hideResponses"
                        />
                        <label class="form-check-label" for="hideResponses"
                          >Hide attendee responses until you reveal them
                          <font-awesome-icon
                            :icon="['fas', 'question-circle']"
                            size="l"
                            style="color: black"
                            @click="hideResponsesInfo"
                        /></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditInteractForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? "Add" : "Update" }} interaction
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
</style>
