<script setup>
import { ref, watch } from 'vue';
import { interactSession } from '../../data/interactSession.js';
import { config } from '../../data/config.js';
import Swal from 'sweetalert2';

const props = defineProps(['index']);
const emit = defineEmits(['hideEditInteractionModal']);

let prompt = ref('');
let type = ref('');
let options = ref([]);
let settings = ref({});

if (props.index > -1) {
  const interaction = interactSession.interactions[props.index];
  prompt.value = interaction.prompt;
  type.value = interaction.type;
  options.value = interaction.options;
  settings.value = interaction.settings;
}

watch(type, (newType, oldType) => {
  if (type.value)
    settings.value =
      config.interact.create.interactions.types[type.value].settings;
  if (settings.value.selectedLimit) {
    settings.value.selectedLimit.max = options.value.length;
    keepSelectedLimitsWithinMinMax();
  }
});

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
  newOption.value = '';
  document
    .getElementById('editInteractionModal' + props.index)
    .classList.add('was-validated');
  if (!type.value) return false;
  if (settings.value.optionsLimit == 0) {
    options.value = [];
  } else if (
    options.value.length < config.interact.create.interactions.minimumOptions
  ) {
    Swal.fire({
      icon: 'error',
      title: 'Too few options added',
      text:
        'You need to add at least ' +
        config.interact.create.interactions.minimumOptions +
        ' options.',
    });
    return false;
  }
  if (options.value.length > settings.value.optionsLimit) {
    Swal.fire({
      icon: 'error',
      title: 'Too many options added',
      text:
        'You can have up to ' +
        settings.value.optionsLimit +
        ' options for the interaction type selected.',
    });
    return false;
  }
  if (!prompt.value) return false;
  if (settings.value.selectedLimit) keepSelectedLimitsWithinMinMax();
  emit(
    'hideEditInteractionModal',
    props.index,
    JSON.stringify({
      prompt: prompt.value,
      type: type.value,
      options: options.value,
      settings: settings.value,
    })
  );
  if (props.index == -1) {
    prompt.value = '';
    type.value = '';
    options.value = [];
    settings.value = {};
  }
  document
    .getElementById('editInteractionModal' + props.index)
    .classList.remove('was-validated');
};
</script>

<template>
  <div class="modal fade" :id="'editInteractionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{ index < 0 ? 'Add an' : 'Edit' }} interaction
          </h4>
          <button
            v-if="index == -1"
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditInteractionModal', null)"
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
              <label for="type" class="form-label">Type:</label>
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
              <div v-if="settings.optionsLimit" class="mb-4">
                <label for="newOption" class="form-label">Options:</label>
                <table class="table" id="optionsTable">
                  <tbody>
                    <tr v-for="(option, index) in options">
                      <td>
                        <button
                          v-if="index != 0"
                          style="float: left"
                          class="btn btn-default btn-sm"
                          id="btnSortUp"
                          @click.prevent="sortOption(index, -1)"
                        >
                          <font-awesome-icon :icon="['fas', 'chevron-up']" />
                        </button>
                        <button
                          v-if="index != options.length - 1"
                          style="float: left"
                          class="btn btn-default btn-sm"
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
                  </tbody>
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
                        >Number of options attendees must select:</label
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
                    <div v-if="settings.submissionLimit" class="mb-4">
                      <label for="submissionLimit" class="form-label"
                        >Number of times attendees can submit a response to this
                        interaction:</label
                      >
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
                          >Hide attendee responses until you reveal them</label
                        >
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
              {{ index < 0 ? 'Add' : 'Update' }} interaction
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
