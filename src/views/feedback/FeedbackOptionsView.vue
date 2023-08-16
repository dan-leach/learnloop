<script setup>
import router from '../../router'
import Quote from '../components/Quote.vue'
import { feedbackSession } from '../../data/feedbackSession.js'

let giveFeedback = () => {
  if (feedbackSession.id) { router.push('/feedback/'+feedbackSession.id) }
  else { document.getElementById("sessionID").classList.add("is-invalid") }
}

</script>

<template>
  <p class="text-center">Quickly and easily gather anonymous feedback on teaching.</p>
  <div class="d-flex justify-content-around flex-wrap">
    <div class="card p-2 m-2 bg-teal" id="giveFeedbackCard">
      <h4 class="card-title">Give feedback</h4>
      <p class="card-text">The session facilitator will have provided you with a session ID code. Enter the code in the search box below, then click 'Give feedback'.</p>
      <div class="input-group">
        <input id="sessionID" v-model="feedbackSession.id" type="text" class="form-control" placeholder="Session ID" autocomplete="off">
        <button type="button" class="btn btn-primary" @click="giveFeedback">Give feedback</button>
      </div>
    </div>
    <div class="card p-2 m-2 bg-teal" id="createSessionCard">
        <h4 class="card-title">Request feedback</h4>
        <p class="card-text">To set up a new feedback request, and generate a link or QR code to share with attendees, click 'Create new session'.
            <br><br>
            <div class="text-center">
                <button class="btn btn-primary" id="startCreateSession" @click="router.push('/feedback/createSession')">Create new session</button>
            </div>
        </p>
    </div>
    <div class="card p-2 m-2 bg-teal" id="otherOptionsCard">
        <h4 class="card-title">More options</h4>
        <p class="card-text">Select the option you require.
            <br>
            <div class="text-center">
                <button type="button" id="loadPinUpdateDetails" class="btn btn-primary m-1" @click="loadPinUpdateDetails">Edit session</button>
                <button type="button" id="loadPinViewFeedback" class="btn btn-primary m-1" @click="loadPinViewFeedback">View feedback</button>
                <button type="button" id="loadPinViewAttendance" class="btn btn-primary m-1" @click="loadPinViewAttendance">View attendance</button>
                <button type="button" id="closeSession" class="btn btn-primary m-1" @click="closeSession">Close session</button>
                <button type="button" id="resetPIN" class="btn btn-primary m-1" @click="resetPIN">Reset PIN</button>
                <button type="button" id="findMySessions" class="btn btn-primary m-1" @click="findMySessions">Find my sessions</button>
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
