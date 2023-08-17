<script setup>
import SingleChoice from './SingleChoice.vue';
const props = defineProps(['index', 'currentIndex', 'interaction']);

let submit = () => {
  console.log('submit', props.interaction.id);
};
</script>

<template>
  <div v-if="index == currentIndex">
    <p class="text-center">{{ interaction.title }}</p>
    <div class="d-flex justify-content-center">
      <div>
        <SingleChoice
          v-if="interaction.type == 'singleChoice'"
          :interaction="interaction"
        />
      </div>
    </div>
    <div class="text-center m-3">
      <button type="button" class="btn btn-teal" @click="submit">Submit</button>
    </div>
  </div>
</template>

<style scoped>
.interactionComponentType {
  margin-right: auto;
  margin-left: auto;
}
</style>
