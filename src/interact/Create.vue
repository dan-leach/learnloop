<script setup>
import { interactSession } from '../data/interactSession.js';
import { api } from '../data/api.js';
import { config } from '../data/config.js';
import Modal from 'bootstrap/js/dist/modal';
import EditInteractionForm from './components/EditInteractionForm.vue';

let editInteractionModal;
const showEditInteractionForm = (index) => {
  editInteractionModal = new Modal(
    document.getElementById('editInteractionModal' + index),
    {
      backdrop: 'static',
      keyboard: false,
      focus: true,
    }
  );
  editInteractionModal.show();
};
const hideEditInteractionModal = (index) => {
  editInteractionModal.hide();
}
const sortInteraction = (index, x) => interactSession.interactions.splice(index+x, 0, interactSession.interactions.splice(index, 1)[0])
const removeInteraction = (index) => interactSession.interactions.splice(index, 1)
</script>

<template>
  <h1 class="text-center display-4">Interact</h1>
  <strong>Please provide details for the interact session you're creating.</strong>
    <form id="createSessionSeriesForm" class="needs-validation" novalidate>
        <div>
          <label for="title">Session series title:</label>
          <input type="text" v-model="interactSession.title" class="form-control" id="title" placeholder="Title for the interact session..." name="title" autocomplete="off" required>
          <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="mt-4">
            <label for="date">Session series date:</label>
            <input type="date" v-model="interactSession.date" class="form-control" id="date" placeholder="dd/mm/yyyy" name="date" autocomplete="off" required>
            <div class="invalid-feedback">Please select a date.</div>
        </div>
        <div class="mt-4">
            <label for="name">Facilitator name:</label>
            <input type="text" v-model="interactSession.name" class="form-control" id="name" placeholder="Facilitator of the interact session..." name="name" autocomplete="off" required>
            <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="mt-4">
            <label for="email">Facilitator email:</label>
            <input type="email" v-model="interactSession.email" class="form-control" id="email" placeholder="Email to send session details to..." name="email" autocomplete="off" required>
            <div class="invalid-feedback">Please fill out this field.</div>
        </div>
    </form>

    <h2 class="mt-4">Interactions</h2>
    <table class="table" id="interactionsTable">
      <thead>
        <tr>
          <th></th>
          <th>Prompt</th>
          <th>Type</th>
          <th></th>
          <th><button class="btn btn-success btn-sm btn-right" id="btnAddInteraction" @click.prevent="showEditInteractionForm(-1)">Add <i class="fas fa-plus"></i></button></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(interaction, index) in interactSession.interactions">
          <td>
              <button v-if="index != 0" style="float:left" class="btn btn-default btn-sm" id="btnSortUp" @click="sortInteraction(index, -1)"><i class="fas fa-chevron-up"></i></button>
              <button v-if="index != interactSession.interactions.length-1" style="float:left" class="btn btn-default btn-sm" id="btnSortDown" @click="sortInteraction(index, 1)"><i class="fas fa-chevron-down"></i></button>
          </td>
          <td>{{interaction.prompt}}</td>
          <td>{{config.interact.create.interactions.types[interaction.type].name}}</td>
          <td><button class="btn btn-secondary btn-sm btn-right" id="btnEditInteraction" @click="showEditInteractionForm(index)"><font-awesome-icon :icon="['fas', 'edit']" /></button></td>
          <td><button class="btn btn-danger btn-sm btn-right" id="btnRemoveInteraction" @click="removeInteraction(index)"><font-awesome-icon :icon="['fas', 'trash-can']" /></button></td>
        </tr>
      </tbody>
    </table>
    <EditInteractionForm
      v-for="(interaction, index) in interactSession.interactions"
      :index="index"
      @hideEditInteractionModal="hideEditInteractionModal"
    />
    <EditInteractionForm
      index=-1
      @hideEditInteractionModal="hideEditInteractionModal"
    />
    <div class="text-center mt-4">
        <button class="btn btn-teal" id="submitCreateSession" @click="submit">Create interact session</button>
    </div>
</template>

<style></style>
