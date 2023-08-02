<script setup>
import { onMounted, ref } from 'vue'
import Quote from '../../components/Quote.vue'
import { interactSession } from '../../data/interactSession.js'

onMounted(() => {
  console.log('FeedbackView mounted')
})
</script>

<template>
  <h1>Interact page</h1>
  {{ interactSession.id }}
  <Quote />
</template>

<style>

</style>
