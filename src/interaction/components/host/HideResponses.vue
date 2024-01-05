<script setup>
const emit = defineEmits(['showResponses']);
const props = defineProps(['slide']);
</script>

<template>
  <div class="text-center m-4">
    <p class="display-6 card p-3 responses-info">
      <strong>
        <transition name="bounce" mode="out-in">
          <span :key="slide.submissions.length">
            {{ slide.submissions.length }}
          </span>
        </transition>
      </strong>
      responses
    </p>
    <button class="btn btn-teal btn-lg m-4" @click="emit('showResponses')">
      Reveal responses
    </button>
  </div>
</template>

<style scoped>
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
