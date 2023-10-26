<script setup>
const props = defineProps(['interaction']);

</script>

<template>
  <div class="short-text-container d-flex flex-column justify">
    <div class="card m-1 p-2" v-for="(submission, index) in interaction.submissions">
      <table>
        <tr>
          <td>{{ submission.response }}</td>
          <td class="cell-btnClose"><button type="button" class="btn-close float-right mx-2" @click="interaction.submissions.splice(index, 1)"></button></td>
        </tr>
      </table>
    </div>
  </div>
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.short-text-container {
  overflow-y: auto;
}
</style>
