<script setup>
import { ref, watch } from "vue";
import { feedbackSession } from "../../data/feedbackSession.js";
import { config } from "../../data/config.js";
import Swal from "sweetalert2";

const props = defineProps(["index"]);
const emit = defineEmits(["hideEditQuestionModal"]);

let title = ref("");
let type = ref("");
let options = ref([]);
let settings = ref({});

if (props.index > -1) {
  const question = feedbackSession.questions[props.index];
  title.value = question.title;
  type.value = question.type;
  options.value = question.options;
  settings.value = question.settings;
}

watch(type, (newType, oldType) => {
  if (type.value) {
    settings.value =
      config.feedback.create.questions.types[type.value].settings;
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
        <p>There are several different custom question types available to capture feedback from your attendees. Click below to see examples and further details about each:</p>
        <div class="accordion" id="accordionTypes">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Text
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/question-type-example-text.png" class="img-fluid mx-auto d-block">
                <p>Text questions will show your prompt above a free-text input area. You can set the question to be compulsory (default) or optional, and set a character limit (default: 500 characters).</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Drop-down select
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/question-type-example-select.png" class="img-fluid mx-auto d-block">
                <p>Drop-down select will show a drop-down menu listing your defined options. Users can only select one of the options. You can define up to 20 options and set the question to be compulsory (default) or optional.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Checkboxes
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionTypes">
              <div class="accordion-body">
                <img src="https://dev.learnloop.co.uk/img/question-type-example-checkbox.png" class="img-fluid mx-auto d-block">
                <p>Checkbox questions will show a list of your defined options. Users can select multiple questions. By default they must select between 1 and all the options, but you can set a minimum and maximum number, as well as setting the question to be compulsory (default) or optional. </p>
              </div>
            </div>
          </div>
        </div>     
      </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

let newOption = ref("");
const addOption = () => {
  if (newOption.value) {
    options.value.push({ title: newOption.value });
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
    .getElementById("editQuestionModal" + props.index)
    .classList.add("was-validated");
  if (!type.value) return false;
  if (settings.value.optionsLimit == 0) {
    options.value = [];
  } else if (
    options.value.length < config.feedback.create.questions.minimumOptions
  ) {
    Swal.fire({
      icon: "error",
      iconColor: "#17a2b8",
      title: "Too few options added",
      text:
        "You need to add at least " +
        config.feedback.create.questions.minimumOptions +
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
        " options for the question type selected.",
      confirmButtonColor: "#17a2b8",
    });
    return false;
  }
  if (!title.value) return false;
  if (settings.value.selectedLimit) keepSelectedLimitsWithinMinMax();
  emit(
    "hideEditQuestionModal",
    props.index,
    JSON.stringify({
      title: title.value,
      type: type.value,
      options: options.value,
      settings: settings.value,
    })
  );
  if (props.index == -1) {
    title.value = "";
    type.value = "";
    options.value = [];
    settings.value = {};
  }
  document
    .getElementById("editQuestionModal" + props.index)
    .classList.remove("was-validated");
};
</script>

<template>
  <div class="modal fade" :id="'editQuestionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? "Add a" : "Edit" }} question
          </h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditQuestionModal')"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editQuestionForm" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="title" class="form-label">Question:</label>
              <input
                type="text"
                v-model="title"
                class="form-control"
                id="title"
                placeholder="Please enter your question..."
                name="title"
                autocomplete="off"
                required
              />
              <div class="invalid-feedback">Please provide a question.</div>
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
                <option disabled value="">Please select a question type</option>
                <option
                  v-for="type in config.feedback.create.questions.types"
                  :value="type.id"
                >
                  {{ type.name }}
                </option>
              </select>
              <div class="invalid-feedback">Please select a question type.</div>
            </div>
            <div v-if="type">
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
                      <td>{{ option.title }}</td>
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
                  Please provide some options for this question.
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
                    <div class="mb-4">
                      <div class="form-check form-switch">
                        <input
                          v-model="settings.required"
                          class="form-check-input"
                          type="checkbox"
                          id="required"
                          name="required"
                        />
                        <label class="form-check-label" for="required"
                          >Make a response to this question compulsory</label
                        >
                      </div>
                    </div>
                    <div v-if="settings.selectedLimit" class="mb-4">
                      <label for="selectedLimit" class="form-label"
                        >Number of options respondents must select:</label
                      >
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
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 text-center">
            <button
              class="btn btn-teal text-center"
              id="submitEditQuestionForm"
              v-on:click.prevent="submit"
            >
              {{ index < 0 ? "Add" : "Update" }} question
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
