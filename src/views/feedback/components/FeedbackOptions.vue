<script setup>

</script>

<template>
    <div class="quote text-center m-4">
        <span>"{{quote.text}}"</span><br>
        <i>- {{quote.cite}}</i><br>
        <a @click="refresh()">
          <font-awesome-icon :icon="['fas', 'rotate-right']" class="refresh"/>
        </a>
    </div>
</template>

<style scoped>
.refresh {
  font-size: 1.5rem;
  color: black;
  transition: font-size 0.2s ease-out, rotate 0.2s ease-out;
}
.refresh:hover {
  font-size: 2.5rem;
  rotate: 90deg;
}
</style>
