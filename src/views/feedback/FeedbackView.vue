<script setup>
import { onMounted, ref } from 'vue'
import Quote from '../../components/Quote.vue'
import { feedbackSession } from '../../data/feedbackSession.js'

onMounted(() => {
  console.log('FeedbackView mounted')
})
</script>

<template>
  <h1>Feedback page</h1>
  {{ feedbackSession.id }}
  <Quote />
</template>

<style>

</style>
