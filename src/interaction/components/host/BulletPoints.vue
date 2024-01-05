<script setup>
const props = defineProps(['slide']);
</script>

<template>
  <div class="bullets-container d-flex flex-column justify">
    <ul>
      <li v-for="bullet in slide.content.bullets">{{ bullet }}</li>
    </ul>
  </div>
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.bullets-container {
  font-size: 1.5em;
}
</style>
