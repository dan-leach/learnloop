<script setup>
const props = defineProps(["slide"]);
</script>

<template>
  <div
    class="d-flex flex-row flex-wrap column-container justify-content-evenly"
  >
    <div class="d-flex flex-column card-column" v-for="colIndex in 3">
      <div v-for="(submission, index) in slide.interaction.submissions">
        <div
          class="card m-1 p-2"
          v-if="index % 3 === colIndex - 1 && !submission.hide"
        >
          <table>
            <tr>
              <td>{{ submission.response }}</td>
              <td class="cell-btnClose">
                <button
                  type="button"
                  class="btn-close float-right mx-2"
                  @click="slide.interaction.submissions[index].hide = true"
                ></button>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.column-container {
  overflow-y: auto;
}
.card-column {
  width: 32%;
  min-width: 200px;
  max-width: 500px;
}
</style>
