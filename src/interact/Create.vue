<script setup>
import { interactSession } from '../data/interactSession.js';
import { api } from '../data/api.js';
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
const hideEditInteractionModal = (index, newInteraction) => {
  if (index == -1) interactSession.interactions.push(newInteraction)
  editInteractionModal.hide();
}

const removeInteraction = (index) => {
  console.log('todo')
}
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
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(interaction, index) in interactSession.interactions">
          <td>
              <button v-if="index != 0" style="float:left" class="btn btn-default btn-sm" id="btnSortUp" v-on:click="sortInteraction(index, -1)"><i class="fas fa-chevron-up"></i></button>
              <button v-if="index != interactSession.interactions.length-1" style="float:left" class="btn btn-default btn-sm" id="btnSortDown" v-on:click="sortInteraction(index, 1)"><i class="fas fa-chevron-down"></i></button>
          </td>
          <td>{{interaction.prompt}}</td>
          <td>{{interaction.type}}</td>
          <td><button style="float:right" class="btn btn-secondary btn-sm" id="btnEditInteraction" @click="showEditInteractionForm(index)"><font-awesome-icon v-if="!showExtraSettings" :icon="['fas', 'edit']" /></button></td>
          <td><button style="float:right" class="btn btn-danger btn-sm" id="btnRemoveInteraction" @click="removeInteraction(index)"><font-awesome-icon v-if="!showExtraSettings" :icon="['fas', 'trash-can']" /></button></td>
        </tr>
      </tbody>
    </table>
    <EditInteractionForm
      v-for="(interaction, index) in interactSession.interactions"
      :index="index"
      :interaction="interaction"
      @hideEditInteractionModal="hideEditInteractionModal"
    />
    <button style="float:right;" class="btn btn-success btn-sm" id="btnAddInteraction" @click.prevent="showEditInteractionForm(-1)">Add <i class="fas fa-plus"></i></button>
    <EditInteractionForm
      index=-1
      :interaction="{}"
      @hideEditInteractionModal="hideEditInteractionModal"
    />
    <div class="text-center">
        <button class="btn btn-primary" id="submitCreateSession" v-on:click="submit">Create interact session</button>
    </div>
</template>

<style></style>
