<script setup>
import SingleChoice from './SingleChoice.vue';
const props = defineProps(['index', 'currentIndex', 'interaction']);
console.log(props.index, props.interaction);
</script>

<template>
  <div v-if="index == currentIndex">
    {{ interaction.title }}<br />
    <SingleChoice
      v-if="interaction.type == 'singleChoice'"
      :interaction="interaction"
    />
  </div>
</template>

<style scoped></style>
