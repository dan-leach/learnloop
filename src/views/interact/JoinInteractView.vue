<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { interactSession } from '../../data/interactSession.js'
import { api } from '../../data/api.js'

let fetchDetails = () => {
    let response = api('interact', 'fetchDetails', interactSession.id, null, null)
    if (response) {
        if (interactSession.id != response.id) {
            console.error('interactSession.id != response.id', interactSession.id, response.id)
            return
        }
        interactSession.title = response.title
        interactSession.name = response.name
    } else {
        console.error('api response error')
    }
}

onMounted(() => {
    interactSession.id = useRouter().currentRoute.value.path.replace("/interact/","")
    fetchDetails()
})

</script>

<template>
  <h2>Join Interact</h2>
  <p>
    Title: {{ interactSession.title }}<br>
    Name: {{ interactSession.name }}
  </p>
</template>

<style>
</style>

