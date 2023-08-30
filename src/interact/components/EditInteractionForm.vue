<script setup>
import {ref, watch} from 'vue'
import { config } from '../../data/config.js';
import Swal from 'sweetalert2';

const props = defineProps(['index', 'interaction']);
const emit = defineEmits(['hideEditInteractionModal']);

let prompt = ref('');
let type = ref('');
let options = ref([]);
let settings = ref({});
if (props.interaction.value) {
  console.log(props.interaction.value)
  prompt.value = props.interaction.prompt
  type.value = props.interaction.type
  options.value = props.interaction.options
  settings.value = props.interaction.settings
}
watch(type, () => settings.value = config.interact.create.interactions.types[type.value].settings)


let newOption = ref('');
const addOption = () => {
  if (newOption.value) {
    console.log(options)
    options.value.push(newOption.value)
    newOption.value = ''
    if (props.interaction.type.config.selectedLimit) if (props.interaction.type.config.selectedLimit.max == options.value.length-1) props.interaction.type.config.selectedLimit.max++
  } else {
    document.getElementById('newOption').classList.add('is-invalid')
    setTimeout(() => document.getElementById('newOption').classList.remove('is-invalid'), 3000)
  }
}
const removeOption = (index) => options.value.splice(index,1)
const sortOption = (index, x) => options.value.splice(index+x, 0, options.value.splice(index, 1)[0])

let showExtraSettings = ref(false)

let submit = () => {
  newOption.value = ''
  document.getElementById('editInteractionModal' + props.index).classList.add('was-validated');
  if (!prompt || !type) return false;
  if (settings.optionsLimit == 0) options.value = []
  if (options.value.length > settings.optionsLimit) {
    Swal.fire({
      icon: 'error',
      title: 'Too many options added',
      text: "You can have up to " + settings.optionsLimit + " options for the interaction type selected.",
    });
    return false;
  }
  emit('hideEditInteractionModal', props.index, {prompt: prompt.value, type: type.value, options: options.value, settings: settings.value});
  if (props.interaction.value) {
    props.interaction.prompt = prompt.value
    props.interaction.type = type.value
    props.interaction.options = options.value
    props.interaction.settings = settings.value
  } else {
    prompt.value = ''
    type.value = undefined
    options.value.splice(0)
  }
};

</script>

<template>
  <div class="modal fade" :id="'editInteractionModal' + index">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {{(index<0)? 'Add' : 'Edit'}} an interaction
          </h4>
          <button
            type="button"
            class="btn-close"
            @click.prevent="emit('hideEditInteractionModal', null)"
          ></button>
        </div>
        <div class="modal-body">
          <div id="editInteractionForm" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="prompt" class="form-label"
                >Prompt:</label
              >
              <input type="text" v-model="prompt" class="form-control" id="prompt" placeholder="Prompt for this interaction..." name="prompt" autocomplete="off" required>
              <div class="invalid-feedback">
                Please provide a prompt for this interaction.
              </div>
            </div>
            <div class="mb-4">
              <label for="type" class="form-label"
                >Type:</label
              >
              <select v-model="type" class="form-control" id="type" name="type" autocomplete="off" required>
                <option disabled value="">Please select an interaction type</option>
                <option v-for="(type) in config.interact.create.interactions.types" :value="type.id">{{ type.name }}</option>
              </select>
              <div class="invalid-feedback">
                Please select an interaction type.
              </div>
            </div>
            <div v-if="type">
              <div v-if="settings.optionsLimit" class="mb-4">
                <label for="newOption" class="form-label"
                  >Options:</label
                >
                <table class="table" id="optionsTable" v-if="options">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Option</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(option, index) in options">
                      <td>
                          <button v-if="index != 0" style="float:left" class="btn btn-default btn-sm" id="btnSortUp" @click.prevent="sortOption(index, -1)"><font-awesome-icon :icon="['fas', 'chevron-up']" /></button>
                          <button v-if="index != options.length-1" style="float:left" class="btn btn-default btn-sm" id="btnSortDown" @click.prevent="sortOption(index, 1)"><font-awesome-icon :icon="['fas', 'chevron-down']" /></button>
                      </td>
                      <td>{{option}}</td>
                      <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveOption" @click.prevent="removeOption(index)"><font-awesome-icon :icon="['fas', 'trash-can']" /></button></td>
                    </tr>
                  </tbody>
                </table>
                <div class="input-group mb-3">
                  <input type="text" @keyup.enter="addOption" class="form-control" id="newOption" v-model="newOption" :placeholder="(options.length >= settings.optionsLimit) ? 'Max options reached' : 'Add an option...'" name="newOption" autocomplete="off" :required="!options.length" :disabled="options.length >= settings.optionsLimit">
                  <button class="btn btn-success btn-sm" @click.prevent="addOption" :disabled="options.length >= settings.optionsLimit">Add</button>
                </div>
                <div class="invalid-feedback">
                  Please define options for this interaction.
                </div>
              </div>
              <div class="card">
                <div class="card-header">
                  <button id="btnExtraSettings" class="btn" @click="showExtraSettings = !showExtraSettings">
                    <span class="me-2">Extra settings</span><font-awesome-icon v-if="!showExtraSettings" :icon="['fas', 'chevron-down']" /> <font-awesome-icon v-else :icon="['fas', 'chevron-up']" />
                  </button>
                </div>
                <div v-if="showExtraSettings">
                  <div class="card-body">
                    <div v-if="settings.selectedLimit" class="mb-4">
                      <label for="selectedLimit" class="form-label"
                        >How many options can attendees select:</label
                      >
                      <div class="input-group" id="selectedLimit">
                        <span class="input-group-text">At least:</span>
                        <input type="number" v-model="interaction.type.config.selectedLimit.min" min="1" :max="options.length" class="form-control" id="selectedLimitMin" name="selectedLimit" autocomplete="off">
                        <span class="input-group-text ms-2">Not more than:</span>
                        <input type="number" v-model="interaction.type.config.selectedLimit.max" min="1" :max="options.length" class="form-control" id="selectedLimitMax" placeholder="1" name="selectedLimit" autocomplete="off">
                      </div>
                      <div class="invalid-feedback">
                        Please enter appropriate values.
                      </div>
                    </div>
                    <div v-if="settings.submissionLimit" class="mb-4">
                      <label for="submissionLimit" class="form-label"
                        >Allow attendees to submit multiple answers:</label
                      >
                      <div class="input-group" id="submissionLimit">
                        <span class="input-group-text">Up to:</span>
                        <input type="number" v-model="settings.submissionLimit" min="1" :max="config.interact.create.interactions.submissionLimitMax" class="form-control" id="selectedLimitMin" name="selectedLimit" autocomplete="off" required>
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
            {{(index<0)? 'Add' : 'Edit'}} interaction
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.collapsed .extraSettingsHide {
  display: none;
}
:not(.collapsed).extraSettingsShow {
  display: none;
}
</style>
