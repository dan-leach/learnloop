<script setup>
const emit = defineEmits(["showResponses"]);
const props = defineProps(["slide"]);
</script>

<template>
  <div class="text-center m-4">
    <div class="d-flex justify-content-center flex-wrap">
      <p class="card p-3">
        <span class="display-5 mb-2">Options</span>
        <ul class="text-start">
          <li v-for="option in slide.interaction.options" :key="option" class="display-6">
            {{ option.text }}
          </li>
        </ul>
      </p>
      <p class="card p-3">
        <span class="display-5">Responses</span>
        <strong>
          <transition name="bounce" mode="out-in">
            <span :key="slide.interaction.submissions.length" class="display-4">
              {{ slide.interaction.submissions.length }}
            </span>
          </transition>
        </strong>
      </p>
    </div>
    <button class="btn btn-teal btn-lg m-2" @click="emit('showResponses')" v-if="!slide.hasContent">
      Reveal responses
    </button>
  </div>
</template>

<style scoped>
.card {
  min-width: 250px
}
.d-flex {
  gap: 1rem; /* Adjust the value as needed for the desired spacing */
}
.display-5 {
  font-size: 1.6em;
}
.display-6 {
  font-size: 1.2em;
}
.bounce-enter-active {
  animation: bounce-in 0.5s;
}
.bounce-leave-active {
  animation: bounce-in 0.5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.25);
  }
  100% {
    transform: scale(1);
  }
}
</style>
