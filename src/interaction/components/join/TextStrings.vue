<script setup>
/**
 * @module interaction/components/join/TextStrings
 * @summary Handles layout for text content.
 *
 */

// Props from parent
const props = defineProps(["slide"]);
</script>

<template>
  <div
    class="d-flex align-items-center align-content-stretch justify-content-around"
  >
    <div
      class="paragraph-container d-flex flex-column align-items-center justify m-2"
      v-if="slide.content.textStrings.length && !slide.content.useBulletPoints"
    >
      <p v-for="textString in slide.content.textStrings">{{ textString }}</p>
    </div>
    <div
      class="bullets-container d-flex flex-column align-items-center justify m-2"
      v-if="slide.content.textStrings.length && slide.content.useBulletPoints"
    >
      <ul>
        <li v-for="textString in slide.content.textStrings">
          {{ textString }}
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.bullets-container,
.paragraph-container {
  font-size: 1.5em;
  flex: 1 1 0;
}
</style>
