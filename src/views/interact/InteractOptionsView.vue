<script setup>
import router from '../../router'
import Quote from '../../components/Quote.vue'
import { interactSession } from '../../data/interactSession.js'

let joinSession = () => {
  if (interactSession.id) { router.push('/interact/'+interactSession.id) }
  else { document.getElementById("sessionID").classList.add("is-invalid") }
}

</script>

<template>
  <p class="text-center">Engage your audience with live interactions.</p>
  <div class="d-flex justify-content-around flex-wrap">
      <div class="card p-2 m-2 bg-teal" id="joinSessionCard">
          <h4 class="card-title">Join session</h4>
          <p class="card-text">The session facilitator will have provided you with a session ID code. Enter the code in the search box below, then click 'Join session'.
              <br><br>
              <div class="input-group">
                  <input id="sessionID" v-model="interactSession.id" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
                  <button type="button" id="joinSession" class="btn btn-primary" @click="joinSession">Join session</button>
              </div>
          </p>
      </div>
      <div class="card p-2 m-2 bg-teal" id="createSessionCard">
          <h4 class="card-title">Create session</h4>
          <p class="card-text">To set up a new interact session, and generate a link or QR code to share with attendees, click 'Create new session'.
              <br><br>
              <div class="text-center">
                  <button class="btn btn-primary" id="startCreateSession" @click="router.push('/interact/createSession')">Create new session</button>
              </div>
          </p>
      </div>
      <div class="card p-2 m-2 bg-teal" id="otherOptionsCard">
          <h4 class="card-title">More options</h4>
          <p class="card-text">Select the option you require.
              <br>
              <div class="text-center">
                  <button type="button" id="loadPinUpdateDetails" class="btn btn-primary m-1" v-on:click="loadPinUpdateDetails">Edit session</button>
                  <button type="button" id="loadPinViewSubmissions" class="btn btn-primary m-1" v-on:click="loadPinViewSubmissions">View submissions</button>
                  <button type="button" id="closeSession" class="btn btn-primary m-1" v-on:click="closeSession">Close session</button>
                  <button type="button" id="resetPIN" class="btn btn-primary m-1" v-on:click="resetPIN">Reset PIN</button>
                  <button type="button" id="findMySessions" class="btn btn-primary m-1" v-on:click="findMySessions">Find my sessions</button>
              </div>
          </p>
      </div>
  </div>
  <Quote />
</template>

<style scoped>
.card {
  width: 30%;
  min-width: 300px;
}
</style>
