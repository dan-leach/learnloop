<script setup>
const props = defineProps(['interaction']);
const emit = defineEmits(['submit']);
let submit = () => emit('submit');
</script>

<template>
  <p class="text-center">{{ interaction.title }}</p>
  <div v-for="(option, index) in interaction.options" class="form-check">
    <input
      type="radio"
      class="form-check-input"
      :id="'option-' + index"
      :name="'option-' + index"
      :value="index"
      v-model="interaction.response"
    />{{ option }}
    <label class="form-check-label" :for="'option-' + index"></label>
  </div>
</template>

<style scoped></style>
