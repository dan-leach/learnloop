<script setup>
const props = defineProps(["slide"]);
</script>

<template>
  <div class="d-flex flex-column">
    <div class="free-text-container d-flex flex-column justify">
      <div
        class="card m-1 p-2"
        v-for="(submission, index) in slide.interaction.submissions"
      >
        <table>
          <tr>
            <td>{{ submission.response }}</td>
            <td class="cell-btnClose">
              <button
                type="button"
                class="btn-close float-right mx-2"
                @click="slide.interaction.submissions.splice(index, 1)"
              ></button>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.free-text-container {
  overflow-y: auto;
}
.slide-image {
  width: 50%;
}
</style>
